<?php
namespace App\controleurs;

use App\modeles\ficheModele;
use Twig\Environment;

class FicheControleur extends Controleur {
    public function __construct($pdo, Environment $twig) {
        $this->model = new FicheModele($pdo);
        $this->twig = $twig;
    }

    public function afficherFiches(){
        $conditions = [];
        if (isset($_GET['recherche'])){
            $conditions['recherche'] = $_GET['recherche'];
        } 
        if (isset($_GET['type'])){
            $conditions['type'] = $_GET['type'];
        } 
        if (isset($_GET['annee'])){
            $conditions['annee'] = $_GET['annee'];
        } 
        if (isset($_GET['bloc'])){
            $conditions['bloc'] = $_GET['bloc'];
        } 

        $fiches = $this->model->afficherFiches($conditions);

        echo $this->twig->render('accueil.twig.html', [
            'fiches' => $fiches
        ]);
    }
}