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
        <div class="header-content">
            <div class="logo">
                <img src="../../assets/images/favicon.png" alt="Logo Finna" class="logo-img">
                <span class="logo-text">Finna</span>
            </div>
            <nav class="nav-links">
                <a href="index.php">Accueil</a>
                <a href="inscription.view.php">S'inscrire</a>
            </nav>
        </div>
    </header>
    <h1 class="main-title">
        Bienvenue sur <span class="highlight-green">Finna</span>
    </h1>
    <div class="login-form">
        <div class="container2">
            <div class="content2">
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

    <!-- Section Identifiants d'exemple -->
    <section class="example-credentials">
        <h2>Identifiants d'exemple</h2>
        <p>Vous pouvez utiliser ces identifiants pour tester l'application :</p>
        <ul>
            <li><strong>Identifiant :</strong> <span class="highlight">demo-user</span></li>
            <li><strong>Mot de passe :</strong> <span class="highlight">demo-password</span></li>
        </ul>
    </section>
</body>

</html>