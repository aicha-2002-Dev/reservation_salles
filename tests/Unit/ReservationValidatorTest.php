<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use PHPUnit\Framework\TestCase;

final class ReservationValidatorTest extends TestCase
{
    private ReservationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ReservationValidator();
    }

    private function donneesValides(array $overrides = []): array
    {
        return array_merge([
            'salle_id'    => 1,
            'responsable' => 'Aissatou Gueye',
            'email'       => 'aissatou@universite.sn',
            'motif'       => 'Réunion de test',
            'date_debut'  => '2027-01-10T10:00',
            'date_fin'    => '2027-01-10T11:00',
        ], $overrides);
    }

    public function testAdresseEmailInvalide(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['email' => 'pas-un-email']));
        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('email', $resultat->errors());
    }

    public function testResponsableVide(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['responsable' => '']));
        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('responsable', $resultat->errors());
    }

    public function testUneDateIncorrecte(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['date_debut' => 'pas-une-date']));
        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('date_debut', $resultat->errors());
    }

    public function testAccepteesDonneesValides(): void
    {
        $resultat = $this->validator->validate($this->donneesValides());
        $this->assertTrue($resultat->isValid());
    }
}