<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/controleur/index.php?error=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_compte'])) {
    include '../../modele/connexion.php';

    try {
        $connexion = new Connexion();
        $id_compte = $_POST['id_compte'];
        $libelle = $_POST['libelle'];
        $type = $_POST['type'];
        $solde = $_POST['solde'];

        $updateCompte = "UPDATE compte_bancaire SET libelle = :libelle, type = :type, solde = :solde WHERE id_compte = :id_compte";
        $connexion->execSQL($updateCompte, [
            'libelle' => $libelle,
            'type' => $type,
            'solde' => $solde,
            'id_compte' => $id_compte
        ]);

        header("Location: ../../controleur/comptes/list_comptes.php?success=1");
        exit();
    } catch (Exception $e) {
        die("Erreur lors de la mise à jour du compte : " . $e->getMessage());
    }
} else {
    header("Location: ../../controleur/comptes/list_comptes.php?error=Erreur lors de la mise à jour");
    exit();
}
