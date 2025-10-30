# GI2 Module: Développement Web Avancé

## Convention de Codage : TP4 (Réservations des terrains)

**Membres du groupe :**

- Lyamani Ismail
- Moeniss Douae
- Oumhella Abdellatif
- Raissouni Aya
- Rohand Douae

---

## Introduction à la convention de codage

Dans le cadre du développement de notre projet en PHP, il est essentiel d'adopter une convention de codage commune afin d'assurer la lisibilité, la cohérence et la maintenabilité du code source. Une convention de code définit un ensemble de règles et de bonnes pratiques que tous les membres de l'équipe s'engagent à respecter lors de la rédaction du code.

Dans un projet collaboratif, l'adoption d'un ensemble de règles de codage uniformes offre plusieurs avantages :

- **Uniformité et Lisibilité** : Indépendamment de qui a écrit le code, celui-ci doit apparaître comme s'il avait été rédigé par une seule et même personne. Cela réduit la charge cognitive lors de la lecture et de la revue de code.

- **Maintenance Facilitée** : Un code cohérent et bien structuré est plus facile à déboguer, à mettre à jour et à étendre. Cela diminue le temps de maintenance future.

- **Qualité du Code** : En respectant des standards élevés, nous évitons les erreurs courantes et les mauvaises pratiques, conduisant à une application plus robuste et plus performante.

Cette convention s'applique à tout le code source créé pour l'application web, y compris :

- **PHP** (Logique métier, contrôleurs, modèles)
- **HTML** (Structure des vues)
- **CSS** (Stylisation)
- **JavaScript** (Interactions côté client)
- **Documentation et Commentaires** associés au code

En nous engageant à suivre ces lignes directrices, nous garantissons que le projet "Réservation de Terrains" sera développé sur une base technique solide, permettant une collaboration efficace et la livraison d'un produit final de haute qualité.

---

## Organisation du projet

Afin d’assurer une structure claire, cohérente et facilement maintenable, il est essentiel de respecter une organisation rigoureuse des dossiers et fichiers du projet.
Notre application repose sur l’architecture MVC (Modèle – Vue – Contrôleur), qui permet de séparer les différentes responsabilités du code.

### Structure de projet

```text
/book-play
│
├── /app
│   ├── /Controllers
│   ├── /Models
│   ├── /Views
│   └── /Core
│       ├── App.php
│       ├── Controller.php
│       ├── Model.php
│       └── Database.php
│
├── /config
│   └── config.php
│
├── /public
│   ├── index.php
│   ├── /css
│   ├── /js
│   └── /uploads
│
├── /vendor
├── .env
├── composer.json
└── README.md
```

### Détails des dossiers et fichiers

***/app***

C’est le cœur de l’application.
Il contient toute la logique métier, les modèles, les contrôleurs et les vues.

***/app/Controllers***

Contient les contrôleurs

Chaque contrôleur gère une partie de l’application (ex : UserController.php, HomeController.php)

Un contrôleur :

