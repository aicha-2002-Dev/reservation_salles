<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container): void {
    $dispatcher = FastRoute\simpleDispatcher(require __DIR__ . '/web.php');

    $uri = $_SERVER['REQUEST_URI'];
    if (false !== $position = strpos($uri, '?')) {
        $uri = substr($uri, 0, $position);
    }
    $uri = rawurldecode($uri);

    $routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo "404 — Page introuvable.";
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            $methodesAutorisees = $routeInfo[1];
            http_response_code(405);
            header('Allow: ' . implode(', ', $methodesAutorisees));
            echo "405 — Méthode non autorisée.";
            break;

        case Dispatcher::FOUND:
            [$classeControleur, $methode] = $routeInfo[1];
            $parametres = $routeInfo[2];

            $controleur = $container->get($classeControleur);

            $donneesRequete = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];

            $arguments = array_values($parametres);
            if (!empty($donneesRequete) || in_array($methode, ['store', 'update'], true)) {
                $arguments[] = $donneesRequete;
            }

            echo $controleur->{$methode}(...array_map(
                fn($valeur) => is_numeric($valeur) ? (int) $valeur : $valeur,
                $arguments
            ));
            break;
    }
};