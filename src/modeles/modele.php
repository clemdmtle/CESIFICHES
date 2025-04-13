<?php
namespace App\modeles;

use PDO;
use App\models\Database;

/**
 * Classe abstraite qui sert de base aux autres
 * classes modèles.
 * Implémente les requêtes préparées génériques
 * du CRUD.
 */

abstract class Modele {
    protected PDO $pdo;
    protected string $table;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // CREATE : Insérer une ligne dans une table
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    // READ : Récupérer des lignes avec des filtres optionnels
    public function select($table, $conditions = []) {
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($conditions)));
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE : Mettre à jour une ligne
    public function update($table, $data, $conditions) {
        $setClause = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = :cond_$key", array_keys($conditions)));
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        $stmt = $this->pdo->prepare($sql);
        foreach ($conditions as $key => $value) {
            $data["cond_$key"] = $value;
        }
        return $stmt->execute($data);
    }

    // DELETE : Supprimer une ligne
    public function delete($table, $conditions) {
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($conditions)));
        $sql = "DELETE FROM $table WHERE $whereClause";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($conditions);
    }
}
?>