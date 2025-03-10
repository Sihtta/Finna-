<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/vue/index.php?error=1");
    exit();
}

include '../../modele/connexion.php';

try {
    $connexion = new Connexion();
    $login = $_SESSION['login'];

    if (empty($_GET['id'])) {
        throw new Exception("ID de la catégorie non fourni.");
    }

    $idCategorie = $_GET['id'];

    $reqClient = "SELECT id_cli FROM client WHERE login = :login";
    $resultClient = $connexion->execSQL($reqClient, ['login' => $login]);

    if (empty($resultClient)) {
        throw new Exception("Utilisateur non trouvé.");
    }

    $id_client = $resultClient[0]['id_cli'];

    $reqCategorie = "SELECT * FROM categories WHERE id = :id AND id_client = :id_client";
    $resultCategorie = $connexion->execSQL($reqCategorie, ['id' => $idCategorie, 'id_client' => $id_client]);

    if (empty($resultCategorie)) {
        throw new Exception("Catégorie introuvable.");
    }

    $reqDeleteCategorie = "DELETE FROM categories WHERE id = :id";
    $connexion->execSQL($reqDeleteCategorie, ['id' => $idCategorie]);

    header("Location: ../../controleur/categories/list_categories.php?success=1");
    exit();
} catch (Exception $e) {
    header("Location: ../../controleur/categories/list_categories.php?error=" . urlencode($e->getMessage()));
    exit();
}
