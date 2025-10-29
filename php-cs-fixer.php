<?php
require __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

// Mettre ici le vrai dossier contenant ton code PHP
$finder = Finder::create()->in(__DIR__); 

$config = new Config();
return $config
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);
