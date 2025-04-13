<?php
namespace App\modeles;

use PDO;
use PDOException;
use App\modeles\DatabaseInterface;

/**
 * Classe pour instancier la connexion à la base
 * de données en utilisant PDO et basée sur le
 * patron Singleton
 */

class Database implements DatabaseInterface {
    private static ?PDO $instance = null;

    // Empêche l'instanciation en dehors de la classe
    private function __construct() {}

    // Empêche le clonage de l'objet
    private function __clone() {}

    // Méthode statique pour créer une instance unique à la BDD
    public static function getInstance(): PDO {
        if (self::$instance === null) { // on utilise self:: car c'est un objet statique
            $config = require __DIR__ . '/../config/databaseConf.php'; // récupère la configuration de la BDD (host, dbname, ...)

            try {
                self::$instance = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                    $config['username'],
                    $config['password'],
                    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // mode de gestion des erreurs en Exception
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ] // récupération des résultats par défaut en tableau associatif
                );
            } catch (PDOException $e) { // renvoie un message d'erreur s'il y a une Exception
                die('Database connection error: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>
