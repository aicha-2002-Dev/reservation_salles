# PARTIE 1
1. Quel est le rôle de Composer ?
Composer est le gestionnaire de dépendances de PHP : il télécharge les bibliothèques externes dont votre projet a besoin (ici FastRoute, Eloquent, PHP-DI...), résout les conflits de version entre elles, et génère un autoloader qui charge automatiquement les classes sans require manuel.

2. Quelle différence existe entre require et require-dev ?
require liste les dépendances nécessaires en production (le code ne fonctionne pas sans elles — ex. illuminate/database, php-di/php-di). require-dev liste les dépendances utiles seulement pendant le développement (ex. PHPUnit pour les tests à l'étape 12) : elles ne sont pas installées si quelqu'un déploie le projet avec composer install --no-dev.

3. Pourquoi faut-il versionner composer.lock ?
composer.json fixe des contraintes de version larges (ex. ^2.4 accepte 2.4, 2.5, 2.9...), mais composer.lock fige les versions exactes installées au moment du composer require. Le versionner garantit que vous, votre binôme éventuel, et le correcteur qui clone le dépôt obtiendrez tous exactement les mêmes versions — évitant le classique « ça marche chez moi » dû à une différence de version mineure d'une bibliothèque.

4. Pourquoi ne versionne-t-on pas vendor/ ?
vendor/ contient le code source complet de toutes les dépendances (souvent des dizaines de Mo), entièrement régénérable en une commande (composer install, qui lit composer.lock). Le versionner gonflerait inutilement le dépôt, dupliquerait du code dont vous n'êtes pas l'auteur, et créerait des conflits Git inutiles à chaque mise à jour de dépendance. C'est exactement pour ça qu'on l'a mis dans .gitignore dès l'étape 0.

# PARTIE 2

1. Quel rôle joue Capsule\Manager ?
C'est le point d'entrée qui permet à Eloquent de fonctionner sans le conteneur de services et le "kernel" complets de Laravel. Normalement, Eloquent va chercher sa connexion via le conteneur global de l'application Laravel ; Capsule\Manager remplace ce mécanisme en jouant lui-même le rôle de gestionnaire de connexions, qu'on configure et démarre manuellement.

2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?
Parce que illuminate/database est un paquet Composer indépendant, découplé du reste du framework Laravel — c'est justement le principe d'Interface Segregation à l'échelle d'un framework : Laravel est découpé en composants installables séparément. Capsule\Manager fournit la petite couche de bootstrap qui, dans Laravel, serait normalement assurée automatiquement par le framework complet.

3. Où doit se trouver le démarrage de l'ORM ?
Dans un seul endroit centralisé — ici config/database.php — jamais dispersé dans plusieurs classes. C'est une application directe du principe de responsabilité unique (S de SOLID) : une seule partie du code a la responsabilité de savoir comment se connecter à la base.

4. Quelle différence existe entre ORM et SQL écrit à la main ?
Le SQL à la main donne un contrôle total et souvent de meilleures performances sur des requêtes complexes, mais oblige à écrire soi-même le mapping entre lignes de résultat et objets PHP, à gérer manuellement l'échappement (risque d'injection SQL si mal fait), et à dupliquer beaucoup de code répétitif (CRUD). Un ORM comme Eloquent représente chaque table comme une classe (pattern Active Record), génère le SQL automatiquement à partir d'appels de méthodes PHP, gère les relations entre tables, et prépare systématiquement les requêtes contre les injections — au prix d'un peu moins de contrôle fin et d'une couche d'abstraction à comprendre.