<?php

/**
 * Gestion de l'affichage des frais
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

use Modeles\PDF;
use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$idVisiteur = $_SESSION['idVisiteur'];
$idVehicule = $pdo->getFraisVehiculeUser($idVisiteur);
switch ($action) {
    case 'selectionnerMois':
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        // Afin de sélectionner par défaut le dernier mois dans la zone de liste
        // on demande toutes les clés, et on prend la première,
        // les mois étant triés décroissants
        $lesCles = array_keys($lesMois);
        if ($lesCles == null) {
            $moisASelectionner = '';
        } else {
            $moisASelectionner = $lesCles[0];
        }
        include PATH_VIEWS . 'v_listeMois.php';
        break;
    case 'voirEtatFrais':
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $_SESSION['leMois'] = $leMois;
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $leMois;
        include PATH_VIEWS . 'v_listeMois.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $_SESSION['date'] = $numMois . '/' . $numAnnee;
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $idEtat = $lesInfosFicheFrais['idEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = Utilitaires::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        $_SESSION['date_valid'] = $dateModif;
        include PATH_VIEWS . 'v_etatFrais.php';
        break;
    case 'pdfGenerator':
        $user = $pdo->getNomFromId($idVisiteur);
        $name = $user['nom'] . ' ' . $user['prenom'];
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitValide($idVisiteur, $_SESSION['leMois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $_SESSION['leMois']);
        if ($type == 'VA') {
            ob_start();
            $pdf = new PDF(); // On passe dans le constructeur toutes les valeurs pour le PDF
            $pdf->sender(['matricule' => $idVisiteur, 'name' => $name, 'fraisforfait' => $lesFraisForfait, 'fraishorsforfait' => $lesFraisHorsForfait, 'mois' => $_SESSION['date'], 'idvehicule' => $idVehicule]);
            $pdf->AddPage();
            $pdf->createForfait();
            ob_end_flush(); // Evite desac mise en tampon
            ob_end_clean();
            $pdf->Output();
        } elseif ($type == 'RB') {
            ob_start();
            $pdf = new PDF(); // On passe dans le constructeur toutes les valeurs pour le PDF
            $pdf->sender(['matricule' => $idVisiteur, 'name' => $name, 'fraisforfait' => $lesFraisForfait, 'fraishorsforfait' => $lesFraisHorsForfait, 'mois' => $_SESSION['date'],'date_valid'=>$_SESSION['date_valid'], 'idvehicule' => $idVehicule]);
            $pdf->AddPage();
            $pdf->createHorsForfait();
            ob_end_flush(); // Evite desac mise en tampon
            ob_end_clean();
            $pdf->Output();
        } else {
            Utilitaires::ajouterErreur('Une erreur est survenue, veuillez recommencer');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        break;
}
