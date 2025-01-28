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

    // Récupération de l'ID du client
    $reqClient = "SELECT id_cli FROM client WHERE login = :login";
    $resultClient = $connexion->execSQL($reqClient, ['login' => $login]);

    if (empty($resultClient)) {
        throw new Exception("Utilisateur non trouvé.");
    }

    $id_client = $resultClient[0]['id_cli'];

    // Récupération des comptes bancaires de l'utilisateur
    $reqComptes = "SELECT * FROM compte_bancaire WHERE id_client = :id_client";
    $comptes = $connexion->execSQL($reqComptes, ['id_client' => $id_client]);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Comptes Bancaires</title>
    <link rel="stylesheet" href="../../../style/finances.css?v=1.1">
    <link rel="icon" type="image/png" href="../../../assets/images/favicon.png">

    <script>
        function toggleCompteList() {
            var compteList = document.getElementById('compte-list');
            compteList.style.display = (compteList.style.display === 'block') ? 'none' : 'block';
        }

        function toggleDeleteCompteForm() {
            var deleteCompteForm = document.getElementById('delete-compte-form');
            deleteCompteForm.style.display = (deleteCompteForm.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</head>

<body>
    <header class="main-header">
        <h1>Bienvenue, <?= htmlspecialchars($login) ?> !</h1>
        <div class="menu">
            <a href="../../vue/index.php" class="btn btn-primary">Retour à l'accueil</a>
            <a href="../../../compte/controleur/logout.php" class="btn btn-danger">Déconnexion</a>
        </div>
    </header>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p class="message success">Compte modifié avec succès !</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <div class="container">
        <div class="content">
            <section>
                <h2>Vos Comptes Bancaires</h2>



                <?php if (!empty($comptes)): ?>
                    <div class="comptes-list">
                        <?php foreach ($comptes as $compte): ?>
                            <div class="compte-card">
                                <h3><?= htmlspecialchars($compte['libelle']) ?></h3>
                                <p><strong>Type :</strong> <?= htmlspecialchars($compte['type']) ?></p>
                                <p><strong>Solde :</strong> <?= htmlspecialchars($compte['solde']) ?> €</p>
                                <a href="#" class="btn">Voir détails</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Vous n'avez aucun compte bancaire enregistré.</p>
                <?php endif; ?>

                <div class="action-buttons">
                    <a href="../../controleur/comptes/ajouter_compte.php" class="btn btn-add" style="margin-right: 20px">Ajouter un compte</a>
                    <a href="javascript:void(0);" class="btn btn-more" style="margin-right: 20px" onclick="toggleCompteList()">Modifier un compte</a>
                    <a href="javascript:void(0);" class="btn btn-delete" onclick="toggleDeleteCompteForm()">Supprimer un compte</a>
                </div>

                <div id="delete-compte-form" style="display:none; margin-top: 20px;">
                    <form action="../../controleur/comptes/supprimer_compte.php" method="POST">
                        <label for="compte">Sélectionnez un compte à supprimer :</label>
                        <select name="id_compte" id="compte" required>
                            <?php foreach ($comptes as $compte): ?>
                                <option value="<?= htmlspecialchars($compte['id_compte']) ?>">
                                    <?= htmlspecialchars($compte['libelle']) ?> (<?= htmlspecialchars($compte['solde']) ?> €)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-delete">Supprimer ce compte</button>
                    </form>
                </div>

                <div id="compte-list" style="display:none; margin-top: 20px;">
                    <form action="../../controleur/comptes/modifier_compte.php" method="POST">
                        <label for="compte">Sélectionnez un compte à modifier :</label>
                        <select name="id_compte" id="compte" required>
                            <?php foreach ($comptes as $compte): ?>
                                <option value="<?= htmlspecialchars($compte['id_compte']) ?>">
                                    <?= htmlspecialchars($compte['libelle']) ?> (<?= htmlspecialchars($compte['solde']) ?> €)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-edit">Modifier ce compte</button>
                    </form>
                </div>
            </section>
        </div>
    </div>

</body>

</html>