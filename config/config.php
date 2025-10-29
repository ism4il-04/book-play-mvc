<?php
// config/config.php

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


// Global configuration constants
const APP_NAME = 'book&play';
const BASE_URL = 'http://localhost/book&play/public/';

// Define if not already defined (helps when included multiple times)
if (!defined('APP_NAME')) {
    define('APP_NAME', 'book&play');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/TPs_Dev-web/book-play-mvc/public/');
}
