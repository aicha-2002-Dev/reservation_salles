<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDto;
use App\Exception\ReglesMetierException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Service\CreerReservationService;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReservationRepository;
use Tests\Fakes\FakeSalleRepository;

final class CreerReservationServiceTest extends TestCase
{
    private FakeSalleRepository $salleRepository;
    private FakeReservationRepository $reservationRepository;
    private CreerReservationService $service;

    protected function setUp(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->salleRepository = new FakeSalleRepository();
        $this->reservationRepository = new FakeReservationRepository();
        $this->service = new CreerReservationService($this->salleRepository, $this->reservationRepository);

        $salleActive = new Salle(['nom' => 'Salle A', 'batiment' => 'B1', 'capacite' => 20, 'type' => 'cours', 'active' => true]);
        $salleActive->id = 1;
        $this->salleRepository->ajouter($salleActive);

        $salleInactive = new Salle(['nom' => 'Salle B', 'batiment' => 'B1', 'capacite' => 20, 'type' => 'cours', 'active' => false]);
        $salleInactive->id = 2;
        $this->salleRepository->ajouter($salleInactive);
    }

    private function dtoValide(array $overrides = []): CreerReservationDto
    {
        $base = [
            'salleId'     => 1,
            'responsable' => 'Aissatou Gueye',
            'email'       => 'aissatou@gmail.sn',
            'motif'       => 'Réunion de test',
            'dateDebut'   => new \DateTimeImmutable('+1 day 10:00'),
            'dateFin'     => new \DateTimeImmutable('+1 day 11:00'),
        ];
        $d = array_merge($base, $overrides);

        return CreerReservationDto::build()
            ->salleId($d['salleId'])
            ->responsable($d['responsable'])
            ->email($d['email'])
            ->motif($d['motif'])
            ->dateDebut($d['dateDebut'])
            ->dateFin($d['dateFin'])
            ->build();
    }

    public function testValideEstCreee(): void
    {
        $reservation = $this->service->creer($this->dtoValide());
        $this->assertSame('confirmée', $reservation->statut);
    }

    public function testSalleInexistante(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        $this->service->creer($this->dtoValide(['salleId' => 999]));
    }

    public function testSalleInactive(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        $this->service->creer($this->dtoValide(['salleId' => 2]));
    }

    public function testDateDeFinAnterieureAuDebut(): void
    {
        $this->expectException(ReglesMetierException::class);
        $this->service->creer($this->dtoValide([
            'dateDebut' => new \DateTimeImmutable('+1 day 12:00'),
            'dateFin'   => new \DateTimeImmutable('+1 day 10:00'),
        ]));
    }

    public function testDureeSuperieureAquatreHeures(): void
    {
        $this->expectException(ReglesMetierException::class);
        $this->service->creer($this->dtoValide([
            'dateDebut' => new \DateTimeImmutable('+1 day 08:00'),
            'dateFin'   => new \DateTimeImmutable('+1 day 13:00'),
        ]));
    }

    public function testDatePassee(): void
    {
        $this->expectException(ReglesMetierException::class);
        $this->service->creer($this->dtoValide([
            'dateDebut' => new \DateTimeImmutable('-1 day 10:00'),
            'dateFin'   => new \DateTimeImmutable('-1 day 11:00'),
        ]));
    }

    public function testConflitAvecReservationExistante(): void
    {
        $existante = new Reservation([
            'salle_id' => 1, 'responsable' => 'X', 'email' => 'x@x.sn', 'motif' => 'Déjà réservé',
            'date_debut' => new \DateTimeImmutable('+1 day 10:30'),
            'date_fin'   => new \DateTimeImmutable('+1 day 11:30'),
            'statut' => 'confirmée',
        ]);
        $existante->id = 1;
        $this->reservationRepository->ajouter($existante);

        $this->expectException(SalleIndisponibleException::class);
        $this->service->creer($this->dtoValide());
    }

    public function testReservationVoisineSansChevauchement(): void
    {
        $existante = new Reservation([
            'salle_id' => 1, 'responsable' => 'X', 'email' => 'x@x.sn', 'motif' => 'Voisine',
            'date_debut' => new \DateTimeImmutable('+1 day 11:00'),
            'date_fin'   => new \DateTimeImmutable('+1 day 12:00'),
            'statut' => 'confirmée',
        ]);
        $existante->id = 1;
        $this->reservationRepository->ajouter($existante);

        $reservation = $this->service->creer($this->dtoValide());
        $this->assertSame('confirmée', $reservation->statut);
    }
}
