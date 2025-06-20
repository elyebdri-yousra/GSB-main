<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $connexion de type PDO
 * $instance qui contiendra l'unique instance de la classe
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

namespace Modeles;

use Exception;
use PDO;
use Outils\Utilitaires;

require '../config/bdd.php';

class PdoGsb
{

    protected $connexion;
    private static $instance = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        $this->connexion = new PDO(DB_DSN, DB_USER, DB_PWD);
        $this->connexion->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        $this->connexion = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb(): PdoGsb
    {
        if (self::$instance == null) {
            self::$instance = new PdoGsb();
        }
        return self::$instance;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login)
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom, visiteur.nom_role AS role '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        //$requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne le frais du véhicule du visiteur
     *
     * @param String $idVisiteur
     * @return void
     */
    public function getFraisVehiculeUser(string $idVisiteur){
        $requetePrepare = $this->connexion->prepare(
            'SELECT prix,libelle,vehicule.id from vehicule inner join visiteur on visiteur.id_vehicule = vehicule.id  where visiteur.id = :idVisiteur '
        );
        $requetePrepare->bindParam(':idVisiteur',$idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }


    /**
     * Retourne la liste des véhicule
     *
     * @return void
     */
    public function getListeVehicule(){
        $req = $this->connexion->prepare('SELECT * FROM vehicule');
        $req->execute();
        return $req->fetchAll();
    }


    /**
     * Met à jour le véhicule de l'utilisateur;
     *
     * @param string $idVisiteur
     * @param integer $idVehicule
     * @return void
     */
    public function updateVehiculeUser(string $idVisiteur, int $idVehicule){
        $req = $this->connexion->prepare('UPDATE visiteur set id_vehicule = :idVehicule WHERE id = :idVisiteur');
        $req->bindParam(':idVehicule', $idVehicule, PDO::PARAM_INT);
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Retourne le mot de passe de l'utilisateur
     *
     * @param [type] $login
     * @return void
     */
    public function getMdpVisiteur($login)
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT mdp '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch(PDO::FETCH_OBJ)->mdp;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utilitaires::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Cette fonction permet de retourner les frais hors forfait qui ne sont pas refusé
     *
     * @param Int $idVisiteur
     * @param String $mois
     * @return array
     */
    public function getLesFraisHorsForfaitValide($idVisiteur, $mois): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois AND libelle NOT like "REFUSE-%"'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utilitaires::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois): int
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        if ($laLigne == false) {
            return 0;
        }
        return $laLigne['nb'];
    }

    /**
     * Retourne le nom et prénom d'un visiteur grâce à son ID
     *
     * @param Int $idVisiteur
     * @return void
     */
    public function getNomFromId($idVisiteur)
    {
        $requete = $this->connexion->prepare('SELECT nom as nom,prenom as prenom FROM visiteur where id = :id');
        $requete->bindParam(':id', $idVisiteur, PDO::PARAM_STR);
        $requete->execute();
        $value = $requete->fetch();
        return $value;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fraisforfait.id as idfrais, '
                . 'fraisforfait.libelle as libelle, '
                . 'lignefraisforfait.quantite as quantite '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait '
                . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais(): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais): void
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = $this->connexion->prepare(
                'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Permet de mettre à jours les frais HorsForfait
     *
     * @param Int $idVisiteur
     * @param String $mois
     * @param Array $frais
     * @return void
     */
    public function majFraisHorsForfait($idVisiteur, $mois, $frais): void
    {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.libelle = :libelle, '
                . 'lignefraishorsforfait.date = :uneDate, '
                . 'lignefraishorsforfait.montant = :montant '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois '
                . 'AND lignefraishorsforfait.id = :id'

        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $frais['id'], PDO::PARAM_INT);
        $requetePrepare->bindParam(':libelle', $frais['libelle'], PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDate', $frais['date'], PDO::PARAM_STR);
        $requetePrepare->bindParam(':montant', $frais['montant'], PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs): void
    {

        $hello = 10 == null ? 0 : 10;

        $requetePrepare = $this->connexion->prepare(
            'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois): bool
    {
        $boolReturn = false;
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur): string
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        if ($dernierMois == null) {
            $dernierMois = Utilitaires::getMois(date('d/m/Y'));
        }
        return $dernierMois;
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant): void
    {
        $dateFr = Utilitaires::dateFrancaisVersAnglais($date);
        $requetePrepare = $this->connexion->prepare(
            'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais): void
    {
        $requetePrepare = $this->connexion->prepare(
            'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Permet de changer le libelle d'un frais hors forfait pour le refuser
     *
     * @param Int $idFrais
     * @param String $libelle
     * @return void
     */
    public function refuseFraisHorsForfait($idFrais, $libelle): void
    {
        if (strtok($libelle, '-') != 'REFUSE') {
            $libelle = 'REFUSE-' . $libelle;
        }
        $requetePrepare = $this->connexion->prepare('UPDATE lignefraishorsforfait set libelle= :libelle WHERE id = :id');
        $requetePrepare->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $idFrais, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Retourne un frais hors forfait grâce à son ID
     *
     * @param Int $idFrais
     * @param String $idVisiteur
     * @return array
     */
    public function getFraisHorsForfaitId($idFrais, $idVisiteur) : array
    {
        $prepare = $this->connexion->prepare('SELECT libelle,date,montant FROM lignefraishorsforfait WHERE id = :idFrais AND idvisiteur = :idVisiteur');
        $prepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
        $prepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $prepare->execute();
        $laLigne = $prepare->fetch();
        return $laLigne;
    }


    /**
     * Permet de reporter un frais hors forfait au moins suivant
     *
     * @param Int $idFrais
     * @param String $idVisiteur
     * @param String $mois
     * @return void
     */
    public function reporteFraisHorsForfait($idFrais, $idVisiteur, $mois): void
    {
        $laLigne = $this->getFraisHorsForfaitId($idFrais, $idVisiteur);
        $date = Utilitaires::dateAnglaisVersFrancais($laLigne['date']);
        $this->creeNouveauFraisHorsForfait($idVisiteur, $mois, $laLigne['libelle'], $date, $laLigne['montant']);
        $this->supprimerFraisHorsForfait($idFrais);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif,'
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        if (isset($idVisiteur['idVisiteur'])) {
            $idVisiteur = $idVisiteur['idVisiteur'];
        }
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat): void
    {
        if ($etat == 'VA') {
            $final = $this->getMontantFinal($idVisiteur, $mois);
            $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                    . 'SET idetat = :unEtat, datemodif = now(), montantvalide = :montant '
                    . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                    . 'AND fichefrais.mois = :unMois'
            );
            $requetePrepare->bindParam(':montant', $final, PDO::PARAM_STR);
        } else {
            $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                    . 'SET idetat = :unEtat, datemodif = now() '
                    . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                    . 'AND fichefrais.mois = :unMois'
            );
        }
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Permet de retourner le montant final, d'une fiche de frais
     *
     * @param Int $idVisiteur
     * @param String $mois
     * @return Int
     */
    public function getMontantFinal($idVisiteur, $mois) : int
    {
        $req = $this->connexion->prepare("SELECT * FROM fraisforfait");
        $req->execute();
        $montants = $req->fetchAll();
        $vehicule = $this->getFraisVehiculeUser($idVisiteur);
        $frais_forfait = $this->getLesFraisForfait($idVisiteur, $mois);
        $frais_hors_forfait = $this->getLesFraisHorsForfaitValide($idVisiteur, $mois);
        $final = ($montants[0]['montant'] * $frais_forfait[0]['quantite']) + ($frais_forfait[1]['quantite'] * $vehicule['prix']) + ($montants[2]['montant'] * $frais_forfait[2]['quantite']) + ($montants[3]['montant'] * $frais_forfait[3]['quantite']) + $frais_forfait[1]['quantite'];
        foreach ($frais_hors_forfait as $frais) {
            $final += $frais['montant'];
        }
        return $final;
    }

    /**
     * Retourne la liste de tous les visiteurs ayant une fiche cloturée
     *
     * @return array
     */
    public function getTousLesVisteurCloture()
    {
        $listeVisiteurs = array();
        $requetePrepare = $this->connexion->prepare(
            "SELECT DISTINCT nom,prenom,id "
                . "FROM fichefrais "
                . "INNER JOIN visiteur "
                . "ON visiteur.id=fichefrais.idvisiteur "
                . "WHERE idetat= 'CL' AND nom_role != 'Comptable'"
        );
        $requetePrepare->execute();
        while ($ligne = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            $nomcplt = $ligne['nom'] . " " . $ligne['prenom'];
            $listeVisiteurs[] = array(
                'visiteur' => $nomcplt,
                'id' => $ligne['id'],
            );
        }

        return $listeVisiteurs;
    }

    /**
     * Retourne tous les clients, qui ont une fiche 
     *
     * @return void
     */
    public function getVisiteurPourSuivi()
    {
        $listeVisiteurs = array();
        $requetePrepare = $this->connexion->prepare(
            "SELECT DISTINCT nom,prenom,id "
                . "FROM fichefrais "
                . "INNER JOIN visiteur "
                . "ON visiteur.id=fichefrais.idvisiteur "
                . "WHERE idetat= 'CL' OR idetat='VA' OR idetat='RB'  AND nom_role != 'Comptable' order by nom"
        );
        $requetePrepare->execute();
        while ($ligne = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            $nomcplt = $ligne['nom'] . " " . $ligne['prenom'];
            $listeVisiteurs[] = array(
                'visiteur' => $nomcplt,
                'id' => $ligne['id']
            );
        }
        return $listeVisiteurs;
    }


    /**
     * Retourne toutes les dates, pour les visiteurs qui on des fiches cloturés
     *
     * @return array
     */
    public function getTousLesMois(): array
    {
        $dates = array();
        $requetePrepare = $this->connexion->prepare(
            "SELECT DISTINCT mois "
                . "FROM fichefrais WHERE idetat='CL'"
        );
        $requetePrepare->execute();
        while ($ligne = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            //Ici comme je modifi déja les dates une fois pour le front, je stocke la valeur de base dans un autre tableau pour mon traitement 
            // A CHANGER -> PAS FOU 
            $datecplt = substr($ligne['mois'], 4, 2) . "/" . substr($ligne['mois'], 0, 4);
            $dates[] = array(
                'date_front' => $datecplt,
                'date_back' => $ligne['mois'],
            );
        }
        return $dates;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois): void
    {

        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = $this->connexion->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
                . 'montantvalide,datemodif,idetat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = $this->connexion->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idfraisforfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Retourne toutes les fiches de frais d'un visiteur en fonction d'un ID
     *
     * @param Int $idVisiteur
     * @return Array
     */
    public function getToutesFicheFraisFromUserId($idVisiteur)
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT DISTINCT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif,'
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'fichefrais.mois as moisFiche, '
                . 'fichefrais.idvisiteur as idVisiteur, '
                . 'fichefrais.idetat as idEtat '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur order by mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetchAll();
        return $laLigne;
    }

    /**
     * Retourne un visiteur grâce à son ID
     *
     * @param Int $idVisiteur
     * @return void
     */
    public function getVisiteurWithId($idVisiteur)
    {
        $prepare = $this->connexion->prepare('SELECT * FROM visiteur where id = :idVisiteur');
        $prepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $prepare->execute();
        return $prepare->fetch();
    }
}
