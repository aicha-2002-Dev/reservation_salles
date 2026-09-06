<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;

interface ReservationRepositoryInterface
{
    public function findById(int $id): ?Reservation;
    public function findAll(): array;
    public function findConfirmeesPourSalle(int $salleId): array;
    public function rechercherConflit(int $salleId, \DateTimeImmutable $debut, \DateTimeImmutable $fin): ?Reservation;
    public function create(array $donnees): Reservation;
    public function annuler(int $id):bool;
}