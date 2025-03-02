<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/vue/index.php?error=1");
    exit();
}

include '../../modele/connexion.php';

try {
    if (empty($_POST['transaction_id']) || empty($_POST['type']) || empty($_POST['montant']) || empty($_POST['categorie']) || empty($_POST['compte'])) {
        throw new Exception("Tous les champs obligatoires doivent être remplis.");
    }

    $connexion = new Connexion();
    $transactionId = $_POST['transaction_id'];
    $type = $_POST['type'];
    $montant = floatval($_POST['montant']);
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $compte = $_POST['compte'];

    if ($type === 'dépense') {
        $montant = -abs($montant);
    }

    $reqOldTransaction = "SELECT montant, id_compte FROM transactions WHERE id = :id";
    $oldTransaction = $connexion->execSQL($reqOldTransaction, ['id' => $transactionId]);

    if (empty($oldTransaction)) {
        throw new Exception("Transaction introuvable.");
    }

    $oldMontant = floatval($oldTransaction[0]['montant']);
    $oldCompte = $oldTransaction[0]['id_compte'];

    $differenceMontant = $montant - $oldMontant;

    if ($oldCompte != $compte) {
        $reqUpdateOldCompte = "UPDATE compte_bancaire SET solde = solde - :diff WHERE id_compte = :id_compte";
        $connexion->execSQL($reqUpdateOldCompte, [
            'diff' => $oldMontant,
            'id_compte' => $oldCompte
        ]);

        $reqUpdateNewCompte = "UPDATE compte_bancaire SET solde = solde + :diff WHERE id_compte = :id_compte";
        $connexion->execSQL($reqUpdateNewCompte, [
            'diff' => $montant,
            'id_compte' => $compte
        ]);
    } else {
        $reqUpdateCompte = "UPDATE compte_bancaire SET solde = solde + :diff WHERE id_compte = :id_compte";
        $connexion->execSQL($reqUpdateCompte, [
            'diff' => $differenceMontant,
            'id_compte' => $compte
        ]);
    }

    $reqUpdate = "UPDATE transactions SET type = :type, montant = :montant, categorie = :categorie, description = :description, id_compte = :compte WHERE id = :id";
    $connexion->execSQL($reqUpdate, [
        'type' => $type,
        'montant' => $montant,
        'categorie' => $categorie,
        'description' => $description,
        'compte' => $compte,
        'id' => $transactionId
    ]);

    header("Location: ../../controleur/transactions/list_transactions.php?success=1");
    exit();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
