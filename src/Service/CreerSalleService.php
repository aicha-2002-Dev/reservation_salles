<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class CreerSalleService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function creer(CreerSalleDTO $dto): Salle
    {
        return $this->salleRepository->create([
            'nom'      => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type'     => $dto->type,
            'active'   => true,
        ]);
    }
}