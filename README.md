# Gestion des réservations de salles universitaires

Application web permettant de consulter les salles de l'université et de gérer
leurs réservations, afin d'éliminer les doublons causés par la réservation par courriel.


## Lancer via l'image Docker Hub publiée

docker pull votre-nom-dockerhub/reservation-salles:latest

Puis, avec le docker-compose.yml de ce dépôt (qui inclut aussi le service MySQL) :

docker compose up -d
docker compose exec app php database/migrate.php
docker compose exec app php database/seed.php