<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;

final class SalleIntegrationTest extends IntegrationTestCase
{
    public function test_creation_d_une_salle_avec_eloquent(): void
    {
        $salle = Salle::create([
            'nom' => 'Amphithéâtre A', 'batiment' => 'Bâtiment principal',
            'capacite' => 200, 'type' => 'amphitheatre', 'active' => true,
        ]);

        $this->assertNotNull($salle->id);
        $this->assertSame('Amphithéâtre A', Salle::find($salle->id)->nom);
    }

    public function test_relation_salle_reservations(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle B12', 'batiment' => 'B', 'capacite' => 30, 'type' => 'cours', 'active' => true,
        ]);

        Reservation::create([
            'salle_id' => $salle->id, 'responsable' => 'Aissatou', 'email' => 'a@a.sn', 'motif' => 'Test',
            'date_debut' => '2027-01-10 10:00:00', 'date_fin' => '2027-01-10 11:00:00', 'statut' => 'confirmée',
        ]);

        $this->assertCount(1, $salle->reservations()->get());
    }
}