<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDto;
use App\Exception\ReglesMetierException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Controller\RenderViewTrait;
use App\Exception\ReservationIntrouvableException;
use App\Service\AnnulerReservationService;

final class ReservationController
{
    use RenderViewTrait;
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService
    ) {}

    public function index(): string
    {
        return $this->renderView('reservation/index', ['reservations' => $this->reservationRepository->findAll()]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            return $this->renderView('error/404', ['message' => "Réservation introuvable."]);
        }

        return $this->renderView('reservation/show', ['reservation' => $reservation]);
    }

    public function create(): string
    {
        return $this->renderView('reservation/create', ['erreurs' => [], 'anciennesValeurs' => []]);
    }

    public function store(array $donneesFormulaire): string
    {
    $resultat = $this->validator->validate($donneesFormulaire);

    if (!$resultat->isValid()) {
        return $this->renderView('reservation/create', [
            'erreurs' => $resultat->errors(),
            'anciennesValeurs' => $donneesFormulaire,
        ]);
    }

    $dto = CreerReservationDTO::fromArray($resultat->validatedData());

    try {
        $reservation = $this->creerReservationService->creer($dto);
    } catch (SalleIndisponibleException|ReglesMetierException $e) {
        return $this->renderView('reservation/create', [
            'erreurs' => ['general' => [$e->getMessage()]],
            'anciennesValeurs' => $donneesFormulaire,
        ]);
    }

    $this->rediriger("/reservations/{$reservation->id}"); 
    }

   public function cancel(int $id): string
{
    try {
        $this->annulerReservationService->annuler($id);
    } catch (ReservationIntrouvableException $e) {
        return $this->renderView('error/404', ['message' => $e->getMessage()]);
    }

    $this->rediriger('/reservations');
}
}