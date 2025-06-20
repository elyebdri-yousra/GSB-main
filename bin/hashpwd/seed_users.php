<?php
/**
 * Seeder, permet de créer un utilisateur
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

$pdo = new PDO('mysql:host=127.0.0.1;dbname=gsb_frais', 'userGsb', 'secret');
$pdo->query('SET CHARACTER SET utf8');

$prepare = $pdo->prepare('INSERT INTO visiteur(id,nom,prenom,login,mdp,nom_role) VALUES ("dev9","ElYebdri","Yousra","y.elyebdri",:mdp, "Comptable")');
$prepare->bindParam(':mdp',password_hash('mdp',PASSWORD_DEFAULT),PDO::PARAM_STR);
$prepare->execute();
