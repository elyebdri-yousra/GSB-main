<?php

/**
 * Valider fiche de frais
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    EL YEBDRI Yousra <yousra.elyebdri@icloud.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

 use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


switch ($action) {
    case 'listeVisiteur':
        $visiteurs = $pdo->getListeVisiteur();
        $date = $pdo->getListeMois();
        $date = Utilitaires::transformDate($date);
        include PATH_VIEWS. 'comptable/v_listeVisiteur.php';
    break;
    
    }

 ?>