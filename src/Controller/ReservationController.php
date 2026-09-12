<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDto;
use App\Exception\ReglesMetierException;
use App\Exception\SalleIndisponibleException;
use App\Exception\ReservationIntrouvableException;
use App\Service\ConsulterReservationService;
use App\Service\CreerReservationService;
use App\Service\AnnulerReservationService;
use App\Session\SessionManagerInterface;
use App\Validation\ReservationValidator;
use App\View\RendererInterface;

final class ReservationController extends AbstractController
{
    public function __construct(
        RendererInterface $renderer,
        private readonly ConsulterReservationService $consulterReservationService,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService,
        private readonly SessionManagerInterface $session,
    ) {
        parent::__construct($renderer);
    }

    public function index(): string
    {
        return $this->renderView(
            'reservation/index',
            ['reservations' => $this->consulterReservationService->lister()]
        );
    }

    public function show(int $id): string
    {
        try {
            $reservation = $this->consulterReservationService->trouver($id);
        } catch (ReservationIntrouvableException $e) {
            return $this->renderView('error/404', ['message' => $e->getMessage()]);
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

        $donnees = $resultat->validatedData();

        $dto = CreerReservationDto::build()
            ->salleId((int) $donnees['salle_id'])
            ->responsable($donnees['responsable'])
            ->email($donnees['email'])
            ->motif($donnees['motif'])
            ->dateDebut(new \DateTimeImmutable($donnees['date_debut']))
            ->dateFin(new \DateTimeImmutable($donnees['date_fin']))
            ->build();

        try {
            $reservation = $this->creerReservationService->creer($dto);
        } catch (SalleIndisponibleException|ReglesMetierException $e) {
            return $this->renderView('reservation/create', [
                'erreurs' => ['general' => [$e->getMessage()]],
                'anciennesValeurs' => $donneesFormulaire,
            ]);
        }

        $this->session->flash('messageSucces', 'Réservation enregistrée avec succès.');

        $this->rediriger("/reservations/{$reservation->id}");
    }

    public function cancel(int $id): string
    {
        try {
            $this->annulerReservationService->annuler($id);
        } catch (ReservationIntrouvableException $e) {
            return $this->renderView('error/404', ['message' => $e->getMessage()]);
        }

        $this->session->flash('messageSucces', 'Réservation annulée avec succès.');

        $this->rediriger('/reservations');
    }
}