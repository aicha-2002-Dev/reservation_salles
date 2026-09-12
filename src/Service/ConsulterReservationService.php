<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class ConsulterReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function lister(): array
    {
        return $this->reservationRepository->findAll();
    }

    public function trouver(int $id): Reservation
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            throw new ReservationIntrouvableException("Réservation #{$id} introuvable.");
        }

        return $reservation;
    }
}