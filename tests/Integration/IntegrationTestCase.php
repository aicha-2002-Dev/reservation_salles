<?php

declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected Capsule $capsule;

    protected function setUp(): void
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        $migrationSalles = require __DIR__ . '/../../database/migrations/01_salles.php';
        $migrationSalles->up($this->capsule);

        $migrationReservations = require __DIR__ . '/../../database/migrations/02_reservations.php';
        $migrationReservations->up($this->capsule);
    }
}