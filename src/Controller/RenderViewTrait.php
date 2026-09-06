<?php

declare(strict_types=1);

namespace App\Controller;

trait RenderViewTrait
{
    private function renderView(string $vue, array $donnees): string
    {
        extract($donnees);
        ob_start();
        require __DIR__ . "/../../templates/{$vue}.php";
        return ob_get_clean();
    }

     private function rediriger(string $url): never
    {
        header("Location: {$url}");
        exit;
    }
}