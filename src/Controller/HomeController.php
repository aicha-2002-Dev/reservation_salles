<?php

declare(strict_types=1);

namespace App\Controller;

final class HomeController
{
    use RenderViewTrait;

    public function index(): string
    {
        return $this->renderView('home/index', []);
    }
}