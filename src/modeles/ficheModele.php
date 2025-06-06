<?php
namespace App\modeles;

use PDO;

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}