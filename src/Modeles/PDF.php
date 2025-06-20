<?php

/**
 * Classe de création de fichiers PDF
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    Erades Baptiste
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

namespace Modeles;

use FPDF;

require(PDF_TOOL . 'fpdf.php');

class PDF extends FPDF
{

    public $values;

    private $hotel = 80;
    private $restau = 25.00;

    private $qtehotel;
    private $qterestau;
    private $qtekm;
    private $vehicule;

    private $tthotel;
    private $ttrestau;
    private $ttkm;

    private $tthf = 0;

    /**
     * Permet de récupérer un array, pour afficher les valeurs dans le PDF
     *
     * @param array $params
     * @return void
     */
    function sender(array $params)
    {
        $this->values = $params;
        $this->setVal();
    }

    /**
     * Permet de faire les calculs pour les frais, mais aussi d'ajouter les valeurs du array aux attributs privé de la classe
     *
     * @return void
     */
    function setVal()
    {
        $this->qtehotel = $this->values['fraisforfait'][2]['quantite'];
        $this->qterestau = $this->values['fraisforfait'][3]['quantite'];
        $this->qtekm = $this->values['fraisforfait'][1]['quantite'];

        $this->vehicule = $this->values['idvehicule'];
        $this->tthotel = $this->qtehotel * $this->hotel;
        $this->ttrestau = $this->qterestau * $this->restau;
        $this->ttkm = $this->qtekm * $this->vehicule['prix'];
    }

    // En-tête
    /**
     * Fonction propre au PDF, pour générer un header
     *
     * @return void
     */
    function Header()
    {
        // Logo
        $this->Image('./images/logo.jpg', 90, 6, 30);
        // Police Arial gras 15
        $this->Ln(20);
    }

    /**
     * Fonction propre au PDF, permet de générer un titre
     *
     * @return void
     */
    function Titre()
    {
        // Arial 12
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 180);
        $this->Cell(0, 10, "ETAT DE FRAIS ENGAGES : ", 'LRTB', 0, 'C');
        $this->Ln(8);
    }

    /**
     * Fonction propre au PDF, permet de générer un sous-titre
     *
     * @return void
     */
    function SousTitre()
    {
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(0, 0, 180);
        $this->Cell(0, 10, 'A retourner accompagné des justificatifs au plus tard le 10 du mois qui suit l’engagement des frais ', 'LRB', 0, 'C');
        $this->Ln(10);
    }

    /**
     * Fonction propre au PDF, permet de générer les informations du visiteur
     *
     * @return void
     */
    function cadreVisiteur()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 10, 'Visiteur', 'L', 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(25, 10, 'Matricule', '', 0, 'C');
        $this->SetFont('', 'U');
        $this->Cell(60, 10, $this->values['matricule'], '', 0, '');
        $this->Cell(0, 15, '', 'R', 0, 'L');
        $this->Ln(10);
        $this->SetFont('', '');
        $this->Cell(31, 10, '', 'L', 0, 'C');
        $this->Cell(19, 10, 'Nom', 0, 0, 'L');
        $this->SetFont('', 'U');
        $this->Cell(70, 10, $this->values['name'], '', 0, '');
        $this->Ln(1);
        $this->Cell(0, 10, '', 'LR', 0, '');
        $this->Ln(10);
        $this->SetTextColor(0, 0, 180);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 10, 'Mois', 'L', 0, 'C');
        $this->SetFont('', 'U', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(73, 10, $this->values['mois'], '', 0, 'C');
        $this->Cell(0, 10, '', 'R', 0, '');

        $this->Ln(1);
        $this->Cell(0, 10, '', 'LRB', 0, '');
        $this->Ln(10);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
    }

    // Pied de page
    /**
     * Fonction propre au PDF, permet de générer le tableau des frais forfaits du visiteur
     *
     * @return void
     */
    function cadreFrais()
    {
        //Header
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(0, 0, 180);

        $this->Cell(40, 8, 'Frais Forfaitaires', 'LRB', 0, 'C');
        $this->Cell(50, 8, 'Quantité', 'RB', 0, 'C');
        $this->Cell(50, 8, 'Montant Unitaire', 'RB', 0, 'C');
        $this->Cell(50, 8, 'Total', 'RB', 0, 'C');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(0, 0, 0);

        $this->Ln(8); //Ligne 1
        $this->Cell(40, 8, 'Nuitée', 'LRB', 0, 'C');
        $this->Cell(50, 8, $this->qtehotel, 'RB', 0, 'C');
        $this->Cell(50, 8, $this->hotel, 'RB', 0, 'C');
        $this->Cell(50, 8, $this->tthotel, 'RB', 0, 'C');
        $this->Ln(8); //Ligne 2
        $this->Cell(40, 8, 'Repas Midi', 'LRB', 0, 'C');
        $this->Cell(50, 8, $this->qterestau, 'RB', 0, 'C');
        $this->Cell(50, 8, $this->restau, 'RB', 0, 'C');
        $this->Cell(50, 8, $this->ttrestau, 'RB', 0, 'C');
        $this->Ln(8); //Ligne 2
        $this->Cell(40, 8, 'Kilométrage ', 'LRB', 0, 'C');
        $this->Cell(50, 8, $this->qtekm, 'RB', 0, 'C');
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(50, 8, $this->vehicule['prix'], 'RB', 0, 'C');
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 8, $this->ttkm, 'RB', 0, 'C');
        $this->Ln(8);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(0, 0, 180);
        $this->Cell(0, 8, 'Autres Frais', 'B', 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(8);
    }

    /**
     * Fonction propre au PDF, peremet de générer le cadre des frais hors forfait
     *
     * @return void
     */
    function cadreFraisHf()
    {
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(0, 0, 180);

        $this->Cell(40, 8, 'Date', 'LRB', 0, 'C');
        $this->Cell(100, 8, 'Libelle', 'RB', 0, 'C');
        $this->Cell(50, 8, 'Montant', 'RB', 0, 'C');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(8);
    }

    /**
     * Fonction propre au PDF, Footer du PDF non remboursée
     *
     * @return void
     */
    function doFraisHf()
    {
        if ($this->values['fraishorsforfait'] == null) {
            $this->Cell(0, 8, 'Aucun frais hors forfait', '', 0, 'C');
        } else {
            $fraishf = $this->values['fraishorsforfait'];
            foreach ($fraishf as $frais) {
                $this->Cell(40, 8, $frais['date'], 'LRB', 0, 'C');
                $this->Cell(100, 8, $frais['libelle'], 'RB', 0, 'C');
                $this->Cell(50, 8, $frais['montant'], 'RB', 0, 'C');
                $this->Ln(8);
            }
        }
        $this->Cell(0, 8, 'Signature', '', 0, 'R');
    }

    /**
     * Fonction propre au PDF, footer du PDF remboursé
     *
     * @return void
     */
    function doTotal()
    {
        if ($this->values['fraishorsforfait'] == null) {
            $this->Cell(0, 8, 'Aucun frais hors forfait', '', 0, 'C');
        } else {
            $fraishf = $this->values['fraishorsforfait'];
            foreach ($fraishf as $frais) {
                $this->Cell(40, 8, $frais['date'], 'LRB', 0, 'C');
                $this->Cell(100, 8, $frais['libelle'], 'RB', 0, 'C');
                $this->Cell(50, 8, $frais['montant'], 'RB', 0, 'C');
                $this->tthf += $frais['montant'];
                $this->Ln(8);
            }
        }


        $this->Cell(115, 8, '', '', 0, '');
        $this->Cell(25, 8, 'Total ' . $this->values['date_valid'], 'LRB', 0, 'R');
        $this->Cell(50, 8, $this->ttkm + $this->tthotel + $this->ttrestau + $this->tthf . '€', 'LRB', 0, 'R');
    }

    /**
     * Fonction propre au PDF, permet de générer le PDF pour les frais en paiement
     *
     * @return void
     */
    function createForfait()
    {
        $this->Titre();
        $this->SousTitre();
        $this->cadreVisiteur();
        $this->cadreFrais();
        $this->cadreFraisHf();
        $this->doFraisHf();
    }

    /**
     * Fonction propre au PDF, permet de générer le PDF pour les frais remboursés
     *
     * @return void
     */
    function createHorsForfait()
    {
        $this->Titre();
        $this->cadreVisiteur();
        $this->cadreFrais();
        $this->cadreFraisHf();
        $this->doTotal();
    }
}
