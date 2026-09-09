<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\autowire;
use function DI\factory;

use Illuminate\Database\Capsule\Manager as Capsule;

use App\Repository\SalleRepository;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepository;
use App\Repository\ReservationRepositoryInterface;

return function (): \DI\Container {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([

        Capsule::class => factory(function () {
            $capsuleFactory = require __DIR__ . '/database.php';
            return $capsuleFactory();
        }),

        SalleRepositoryInterface::class => autowire(SalleRepository::class),
        ReservationRepositoryInterface::class => autowire(ReservationRepository::class),

    ]);

    return $builder->build();
};