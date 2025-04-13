<?php
namespace App\controlleurs;

/**
 * Classe abstraite qui sert de base
 * aux autres classes controlleurs.
 */

abstract class Controlleur {
    protected $model = null;
    protected $twig = null;
}
?>
