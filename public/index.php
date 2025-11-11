<?php
/**
 * Front Controller
 * Point d'entrée unique de l'application
 */

// Démarrer la session
session_start();

// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Charger la configuration
require_once __DIR__ . '/../config/config.php';

// Charger les classes principales
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/App.php';



// Déclencher la vérification en arrière-plan
// Cela ne ralentira pas le chargement de la page
register_shutdown_function(function() {
   
});
// ============================================

// Initialiser et lancer l'application
$app = new App();