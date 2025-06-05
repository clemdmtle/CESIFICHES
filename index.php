<?php

require "vendor/autoload.php"; // charge automatiquement les dépendances

use App\modeles\Database;
use App\controleurs\FicheControleur;

$pdo = Database::getInstance(); // on crée l'instance à la BDD
$loader = new \Twig\Loader\FilesystemLoader('src\vues'); // situe les fichiers de vue
$twig = new \Twig\Environment($loader, [ // gère le rendu des templates twig
    'debug' => true,
    'cache' => false,
]);

// Ajout de la fonction path pour Twig pour page connexion
$function = new \Twig\TwigFunction('path', function ($name) {
    $routes = [
        'favoris' => '?uri=favoris',
        'accueil' => '?uri=/',
        'profil' => '?uri=profil',
        'connexion' => '?uri=connexion',
        'creation_de_compte' => '?uri=creation_de_compte',
        'creation_de_fiche' => '?uri=creation_de_fiche',
        'modifier_fiche' => '?uri=modifier_fiche',
        'supprimer_fiche' => '?uri=supprimer_fiche',
    ];
    return $routes[$name] ?? '/';
});

$twig->addFunction($function);

$uri = $_GET['uri'] ?? '/';

switch ($uri) {

    case '/':
        $controleur = new FicheControleur($pdo, $twig);
        $controleur->afficherFiches();
        break;
    default:
        http_response_code(404);
        break;
}
?>
