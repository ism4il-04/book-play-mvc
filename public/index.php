<?php
// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session (if you need it)
session_start();

// Load configuration
require_once '../config/config.php';

// Load the core classes
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Model.php';
require_once '../app/Core/App.php';



// Run the application
$app = new App();
