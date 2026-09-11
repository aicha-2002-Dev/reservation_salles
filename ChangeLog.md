# Architecture — analyse des choix techniques

Ce document identifie, pour chaque notion demandée, les classes concernées dans le
projet, son rôle, un avantage, une limite, et un extrait représentatif.

---

## MVC (Modèle-Vue-Contrôleur)

**Classes concernées** : `src/Model/*`, `src/Controller/*`, `templates/*`

**Rôle** : sépare les données et la logique métier (Modèle), l'affichage (Vue) et
l'orchestration d'une requête (Contrôleur), pour que chaque partie évolue indépendamment.

**Avantage** : un changement de design HTML (Vue) ne touche jamais à la logique métier
(Modèle/Service) ; un changement de règle métier ne touche jamais à l'affichage.

**Limite** : sur un projet aussi réduit que celui-ci (2 ressources), la séparation ajoute
plusieurs fichiers pour des opérations simples — un compromis accepté au profit de la
clarté et de l'évolutivité.

**Extrait** : `SalleController::index()` récupère les données via le Repository, puis
délègue l'affichage à `templates/salle/index.php`, sans jamais générer de HTML lui-même.

---

## Front Controller

**Classes concernées** : `public/index.php`

**Rôle** : constitue l'unique point d'entrée HTTP de l'application — toute requête,
quelle que soit l'URL, passe par ce seul fichier avant d'être redirigée vers le bon
Contrôleur.

**Avantage** : centralise le chargement de l'autoload, du conteneur, et le traitement
des erreurs (404/405) en un seul endroit, plutôt que de dupliquer cette logique dans
chaque page PHP.

**Limite** : ce fichier devient un point de passage obligé — toute erreur de
configuration ici bloque l'application entière.

**Extrait** :
```php
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
switch ($routeInfo[0]) {
    case Dispatcher::FOUND:
        $controleur = $container->get($classeControleur);
        echo $controleur->{$methode}(...$arguments);
}
```

---

## Router

**Classes concernées** : `routes/web.php`, `nikic/fast-route` (dépendance externe)

**Rôle** : associe une méthode HTTP et un chemin d'URL à un handler `[Controller::class, 'methode']`, avec extraction des paramètres dynamiques (`{id:\d+}`).

**Avantage** : découple la structure des URL du code métier — changer une URL ne
demande de modifier qu'une ligne dans `routes/web.php`.

**Limite** : FastRoute ne construit pas lui-même les contrôleurs — il faut une couche
supplémentaire (le conteneur) pour les instancier avec leurs dépendances.

**Extrait** :
```php
$r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
```

---

## Validator

**Classes concernées** : `App\Validation\ValidatorInterface`, `SalleValidator`,
`ReservationValidator`, `ValidationResult`

**Rôle** : vérifie la forme des données entrantes (types, longueurs, formats) avant
qu'elles n'atteignent la logique métier.

**Avantage** : centralise les règles de format ; un même contrat (`ValidatorInterface`)
permet d'ajouter facilement un nouveau validateur.

