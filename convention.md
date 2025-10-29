# GI2 Module: Développement Web Avancé

## Convention de Codage : TP4 (Réservations des terrains)

*Membres du groupe :*
- Lyamani Ismail
- Moeniss Douae
- Oumhella Abdellatif
- Raissouni Aya
- Rohand Douae

---

## Introduction à la convention de codage

Dans le cadre du développement de notre projet en PHP, il est essentiel d'adopter une convention de codage commune afin d'assurer la lisibilité, la cohérence et la maintenabilité du code source. Une convention de code définit un ensemble de règles et de bonnes pratiques que tous les membres de l'équipe s'engagent à respecter lors de la rédaction du code.

Dans un projet collaboratif, l'adoption d'un ensemble de règles de codage uniformes offre plusieurs avantages :

- *Uniformité et Lisibilité* : Indépendamment de qui a écrit le code, celui-ci doit apparaître comme s'il avait été rédigé par une seule et même personne. Cela réduit la charge cognitive lors de la lecture et de la revue de code.

- *Maintenance Facilitée* : Un code cohérent et bien structuré est plus facile à déboguer, à mettre à jour et à étendre. Cela diminue le temps de maintenance future.

- *Qualité du Code* : En respectant des standards élevés, nous évitons les erreurs courantes et les mauvaises pratiques, conduisant à une application plus robuste et plus performante.

Cette convention s'applique à tout le code source créé pour l'application web, y compris :

- *PHP* (Logique métier, contrôleurs, modèles)
- *HTML* (Structure des vues)
- *CSS* (Stylisation)
- *JavaScript* (Interactions côté client)
- *Documentation et Commentaires* associés au code

En nous engageant à suivre ces lignes directrices, nous garantissons que le projet "Réservation de Terrains" sera développé sur une base technique solide, permettant une collaboration efficace et la livraison d'un produit final de haute qualité.

---

## Organisation du projet

Afin d'assurer une structure claire et compréhensible du projet, il est important de respecter une organisation cohérente des dossiers et fichiers. Chaque répertoire doit avoir un rôle précis afin de faciliter la navigation et la maintenance du code.

Le projet est organisé en plusieurs dossiers principaux, chacun ayant un rôle précis :