reçoit une requête (via l'URL)

demande les données au modèle

envoie les données à la vue

Exemple : UserController.php

```php
class UserController extends Controller {
    public function index() {
        $users = $this->model("User")->getAll();
        $this->view("users/index", ['users' => $users]);
    }
}
```

***/app/Models***

Contient les modèles

Un modèle permet d’interagir avec la base de données

Il représente une entité : ex : User.php, Article.php

Exemple : User.php

```php
class User extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM users")->fetchAll();
    }
}
```

***/app/Views***

Contient les fichiers qui affichent les pages HTML

Chaque dossier correspond à un contrôleur (ex : /Views/users/index.php)

❗ Les vues n’ont pas de logique métier, seulement de l’affichage.

Exemple : Views/users/index.php

```html
<h1>Liste des utilisateurs</h1>
<?php foreach ($users as $user): ?>
    <p><?= $user["name"] ?></p>
<?php endforeach ?>
```

***/app/Core***

Contient les classes de base du framework MVC que tu construis :

Fichier Rôle

App.php: Analyse l'URL et appelle le bon contrôleur et la bonne méthode. (Front Controller / Router)

Controller.php: Classe parent de tous les contrôleurs : gère load model et load view.

Model.php: Classe parent de tous les modèles : initialise la connexion à la DB.

***/app/Database/ → Database.php***

Gère la connexion à la base de données via PDO.

Utilisée par Model.php

Exemple : Singleton Database

```php
class Database {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new PDO(...);
        }
        return self::$instance;
    }
}
```

***/config → config.php***

Contient les variables de configuration statiques (ex: gestion de sessions, constantes globales).

***/public***

Point d’entrée du projet (accessible depuis le navigateur)

Elément Rôle
index.php: Redirige toutes les requêtes vers App.php
/css: Styles CSS
/js: Scripts JavaScript
/uploads: Images, fichiers uploadés
public/index.php

C’est le fichier principal exécuté par le serveur Apache.

```php
require_once "../app/Core/App.php";
require_once "../app/Core/Controller.php";
```

Il charge l’application.

***.env***

Contient les informations sensibles :

```text
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=monmvc
```

***composer.json***

Contient les dépendances du projet et l’autoloading des classes.

---

## Règles de nommage

Afin de garantir la cohérence et la lisibilité du code dans tout le projet, nous avons défini des règles de nommage applicables à l'ensemble des éléments : dossiers, fichiers, classes, méthodes, variables, constantes, etc. Ces conventions permettent à tous les membres de l'équipe de comprendre rapidement le rôle de chaque élément du projet.

### Nommage des dossiers

**kebab-case** (nom-dossier) : minuscules avec tirets

Exemple : `user-profile`, `booking-system`

### Nommage des fichiers

**snake_case** (nom_fichier) : minuscules avec underscores

Exemple : `user_dashboard.php`, `booking_manager.php`

### Nommage des classes

**PascalCase** : première lettre de chaque mot en majuscule

Exemple :

pour les classes **JavaScript**

```php
class UserReservation {
    private $userId;
    private $terrainId;
}
```

Pour les classes CSS : **kebab-case**

```css
.form-container {
    padding: 2rem;
}
```

### Nommage des méthodes et fonctions

**camelCase** (nomMethode) : première lettre minuscule, majuscules pour les mots suivants

Exemple :

#### PHP

```php
public function getReservations() {
    return $this->reservations;
}
```

#### JavaScript

```javascript
function updatePage() {
    let x = 0;
    return x;
}
```

### Nommage des variables

**camelCase** (nomVariable) : première lettre minuscule, majuscules pour les mots suivants

pour javascript, il faut declarer les variables avec **let**

Exemple :

#### php

```php
$userId = 123;
$terrainName = "Terrain A";
```

#### javascript

```javascript
let variableUser = 'user';
```

Dans CSS : **kebab-case** avec préfixe sémantique

Exemple : `--primary-color`, `--secondary-color`

### Nommage des constantes

**UPPER_SNAKE_CASE** : majuscules avec underscores

Exemple :

```php
const MAX_RESERVATIONS = 5;
const DEFAULT_STATUS = 'pending';
```

### Nommage de base de données

- **Database** : snake_case (minuscules avec underscores)
- **Tables** : snake_case (minuscules avec underscores)
- **Colonnes** : snake_case (minuscules avec underscores)

Exemple :

```sql
CREATE TABLE user_reservations (
    id INT PRIMARY KEY,
    user_id INT,
    terrain_id INT,
    reservation_date DATE
);
```

### Nommage de ID dans CSS

#### camelCase

```css
#userName {
    width: 100%;
}
```

### Nommage des attributs et balises en HTML

#### miniscules

```html
<div class="container">
    <input type="text" id="userName" name="user_name" />
</div>
```

---

## Style de code

Le style de code a pour objectif d'assurer une lecture fluide et uniforme du code entre tous les membres de l'équipe. Nous suivons principalement les recommandations PSR-12, adaptées à notre projet PHP natif.

> **Note importante** : PHP et JavaScript suivent les mêmes conventions de style de code en ce qui concerne l'indentation (4 espaces), le nommage (camelCase pour variables et méthodes, PascalCase pour les classes), les accolades (même ligne), et les espaces autour des opérateurs. Cela garantit une cohérence totale entre le code backend (PHP) et le code frontend (JavaScript).

### Indentation et espacement

- **JavaScript et PHP** : 4 espaces (pas de tabulation)
- **HTML et CSS** : 2 espaces (pas de tabulation)

#### Accolades

Pour les classes : accolade dans la même ligne de déclaration de classe

**PHP** :

```php
class User {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
}
```

**JavaScript** :

```javascript
class User {
    constructor(name) {
        this.name = name;
    }
    
    getName() {
        return this.name;
    }
}
```

Pour les méthodes/fonctions : accolade dans la même ligne de déclaration

**PHP** :

```php
public function getReservations() {
    return $this->reservations;
}
```

**JavaScript** :

```javascript
function getReservations() {
    return this.reservations;
}

const getReservations = () => {
    return this.reservations;
};
```

#### Espacement vertical

- On déclare la class, puis les attributs
- On laisse une ligne et on déclare les méthodes
- Une ligne entre deux méthodes

#### Longueur des lignes

- **Limite stricte** : 120 caractères maximum
- **Limite recommandée** : 80 caractères pour une meilleure lisibilité

Si une ligne dépasse, la découper de manière logique

**Correct** :

```php
$reservation = new Reservation(
    $userId,
    $terrainId,
    $startDate,
    $endDate
);
```

**Incorrect** :

```php
$reservation = new Reservation($userId, $terrainId, $startDate, $endDate, $additionalInfo, $comments);
```

#### Espaces et parenthèses

**Autour des opérateurs** :

Correct (PHP et JavaScript identique) :

```php
// PHP
$total = $price + $tax;
$isValid = ($age >= 18) && ($hasLicense === true);
```

```javascript
// JavaScript
let total = price + tax;
let isValid = (age >= 18) && (hasLicense === true);
```

Incorrect :

```php
// PHP
$total=$price+$tax;
```

```javascript
// JavaScript
let total=price+tax;
```

**Structures de contrôle** :

Correct (PHP et JavaScript identique) :

```php
// PHP
if ($condition) {
    // code
}

foreach ($items as $item) {
    // code
}
```

```javascript
// JavaScript
if (condition) {
    // code
}

for (let item of items) {
    // code
}
```

Incorrect :

```php
// PHP
if($condition){
    // code
}

foreach($items as $item){
    // code
}
```

```javascript
// JavaScript
if(condition){
    // code
}

for(let item of items){
    // code
}
```

**Appels de fonction** :

Correct (PHP et JavaScript identique) :

```php
// PHP
calculateTotal($items, $discount);
```

```javascript
// JavaScript
calculateTotal(items, discount);
```

Incorrect :

```php
// PHP
calculateTotal( $items , $discount );
```

```javascript
// JavaScript
calculateTotal( items , discount );
```

**Déclarations de fonction** :

Correct (PHP et JavaScript identique) :

```php
// PHP
public function updateUser($id, $data) {
    // code
}
```

```javascript
// JavaScript
function updateUser(id, data) {
    // code
}

const updateUser = (id, data) => {
    // code
};
```

Incorrect :

```php
// PHP
public function updateUser ($id,$data) {
    // code
}
```

```javascript
// JavaScript
function updateUser (id,data) {
    // code
}
```

#### Ordre des propriétés des attributs dans CSS

Respecter un ordre logique pour les propriétés :

1. Positionnement : `position`, `top`, `left`, `z-index`
2. Boîte : `display`, `flex`, `width`, `height`, `margin`, `padding`
3. Texte : `font`, `color`, `text-align`, `line-height`
4. Décoration : `background`, `border`, `box-shadow`, `border-radius`
5. Animation : `transition`, `transform`

Exemple :

```css
.card {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 300px;
    padding: 15px;
    color: var(--text-color);
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}
```

### Pour CSS

- Utilisez 2 espaces (pas de tabulation)
- Une seule règle par ligne
- Une ligne vide entre deux blocs de style
- Les accolades sont toujours sur la même ligne que le sélecteur

---

## Commentaire et documentation

Les commentaires permettent de rendre le code plus compréhensible pour les autres développeurs, ainsi que pour soi-même lors d'une relecture ultérieure. Une bonne documentation interne facilite la maintenance, le débogage et la collaboration entre les membres du projet.

### Types de commentaires

**Commentaires sur une ligne** :

```php
// Calcul du prix total avec réduction
$totalPrice = $basePrice * (1 - $discount);
```

**Commentaires sur plusieurs lignes** :

```php
/**
 * Cette fonction vérifie la disponibilité d'un terrain
 * pour une période donnée en tenant compte des
 * réservations existantes et des maintenances planifiées.
 */
```

### Documentation de fonctions et méthodes (DocBlocks)

Utiliser le format PHPDoc pour toutes les fonctions et méthodes publiques :

```php
/**
 * Description courte sur une ligne - Ce que fait la fonction
 *
 * Description longue optionnelle sur plusieurs lignes
 * Explications détaillées, algorithmes, comportements spéciaux
 *
 * @param type $parametre Description du paramètre
 * @return type Description de la valeur retournée
 * @throws ExceptionType Quand l'exception est levée
 */
function maFonction($parametre) {
    // Code de la fonction
}
```

### Documentation des classes

```php
/**
 * Description courte de la classe
 *
 * Description détaillée expliquant le rôle, la responsabilité
 * et le contexte d'utilisation de la classe.
 *
 * @package NomDuPackage
 */
class NomClasse {
    // ...
}
```

### Commentaires de sections

Pour organiser les classes volumineuses, utiliser des commentaires de sections :

```php
class User {
    // ==========================================
    // PROPRIÉTÉS
    // ==========================================
    
    private $id;
    private $email;
    private $password;
    
    // ==========================================
    // CONSTRUCTEUR
    // ==========================================
    
    public function __construct($id, $email) {
        $this->id = $id;
        $this->email = $email;
    }
    
    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    // ==========================================
    // MÉTHODES MÉTIER
    // ==========================================
    
    public function makeReservation($terrain) {
        // Implémentation
    }
    
    public function cancelReservation($reservationId) {
        // Implémentation
    }
}
```

### Commentaires en HTML

```html
<!-- ========================================
     HEADER SECTION
     ======================================== -->
<header class="site-header">
    <!-- Logo et navigation -->
    <nav class="navbar">
        <!-- ... -->
    </nav>
</header>

<!-- ========================================
     MAIN CONTENT
     ======================================== -->
<main class="main-content">
    <!-- Contenu principal -->
</main>
```

### Commentaires dans CSS

Ajouter un titre de section avec `/* --- Section --- */`. Ajouter un commentaire de bloc pour décrire un composant complexe.

Exemple :

```css
/* --- Header Section --- */

.site-header {
    background-color: #fff;
    padding: 1rem;
}

/* --- Navigation --- */

.navbar {
    display: flex;
    justify-content: space-between;
}
```

---

## Références

- **Documentation PHP officielle** : [https://www.php.net/docs.php](https://www.php.net/docs.php)
- **Normes de codage PHP-FIG (PSR)** : [https://www.php-fig.org/psr/](https://www.php-fig.org/psr/)
- **Exemples des conventions de codage** :
  - [https://github.com/florentdupont/Conventions/wiki/Convention-de-codage](https://github.com/florentdupont/Conventions/wiki/Convention-de-codage)
  - [https://github.com/Romain-Donze/Conventions/tree/main/Qt%20Qml](https://github.com/florentdupont/Conventions/wiki/Convention-de-codage)

---

**Note** : Cette convention de codage est un document vivant et peut être mis à jour par consensus de l'équipe selon les besoins du projet.
