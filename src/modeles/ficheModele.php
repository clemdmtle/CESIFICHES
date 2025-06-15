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
        ON b.id_bloc = f.id_bloc
        INNER JOIN Utilisateur u
        ON u.id_utilisateur = f.id_utilisateur
        WHERE 1=1";

        $params = [];

        if (!empty($conditions["type"])) {
            $placeholders = [];
            foreach ($conditions["type"] as $i => $val) {
                $ph = ":type$i";
                $placeholders[] = $ph;
                $params[$ph] = $val;
            }
            $sql .= " AND f.type IN (" . implode(',', $placeholders) . ")";
        }

        if (!empty($conditions["annee"])) {
            $placeholders = [];
            foreach ($conditions["annee"] as $index => $val) {
                $ph = ":annee$index";
                $placeholders[] = $ph;
                $params[$ph] = $val;
            }
            $sql .= " AND a.annee IN (" . implode(',', $placeholders) . ")";
        }

        if (!empty($conditions["bloc"])) {
            $placeholders = [];
            foreach ($conditions["bloc"] as $i => $val) {
                $ph = ":bloc$i";
                $placeholders[] = $ph;
                $params[$ph] = $val;
            }
            $sql .= " AND b.bloc IN (" . implode(',', $placeholders) . ")";
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

        if (!empty($conditions["id_fiche"])){
            $sql .= " AND f.id_fiche = :id_fiche";
            $params[':id_fiche'] = $conditions["id_fiche"];
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

    public function afficherFiltres($colonne, $annee = null){
        if ($colonne == "a.annee") {
        $sql = "SELECT DISTINCT annee FROM Annee ORDER BY annee";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        $sql = "SELECT DISTINCT $colonne FROM Fiche f
        INNER JOIN Annee a
        ON f.id_annee = a.id_annee
        INNER JOIN Bloc b
        ON f.id_bloc = b.id_bloc";

        $params = [];
        // if ($annee){
        //     $queryIdAnnee = "SELECT DISTINCT id_annee FROM Annee WHERE annee = :annee";
        //     $stmtIdAnnee = $this->pdo->prepare($queryIdAnnee);
        //     $stmtIdAnnee->execute([':annee' => $annee]);
        //     $idAnnee = $stmtIdAnnee->fetchColumn();

        //     $sql .= " WHERE b.id_annee = :idAnnee";
        //     $params[':idAnnee'] = $idAnnee;
        // } 
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modifierFiche($data){
        $queryAnnee = "SELECT id_annee FROM Annee WHERE annee = :annee LIMIT 1";
        $stmtAnnee = $this->pdo->prepare($queryAnnee);
        $stmtAnnee->execute([':annee' => $data['annee']]);
        $id_annee = $stmtAnnee->fetchColumn();
        if ($id_annee == false){
            $id_annee = $this->creerAnnee($data['annee']);
        }

        $queryBloc = "SELECT id_bloc FROM Bloc WHERE bloc = :bloc LIMIT 1";
        $stmtBloc = $this->pdo->prepare($queryBloc);
        $stmtBloc->execute([':bloc' => $data['bloc']]);
        $id_bloc = $stmtBloc->fetchColumn();
        if ($id_bloc == false){
            $id_bloc = $this->creerBloc($data['bloc'], $id_annee);
        }

        $sqlFiche = "UPDATE Fiche 
                SET titre = :titre, type = :type, 
                    chemin_fichier1 = :chemin_fichier1, chemin_fichier2 = :chemin_fichier2, 
                    chemin_fichier3 = :chemin_fichier3, id_annee = :id_annee, id_bloc = :id_bloc
                WHERE id_fiche = :id_fiche";
    
        $stmtFiche = $this->pdo->prepare($sqlFiche);
        $stmtFiche->execute([
            'titre' => $data['titre'],
            'type' => $type,
            'chemin_fichier1' => $data['chemin_fichier1'],
            'chemin_fichier2' => $data['chemin_fichier2'],
            'chemin_fichier3' => $data['chemin_fichier3'],
            'id_fiche' => $data['id_fiche'],
            'id_annee' => $id_annee,
            'id_bloc' => $id_bloc
        ]);

        header('Location: ?uri=profil');
    }
}