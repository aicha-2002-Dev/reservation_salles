<?php

declare(strict_types=1);

namespace App\Controller;
use App\View\RendererInterface;

final class HomeController extends AbstractController
{    
    public function __construct(RendererInterface $renderer)
    {
        return parent::__construct($renderer);
    }
    public function index(): string
    {
        return $this->renderView('home/index', []);
    }
}