<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use Override;

final class ReservationRepository implements ReservationRepositoryInterface
{
    #[Override]
    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function findAll(): array
    {
        return Reservation::orderBy('date_debut')->get()->all();
    }


    #[Override]
    public function findConfirmeesPourSalle(int $salleId): array
    {
          return Reservation::where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->get()
            ->all();
    }

    public function rechercherConflit(int $salleId, \DateTimeImmutable $debut, \DateTimeImmutable $fin): ?Reservation
    {
        return Reservation::where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $fin)
            ->where('date_fin', '>', $debut)
            ->first();
    }

    #[Override]
    public function create(array $donnees): Reservation
    {
        return Reservation::create($donnees);
    }

        public function annuler(int $id): bool
    {
        $reservation = $this->findById($id);

        if ($reservation === null) {
            return false;
        }

        $reservation->statut = 'annulée';

        return $reservation->save();
    }

}