**Limite** : ne vérifie que la syntaxe, jamais le contexte métier (une salle "valide en
apparence" peut être inactive) — cette limite est volontaire et déplacée vers le Service.

**Extrait** :
```php
'capacite' => v::intVal()->between(1, 1000)->setName('La capacité'),
```

---

## DTO (Data Transfer Object)

**Classes concernées** : `CreerSalleDto`, `CreerReservationDto`, `ModifierSalleDto`,
et leurs Builders associés

**Rôle** : transporte des données déjà validées, fortement typées, entre le Contrôleur
et le Service — sans jamais transmettre `$_POST` brut.

**Avantage** : garantit à la compilation que chaque champ a le bon type (ex.
`\DateTimeImmutable` plutôt qu'une chaîne), éliminant une classe entière de bugs de
conversion silencieuse.

**Limite** : ajoute une classe (voire un Builder) par opération — un DTO qui ressemble
beaucoup à l'entité peut sembler redondant pour une simple création.

**Extrait** :
```php
final class CreerReservationDto {
    public function __construct(
        public readonly int $salleId,
        public readonly \DateTimeImmutable $dateDebut,
        public readonly \DateTimeImmutable $dateFin,
        // ...
    ) {}
}
```

---

## ORM (Object-Relational Mapping)

**Classes concernées** : `illuminate/database` (Eloquent), `App\Model\Salle`,
`App\Model\Reservation`, `config/database.php`

**Rôle** : fait correspondre automatiquement les tables SQL à des classes PHP, et les
lignes à des objets.

**Avantage** : élimine l'écriture manuelle de SQL pour les opérations courantes
(`Salle::find()`, `Salle::create()`), et prépare les requêtes contre les injections.

**Limite** : masque le SQL réellement exécuté, ce qui peut nuire aux performances sur
des requêtes complexes sans vigilance.

**Extrait** : `config/database.php`, où `Capsule\Manager` démarre Eloquent en dehors de
Laravel.

---

## Active Record

**Classes concernées** : `App\Model\Salle`, `App\Model\Reservation`

**Rôle** : chaque classe représente à la fois les données d'une ligne et le comportement
pour la manipuler (`->save()`, `::find()`), pattern implémenté nativement par Eloquent.

**Avantage** : syntaxe concise et lisible pour les opérations CRUD simples.

**Limite** : couple fortement la représentation des données à la persistance — c'est
justement pour compenser cette limite que le Repository a été ajouté par-dessus.

**Extrait** :
```php
class Salle extends Model {
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'salle_id');
    }
}
```

---

## Repository

**Classes concernées** : `SalleRepositoryInterface`/`SalleRepository`,
`ReservationRepositoryInterface`/`ReservationRepository`

**Rôle** : encapsule toutes les requêtes Eloquent derrière une interface métier
(`findActives()`, `rechercherConflit()`), pour que le reste de l'application ne parle
jamais directement à Eloquent.

**Avantage** : les Services/Contrôleurs dépendent d'une abstraction, testable avec des
doublures en mémoire, sans jamais toucher MySQL.

**Limite** : ajoute une couche d'indirection supplémentaire, dont l'intérêt n'est
pleinement visible que si l'implémentation change un jour (ou pour les tests).

**Extrait** :
```php
interface ReservationRepositoryInterface {
    public function rechercherConflit(int $salleId, \DateTimeImmutable $debut, \DateTimeImmutable $fin): ?Reservation;
}
```

---

## Service

**Classes concernées** : `CreerReservationService`, `AnnulerReservationService`,
`CreerSalleService`, `ModifierSalleService`

**Rôle** : héberge les règles métier (chevauchement, durée maximale, salle active) —
la seule couche qui orchestre Repository + règles pour produire une décision.

**Avantage** : la logique métier est isolée, testable sans MySQL, réutilisable
indépendamment du canal d'entrée (formulaire web, future API JSON...).

**Limite** : pour un projet à 2 ressources, plusieurs petits Services peuvent sembler
nombreux comparés à une grosse classe unique — un compromis pour la responsabilité
unique.

**Extrait** :
```php
public function creer(CreerReservationDto $dto): Reservation {
    $salle = $this->recupererSalleDisponible($dto->salleId);
    $this->verifierPeriodeCoherente($dto);
    $this->verifierAbsenceDeConflit($dto);
    return $this->enregistrerReservation($dto);
}
```

---

## Injection par constructeur

**Classes concernées** : tous les Contrôleurs, Services, Repositories du projet

**Rôle** : chaque classe reçoit ses dépendances via son constructeur, jamais en les
créant elle-même (`new`) ni en interrogeant un conteneur.

**Avantage** : rend chaque classe testable isolément (injecter une doublure), et rend
explicite, rien qu'en lisant la signature, tout ce dont une classe a besoin.

**Limite** : un constructeur avec beaucoup de dépendances peut devenir long à lire — un
signe, dans ce cas, qu'une classe fait peut-être trop de choses.

**Extrait** :
```php
public function __construct(
    private readonly SalleRepositoryInterface $salleRepository,
    private readonly ReservationRepositoryInterface $reservationRepository,
) {}
```

---

## Conteneur d'injection

**Classes concernées** : `config/container.php`, `php-di/php-di`

**Rôle** : construit automatiquement les objets et leurs dépendances, à partir des
types déclarés dans les constructeurs et de quelques définitions explicites.

**Avantage** : évite d'écrire manuellement `new Controller(new Service(new Repo()))`
pour chaque classe du projet.

**Limite** : ajoute une "boîte noire" dans le flux de construction des objets, qui peut
compliquer le débogage si mal comprise — d'où la contrainte du sujet de limiter
`$container->get()` au seul point d'entrée.

**Extrait** :
```php
$builder->addDefinitions([
    SalleRepositoryInterface::class => autowire(SalleRepository::class),
]);
```

---

## Autowiring

**Classes concernées** : `config/container.php` (fonction `autowire()`)

**Rôle** : mécanisme par lequel PHP-DI lit automatiquement les types déclarés dans un
constructeur, pour deviner comment construire une classe sans configuration explicite.

**Avantage** : réduit `container.php` à quelques lignes seulement (les interfaces),
tout le reste étant déduit automatiquement.

**Limite** : ne fonctionne que si chaque dépendance est elle-même résolvable sans
ambiguïté — une interface avec deux implémentations possibles nécessite toujours une
définition explicite.

**Extrait** : `SalleController` n'a aucune définition dans `container.php`, pourtant
`$container->get(SalleController::class)` fonctionne, car PHP-DI résout chacune de ses
dépendances en cascade.

---

## Inversion de contrôle (IoC)

**Classes concernées** : l'ensemble de l'architecture Repository/Service/Contrôleur

**Rôle** : au lieu qu'une classe décide elle-même comment obtenir ses dépendances
(en les construisant, ou en interrogeant un conteneur), c'est une autorité externe (le
conteneur, au point d'entrée) qui les lui fournit.

**Avantage** : chaque classe reste ignorante de la façon dont ses dépendances sont
construites, ce qui les rend interchangeables (vrai Repository ↔ doublure de test).

**Limite** : demande une discipline stricte — le sujet interdit explicitement qu'une
classe reçoive `ContainerInterface` pour aller chercher elle-même ses dépendances,
ce qui romprait ce principe (anti-pattern Service Locator).

**Extrait** : le code à éviter (`ContainerInterface` injecté) vs le code attendu
(dépendances typées explicitement) — voir la section correspondante du sujet.

---

## Les cinq principes SOLID

**S — Responsabilité unique** : `SalleRepository` ne fait que l'accès aux données ;
`CreerReservationService` ne fait que la logique métier ; `SalleController` n'orchestre
que la requête HTTP.

**O — Ouvert/fermé** : `ValidatorInterface` permet d'ajouter un nouveau validateur sans
modifier le code existant qui utilise l'interface.

**L — Substitution de Liskov** : `SalleRepository` et une éventuelle doublure de test
(`FakeSalleRepository`) sont interchangeables partout où `SalleRepositoryInterface` est
attendu, sans changer le comportement du code appelant.

**I — Ségrégation des interfaces** : `SalleRepositoryInterface` et
`ReservationRepositoryInterface` restent séparées, chacune ne portant que les méthodes
pertinentes à sa ressource.

**D — Inversion des dépendances** : `CreerReservationService` dépend de
`SalleRepositoryInterface`, jamais de `SalleRepository` directement — permettant
l'injection d'une doublure dans `tests/Unit/CreerReservationServiceTest.php`.

## [1.0.0] - 2026-09-10

### Ajouté
- Mise en forme CSS complète (`public/assets/style.css`) : palette, tableaux, formulaires, badges de statut, responsive
- Messages flash de succès/erreur après les actions de création et d'annulation
- README.md complet (prérequis, installation, configuration, tests)
- ARCHITECTURE.md : analyse des 14 notions demandées (MVC, Repository, SOLID...)
- Diagramme de classes (`diagramme-de-classes.md`)

### Corrigé
- Gestion propre des exceptions non prévues (page d'erreur générique au lieu d'un plantage brut)
- Anomalies mineures relevées lors de la vérification sur dépôt fraîchement cloné

## [0.12.0] - 2026-09-09

### Ajouté
- PHPUnit installé et configuré (`phpunit.xml`, testsuites Unit/Integration)
- Doublures `FakeSalleRepository` et `FakeReservationRepository` (tests sans MySQL)
- 8 tests unitaires de `CreerReservationService` (réservation valide, salle inexistante,
  salle inactive, dates incohérentes, durée excessive, date passée, conflit, voisinage)
- Tests de validation (email invalide, responsable vide, capacité négative, type inconnu, date incorrecte)
- Tests d'intégration Eloquent sur SQLite en mémoire (création, relation, conflit, annulation)

## [0.11.0] - 2026-09-08

### Ajouté
- `config/container.php` : conteneur PHP-DI avec autowiring pour les Repositories
- Utilisation du conteneur dans `public/index.php` (remplace le conteneur transitoire de l'étape 10)

### Supprimé
- `config/conteneur_transitoire.php`, devenu obsolète

## [0.10.0] - 2026-09-07

### Ajouté
- Déclaration des 12 routes FastRoute (`routes/web.php`)
- Point d'entrée HTTP unique (`public/index.php`) avec gestion NOT_FOUND / METHOD_NOT_ALLOWED / FOUND
- En-tête `Allow` sur les réponses 405
- `AnnulerReservationService` et `ReservationIntrouvableException` (complément de l'étape 8)

## [0.9.0] - 2026-09-06

### Ajouté
- `SalleController` (index, show, create, store, edit, update)
- `ReservationController` (index, show, create, store, cancel)
- Trait `RenderViewTrait` partagé (rendu de vue + redirection)
- Vues salle et réservation (index, show, create, edit)
- Pattern Post/Redirect/Get après chaque soumission réussie

## [0.8.0] - 2026-09-05

### Ajouté
- `CreerReservationService`, `CreerSalleService`, `ModifierSalleService`
- `SalleIndisponibleException`, `ReglesMetierException`
- Règles métier découpées en méthodes privées (période cohérente, futur, durée max, conflit)

## [0.7.0] - 2026-09-05

### Ajouté
- `SalleRepositoryInterface` / `SalleRepository`
- `ReservationRepositoryInterface` / `ReservationRepository`
- Méthode `rechercherConflit()` traduisant la règle de chevauchement en requête SQL

## [0.6.0] - 2026-09-05

### Ajouté
- `CreerSalleDto`, `CreerReservationDto`, `ModifierSalleDto`
- `CreerSalleDtoBuilder`, `CreerReservationDtoBuilder` (pattern Builder)

## [0.5.0] - 2026-09-05

### Ajouté
- `ValidatorInterface`, `ValidationResult`
- `SalleValidator`, `ReservationValidator` (Respect\Validation, messages normalisés avec `setName()`)

## [0.4.0] - 2026-09-05

### Ajouté
- Script `database/seed.php` : 5 salles initiales, idempotent (`firstOrCreate`)

## [0.3.0] - 2026-09-05

### Ajouté
- `App\Model\Salle`, `App\Model\Reservation`
- Relations Eloquent `hasMany` / `belongsTo`

## [0.2.0] - 2026-09-05

### Ajouté
- `.env.example`
- `config/database.php` : connexion Eloquent via `Capsule\Manager`
- Migrations des tables `salles` et `reservations`
- Script `database/migrate.php`

## [0.1.0] - 2026-09-05

### Ajouté
- `composer.json`, autoload PSR-4
- Dépendances installées (FastRoute, Respect\Validation, Eloquent, PHP-DI, phpdotenv)
- Arborescence du projet

## [0.0.0] - 2026-09-05

### Ajouté
- Initialisation du dépôt Git
- `.gitignore`, `README.md`, `CHANGELOG.md`