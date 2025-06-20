<?php

/**
 * Index du projet GSB
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
use Modeles\PdoGsb;
use Outils\Utilitaires;

require '../vendor/autoload.php';
require '../config/define.php';

session_start();

$pdo = PdoGsb::getPdoGsb();
$estConnecte = Utilitaires::estConnecte();

require PATH_VIEWS . "v_entete.php";

$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($uc && !$estConnecte) {
    $uc = 'connexion';
} elseif (empty($uc)) {
    $uc = 'accueil';
}

switch ($uc) {
    case 'connexion':
        include PATH_CTRLS . 'c_connexion.php';
        break;
    case 'accueil':
        include PATH_CTRLS . 'c_accueil.php';
        break;
    case 'gererFrais':
        include PATH_CTRLS . 'c_gererFrais.php';
        break;
    case 'etatFrais':
        include PATH_CTRLS . 'c_etatFrais.php';
        break;
    case 'validerFrais':
        include PATH_CTRLS . 'c_validerFrais.php';
        break;
    case 'deconnexion':
        include PATH_CTRLS . 'c_deconnexion.php';
        break;
    case 'suivreFichesFrais':
        include PATH_CTRLS . 'c_suivreFichesFrais.php';
        break;
    // En gros tu copis colle ceux du dessus, donc si je veux créer un controller qui s'appel testpourtester je fais
    //case 'testpourtester': // Ca c'est le uc dans l'url -> doit être similaire au nom du controller
    //    include PATH_CTRLS . 'c_suivreFichesFrais.php'; // Penser à créer le fichier dans le dossier controller donc c_testpourteset.php
    //    break; // Obligatoire quand case 
    default:
        Utilitaires::ajouterErreur('Page non trouvée, veuillez vérifier votre lien...');
        include PATH_VIEWS . 'v_erreurs.php';
        break;
}
require PATH_VIEWS . 'v_pied.php';
