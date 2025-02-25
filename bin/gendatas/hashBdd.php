<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=gsb_frais', 'userGsb', 'secret');
$req = 'select visiteur.id as id, visiteur.mdp as mdp from visiteur';
$res = $pdo->query($req);
$visiteurs = $res->fetchAll();

foreach ($visiteurs as $visiteur){
    $mdp=$visiteur['mdp'];
    $hash_pwd = password_hash($mdp, algo: PASSWORD_DEFAULT);
    $requetePrepare = $pdo->prepare(
        'UPDATE visiteur'
        . ' SET visiteur.mdp = :mdpVisiteur'
        .'  WHERE visiteur.id = :idVisiteur '
    );
    $requetePrepare->bindParam(':mdpVisiteur',$hash_pwd, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idVisiteur',$visiteur['id'], PDO::PARAM_STR);
    $requetePrepare->execute();
}