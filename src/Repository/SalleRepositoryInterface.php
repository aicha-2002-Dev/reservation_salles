<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

interface SalleRepositoryInterface
{
    public function findById(int $id): ?Salle;
    public function findActives(): array;
    public function create(array $donnees): Salle;
}