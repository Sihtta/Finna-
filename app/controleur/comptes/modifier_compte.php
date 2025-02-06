<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/controleur/index.php?error=1");
    exit();
}

if (!isset($_POST['id_compte']) || !is_numeric($_POST['id_compte'])) {
    header("Location: ../comptes/list_comptes.php?error=Aucun compte sélectionné.");
    exit();
}

$id_compte = $_POST['id_compte'];
$login = $_SESSION['login'];

include '../../modele/connexion.php';

try {
    $connexion = new Connexion();

    $reqCompte = "SELECT * FROM compte_bancaire WHERE id_compte = :id_compte";
    $compte = $connexion->execSQL($reqCompte, ['id_compte' => $id_compte]);

    if (empty($compte)) {
        throw new Exception("Compte introuvable.");
    }

    $compte = $compte[0];
} catch (Exception $e) {
    die("Erreur lors du chargement des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un compte bancaire</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.2">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            ajusterSolde();
        });

        function ajusterSolde() {
            const typeSelect = document.getElementById('type');
            const soldeInput = document.getElementById('solde');

            if (typeSelect.value === 'dépense') {
                soldeInput.value = soldeInput.value < 0 ? soldeInput.value : -Math.abs(soldeInput.value || 0);
            } else if (typeSelect.value === 'revenu') {
                soldeInput.value = Math.abs(soldeInput.value || 0);
            }
        }
    </script>

    <style>
        .container p {
            margin-bottom: 5px;
        }

        .container select,
        .container input,
        .container textarea,
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
        <h1>Modifier un compte bancaire</h1>
        <div class="menu">
            <a href="../comptes/list_comptes.php" class="btn">Retour à la liste</a>
            <a href="../../../compte/controleur/logout.php" class="btn logout-btn">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <div class="content">
            <form method="POST" action="../comptes/update_compte.php">
                <input type="hidden" name="id_compte" value="<?= htmlspecialchars($compte['id_compte']) ?>">

                <p><strong>Libellé du compte :</strong></p>
                <input type="text" name="libelle" required value="<?= htmlspecialchars($compte['libelle']) ?>" />

                <p><strong>Type de compte :</strong></p>
                <select id="type" name="type" onchange="ajusterSolde()">
                    <option value="épargne" <?= $compte['type'] === 'épargne' ? 'selected' : '' ?>>Épargne</option>
                    <option value="courant" <?= $compte['type'] === 'courant' ? 'selected' : '' ?>>Courant</option>
                </select>

                <p><strong>Solde :</strong></p>
                <input type="number" id="solde" name="solde" required value="<?= htmlspecialchars($compte['solde']) ?>" step="0.01" />

                <div class="button-container">
                    <button type="submit">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>