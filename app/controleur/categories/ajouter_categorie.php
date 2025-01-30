<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/vue/index.php?error=1");
    exit();
}

include '../../modele/connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $connexion = new Connexion();
        $login = $_SESSION['login'];

        // Vérifier si la catégorie est vide
        if (empty($_POST['nom'])) {
            throw new Exception("Le nom de la catégorie ne peut pas être vide.");
        }

        $nomCategorie = $_POST['nom'];

        // Récupérer l'id_client
        $reqClient = "SELECT id_cli FROM client WHERE login = :login";
        $resultClient = $connexion->execSQL($reqClient, ['login' => $login]);

        if (empty($resultClient)) {
            throw new Exception("Utilisateur non trouvé.");
        }

        $id_client = $resultClient[0]['id_cli'];

        // Insertion de la nouvelle catégorie dans la base de données
        $reqInsertCategorie = "INSERT INTO categories (id_client, nom) VALUES (:id_client, :nom)";
        $connexion->execSQL($reqInsertCategorie, ['id_client' => $id_client, 'nom' => $nomCategorie]);

        header("Location: ../../controleur/categories/list_categories.php?success=1");
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
    <title>Finna - Ajout catégorie</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.2">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">
</head>

<body>
    <header class="main-header" style="top:0; position: fixed;">
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
        <h1>Ajouter une nouvelle catégorie</h1>
        <div class=" menu">
            <a href="../../vue/index.php" class="btn">Retour à l'acceuil</a>
            <a href="../../../compte/controleur/logout.php" class="btn logout-btn">Déconnexion</a>
        </div>
    </header>

    <div class="container" style="max-width: 500px; width: 90%">
        <div class="content">

            <?php if (isset($errorMessage)): ?>
                <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form method="POST" action="ajouter_categorie.php">
                <p><strong>Nom de la catégorie :</strong></p>
                <input type="text" id="category-name" name="nom" placeholder="Nom de la catégorie" />

                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

</body>

</html>