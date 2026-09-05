<?php
namespace Config;

use Illuminate\Database\Capsule\Manager as Capsule;

// Chargement du .env — une seule fois, ici, nulle part ailleurs
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

/**
 * Renvoie toujours la même instance de Capsule, déjà connectée et démarrée.
 */
return static function (): Capsule {
    static $capsule = null;

    if ($capsule === null) {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => $_ENV['DB_DRIVER']   ?? 'mysql',
            'host'      => $_ENV['DB_HOST']     ?? '127.0.0.1',
            'port'      => $_ENV['DB_PORT']     ?? '3306',
            'database'  => $_ENV['DB_DATABASE'] ?? '',
            'username'  => $_ENV['DB_USERNAME'] ?? '',
            'password'  => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    return $capsule;
};