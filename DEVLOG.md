# PARTIE1
1. Quel est le rôle de Composer ?
Composer est le gestionnaire de dépendances de PHP : il télécharge les bibliothèques externes dont votre projet a besoin (ici FastRoute, Eloquent, PHP-DI...), résout les conflits de version entre elles, et génère un autoloader qui charge automatiquement les classes sans require manuel.

2. Quelle différence existe entre require et require-dev ?
require liste les dépendances nécessaires en production (le code ne fonctionne pas sans elles — ex. illuminate/database, php-di/php-di). require-dev liste les dépendances utiles seulement pendant le développement (ex. PHPUnit pour les tests à l'étape 12) : elles ne sont pas installées si quelqu'un déploie le projet avec composer install --no-dev.

3. Pourquoi faut-il versionner composer.lock ?
composer.json fixe des contraintes de version larges (ex. ^2.4 accepte 2.4, 2.5, 2.9...), mais composer.lock fige les versions exactes installées au moment du composer require. Le versionner garantit que vous, votre binôme éventuel, et le correcteur qui clone le dépôt obtiendrez tous exactement les mêmes versions — évitant le classique « ça marche chez moi » dû à une différence de version mineure d'une bibliothèque.

4. Pourquoi ne versionne-t-on pas vendor/ ?
vendor/ contient le code source complet de toutes les dépendances (souvent des dizaines de Mo), entièrement régénérable en une commande (composer install, qui lit composer.lock). Le versionner gonflerait inutilement le dépôt, dupliquerait du code dont vous n'êtes pas l'auteur, et créerait des conflits Git inutiles à chaque mise à jour de dépendance. C'est exactement pour ça qu'on l'a mis dans .gitignore dès l'étape 0.

