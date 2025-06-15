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
        $conditions = [
        'type' => isset($_GET['type']) ? (array) $_GET['type'] : [],
        'annee' => isset($_GET['promo']) ? (array) $_GET['promo'] : [],
        'bloc' => isset($_GET['bloc']) ? (array) $_GET['bloc'] : [],
        'recherche' => $_GET['recherche'] ?? '',
        'utilisateur' => $_GET['utilisateur'] ?? null
        ];

        $fiches = $this->model->afficherFiches($conditions);
        $annees = [];
        $blocs = [];

        $annees = $this->model->afficherFiltres("a.annee");
        $blocs = $this->model->afficherFiltres("b.bloc");
        $types = $this->model->afficherFiltres("f.type");
        //$blocs = isset($_GET['promo']) ? $this->model->afficherFiltres("b.bloc", $_GET['promo']) : [];


        echo $this->twig->render('accueil.twig.html', [
            'fiches' => $fiches,
            'annees' => $annees,
            'blocs' => $blocs,
            'types' => $types
        ]);
    }

    public function afficherFichesProfil(){
    //     $conditions = [];

    //     $idUtilisateur = 1;
    //     if (isset($idUtilisateur)){
    //         $conditions["utilisateur"] = $idUtilisateur; //en attendant de gérer les sessions
    //    // if (isset($_GET["id_utilisateur"])){
    //     //    $conditions['utilisateur'] = $_GET['utilisateur'];
    //     } else {
    //         $this->afficherFiches();
    //     }
    //     if (isset($_GET['recherche'])){
    //         $conditions['recherche'] = $_GET['recherche'];
    //     } 
    //     if (isset($_GET['type'])){
    //         $conditions['type'] = $_GET['type'];
    //     } 
    //     if (isset($_GET['annee'])){
    //         $conditions['annee'] = $_GET['annee'];
    //     } 
    //     if (isset($_GET['bloc'])){
    //         $conditions['bloc'] = $_GET['bloc'];
    //     } 

    //     $fichesProfil = $this->model->afficherFiches($conditions);

    //     echo $this->twig->render('profil.twig.html', [
    //         'fichesProfil' => $fichesProfil
    //     ]);

        ///////
        $conditions = [
        'type' => isset($_GET['type']) ? (array) $_GET['type'] : [],
        'annee' => isset($_GET['promo']) ? (array) $_GET['promo'] : [],
        'bloc' => isset($_GET['bloc']) ? (array) $_GET['bloc'] : [],
        'recherche' => $_GET['recherche'] ?? ''
        ];

        $idUtilisateur = 1;
        if (isset($idUtilisateur)){
            $conditions["utilisateur"] = $idUtilisateur; //en attendant de gérer les sessions
       // if (isset($_GET["id_utilisateur"])){
        //    $conditions['utilisateur'] = $_GET['utilisateur'];
        } else {
            $this->afficherFiches();
        }

        $fichesProfil = $this->model->afficherFiches($conditions);
        $annees = [];
        $blocs = [];

        $annees = $this->model->afficherFiltres("a.annee");
        $blocs = $this->model->afficherFiltres("b.bloc");
        $types = $this->model->afficherFiltres("f.type");
        //$blocs = isset($_GET['promo']) ? $this->model->afficherFiltres("b.bloc", $_GET['promo']) : [];


        echo $this->twig->render('profil.twig.html', [
            'fichesProfil' => $fichesProfil,
            'annees' => $annees,
            'blocs' => $blocs,
            'types' => $types
        ]);
    }

    public function supprimerFiche(){
        $idFiche = isset($_GET['id']) ? $_GET['id'] : null;
        if (!empty($idFiche)) {
            $this->model->supprimerFiche($idFiche);
        }
        header("Location: ?uri=profil");
        exit();
    }

    public function creerFiche(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [];

            $data['titre'] = $_POST['titre'] ? htmlspecialchars($_POST['titre']) : NULL;
            $data['type'] = $_POST['type'] ? htmlspecialchars($_POST['type']) : NULL;
            $data['annee'] = $_POST['promotion'] ? htmlspecialchars($_POST['promotion']) : NULL;
            $data['bloc'] = $_POST['bloc'] ? htmlspecialchars($_POST['bloc']) : NULL;
            $data['chemin_fichier1'] = "chemin/fichier"; //$data['
            // chemin_fichier1'] = $_POST['fichier1'] ? htmlspecialchars($_POST['fichier1']) : NULL;
            $data['chemin_fichier2'] = isset($_FILES['fichier2']) && $_FILES['fichier2']['error'] === UPLOAD_ERR_OK
            ? 'uploads/' . basename($_FILES['fichier2']['name'])
            : null;

            $data['chemin_fichier3'] = isset($_FILES['fichier3']) && $_FILES['fichier3']['error'] === UPLOAD_ERR_OK
            ? 'uploads/' . basename($_FILES['fichier3']['name'])
            : null;
            $data['date_ajout'] = date('Y-m-d H:i:s');
            $data['id_utilisateur'] = 1; //$data['id_utilisateur'] = $_SESSION['id_utilisateur'];
            $data['id_fiche'] = $_POST['id_fiche'] ? htmlspecialchars($_POST['id_fiche']) : NULL;

            if ($data['id_fiche'] == null){ //si la fiche n'existe pas on la crée
                $this->model->creerFiche($data);
            } else { //sinon on modif juste
                $this->model->modifierFiche($data);
            }
            
        }
    }

    public function recolterFicheAModifier(){
        $idFiche = $_GET['id'] ? $_GET['id'] : '';
        if (isset($idFiche)){
            $conditions = [];
            $conditions['id_fiche'] = $idFiche;
            $ficheAModifier = $this->model->afficherFiches($conditions);
            $ficheAModifier = $ficheAModifier[0] ?? null;
            if ($ficheAModifier && isset($ficheAModifier['type'])) {
                $ficheAModifier['type'] = strtoupper($ficheAModifier['type']);
            }
            echo $this->twig->render('creation-fiche.twig.html', [
            'ficheAModifier' => $ficheAModifier
        ]);
        }
    }
}