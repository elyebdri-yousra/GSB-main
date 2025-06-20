<?php

/**
 * Vue Liste des frais au forfait
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
 * @link      https://getbootstrap.com/docs/3.3/ Documentation Bootstrap v3
 */

?>
<div class="row">
    <h2>Renseigner ma fiche de frais du mois
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <div>
        <form method="post" action="index.php?uc=gererFrais&action=updateVehicule">
            <h3>Eléments forfaitisés</h3>
            <label for="fraiskm">Choisir véhicule:</label>
            <select name="fraiskm" id="fraiskm">
                <?php if ($vehicule_user == null) { ?>
                    <option id="value_km" value="">Choisir véhicule</option>
                <?php }else{ ?>
                    <option id="value_km" value="<?php echo $vehicule_user['id'] ?>"><?php echo $vehicule_user['libelle'] ?></option>
                <?php } ?>
                <?php foreach ($vehicule as $vehicule) { ?>
                    <?php if ($vehicule_user['id'] != $vehicule['id']) { ?>
                        <option id="value_km" value="<?php echo $vehicule['id'] ?>"><?php echo $vehicule['libelle'] ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <input type="submit" value="Ok">
        </form>
    </div>
    <div class="col-md-4">
        <form method="post" action="index.php?uc=gererFrais&action=validerMajFraisForfait" role="form">
            <fieldset>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" name="lesFrais[<?php echo $idFrais ?>]" size="10" maxlength="5" value="<?php echo $quantite ?>" class="form-control">
                    </div>
                <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>