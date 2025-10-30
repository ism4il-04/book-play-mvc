<?php

// config/config.php

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Global configuration constants
const APP_NAME = 'book&play';

// Load BASE_URL from environment at runtime (const can't use $_ENV)
if (isset($_ENV['BASE_URL']) && '' !== $_ENV['BASE_URL']) {
    define('BASE_URL', $_ENV['BASE_URL']);
} elseif (getenv('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL'));
}

// Define if not already defined (helps when included multiple times)
if (!defined('APP_NAME')) {
    define('APP_NAME', 'book&play');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/TPs_Dev-web/book-play-mvc/public/');
}

define('RECAPTCHA_SITE_KEY', '6LdHhuQrAAAAAN_Jx1i6sic0N2KxGmrz3J1yyRAL');
define('RECAPTCHA_SECRET_KEY', '6LdHhuQrAAAAAFDFflYcxPYgQSS7w-UwqaZUUanI');
