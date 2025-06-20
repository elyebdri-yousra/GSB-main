<?php

/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

namespace Outils;

use DateTime;

abstract class Utilitaires
{
    /**
     * Teste si un quelconque visiteur est connecté
     *
     * @return vrai ou faux
     */
    public static function estConnecte(): bool
    {
        return isset($_SESSION['idVisiteur']);
    }

    /**
     * Enregistre dans une variable session les infos d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $nom        Nom du visiteur
     * @param String $prenom     Prénom du visiteur
     * @param String $role       Id du role du visiteur
     *
     * @return null
     */
    public static function connecter($idVisiteur, $nom, $prenom,$role): void
    {
        $_SESSION['idVisiteur'] = $idVisiteur;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['role'] = $role;
    }

    /**
     * Détruit la session active
     *
     * @return null
     */
    public static function deconnecter(): void
    {
        session_destroy();
    }

    /**
     * Transforme une date au format français jj/mm/aaaa vers le format anglais
     * aaaa-mm-jj
     *
     * @param String $maDate au format  jj/mm/aaaa
     *
     * @return Date au format anglais aaaa-mm-jj
     */
    public static function dateFrancaisVersAnglais($maDate): string
    {
        @list($jour, $mois, $annee) = explode('/', $maDate);
        return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
    }

    /**
     * Transforme une date au format format anglais aaaa-mm-jj vers le format
     * français jj/mm/aaaa
     *
     * @param String $maDate au format  aaaa-mm-jj
     *
     * @return Date au format format français jj/mm/aaaa
     */
    public static function dateAnglaisVersFrancais($maDate): string
    {
        @list($annee, $mois, $jour) = explode('-', $maDate);
        $date = $jour . '/' . $mois . '/' . $annee;
        return $date;
    }

    /**
     * Retourne le mois au format aaaamm selon le jour dans le mois
     *
     * @param String $date au format  jj/mm/aaaa
     *
     * @return String Mois au format aaaamm
     */
    public static function getMois($date): string
    {
        @list($jour, $mois, $annee) = explode('/', $date);
        unset($jour);
        if (strlen($mois) == 1) {
            $mois = '0' . $mois;
        }
        return $annee . $mois;
    }

    public static function getCurrentTimePlusOneMonth(){
        $date = new DateTime('now');
        $date->modify('+1 months');
        $date = $date->format('d/m/Y');
        
        return $date;
    }


    /* gestion des erreurs */

    /**
     * Indique si une valeur est un entier positif ou nul
     *
     * @param Integer $valeur Valeur
     *
     * @return Boolean vrai ou faux
     */
    public static function estEntierPositif($valeur): bool
    {
        return preg_match('/[^0-9]/', $valeur) == 0;
    }

    /**
     * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
     *
     * @param Array $tabEntiers Un tableau d'entier
     *
     * @return Boolean vrai ou faux
     */
    public static function estTableauEntiers($tabEntiers): bool
    {
        $boolReturn = true;
        foreach ($tabEntiers as $unEntier) {
            if (!self::estEntierPositif($unEntier)) {
                $boolReturn = false;
            }
        }
        return $boolReturn;
    }

    /**
     * Vérifie si une date est inférieure d'un an à la date actuelle
     *
     * @param String $dateTestee Date à tester
     *
     * @return Boolean vrai ou faux
     */
    public static function estDateDepassee($dateTestee): bool
    {
        $dateActuelle = date('d/m/Y');
        @list($jour, $mois, $annee) = explode('/', $dateActuelle);
        $annee--;
        $anPasse = $annee . $mois . $jour;
        @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
        return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
    }

    /**
     * Vérifie la validité du format d'une date française jj/mm/aaaa
     *
     * @param String $date Date à tester
     *
     * @return Boolean vrai ou faux
     */
    public static function estDateValide($date): bool
    {
        $tabDate = explode('/', $date);
        $dateOK = true;
        if (count($tabDate) != 3) {
            $dateOK = false;
        } else {
            if (!self::estTableauEntiers($tabDate)) {
                $dateOK = false;
            } else {
                if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                    $dateOK = false;
                }
            }
        }
        return $dateOK;
    }

    /**
     * Vérifie que le tableau de frais ne contient que des valeurs numériques
     *
     * @param Array $lesFrais Tableau d'entier
     *
     * @return Boolean vrai ou faux
     */
    public static function lesQteFraisValides($lesFrais): bool
    {
        return self::estTableauEntiers($lesFrais);
    }

    /**
     * Vérifie la validité des trois arguments : la date, le libellé du frais
     * et le montant
     *
     * Des message d'erreurs sont ajoutés au tableau des erreurs
     *
     * @param String $dateFrais Date des frais
     * @param String $libelle   Libellé des frais
     * @param Float  $montant   Montant des frais
     *
     * @return null
     */
    public static function valideInfosFrais($dateFrais, $libelle, $montant): void
    {
        if ($dateFrais == '') {
            self::ajouterErreur('Le champ date ne doit pas être vide');
        } else {
            if (!self::estDatevalide($dateFrais)) {
                self::ajouterErreur('Date invalide');
            } else {
                if (self::estDateDepassee($dateFrais)) {
                    self::ajouterErreur("date d'enregistrement du frais dépassé, plus de 1 an");
                }
            }
        }
        if ($libelle == '') {
            self::ajouterErreur('Le champ description ne peut pas être vide');
        }
        if ($montant == '') {
            self::ajouterErreur('Le champ montant ne peut pas être vide');
        } elseif (!is_numeric($montant)) {
            self::ajouterErreur('Le champ montant doit être numérique');
        }
    }

    /**
     * Ajoute le libellé d'une erreur au tableau des erreurs
     *
     * @param String $msg Libellé de l'erreur
     *
     * @return null
     */
    public static function ajouterErreur($msg): void
    {
        if (!isset($_REQUEST['erreurs'])) {
            $_REQUEST['erreurs'] = array();
        }
        $_REQUEST['erreurs'][] = $msg;
    }

    /**
     * Ajoute le libellé d'un succès au tableau des succès
     *
     * @param String $msg Libellé de l'erreur
     *
     * @return null
     */
    public static function ajouterSuccess($msg): void
    {
        if (!isset($_REQUEST['success'])) {
            $_REQUEST['success'] = array();
        }
        $_REQUEST['success'][] = $msg;
    }

    /**
     * Retoune le nombre de lignes du tableau des erreurs
     *
     * @return Integer le nombre d'erreurs
     */
    public static function nbErreurs(): int
    {
        if (!isset($_REQUEST['erreurs'])) {
            return 0;
        } else {
            return count($_REQUEST['erreurs']);
        }
    }

    /**
     * Permet à partir d'array de fiche de frais de retourner un array trié.
     * @param array Liste de fiche de frais "id-type"
     * 
     * @return array
     */
    public static function formatedArray(array $tab){
        $final = [];
        $temp = null;
        for($i = 0; $i < count($tab); $i=$i+2){
            $temp = array_splice($tab, 0,3);
            $final[strtok(key($temp),'-')] = self::doSort($temp); 
        }
        return $final[strtok(key($temp),'-')];
    }

    private static function doSort(array $tab){
        $f = [];
        foreach($tab as $key => $value){
            $f[explode('-',$key,2)[1]] = $value;
        }
        $f['id'] = explode('-',$key,2)[0];
        return $f;
    }


    public static function checkHorsFrais(array $tab){
        if(count($tab) != null){
            foreach($tab as $t){
                if(count($t) != 4){
                    return true;
                }
                foreach($t as $a){
                    if($a == null){
                        return true;
                    }
                }
            }
        }return false;
    }
}
