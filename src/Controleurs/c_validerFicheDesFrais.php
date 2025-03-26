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
        $date = Utilitaires::transformDate(date: $date);
        include PATH_VIEWS . 'comptable/v_listeVisiteur.php';
        break;
    case 'afficheFicheFrais':
        $visiteur = filter_input(INPUT_POST, 'visiteur', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $visiteurs = $pdo->getListeVisiteur();
        $date = $pdo->getListeMois();
        $date = Utilitaires::transformDate(date: $date);

        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $mois);
        $nbjustificatif = count($lesFraisHorsForfait);
        include PATH_VIEWS . 'comptable/v_listeVisiteur.php';
        require PATH_VIEWS . 'comptable/v_listeFraisForfait.php';
        require PATH_VIEWS . 'comptable/v_listeFraisHorsForfait.php';

        break;
    case 'validerMajFicheFrais':
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $corrigeLesFraisHorsForfait = $pdo->majLesFraisHorsForfait($id, $libelle, $montant, $date);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $mois);
        $nbjustificatif = count($lesFraisHorsForfait);



        
        include PATH_VIEWS . 'comptable/v_listeVisiteur.php';
        require PATH_VIEWS . 'comptable/v_listeFraisForfait.php';
        require PATH_VIEWS . 'comptable/v_listeFraisHorsForfait.php';
        break;
}
