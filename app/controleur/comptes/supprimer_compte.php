<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/vue/index.php?error=1");
    exit();
}

include '../../modele/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $connexion = new Connexion();

        if (isset($_POST['id_compte']) && !empty($_POST['id_compte'])) {
            $idCompte = $_POST['id_compte'];

            $reqCompte = "SELECT * FROM compte_bancaire WHERE id_compte = ?";
            $compte = $connexion->execSQL($reqCompte, [$idCompte]);

            if (empty($compte)) {
                header("Location: ../../controleur/comptes/list_comptes.php?error=Compte introuvable.");
                exit();
            }

            $reqDeleteCompte = "DELETE FROM compte_bancaire WHERE id_compte = ?";
            $connexion->execSQL($reqDeleteCompte, [$idCompte]);

            header("Location: ../../controleur/comptes/list_comptes.php?success=1");
            exit();
        } else {
            header("Location: ../../controleur/comptes/list_comptes.php?error=Aucun compte sélectionné.");
            exit();
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
} else {
    header("Location: ../../controleur/comptes/list_comptes.php");
    exit();
}
