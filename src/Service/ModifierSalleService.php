<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ModifierSalleDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class ModifierSalleService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
    ) {}

    public function modifier(ModifierSalleDTO $dto): Salle
    {
        $salle = $this->salleRepository->update($dto->id, [
            'nom'      => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type'     => $dto->type,
            'active'   => $dto->active,
        ]);

        if ($salle === null) {
            throw new SalleIndisponibleException("Impossible de modifier : salle introuvable.");
        }

        return $salle;
    }
}