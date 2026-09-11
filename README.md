# Gestion des réservations de salles universitaires

Application web permettant de consulter les salles de l'université et de gérer leurs
réservations, développée en PHP orienté objet sans framework complet, avec des composants
Composer (FastRoute, Eloquent, PHP-DI, Respect\Validation).

## Prérequis

- Docker et Docker Compose (recommandé), **ou** :
  - PHP 8.2 ou 8.3 avec les extensions `pdo_mysql`, `zip`
  - Composer 2
  - MySQL 8.0

## Installation

### Avec Docker (recommandé)

```bash
git clone https://github.com/votre-compte/reservation-salles.git
cd reservation-salles
docker compose up -d --build
```

### Sans Docker

```bash
git clone https://github.com/votre-compte/reservation-salles.git
cd reservation-salles
composer install
```

## Configuration de la base de données

Copiez le fichier d'exemple et ajustez si besoin :

```bash
cp .env.example .env
```

Variables à renseigner dans `.env` :

```env
APP_ENV=development
APP_DEBUG=true

DB_DRIVER=mysql
DB_HOST=mysql        # "127.0.0.1" si vous n'utilisez pas Docker
DB_PORT=3306
DB_DATABASE=reservation_salles
DB_USERNAME=root
DB_PASSWORD=root
```

## Création des tables

```bash
docker compose exec app php database/migrate.php
```

*(sans Docker : `php database/migrate.php`)*

## Ajout des données initiales

```bash
docker compose exec app php database/seed.php
```

*(sans Docker : `php database/seed.php`)*

Insère 5 salles de démonstration (Amphithéâtre A, Salle B12, Laboratoire Chimie, Salle
Informatique 1, Salle de réunion). Le script est idempotent : il peut être relancé sans
créer de doublons.

## Lancement du serveur

Avec Docker, le serveur démarre automatiquement à la construction du conteneur :  http://localhost:8000


Sans Docker :

```bash
php -S localhost:8000 -t public
```

## Exécution des tests

```bash
docker compose exec app vendor/bin/phpunit
```

*(sans Docker : `vendor/bin/phpunit`)*

Lancer uniquement une suite :

```bash
vendor/bin/phpunit --testsuite Unit
vendor/bin/phpunit --testsuite Integration
```

## Image Docker Hub

Une image de l'application est disponible publiquement :

```bash
docker pull votre-nom-dockerhub/reservation-salles:latest
```

## Structure du projet

config/ connexion Eloquent, conteneur PHP-DI
database/ migrations et script de données initiales
public/ point d'entrée HTTP unique (index.php) et assets
routes/ déclaration des routes FastRoute
src/ code applicatif (Controller, DTO, Exception, Model, Repository, Service, Validation)
templates/ vues PHP
tests/ tests unitaires et d'intégration


## Documentation complémentaire

- [CHANGELOG.md](./CHANGELOG.md) — historique des versions
- [ARCHITECTURE.md](./ARCHITECTURE.md) — analyse des choix architecturaux et des principes SOLID appliqués