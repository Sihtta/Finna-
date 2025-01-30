<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../../compte/controleur/index.php?error=1");
    exit();
}

if (!isset($_POST['transaction_id']) || !is_numeric($_POST['transaction_id'])) {
    header("Location: ../../controleur/transactions/list_transactions.php?error=Aucune transaction sélectionnée.");
    exit();
}

$transaction_id = $_POST['transaction_id'];
$login = $_SESSION['login'];

include '../../modele/connexion.php';

try {
    $connexion = new Connexion();

    // Récupération des détails de la transaction
    $reqTransaction = "SELECT * FROM transactions WHERE id = :id";
    $transaction = $connexion->execSQL($reqTransaction, ['id' => $transaction_id]);

    if (empty($transaction)) {
        throw new Exception("Transaction introuvable.");
    }

    $transaction = $transaction[0];

    // Récupération des comptes bancaires de l'utilisateur
    $reqComptes = "SELECT id_compte, libelle FROM compte_bancaire WHERE id_client = (SELECT id_cli FROM client WHERE login = :login)";
    $comptes = $connexion->execSQL($reqComptes, ['login' => $login]);

    // Récupération des catégories de l'utilisateur
    $reqCategories = "SELECT id, nom FROM categories WHERE id_client = (SELECT id_cli FROM client WHERE login = :login)";
    $categories = $connexion->execSQL($reqCategories, ['login' => $login]);
} catch (Exception $e) {
    die("Erreur lors du chargement des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finna - Modifier Transaction</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.2">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            ajusterMontant();
        });

        function ajusterMontant() {
            const typeSelect = document.getElementById('type');
            const montantInput = document.getElementById('montant');

            if (typeSelect.value === 'dépense') {
                montantInput.value = montantInput.value < 0 ? montantInput.value : -Math.abs(montantInput.value || 0);
            } else if (typeSelect.value === 'revenu') {
                montantInput.value = Math.abs(montantInput.value || 0);
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
        <h1>Modifier une transaction</h1>
        <div class="menu">
            <a href="../../controleur/transactions/list_transactions.php" class="btn">Retour à la liste</a>
            <a href="../../../compte/controleur/logout.php" class="btn logout-btn">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <div class="content">
            <form method="POST" action="../../controleur/transactions/update_transaction.php">
                <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($transaction['id']) ?>">

                <p><strong>Type de transaction :</strong></p>
                <select id="type" name="type" onchange="ajusterMontant()">
                    <option value="revenu" <?= $transaction['type'] === 'revenu' ? 'selected' : '' ?>>Revenu</option>
                    <option value="dépense" <?= $transaction['type'] === 'dépense' ? 'selected' : '' ?>>Dépense</option>
                </select>

                <p><strong>Montant :</strong></p>
                <input type="number" id="montant" name="montant" placeholder="Montant de la transaction" onblur="ajusterMontant()" required value="<?= htmlspecialchars($transaction['montant']) ?>" step="0.01" />

                <p><strong>Catégorie :</strong></p>
                <select name="categorie" required>
                    <?php
                    foreach ($categories as $categorie) {
                        $selected = $categorie['id'] == $transaction['categorie'] ? 'selected' : '';
                        echo "<option value=\"{$categorie['id']}\" $selected>{$categorie['nom']}</option>";
                    }
                    ?>
                </select>

                <p><strong>Date :</strong></p>
                <input type="date" name="date" required value="<?= htmlspecialchars($transaction['date']) ?>" />

                <p><strong>Description (facultative) :</strong></p>
                <textarea id="description" name="description" placeholder="Description de la transaction"><?= htmlspecialchars($transaction['description']) ?></textarea>

                <p><strong>Compte :</strong></p>
                <select name="compte" required>
                    <?php
                    foreach ($comptes as $compte) {
                        $selected = $compte['id_compte'] == $transaction['id_compte'] ? 'selected' : '';
                        echo "<option value=\"{$compte['id_compte']}\" $selected>{$compte['libelle']}</option>";
                    }
                    ?>
                </select>

                <div class="button-container">
                    <button type="submit">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>