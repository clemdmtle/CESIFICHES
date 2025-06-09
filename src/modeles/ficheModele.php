<?php
namespace App\modeles;

use PDO;
use PhpParser\Node\Stmt;

class FicheModele extends Modele {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function afficherFiches($conditions){
        $sql = "SELECT * FROM Fiche f
        INNER JOIN Annee a
        ON a.id_annee = f.id_annee
        INNER JOIN Bloc b 
        ON b.id_annee = a.id_annee
        INNER JOIN Utilisateur u
        ON u.id_utilisateur = f.id_utilisateur
        WHERE 1=1";

        $params = [];

        if (!empty($conditions["type"]) && in_array($conditions["type"], ["CCTL", "EI", "Fiche"])) {
            $sql .= " AND f.type = :type";
            $params[':type'] = $conditions["type"];
        }

        if (!empty($conditions["annee"])) {
            $sql .= " AND a.annee = :annee";
            $params[':annee'] = $conditions["annee"];
        }

        if (!empty($conditions["bloc"])) {
            $sql .= " AND b.bloc = :bloc";
            $params[':bloc'] = $conditions["bloc"];
        }

        if (!empty($conditions["recherche"])) {
            $sql .= " AND (
                f.titre LIKE :motCle
                OR f.type LIKE :motCle
                OR u.nom LIKE :motCle
                OR u.prenom LIKE :motCle
                OR a.annee LIKE :motCle
                OR b.bloc LIKE :motCle
            )";
            $params[':motCle'] = "%" . $conditions["recherche"] . "%";
        }

        if (!empty($conditions["utilisateur"])){
            $sql .= " AND u.id_utilisateur = :utilisateur";
            $params[':utilisateur'] = $conditions["utilisateur"];
        }

        $sql .= " ORDER BY date_ajout DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creerAnnee($annee){
        $sql= "INSERT INTO Annee (annee) VALUES (:annee)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':annee' => $annee
        ]);

        $queryRetour = "SELECT id_annee FROM Annee WHERE annee = :annee";
        $stmtRetour = $this->pdo->prepare($queryRetour);
        $stmtRetour->execute([':annee' => $annee]);
        return $stmtRetour->fetchColumn();
    }

    public function creerBloc($bloc, $id_annee){
        $sql= "INSERT INTO Bloc (bloc, id_annee) VALUES (:bloc, :id_annee)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':bloc' => $bloc,
            ':id_annee' => $id_annee
        ]);

        $queryRetour = "SELECT id_bloc FROM Bloc WHERE bloc = :bloc";
        $stmtRetour = $this->pdo->prepare($queryRetour);
        $stmtRetour->execute([':bloc' => $bloc]);
        return $stmtRetour->fetchColumn();
    }

    public function supprimerFiche($idFiche){
        $sql1 = "DELETE FROM contenir WHERE id_fiche = :idFiche";
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([':idFiche' => $idFiche]);

        $sql2 = "DELETE FROM fiche WHERE id_fiche = :idFiche";
        $params[':idFiche'] = $idFiche;
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute($params);
    }

    public function creerFiche($data){
        $titre = $data['titre'];
        $type = $data['type'];
        $chemin_fichier1 = $data['chemin_fichier1'];
        $chemin_fichier2 = $data['chemin_fichier2'];
        $chemin_fichier3 = $data['chemin_fichier3'];
        $date_ajout = $data['date_ajout'];
        $bloc = $data['bloc'];
        $annee = $data['annee'];
        $id_utilisateur = $data['id_utilisateur'];

        $queryAnnee = "SELECT id_annee FROM Annee WHERE annee = :annee LIMIT 1";
        $stmtAnnee = $this->pdo->prepare($queryAnnee);
        $stmtAnnee->execute([':annee' => $annee]);
        $id_annee = $stmtAnnee->fetchColumn();
        if ($id_annee == false){
            $id_annee = $this->creerAnnee($annee);
        }

        $queryBloc = "SELECT id_bloc FROM Bloc WHERE bloc = :bloc LIMIT 1";
        $stmtBloc = $this->pdo->prepare($queryBloc);
        $stmtBloc->execute([':bloc' => $bloc]);
        $id_bloc = $stmtBloc->fetchColumn();
        if ($id_bloc == false){
            $id_bloc = $this->creerBloc($bloc, $id_annee);
        }

        $sql = "INSERT INTO Fiche (titre, type, chemin_fichier1, chemin_fichier2, chemin_fichier3, date_ajout, id_bloc, id_annee, id_utilisateur) 
        VALUES (:titre, :type, :chemin_fichier1, :chemin_fichier2, :chemin_fichier3, :date_ajout, :id_bloc, :id_annee, :id_utilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $titre,
            ':type' => $type,
            ':chemin_fichier1' => $chemin_fichier1,
            ':chemin_fichier2' => $chemin_fichier2,
            ':chemin_fichier3' => $chemin_fichier3,
            ':date_ajout' => $date_ajout,
            ':id_bloc' => $id_bloc,
            ':id_annee' => $id_annee,
            ':id_utilisateur' => $id_utilisateur
        ]);

        header('Location: ?uri=/');
    }
}