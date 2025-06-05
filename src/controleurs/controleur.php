<?php
namespace App\controleurs;

/**
 * Classe abstraite qui sert de base
 * aux autres classes controlleurs.
 */

abstract class Controleur {
    protected $model = null;
    protected $twig = null;
}
?>
