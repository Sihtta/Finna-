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

    $reqComptes = "SELECT id_compte, libelle FROM compte_bancaire WHERE id_client = (SELECT id_cli FROM client WHERE login = :login)";
    $comptes = $connexion->execSQL($reqComptes, ['login' => $login]);

    $reqCategories = "SELECT id, nom FROM categories WHERE id_client = (SELECT id_cli FROM client WHERE login = :login)";
    $categories = $connexion->execSQL($reqCategories, ['login' => $login]);
} catch (Exception $e) {
    die("Erreur lors du chargement des données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $connexion->beginTransaction();

        if (empty($_POST['type']) || empty($_POST['montant']) || empty($_POST['categorie']) || empty($_POST['compte']) || empty($_POST['date'])) {
            throw new Exception("Tous les champs sauf description doivent être remplis.");
        }

        $typeTransaction = $_POST['type'];
        $montant = floatval($_POST['montant']);
        $categorie = $_POST['categorie'];
        $description = $_POST['description'];
        $compteId = $_POST['compte'];

        $dateTransaction = $_POST['date'] . ' ' . date('H:i:s');

        $compteSourceId = $_POST['compte_source'] ?? null;

        if ($compteSourceId && $compteSourceId === $compteId) {
            throw new Exception("Le compte source ne peut pas être le même que le compte de destination.");
        }

        if ($typeTransaction === 'dépense' && $montant > 0) {
            $montant = -$montant;
        }

        $reqInsertTransaction = "INSERT INTO transactions (type, montant, categorie, date, description, id_compte) 
                                    VALUES (:type, :montant, :categorie, :date, :description, :id_compte)";
        $connexion->execSQL($reqInsertTransaction, [
            'type' => $typeTransaction,
            'montant' => $montant,
            'categorie' => $categorie,
            'date' => $dateTransaction,
            'description' => $description,
            'id_compte' => $compteId
        ]);

        $connexion->mettreAJourSolde($compteId);

        if ($compteSourceId && $compteSourceId !== $compteId) {
            $typeInverse = ($typeTransaction === 'dépense') ? 'revenu' : 'dépense';

            $montantInverse = -$montant;

            $reqInsertTransactionSource = "INSERT INTO transactions (type, montant, categorie, date, description, id_compte) 
                                            VALUES (:type, :montant, :categorie, :date, :description, :id_compte)";
            $connexion->execSQL($reqInsertTransactionSource, [
                'type' => $typeInverse,
                'montant' => $montantInverse,
                'categorie' => $categorie,
                'date' => $dateTransaction,
                'description' => $description,
                'id_compte' => $compteSourceId
            ]);

            $connexion->mettreAJourSolde($compteSourceId);
        }

        $connexion->commit();

        header("Location: ../../controleur/transactions/list_transactions.php?success=1");
        exit();
    } catch (Exception $e) {
        $connexion->rollBack();
        $errorMessage = $e->getMessage();
    }
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une transaction</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.2">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            ajusterMontant();
        });

        function ajusterMontant() {
            const typeSelect = document.getElementById('type');
            const montantInput = document.getElementById('montant');

            let montant = parseFloat(montantInput.value);

            if (isNaN(montant)) {
                montant = 0;
            }

            if (typeSelect.value === 'dépense') {
                montant = -Math.abs(montant);
            } else if (typeSelect.value === 'revenu') {
                montant = Math.abs(montant);
            }

            montantInput.value = montant.toString();
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
        <h1>Finna - Ajouter Transaction</h1>
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

            <form method="POST" action="ajouter_transaction.php">
                <p><strong>Type de transaction :</strong></p>
                <select id="type" name="type" onchange="ajusterMontant()">
                    <option value="revenu">Revenu</option>
                    <option value="dépense">Dépense</option>
                </select>

                <p><strong>Montant :</strong></p>
                <input type="number" id="montant" name="montant" placeholder="Montant de la transaction" onblur="ajusterMontant()" required value="0" step="0.01" />

                <p><strong>Catégorie :</strong></p>
                <select name="categorie" required>
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $categorie) {
                            echo "<option value=\"{$categorie['id']}\">{$categorie['nom']}</option>";
                        }
                    } else {
                        echo "<option value=\"\">Aucune catégorie disponible</option>";
                    }
                    ?>
                </select>

                <p><strong>Date :</strong></p>
                <input type="date" name="date" required value="<?= date('Y-m-d') ?>" />

                <p><strong>Description (facultative) :</strong></p>
                <textarea id="description" name="description" placeholder="Description de la transaction"></textarea>

                <p><strong>Compte :</strong></p>
                <select name="compte" required>
                    <?php foreach ($comptes as $compte): ?>
                        <option value="<?= $compte['id_compte'] ?>"><?= htmlspecialchars($compte['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>

                <p><strong>Compte source (optionnel) :</strong></p>
                <select name="compte_source">
                    <option value="">Sélectionner un compte source</option>
                    <?php foreach ($comptes as $compte): ?>
                        <option value="<?= $compte['id_compte'] ?>"><?= htmlspecialchars($compte['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>



                <div class="button-container">
                    <button type="submit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>