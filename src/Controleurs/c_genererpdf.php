<?php

use Modeles\PDF;
use Outils\Utilitaires;


$idVisiteur = $_SESSION['idVisiteur'];
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
switch ($action) {
    case 'etatFrais':
        if ($type == 'VA') {
            ob_start();
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->create();
            ob_end_flush(); // Evite desac mise en tampon
            ob_end_clean();
            $pdf->Output();

        } elseif ($type == 'RB') {
        } else {
            Utilitaires::ajouterErreur('Une erreur est survenue, veuillez recommencer');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        break;
}



