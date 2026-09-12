<?php

declare(strict_types=1);

namespace App\View;

interface RendererInterface
{
    public function renderView(string $vue, array $donnees): string;
}