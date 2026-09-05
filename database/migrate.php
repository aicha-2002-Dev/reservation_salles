<?php
namespace Database;

require __DIR__ . '/../vendor/autoload.php';

$capsuleFactory = require __DIR__ . '/../config/database.php';

try {
    $capsule = $capsuleFactory();
    $capsule->connection()->getPdo(); // force réellement la connexion
} catch (\Throwable $e) {
    fwrite(STDERR, "Erreur de connexion à la base de données : {$e->getMessage()}" . PHP_EOL);
    exit(1);
}

echo "Connexion à la base de données réussie." . PHP_EOL;

$migrationsPath = __DIR__ . '/migrations';
$files = glob($migrationsPath . '/*.php');
sort($files);

foreach ($files as $file) {
    $migration = require $file;
    $migration->up($capsule);
    echo "Migration exécutée : " . basename($file) . PHP_EOL;
}

echo "Toutes les migrations ont été exécutées avec succès." . PHP_EOL;