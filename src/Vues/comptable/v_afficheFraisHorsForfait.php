<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
<hr>
<div class="row">
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
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $date = $unFraisHorsForfait[4];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id'];
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                ?> <form method="POST" action="index.php?uc=validerFrais&action=validerMajHorsCorrection">
                        <tr>
                            <td><input type="date" value="<?php echo $date ?>" name=<?php echo $id . "-date" ?>></td>
                            <td><input type="text" value="<?php echo $libelle ?>" name="<?php echo $id . "-libelle" ?>"></td>
                            <td><input type="number" value="<?php echo $montant; ?>" name="<?php echo $id . "-montant" ?>">
                            <td>
                                <input type="hidden" value="<?php echo $id?>" name="id">
                                <input type="submit" class="btn btn-success" value="Corriger">
                                <input type="reset" class="btn btn-danger" value="Réinitialiser">
                                <input type="submit" class="btn btn-warning" value="Supprimer" name="idhorsfrais">
                                <input type="submit" class="btn btn-secondary" value="Reporter" name="reportelement">
                            </td>
                        </tr>
                    </form>

                <?php
                }
                ?>

            </tbody>
        </table>
    </div>
    <div>
        Nombre de justificatifs : <?php echo $nombresJustificatifs ?>
    </div>
    <div>
        <form method="POST" action="index.php?uc=validerFrais&action=validationFinale">
            <button class="btn btn-success" type="submit">Valider</button>
            <button class="btn btn-danger" type="reset">Reinitialiser</button>
        </form>
    </div>
</div>