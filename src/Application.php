<?php

declare(strict_types=1);

namespace App;

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;

final class Application
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly ContainerInterface $container,
    ) {}

    public function run(): void
    {
        $uri = $this->extraireChemin($_SERVER['REQUEST_URI']);

        try {
            $routeInfo = $this->dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);

            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $this->repondre404();
                    break;

                case Dispatcher::METHOD_NOT_ALLOWED:
                    $this->repondre405($routeInfo[1]);
                    break;

                case Dispatcher::FOUND:
                    $this->traiterRoute($routeInfo[1], $routeInfo[2]);
                    break;
            }
        } catch (\Throwable $e) {
            $this->repondreErreurInattendue($e);
        }
    }

    private function extraireChemin(string $uri): string
    {
        if (false !== $position = strpos($uri, '?')) {
            $uri = substr($uri, 0, $position);
        }
        return rawurldecode($uri);
    }

    private function traiterRoute(array $handler, array $parametres): void
    {
        [$classeControleur, $methode] = $handler;

        $controleur = $this->container->get($classeControleur);

        $donneesRequete = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];

        $arguments = array_map(
            fn($valeur) => is_numeric($valeur) ? (int) $valeur : $valeur,
            array_values($parametres)
        );

        if (!empty($donneesRequete) || in_array($methode, ['store', 'update'], true)) {
            $arguments[] = $donneesRequete;
        }

        echo $controleur->{$methode}(...$arguments);
    }

    private function repondre404(): void
    {
        http_response_code(404);
        echo "404 — Page introuvable.";
    }

    private function repondre405(array $methodesAutorisees): void
    {
        http_response_code(405);
        header('Allow: ' . implode(', ', $methodesAutorisees));
        echo "405 — Méthode non autorisée.";
    }

    private function repondreErreurInattendue(\Throwable $e): void
    {
        http_response_code(500);

        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            echo "<pre>Erreur : " . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo "Une erreur inattendue est survenue.";
        }
    }
}