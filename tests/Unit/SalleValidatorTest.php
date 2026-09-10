<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class SalleValidatorTest extends TestCase
{
    private SalleValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SalleValidator();
    }

    private function donneesValides(array $overrides = []): array
    {
        return array_merge([
            'nom'      => 'Salle Test',
            'batiment' => 'Bâtiment A',
            'capacite' => 30,
            'type'     => 'cours',
        ], $overrides);
    }

    public function testCapaciteNegative(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['capacite' => -5]));
        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('capacite', $resultat->errors());
    }

    public function testTypeSalleInconnu(): void
    {
        $resultat = $this->validator->validate($this->donneesValides(['type' => 'salle_de_bain']));
        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('type', $resultat->errors());
    }

    public function testDonneesValides(): void
    {
        $resultat = $this->validator->validate($this->donneesValides());
        $this->assertTrue($resultat->isValid());
    }
}