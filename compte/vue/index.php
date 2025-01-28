<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Finna</title>
    <link rel="stylesheet" href="../../style/compte.css?v=1.2">
    <link rel="icon" type="image/png" href="../../assets/images/favicon.png">
</head>

<body>
    <header class="main-header">
        <h1>Bienvenue sur Finna</h1>
        <p>
            Votre assistant de gestion financière. Suivez vos comptes, contrôlez vos dépenses et
            maîtrisez vos finances en toute simplicité.
        </p>
        <p>Connectez-vous pour découvrir toutes les fonctionnalités !</p>
    </header>

    <div class="login-form">
        <div class="container">
            <div class="content">
                <section>
                    <h2>Authentification</h2>
                    <form action="../controleur/login.php" method="POST">
                        <label for="login">Numéro d'Utilisateur :</label>
                        <input type="text" id="login" name="login" required>

                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" required>

                        <button type="submit">Connexion</button>

                        <a href="../controleur/inscription.php">Pas encore de compte ? Inscrivez-vous.</a>

                        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                            <p class="error">Identification incorrecte. Essayez de nouveau.</p>
                        <?php endif; ?>
                    </form>
                </section>
            </div>
        </div>
    </div>
    <div class="example-credentials">
        <h2>Identifiants d'exemple</h2>
        <p>Vous pouvez utiliser ces identifiants pour tester l'application :</p>
        <ul>
            <li><strong>Identifiant :</strong> <span class="highlight">demo-user</span></li>
            <li><strong>Mot de passe :</strong> <span class="highlight">demo-password</span></li>
        </ul>
    </div>
</body>

</html>