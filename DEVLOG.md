Un ORM (Objet Relationel Mapping) est un outil qui permet a une base de donnees de communiquer avec sa base de donnees sans ecrire directement des requetes SQL.
Dans le code on manipule des objets mais l'ORM se charge de les convertir en tables et requetes sql.
# PARTIE 1
1. Quel est le rôle de Composer ?
Composer est le gestionnaire de dépendances de PHP : il télécharge les bibliothèques externes dont votre projet a besoin (FastRoute, Eloquent, PHP-DI...), résout les conflits de version entre elles, et génère un autoloader qui charge automatiquement les classes sans require manuel.

2. Quelle différence existe entre require et require-dev ?
require liste les dépendances nécessaires en production (le code ne fonctionne pas sans elles — ex. illuminate/database, php-di/php-di). require-dev liste les dépendances utiles seulement pendant le développement (ex. PHPUnit pour les tests à l'étape 12) : elles ne sont pas installées si quelqu'un déploie le projet avec composer install --no-dev.

3. Pourquoi faut-il versionner composer.lock ?
composer.json fixe des contraintes de version larges (ex. ^2.4 accepte 2.4, 2.5, 2.9...), mais composer.lock fige les versions exactes installées au moment du composer require. Le versionner garantit que vous, votre binôme éventuel, et le correcteur qui clone le dépôt obtiendrez tous exactement les mêmes versions — évitant le classique « ça marche chez moi » dû à une différence de version mineure d'une bibliothèque.

4. Pourquoi ne versionne-t-on pas vendor/ ?
vendor/ contient le code source complet de toutes les dépendances (souvent des dizaines de Mo), entièrement régénérable en une commande (composer install, qui lit composer.lock). Le versionner gonflerait inutilement le dépôt, dupliquerait du code dont vous n'êtes pas l'auteur, et créerait des conflits Git inutiles à chaque mise à jour de dépendance. C'est exactement pour ça qu'on l'a mis dans .gitignore dès l'étape 0.

# PARTIE 2

1. Quel rôle joue Capsule\Manager ?
C'est le point d'entrée qui permet à Eloquent de fonctionner sans Laravel. Normalement, Eloquent va chercher sa connexion via le conteneur global de l'application Laravel ; Capsule\Manager remplace ce mécanisme en jouant lui-même le rôle de gestionnaire de connexions, qu'on configure et démarre manuellement.

2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?
Parce que illuminate/database est un paquet Composer indépendant, découplé du reste du framework Laravel — Laravel est découpé en composants installables séparément. Capsule\Manager fournit la petite couche de bootstrap qui, dans Laravel, serait normalement assurée automatiquement par le framework complet.

3. Où doit se trouver le démarrage de l'ORM ?
Dans un endroit centralisé config/database.php — jamais dispersé dans plusieurs classes. C'est une application directe du principe de responsabilité unique (S de SOLID) : une seule partie du code a la responsabilité de savoir comment se connecter à la base.

1. Quelle différence existe entre ORM et SQL écrit à la main ?
Le SQL à la main donne un contrôle total et souvent de meilleures performances sur des requêtes complexes, mais oblige à écrire soi-même le mapping entre lignes de résultat et objets PHP, à gérer manuellement l'échappement (risque d'injection SQL si mal fait), et à dupliquer beaucoup de code répétitif (CRUD). Un ORM comme Eloquent représente chaque table comme une classe (pattern Active Record), génère le SQL automatiquement à partir d'appels de méthodes PHP, gère les relations entre tables, et prépare systématiquement les requêtes contre les injections — au prix d'un peu moins de contrôle fin et d'une couche d'abstraction à comprendre.

# PARTIE 3

1. Quel type de relation Eloquent avez-vous utilisé ?
Une relation (one to many) un-à-plusieurs (hasMany / belongsTo) : une salle peut avoir plusieurs réservations, mais chaque réservation appartient à une seule salle. C'est la paire de relations Eloquent la plus courante pour ce genre de lien parent-enfant.

