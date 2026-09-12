<?php

declare(strict_types=1);

namespace App\Controller;

use App\View\RendererInterface;

abstract class AbstractController
{
    public function __construct(
        protected readonly RendererInterface $renderer,
    ) {}

    protected function renderView(string $vue, array $donnees): string
    {
        return $this->renderer->renderView($vue, $donnees);
    }

    protected function rediriger(string $url): never
    {
        header("Location: {$url}");
        exit;
    }
}