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

    $reqClient = "SELECT id_cli FROM client WHERE login = :login";
    $resultClient = $connexion->execSQL($reqClient, ['login' => $login]);

    if (empty($resultClient)) {
        throw new Exception("Utilisateur non trouvé.");
    }
    $idClient = $resultClient[0]['id_cli'];
} catch (Exception $e) {
    die("Erreur lors du chargement des données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (empty($_POST['libelle']) || empty($_POST['type'])) {
            throw new Exception("Le nom du compte et le type sont obligatoires.");
        }

        $libelle = $_POST['libelle'];
        $type = $_POST['type'];
        $soldeInitial = !empty($_POST['solde']) ? floatval($_POST['solde']) : 0.0;
        $solde = $soldeInitial;

        $reqInsertCompte = "INSERT INTO compte_bancaire (libelle, type, solde, solde_initial, id_client) 
                            VALUES (:libelle, :type, :solde, :solde_initial, :id_client)";
        $connexion->execSQL($reqInsertCompte, [
            'libelle' => $libelle,
            'type' => $type,
            'solde' => $solde,
            'solde_initial' => $soldeInitial,
            'id_client' => $idClient
        ]);

        header("Location: ../../controleur/comptes/list_comptes.php?success=1");
        exit();
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finna - Ajouter Compte Bancaire</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.2">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">
    <style>
        .container p {
            margin-bottom: 5px;
        }

        .container input,
        .container select,
        .container button {
            margin-bottom: 15px;
            padding: 8px;
            width: 40%;
            box-sizing: border-box;
        }

        .container button {
            margin-top: 15px;
            height: 35px;
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <img src="../../../assets/images/favicon.png" alt="Logo Finna" class="logo-img">
                </a>
                <a href="index.php" style="text-decoration: none"> <span class="logo-text">Finna</span> </a>
            </div>
            <nav class="nav-links">
                <a href="../../vue/index.php">Accueil</a>
                <a href="../comptes/list_comptes.php">Mes comptes</a>
                <a href="../transactions/list_transactions.php">Mes transactions</a>
                <a href="../categories/list_categories.php">Mes catégories</a>
            </nav>
        </div>
    </header>
    <header class="main-header2">
        <h1>Ajouter un nouveau compte bancaire</h1>
        <div class="menu">
            <a href="../../vue/index.php" class="btn">Retour à l'accueil</a>
            <a href="../../../compte/controleur/logout.php" class="btn logout-btn">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <div class="content">

            <?php if (isset($errorMessage)): ?>
                <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form method="POST" action="ajouter_compte.php">
                <p><strong>Nom du compte :</strong></p>
                <input type="text" name="libelle" placeholder="Nom du compte" required />

                <p><strong>Type de compte :</strong></p>
                <select name="type" required>
                    <option value="courant">Courant</option>
                    <option value="épargne">Épargne</option>
                    <option value="investissement">Investissement</option>
                </select>

                <p><strong>Solde initial (facultatif) :</strong></p>
                <input type="number" name="solde" placeholder="0.00" step="0.01" />

                <div class="button-container">
                    <button type="submit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>