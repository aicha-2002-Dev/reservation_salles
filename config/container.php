<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\autowire;

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\SalleRepository;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Service\CreerSalleService;
use App\Service\ModifierSalleService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;

return function (): \DI\Container {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        SalleRepositoryInterface::class => autowire(SalleRepository::class),
        ReservationRepositoryInterface::class => autowire(ReservationRepository::class),

        ReservationController::class => autowire(),
        SalleController::class => autowire(),

        AnnulerReservationService::class => autowire(),
        CreerReservationService::class => autowire(),
        CreerSalleService::class => autowire(),
        ModifierSalleService::class => autowire(),

        ReservationValidator::class => autowire(),
        SalleValidator::class => autowire(),
    ]);

    return $builder->build();
};
