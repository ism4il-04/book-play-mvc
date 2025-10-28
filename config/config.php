<?php
// config/config.php

// Global configuration constants
const APP_NAME = 'book&play';
const BASE_URL = 'http://localhost/book&play/public/';

// Define if not already defined (helps when included multiple times)
if (!defined('APP_NAME')) {
    define('APP_NAME', 'book&play');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/book&play/public/');
}
