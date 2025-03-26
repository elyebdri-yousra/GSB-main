<?php

/**
 * Vue Liste des frais hors forfait
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
<hr>
<div class="row">
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class="montant">Montant</th>
                    <th class="action">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id']; ?>
                    <tr>
                        <form method="post"
                            action="index.php?uc=validerFicheDesFrais&action=validerMajFicheFrais"
                            role="form">
                            <input type="hidden" id="ligneFraisHorsForfait"
                                    name="id"
                                    value="<?php echo $id ?>"
                                    class="form-control">
                            <td> <input type="text" id="ligneFraisHorsForfait"
                                    name="date"
                                    value="<?php echo $date ?>"
                                    class="form-control"></td>
                            <td> <input type="text" id="ligneFraisHorsForfait"
                                    name="libelle"
                                    value="<?php echo $libelle ?>"
                                    class="form-control"></td>
                            <td> <input type="text" id="ligneFraisHorsForfait"
                                    name="montant"
                                    value="<?php echo $montant ?>"
                                    class="form-control"></td>
                            <td>
                                <button class="btn btn-success" type="submit">Corriger</button>
                                <button class="btn btn-danger" type="reset">Supprimer</button>
                            </td>
                        </form>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="column">
    <p>nb:  <?php echo $nbjustificatif ?></p>

</div>