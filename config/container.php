<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\autowire;

use App\View\RendererInterface;
use App\View\HtmlRenderer;
use App\View\JsonRenderer;
use App\Repository\SalleRepository;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepository;
use App\Repository\ReservationRepositoryInterface;


return function (): \DI\Container {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        SalleRepositoryInterface::class => autowire(SalleRepository::class),
        ReservationRepositoryInterface::class => autowire(ReservationRepository::class),   
        RendererInterface::class => autowire(HtmlRenderer::class)




        
    ]);

    return $builder->build();
};
