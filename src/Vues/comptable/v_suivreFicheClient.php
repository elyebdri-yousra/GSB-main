<div class="row">
    <h2>Suivre la fiche de frais : <?php echo $curr_user['nom'] .' '. $curr_user['prenom'] ?></h2>
    <h3>Date: <?php  echo substr($mois, 4, 2) . "/" . substr($mois, 0, 4);  ?></h2>
    <h3>Eléments forfaitisés</h3>
    <fieldset>
        <?php
        foreach ($frais as $unFrais) {
            $idFrais = $unFrais['idfrais'];
            $libelle = htmlspecialchars($unFrais['libelle']);
            $quantite = $unFrais['quantite']; ?>
            <div class="form-group">
                <label for="idFrais"><?php echo $libelle ?></label>
                <input disabled type="text" id="idFrais" name="lesFrais[<?php echo $idFrais ?>]" size="10" maxlength="5" value="<?php echo $quantite ?>" class="form-control">
            </div>
        <?php
        }
        ?>

    </fieldset>
</div>
<div class="row">
<h3>Eléments hors forfaits</h3>

    <div class="panel user">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <table border="1" class="table border-warning table-bordered" style="margin-bottom: 0px !important;">
            <thead>
                <tr>
                    <td class="gras border-warning">Date</td>
                    <td class="gras border-warning">Libellé</td>
                    <td class="gras border-warning">Montant</td>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($fraisHors as $unFraisHorsForfait) {
                    $date = $unFraisHorsForfait[4];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id'];
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                ?>
                    <tr>
                        <td><input disabled type="text" value="<?php echo $date ?>" name=<?php echo $id . "-date" ?>></td>
                        <td><input disabled type="text" value="<?php echo $libelle ?>" name="<?php echo $id . "-libelle" ?>"></td>
                        <td><input disabled type="number" value="<?php echo $montant; ?>" name="<?php echo $id . "-montant" ?>">
                    </tr>

                <?php
                }
                ?>

            </tbody>
        </table>
    </div>
    <div>
        Nombre de justificatifs : <?php echo $nombresJustificatifs ?>
    </div>
</div>