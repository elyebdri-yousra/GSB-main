<div class="panel user scrollable-table">
    <div class="panel-heading">Liste des fiches de frais.</div>

    <table border="1" class="table border-warning table-bordered table-fixed" style="margin-bottom: 0px !important;">
        <thead>
            <tr>
                <th class="gras border-warning text-uppercase">Date</th>
                <th class="gras border-warning text-uppercase">Justificatifs</th>
                <th class="gras border-warning text-uppercase">Montant</th>
                <th class="gras border-warning text-uppercase">État</th>
                <th class="gras border-warning text-uppercase">Autres options</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fichedefrais as $frais) { ?>
                <form method="POST" action="index.php?uc=suivreFichesFrais&action=rembourseFiche">
                    <tr>
                        <td><?php echo substr($frais['moisFiche'], 4, 2) . "/" . substr($frais['moisFiche'], 0, 4); ?></td>
                        <td><?php echo $frais['nbJustificatifs'] ?></td>
                        <td><?php echo $frais['montantValide'] ?></td>
                        <td><?php echo $frais['idEtat'] ?></td>
                        <td><a href="index.php?uc=suivreFichesFrais&action=voirInfoFiche&idVisiteur=<?php echo $frais['idVisiteur'] ?>&mois=<?php echo $frais['moisFiche'] ?>" target="_blank" class="btn btn-primary">Voir plus...</a>
                            <?php if ($frais['idEtat'] != 'RB' && $frais['idEtat'] != 'VA') { ?>
                                <input type="submit" value="Valider" name='task' class=" btn btn-warning">
                            <?php }
                            if ($frais['idEtat'] != 'RB' && $frais['idEtat'] == 'VA') { ?>
                                <input type="submit" value="Rembourser" name='task' class=" btn btn-danger">
                            <?php } ?>
                        </td>

                    </tr>
                    <input type="hidden" value="<?php echo $frais['idVisiteur'] ?>" name="idVisiteur">
                    <input type="hidden" value="<?php echo $frais['moisFiche'] ?>" name="mois">
                </form>
            <?php } ?>
        </tbody>
    </table>
</div>

<style>
    /* Style adjustments for the scrollable table */
    .scrollable-table {
        max-height: 60vh;
        /* Set max height to enable scrolling */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }
</style>