2. Pourquoi déclarer $fillable ou $guarded ?
Pour se protéger contre l'assignation de masse non contrôlée. Sans l'un ou l'autre, un appel comme Salle::create($_POST) pourrait laisser un utilisateur malveillant injecter n'importe quel champ dans le formulaire (même des colonnes qu'il ne devrait pas pouvoir toucher, comme un futur champ is_admin sur un autre modèle). $fillable est une liste blanche (seuls ces champs sont autorisés), $guarded est une liste noire (tous les champs sauf ceux-ci) — on utilise généralement $fillable, plus sûr par défaut car explicite.

3. Pourquoi convertir active en booléen ?
Parce que MySQL n'a pas de vrai type booléen natif — active est stockée en TINYINT(1) (0 ou 1). Sans le cast, $salle->active renverrait l'entier 1 ou 0, ce qui fonctionne dans un if par coïncidence de type, mais rend le code moins lisible et plus fragile (une comparaison stricte $salle->active === true échouerait sans le cast). Le cast garantit un vrai type PHP cohérent partout dans l'application.

4. Pourquoi convertir les dates en objets ?
Parce que manipuler des dates comme de simples chaînes de caractères oblige à reparser manuellement à chaque comparaison (strtotime(), DateTime::createFromFormat()...). Un objet Carbon offre directement des méthodes lisibles (isPast(), diffInHours(), format()) — ce qui sera indispensable pour implémenter les règles métier de l'étape 8 (durée max 4h, date dans le futur, détection de chevauchement).

# PARTIE 4

1. Quelle différence existe entre migration et seeder ?
Une migration définit et fait évoluer la structure de la base de données (créer une table, ajouter une colonne, changer un type) — c'est le "contenant". Un seeder insère des données dans des tables déjà existantes — c'est le "contenu".Une migration s'execute une sule fois alors q'un seeder peut  s'executer plusieurs fois pour peupler la base de donnees.

2. Pourquoi les données initiales doivent-elles être reproductibles ?
Parce que le script sera exécuté sur des environnements différents et à des moments différents : votre machine, celle d'un correcteur qui clone le dépôt, éventuellement une réinstallation après un problème local.

3. Comment empêcher les doublons ?
En vérifiant, avant chaque insertion, qu'une ligne correspondant aux mêmes critères n'existe pas déjà — c'est exactement le rôle de firstOrCreate() ici, qui encapsule ce couple recherche-puis-création-conditionnelle en une seule méthode Eloquent, plutôt que d'écrire vous-même un SELECT suivi d'un if puis d'un INSERT.

# PARTIE 5

1. Pourquoi séparer la validation syntaxique des règles métier ?
Parce que ce sont deux préoccupations différentes, qui changent pour des raisons différentes (principe S de SOLID) : la validation syntaxique vérifie la forme des données (une date est bien une date, un email a bien un @), indépendamment de tout contexte. Les règles métier, elles, dépendent de l'état de l'application.

2. Pourquoi créer une interface de validation ?
Pour que le code qui utilise le validateur (le futur Contrôleur) dépende d'une abstraction plutôt que d'une classe concrète précise — c'est le principe D de SOLID (inversion des dépendances), rendu concret par le pattern Strategy : demain, si vous ajoutez un troisième type de ressource à valider, vous créez une nouvelle classe qui implémente ValidatorInterface, sans toucher au code existant qui l'utilise.

3. Pourquoi le validateur ne doit-il pas enregistrer les données ?
Parce que ce serait mélanger deux responsabilités distinctes : vérifier la forme des données, et les persister. Un validateur qui écrirait aussi en base deviendrait difficile à réutiliser (impossible de valider sans effet de bord).

4. Comment retourner plusieurs erreurs en une seule fois ?
Le tableau $erreurs accumule ainsi toutes les erreurs de tous les champs, avant d'être empaqueté dans un seul ValidationResult::failure($erreurs)

# PARTIE 6

1. Quelle différence existe entre un DTO et une entité (modèle Eloquent) ?
Une entité est liée à la persistance : elle sait se sauvegarder, se charger depuis la base, gérer des relations, et porte souvent plus de champs que nécessaire pour une opération précise. Un DTO n'a aucun lien avec la base de données — c'est une structure de données neutre, qui ne transporte que ce qui est strictement nécessaire pour une opération donnée.

2. Pourquoi le DTO ne doit-il pas contenir de logique métier ?
Parce que son rôle est uniquement de transporter des données déjà validées, entre deux couches de l'application (typiquement du Contrôleur vers le Service).

3. Pourquoi utiliser des propriétés typées ?
Pour que PHP lui-même garantisse, à la compilation et à l'exécution, que $dto->capacite est toujours un vrai entier et jamais une chaîne mal formée — combiné à declare(strict_types=1), ça élimine les bugs silencieux liés aux conversions implicites, en forçant les erreurs de type à apparaître immédiatement plutôt que de se propager silencieusement plus loin dans le code.

