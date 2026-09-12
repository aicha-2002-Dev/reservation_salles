<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class ConsulterSalleService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function lister(): array
    {
        return $this->salleRepository->findAll();
    }

    public function trouver(int $id): Salle
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            throw new SalleIndisponibleException("Salle #{$id} introuvable.");
        }

        return $salle;
    }
}