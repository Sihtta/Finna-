<?php
include '../modele/connexion.php';

session_start();

$identifiants = [
    'login' => isset($_POST['login']) ? $_POST['login'] : '',
    'password' => isset($_POST['password']) ? $_POST['password'] : ''
];

// Fonction pour vérifier l'existence du client
function existeCli(array $identifiants): bool
{
    try {
        $connexion = new Connexion();

        $req = "SELECT mot_de_passe FROM client WHERE login = :login";
        $resultat = $connexion->execSQL($req, ['login' => $identifiants['login']]);

        if (empty($resultat)) {
            return false;
        }

        $stored_password = $resultat[0]['mot_de_passe'];

        if (password_verify($identifiants['password'], $stored_password)) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        die('Erreur lors de la vérification du client : ' . $e->getMessage());
    }
}

function estAdmin($login): bool
{
    try {
        $connexion = new Connexion();

        // Requête pour vérifier le rôle du client en utilisant le login
        $req = "SELECT role FROM client WHERE login = :login";
        $resultat = $connexion->execSQL($req, ['login' => $login]);

        return !empty($resultat) && $resultat[0]['role'] === 'admin';
    } catch (Exception $e) {
        die('Erreur lors de la vérification du rôle du client : ' . $e->getMessage());
    }
}

// Vérification de l'authentification
if (existeCli($identifiants)) {
    $_SESSION['login'] = $identifiants['login'];
    if (estAdmin($identifiants['login'])) {
        header("Location: ../../app/vue/index.php");
    } else {
        header("Location: ../../app/vue/index.php");
    }
    exit();
} else {
    header("Location: ../vue/index.php?error=1");
    exit();
}
