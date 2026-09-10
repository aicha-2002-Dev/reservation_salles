<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepository;

final class ReservationIntegrationTest extends IntegrationTestCase
{
    public function test_recherche_de_chevauchement(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle C', 'batiment' => 'C', 'capacite' => 20, 'type' => 'cours', 'active' => true,
        ]);

        Reservation::create([
            'salle_id' => $salle->id, 'responsable' => 'A', 'email' => 'a@a.sn', 'motif' => 'Test',
            'date_debut' => '2027-01-10 10:00:00', 'date_fin' => '2027-01-10 11:00:00', 'statut' => 'confirmée',
        ]);

        $repository = new ReservationRepository();
        $conflit = $repository->rechercherConflit(
            $salle->id,
            new \DateTimeImmutable('2027-01-10 10:30:00'),
            new \DateTimeImmutable('2027-01-10 11:30:00')
        );

        $this->assertNotNull($conflit);
    }

    public function test_annulation_d_une_reservation(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle D', 'batiment' => 'D', 'capacite' => 20, 'type' => 'cours', 'active' => true,
        ]);

        $reservation = Reservation::create([
            'salle_id' => $salle->id, 'responsable' => 'A', 'email' => 'a@a.sn', 'motif' => 'Test',
            'date_debut' => '2027-01-10 10:00:00', 'date_fin' => '2027-01-10 11:00:00', 'statut' => 'confirmée',
        ]);

        $repository = new ReservationRepository();
        $repository->annuler($reservation->id);

        $this->assertSame('annulée', Reservation::find($reservation->id)->statut);
    }
}