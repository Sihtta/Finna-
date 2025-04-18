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

        if (empty($_POST['nom'])) {
            throw new Exception("Le nom de la catégorie ne peut pas être vide.");
        }

        $idCategorie = $_POST['id'];
        $nomCategorie = $_POST['nom'];

        // Récupération de l'id_client
        $reqClient = "SELECT id_cli FROM client WHERE login = :login";
        $resultClient = $connexion->execSQL($reqClient, ['login' => $login]);

        if (empty($resultClient)) {
            throw new Exception("Utilisateur non trouvé.");
        }

        $id_client = $resultClient[0]['id_cli'];

        // Vérification de l'existence de la catégorie
        $reqCategorie = "SELECT * FROM categories WHERE id = :id AND id_client = :id_client";
        $resultCategorie = $connexion->execSQL($reqCategorie, ['id' => $idCategorie, 'id_client' => $id_client]);

        if (empty($resultCategorie)) {
            throw new Exception("Catégorie introuvable.");
        }

        // Mise à jour de la catégorie
        $reqUpdateCategorie = "UPDATE categories SET nom = :nom WHERE id = :id";
        $connexion->execSQL($reqUpdateCategorie, ['nom' => $nomCategorie, 'id' => $idCategorie]);

        header("Location: ../../controleur/categories/list_categories.php?success=1");
        exit();
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
} else {
    $idCategorie = $_GET['id'];

    // Récupérer la catégorie à modifier
    $connexion = new Connexion();
    $reqCategorie = "SELECT * FROM categories WHERE id = :id";
    $resultCategorie = $connexion->execSQL($reqCategorie, ['id' => $idCategorie]);

    if (empty($resultCategorie)) {
        header("Location: ../../controleur/categories/list_categories.php?error=1");
        exit();
    }

    $categorie = $resultCategorie[0];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finna - Modifier Catégorie</title>
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
        <h1>Modifier la catégorie</h1>
        <div class="menu">
            <a href="../../vue/index.php" class="btn">Retour à l'accueil</a>
            <a href="../../../compte/controleur/logout.php" class="btn logout-btn">Déconnexion</a>
        </div>
    </header>
    <div class="container" style="max-width: 500px; width: 90%">
        <div class="content">
            <p><strong>Nom de la catégorie :</strong></p>
            <form action="modifier_categorie.php" method="POST">
                <input type="text" name="nom" value="<?php echo htmlspecialchars($categorie['nom']); ?>" id="category-name" placeholder="Entrez le nouveau nom de la catégorie" />
                <input type="hidden" name="id" value="<?php echo $idCategorie; ?>" />
                <button type="submit">Mettre à jour</button>
            </form>
        </div>
    </div>

</body>

</html>