<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
<div id="accueil">
    <h2>
        Gestion des frais<small> - Comptable : 
            <?= $_SESSION['prenom'] . ' ' . $_SESSION['nom'] ?></small>
    </h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel user">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body div-user">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a href="?uc=validerFrais&action=choisirClientDate"
                           class="btn btn-primary btn-lg btn-user btn-valider" role="button">
                            <span class="glyphicon glyphicon-ok"></span>
                            <br>Valider les fiches de frais</a>
                        <a href="?uc=suivreFichesFrais&action=selectionnerUser"
                           class="btn btn-primary btn-lg btn-user" role="button">
                            <span class="glyphicon glyphicon-euro"></span>
                            <br>Suivre le paiement des fiches de frais</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
