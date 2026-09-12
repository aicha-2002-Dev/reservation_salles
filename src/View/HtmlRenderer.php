<?php

declare(strict_types=1);

namespace App\View;
use App\View\RendererInterface;
use App\Session\SessionManagerInterface;


final class HtmlRenderer implements RendererInterface
{
   public function __construct(
        private readonly SessionManagerInterface $session,
    ) {}
    
    public function renderView(string $vue, array $donnees): string
    {
        extract($donnees);

        $messageSucces = $this->session->recupererFlash('messageSucces');
        unset($_SESSION['messageSucces']);

          ob_start();

        require __DIR__ . "/../../templates/{$vue}.php";

        $contenu = ob_get_clean();
          ob_start();

        require __DIR__ . '/../../templates/layout/base.php';

        return ob_get_clean();

    }
}