<?php
namespace App\modeles;

use PDO;

/**
 * Interface utilisée comme base pour
 * la classe Database
 */

interface DatabaseInterface {
    public static function getInstance() : PDO;
}
?>
