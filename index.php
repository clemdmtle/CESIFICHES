<?php

require "vendor/autoload.php"; // charge automatiquement les dépendances

use App\modeles\Database;

$pdo = Database::getInstance(); // on crée l'instance à la BDD
$loader = new \Twig\Loader\FilesystemLoader('src\vues'); // situe les fichiers de vue
$twig = new \Twig\Environment($loader, [ // gère le rendu des templates twig
    'debug' => true,
    'cache' => false,
]);