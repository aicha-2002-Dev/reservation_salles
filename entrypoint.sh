#!/bin/sh
set -e

echo "En attente de MySQL..."

MAX_TENTATIVES=30
TENTATIVE=0

until php -r "
    try {
        new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        exit(0);
    } catch (\PDOException \$e) {
        exit(1);
    }
"; do
    TENTATIVE=$((TENTATIVE + 1))
    if [ "$TENTATIVE" -ge "$MAX_TENTATIVES" ]; then
        echo "MySQL n'a pas démarré à temps, abandon."
        exit 1
    fi
    echo "MySQL indisponible, nouvelle tentative dans 2 secondes... ($TENTATIVE/$MAX_TENTATIVES)"
    sleep 2
done

echo "MySQL est prêt."

echo "Exécution des migrations..."
php database/migrate.php

echo "Exécution du seed..."
php database/seed.php

echo "Démarrage du serveur PHP..."
exec php -S 0.0.0.0:8000 -t public