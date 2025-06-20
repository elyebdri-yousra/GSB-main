<?php
/**
 * Script PHP qui permet d'Hasher tous les mots de passe
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

use PDO;

$pdo = new PDO('mysql:host=127.0.0.1;dbname=gsb_frai', 'userGsb', 'secret');
$pdo->query('SET CHARACTER SET utf8');

$prepare = $pdo->prepare('SELECT visiteur.id as id, visiteur.mdp as password FROM visiteur');
$prepare->execute();
$visiteurs = $prepare->fetchAll();

echo "Visiteurs collectés";

foreach($visiteurs as $visiteur){
    $pwd_hashed = password_hash($visiteur['password'],PASSWORD_DEFAULT);

    $req = $pdo->prepare('UPDATE visiteur SET mdp = :pwd WHERE id= :idV');
    $req->bindParam(':pwd', $pwd_hashed, PDO::PARAM_STR);
    $req->bindParam(':idV', $visiteur['id'], PDO::PARAM_STR);
    $req->execute();
}