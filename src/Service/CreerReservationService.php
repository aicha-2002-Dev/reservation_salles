<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\ReglesMetierException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;

final class CreerReservationService
{
    private const DUREE_MAX_HEURES = 4;

    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function creer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->recupererSalleDisponible($dto->salleId);

        $this->verifierPeriodeCoherente($dto);
        $this->verifierReservationDansLeFutur($dto);
        $this->verifierDureeMaximale($dto);
        $this->verifierAbsenceDeConflit($dto);

        return $this->enregistrerReservation($dto);
    }

    private function recupererSalleDisponible(int $salleId): Salle
    {
        $salle = $this->salleRepository->findById($salleId);

        if ($salle === null || !$salle->active) {
            throw new SalleIndisponibleException("La salle demandée n'existe pas ou n'est plus active.");
        }

        return $salle;
    }

    private function verifierPeriodeCoherente(CreerReservationDTO $dto): void
    {
        if ($dto->dateDebut >= $dto->dateFin) {
            throw new ReglesMetierException("La date de début doit précéder la date de fin.");
        }
    }

    private function verifierReservationDansLeFutur(CreerReservationDTO $dto): void
    {
        if ($dto->dateDebut < new \DateTimeImmutable()) {
            throw new ReglesMetierException("Impossible de réserver une salle dans le passé.");
        }
    }

    private function verifierDureeMaximale(CreerReservationDTO $dto): void
    {
        $dureeEnHeures = $this->calculerDureeEnHeures($dto);

        if ($dureeEnHeures > self::DUREE_MAX_HEURES) {
            throw new ReglesMetierException(
                sprintf("La durée maximale d'une réservation est de %d heures.", self::DUREE_MAX_HEURES)
            );
        }
    }

    private function calculerDureeEnHeures(CreerReservationDTO $dto): float
    {
        return ($dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp()) / 3600;
    }

    private function verifierAbsenceDeConflit(CreerReservationDTO $dto): void
    {
        $conflit = $this->reservationRepository->rechercherConflit($dto->salleId, $dto->dateDebut, $dto->dateFin);

        if ($conflit !== null) {
            throw new SalleIndisponibleException(
                sprintf(
                    "La salle est déjà réservée sur ce créneau (réservation #%d, de %s à %s).",
                    $conflit->id,
                    $conflit->date_debut->format('d/m/Y H:i'),
                    $conflit->date_fin->format('d/m/Y H:i')
                )
            );
        }
    }

    private function enregistrerReservation(CreerReservationDTO $dto): Reservation
    {
        return $this->reservationRepository->create([
            'salle_id'    => $dto->salleId,
            'responsable' => $dto->responsable,
            'email'       => $dto->email,
            'motif'       => $dto->motif,
            'date_debut'  => $dto->dateDebut,
            'date_fin'    => $dto->dateFin,
            'statut'      => 'confirmée',
        ]);
    }
}