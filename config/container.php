<?php

declare(strict_types=1);

use function DI\autowire;
use function DI\factory;

use App\Application;
use App\Repository\SalleRepository;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Session\SessionManagerInterface;
use App\Session\PhpSessionManager;
use App\View\RendererInterface;
use App\View\HtmlRenderer;
use FastRoute\Dispatcher;

return [
    SalleRepositoryInterface::class => autowire(SalleRepository::class),
    ReservationRepositoryInterface::class => autowire(ReservationRepository::class),
    RendererInterface::class => autowire(HtmlRenderer::class),
    SessionManagerInterface::class => autowire(PhpSessionManager::class),

    Dispatcher::class => factory(function () {
        return \FastRoute\simpleDispatcher(require __DIR__ . '/../routes/web.php');
    }),

    Application::class => autowire(Application::class),
];