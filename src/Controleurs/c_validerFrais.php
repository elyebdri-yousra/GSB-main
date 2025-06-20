<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$visiteurs = $pdo->getTousLesVisteurCloture(); // Tous les visiteurs avec leurs mois
$dates = $pdo->getTousLesMois();
switch ($action) {
    case 'choisirClientDate':
        require PATH_VIEWS_COMPTABLE . 'v_choixClientDate.php';
        break;
    case 'voirClient':
        $_SESSION['idVisiteur'] = filter_input(INPUT_POST, 'visiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $_SESSION['mois'] = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Ici grave à mon $post je récupère les éléments associés à mon visiteur en tant que comptable
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $nombresJustificatifs = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $_SESSION['mois']);
        if ($lesFraisForfait == null && $lesFraisHorsForfait == null) {
            Utilitaires::ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
            require PATH_VIEWS_COMPTABLE . 'v_choixClientDate.php';
            include PATH_VIEWS . 'v_erreurs.php';
        } else {
            require PATH_VIEWS_COMPTABLE . 'v_choixClientDate.php';
            require PATH_VIEWS_COMPTABLE . 'v_afficheFraisForfait.php';
            require PATH_VIEWS_COMPTABLE . 'v_afficheFraisHorsForfait.php';
        }
        break;
    case 'validerMajFraisCorrection':
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (Utilitaires::lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($_SESSION['idVisiteur'], $_SESSION['mois'], $lesFrais);
            Utilitaires::ajouterSuccess('Modification faite');
        } else {
            Utilitaires::ajouterErreur('Les valeurs des frais doivent être numériques');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $nombresJustificatifs = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $_SESSION['mois']);
        include PATH_VIEWS . 'v_success.php';
        require PATH_VIEWS_COMPTABLE . 'v_choixClientDate.php';
        require PATH_VIEWS_COMPTABLE . 'v_afficheFraisForfait.php';
        require PATH_VIEWS_COMPTABLE . 'v_afficheFraisHorsForfait.php';
        break;
    case 'validerMajHorsCorrection':
        $isDelete = filter_input(INPUT_POST, 'idhorsfrais', FILTER_SANITIZE_SPECIAL_CHARS);
        $isReported = filter_input(INPUT_POST, 'reportelement', FILTER_SANITIZE_SPECIAL_CHARS);
        $frais = Utilitaires::formatedArray(filter_input_array(INPUT_POST, $_POST));
        if ($isDelete != null) {
            $pdo->refuseFraisHorsForfait($frais['id'], $frais['libelle']);
            Utilitaires::ajouterSuccess('Frais refusé');
        } elseif ($isReported != null) {
            $date = Utilitaires::getCurrentTimePlusOneMonth();
            $mois = Utilitaires::getMois($date);
            if ($pdo->getLesInfosFicheFrais($_SESSION['idVisiteur'], $mois) == false) {
                $pdo->creeNouvellesLignesFrais($_SESSION['idVisiteur'], $mois);
            }                 $pdo->reporteFraisHorsForfait($frais['id'], $_SESSION['idVisiteur'], $mois);

            Utilitaires::ajouterSuccess('Frais reporté');
        } elseif ($isDelete == null && $isReported == null) {
            $pdo->majFraisHorsForfait($_SESSION['idVisiteur'], $_SESSION['mois'],$frais);
            Utilitaires::ajouterSuccess('Modification faite');

        }
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteur'], $_SESSION['mois']);
        $nombresJustificatifs = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $_SESSION['mois']);
        include PATH_VIEWS . 'v_success.php';
        require PATH_VIEWS_COMPTABLE . 'v_choixClientDate.php';
        require PATH_VIEWS_COMPTABLE . 'v_afficheFraisForfait.php';
        require PATH_VIEWS_COMPTABLE . 'v_afficheFraisHorsForfait.php';
        break;

    case 'validationFinale':

        $pdo->majEtatFicheFrais($_SESSION['idVisiteur'], $_SESSION['mois'], 'VA');
        Utilitaires::ajouterSuccess('Validation faite !');
        include PATH_VIEWS . 'v_success.php';
        break; // mettre JS pour avertir.
}
