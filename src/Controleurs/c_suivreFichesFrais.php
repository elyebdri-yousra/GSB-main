<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$visiteurs = $pdo->getVisiteurPourSuivi();

switch ($action) {
    case 'selectionnerUser':
        $curr_user = null;
        require PATH_VIEWS_COMPTABLE . 'v_choixClient.php';
        break;

    case 'voirFiches':
        $_SESSION['idVisiteur'] = filter_input(INPUT_POST, 'visiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $curr_user =  ($pdo->getVisiteurWithId($_SESSION['idVisiteur']));
        $fichedefrais = $pdo->getToutesFicheFraisFromUserId($_SESSION['idVisiteur']);
        if ($curr_user == false or $fichedefrais == false) {
            Utilitaires::ajouterErreur('Aucune fiche n\'existe pour cet utilisateur.');
            include PATH_VIEWS . 'v_erreurs.php';
        } else {
            require PATH_VIEWS_COMPTABLE . 'v_choixClient.php';
            require PATH_VIEWS_COMPTABLE . 'v_suivreFichesClient.php';
        }
        break;
    case 'voirInfoFiche':
        $curr_user =  ($pdo->getVisiteurWithId($_SESSION['idVisiteur']));
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mois = filter_input(INPUT_GET, 'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $frais = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $fraisHors = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $nombresJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $mois);
        if ($frais == false or $fraisHors == false) {
            Utilitaires::ajouterErreur('Aucune fiche n\'existe pour cet utilisateur et ce mois.');
            include PATH_VIEWS . 'v_erreurs.php';
        } else {
            require PATH_VIEWS_COMPTABLE . 'v_suivreFicheClient.php';
        }
        break;
    case 'rembourseFiche':
        $idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $task = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if($task == 'Valider'){
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');
            Utilitaires::ajouterSuccess('Fiche mise en paiment');
            include PATH_VIEWS . 'v_success.php';

        }elseif($task == 'Rembourser'){
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'RB');
            Utilitaires::ajouterSuccess('Fiche remboursée');
            include PATH_VIEWS . 'v_success.php';
        }else{
            Utilitaires::ajouterErreur('Erreur est survenue');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        break;
}
//$datecplt = 