- *admin/* : contient les pages et fichiers relatifs à la partie administrateur du site, comme dashboard.php, qui permet la gestion du contenu et des utilisateurs.

- *gestionnaire/* : regroupe les fichiers spécifiques au profil du gestionnaire, notamment son tableau de bord et les fonctionnalités associées.

- *user/* : contient les fichiers liés à l'espace utilisateur, comme dashboard.php, ainsi que les éléments d'interface (user_header.php, user_footer.php, user_navbar.php, etc.).

- *includes/* : regroupe les fichiers communs réutilisables, partagés entre les différentes parties du projet. Ce dossier contient par exemple :
  - Des fichiers PHP de connexion (db.php)
  - Des sous-dossiers (admin, gestionnaire, user) contenant les styles des en-têtes, pieds de page et autres composants utilisés dans chaque interface
  - Les fichiers CSS et JS

- *assets/* : contient les ressources statiques telles que les images.

- *classes/* : regroupe les classes PHP utilisées pour structurer la logique du projet.

- *vendor/* : généré automatiquement par Composer, il contient les dépendances externes nécessaires au projet.

- *Fichiers racine* :
  - index.php → point d'entrée du site
  - landing.php → page d'accueil principale
  - Fichiers de configuration (.env, composer.json, etc.)

---

## Règles de nommage

Afin de garantir la cohérence et la lisibilité du code dans tout le projet, nous avons défini des règles de nommage applicables à l'ensemble des éléments : dossiers, fichiers, classes, méthodes, variables, constantes, etc. Ces conventions permettent à tous les membres de l'équipe de comprendre rapidement le rôle de chaque élément du projet.

### Nommage des dossiers

*kebab-case* (nom-dossier) : minuscules avec tirets

Exemple : user-profile, booking-system

### Nommage des fichiers

*snake_case* (nom_fichier) : minuscules avec underscores

Exemple : user_dashboard.php, booking_manager.php

### Nommage des classes

*PascalCase* : première lettre de chaque mot en majuscule

Exemple : <br> pour les classes *JavaScript*
php
class UserReservation {
    private $userId;
    private $terrainId;
}


Pour les classes CSS : *kebab-case*

css
.form-container {
    padding: 2rem;
}


### 4. Nommage des méthodes et fonctions

*camelCase* (nomMethode) : première lettre minuscule, majuscules pour les mots suivants

Exemple : <br>
*PHP*
php
public function getReservations() {
    return $this->reservations;
}

*JavaScript*
javascript
function updatePage() {
    let x = 0;
    return x;
}


### Nommage des variables

*camelCase* (nomVariable) : première lettre minuscule, majuscules pour les mots suivants

pour javascript, il faut declarer les variables avec *let*

Exemple : <br>
*PHP*
php
$userId = 123;
$terrainName = "Terrain A";

*JavaScript*
javascript
let variableUser = 'user';



Dans CSS : *kebab-case* avec préfixe sémantique

Exemple : --primary-color, --secondary-color

### Nommage des constantes

*UPPER_SNAKE_CASE* : majuscules avec underscores

Exemple :

php
const MAX_RESERVATIONS = 5;
const DEFAULT_STATUS = 'pending';


### Nommage de base de données

- *Database* : snake_case (minuscules avec underscores)
- *Tables* : snake_case (minuscules avec underscores)
- *Colonnes* : snake_case (minuscules avec underscores)

Exemple :

sql
CREATE TABLE user_reservations (
    id INT PRIMARY KEY,
    user_id INT,
    terrain_id INT,
    reservation_date DATE
);


### Nommage de ID dans CSS

*camelCase*

css
#userName {
    width: 100%;
}


### Nommage des attributs et balises en HTML

*minuscules*

html
<div class="container">
    <input type="text" id="userName" name="user_name" />
</div>


---

## Style de code

Le style de code a pour objectif d'assurer une lecture fluide et uniforme du code entre tous les membres de l'équipe. Nous suivons principalement les recommandations PSR-12, adaptées à notre projet PHP natif.

> *Note importante* : PHP et JavaScript suivent les mêmes conventions de style de code en ce qui concerne l'indentation (4 espaces), le nommage (camelCase pour variables et méthodes, PascalCase pour les classes), les accolades (même ligne), et les espaces autour des opérateurs. Cela garantit une cohérence totale entre le code backend (PHP) et le code frontend (JavaScript).

#### Indentation et espacement

- *JavaScript et PHP* : 4 espaces (pas de tabulation)
- *HTML et CSS* : 2 espaces (pas de tabulation)

#### Accolades

Pour les classes : accolade dans la même ligne de déclaration de classe

*PHP* :
php
class User {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
}


*JavaScript* :
javascript
class User {
    constructor(name) {
        this.name = name;
    }
    
    getName() {
        return this.name;
    }
}


Pour les méthodes/fonctions : accolade dans la même ligne de déclaration

*PHP* :
php
public function getReservations() {
    return $this->reservations;
}


*JavaScript* :
javascript
function getReservations() {
    return this.reservations;
}

const getReservations = () => {
    return this.reservations;
};


#### Espacement vertical

- On déclare la class, puis les attributs
- On laisse une ligne et on déclare les méthodes
- Une ligne entre deux méthodes

#### Longueur des lignes

- *Limite stricte* : 120 caractères maximum
- *Limite recommandée* : 80 caractères pour une meilleure lisibilité

Si une ligne dépasse, la découper de manière logique

*Correct* :
php
$reservation = new Reservation(
    $userId,
    $terrainId,
    $startDate,
    $endDate
);


*Incorrect* :
php
$reservation = new Reservation($userId, $terrainId, $startDate, $endDate, $additionalInfo, $comments);


#### Espaces et parenthèses

*Autour des opérateurs* :

Correct (PHP et JavaScript identique) :
php
// PHP
$total = $price + $tax;
$isValid = ($age >= 18) && ($hasLicense === true);


javascript
// JavaScript
let total = price + tax;
let isValid = (age >= 18) && (hasLicense === true);


Incorrect :
php
// PHP
$total=$price+$tax;


javascript
// JavaScript
let total=price+tax;


*Structures de contrôle* :

Correct (PHP et JavaScript identique) :
php
// PHP
if ($condition) {
    // code
}

foreach ($items as $item) {
    // code
}


javascript
// JavaScript
if (condition) {
    // code
}

for (let item of items) {
    // code
}


Incorrect :
php
// PHP
if($condition){
    // code
}

foreach($items as $item){
    // code
}


javascript
// JavaScript
if(condition){
    // code
}

for(let item of items){
    // code
}


*Appels de fonction* :

Correct (PHP et JavaScript identique) :
php
// PHP
calculateTotal($items, $discount);


javascript
// JavaScript
calculateTotal(items, discount);


Incorrect :
php
// PHP
calculateTotal( $items , $discount );


javascript
// JavaScript
calculateTotal( items , discount );


*Déclarations de fonction* :

Correct (PHP et JavaScript identique) :
php
// PHP
public function updateUser($id, $data) {
    // code
}


javascript
// JavaScript
function updateUser(id, data) {
    // code
}

const updateUser = (id, data) => {
    // code
};


Incorrect :
php
// PHP
public function updateUser ($id,$data) {
    // code
}


javascript
// JavaScript
function updateUser (id,data) {
    // code
}


#### Ordre des propriétés des attributs dans CSS

Respecter un ordre logique pour les propriétés :

1. Positionnement : position, top, left, z-index
2. Boîte : display, flex, width, height, margin, padding
3. Texte : font, color, text-align, line-height
4. Décoration : background, border, box-shadow, border-radius
5. Animation : transition, transform

Exemple :
css
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


### Pour CSS

- Utilisez 2 espaces (pas de tabulation)
- Une seule règle par ligne
- Une ligne vide entre deux blocs de style
- Les accolades sont toujours sur la même ligne que le sélecteur

---

## Commentaire et documentation

Les commentaires permettent de rendre le code plus compréhensible pour les autres développeurs, ainsi que pour soi-même lors d'une relecture ultérieure. Une bonne documentation interne facilite la maintenance, le débogage et la collaboration entre les membres du projet.

### Types de commentaires

*Commentaires sur une ligne* :

php
// Calcul du prix total avec réduction
$totalPrice = $basePrice * (1 - $discount);


*Commentaires sur plusieurs lignes* :

php
/**
 * Cette fonction vérifie la disponibilité d'un terrain
 * pour une période donnée en tenant compte des
 * réservations existantes et des maintenances planifiées.
 */


### Documentation de fonctions et méthodes (DocBlocks)

Utiliser le format PHPDoc pour toutes les fonctions et méthodes publiques :

php
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


### Documentation des classes

php
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


### Commentaires de sections

Pour organiser les classes volumineuses, utiliser des commentaires de sections :

php
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


### Commentaires en HTML

html
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


### Commentaires dans CSS

Ajouter un titre de section avec /* --- Section --- */. Ajouter un commentaire de bloc pour décrire un composant complexe.

Exemple :

css
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


---

## Références

- *Documentation PHP officielle* : https://www.php.net/docs.php
- *Normes de codage PHP-FIG (PSR)* : https://www.php-fig.org/psr/
- *Exemples des conventions de codage* :
  - https://github.com/florentdupont/Conventions/wiki/Convention-de-codage
  - https://github.com/Romain-Donze/Conventions/tree/main/Qt%20Qml

---

*Note* : Cette convention de codage est un document vivant et peut être mis à jour par consensus de l'équipe selon les besoins du projet.