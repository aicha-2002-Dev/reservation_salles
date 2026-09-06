<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\DTO\ModifierSalleDTO;
use App\Exception\SalleIndisponibleException;
use App\Repository\SalleRepositoryInterface;
use App\Service\CreerSalleService;
use App\Service\ModifierSalleService;
use App\Validation\SalleValidator;
use App\Controller\RenderViewTrait;

final class SalleController
{
    use RenderViewTrait;
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly SalleValidator $validator,
        private readonly CreerSalleService $creerSalleService,
        private readonly ModifierSalleService $modifierSalleService,
    ) {}

    public function index(): string
    {
        return $this->renderView('salle/index', ['salles' => $this->salleRepository->findAll()]);
    }

    public function show(int $id): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            return $this->renderView('error/404', ['message' => "Salle introuvable."]);
        }

        return $this->renderView('salle/show', ['salle' => $salle]);
    }

    public function create(): string
    {
        return $this->renderView('salle/create', ['erreurs' => [], 'anciennesValeurs' => []]);
    }

    public function store(array $donneesFormulaire): string
{
    $resultat = $this->validator->validate($donneesFormulaire);

    if (!$resultat->isValid()) {
        return $this->rendre('salle/create', [
            'erreurs' => $resultat->errors(),
            'anciennesValeurs' => $donneesFormulaire,
        ]);
    }

    $dto = CreerSalleDTO::fromArray($resultat->validatedData());
    $salle = $this->creerSalleService->creer($dto);

    $this->rediriger("/salles/{$salle->id}");
}

    public function edit(int $id): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            return $this->renderView('error/404', ['message' => "Salle introuvable."]);
        }

        return $this->renderView('salle/edit', ['salle' => $salle, 'erreurs' => []]);
    }
public function update(int $id, array $donneesFormulaire): string
{
    $resultat = $this->validator->validate($donneesFormulaire);

    if (!$resultat->isValid()) {
        $salle = $this->salleRepository->findById($id);
        return $this->renderView('salle/edit', ['salle' => $salle, 'erreurs' => $resultat->errors()]);
    }

    $dto = ModifierSalleDTO::fromArray($id, $resultat->validatedData());

    try {
        $salle = $this->modifierSalleService->modifier($dto);
    } catch (SalleIndisponibleException $e) {
        return $this->renderView('error/404', ['message' => $e->getMessage()]);
    }

    $this->rediriger("/salles/{$salle->id}");
    
}
}