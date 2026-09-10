<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class FakeReservationRepository implements ReservationRepositoryInterface
{
    /** @var array<int, Reservation> */
    private array $reservations = [];

    public function ajouter(Reservation $reservation): void
    {
        $this->reservations[$reservation->id] = $reservation;
    }

    public function findById(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->reservations);
    }

    public function findConfirmeesPourSalle(int $salleId): array
    {
        return array_values(array_filter(
            $this->reservations,
            fn(Reservation $r) => $r->salle_id === $salleId && $r->statut === 'confirmée'
        ));
    }

    public function rechercherConflit(int $salleId, \DateTimeImmutable $debut, \DateTimeImmutable $fin): ?Reservation
    {
        foreach ($this->findConfirmeesPourSalle($salleId) as $reservation) {
            $debutExistant = new \DateTimeImmutable((string) $reservation->date_debut);
            $finExistant   = new \DateTimeImmutable((string) $reservation->date_fin);

            if ($debut < $finExistant && $fin > $debutExistant) {
                return $reservation;
            }
        }
        return null;
    }

    public function create(array $donnees): Reservation
    {
        $reservation = new Reservation($donnees);
        $reservation->id = count($this->reservations) + 1;
        $this->reservations[$reservation->id] = $reservation;
        return $reservation;
    }

    public function annuler(int $id): bool
    {
        if (!isset($this->reservations[$id])) {
            return false;
        }
        $this->reservations[$id]->statut = 'annulée';
        return true;
    }
}