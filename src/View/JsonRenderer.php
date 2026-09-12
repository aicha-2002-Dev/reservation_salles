<?php

declare(strict_types=1);

namespace App\View;
use App\View\RendererInterface;

final class JsonRenderer implements RendererInterface
{
    public function renderView(string $vue, array $donnees): string
    {
        return json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}