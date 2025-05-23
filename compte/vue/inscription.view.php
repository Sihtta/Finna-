<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Finna - Inscription</title>
    <link rel="stylesheet" href="../../style/compte.css">
    <link rel="icon" type="image/png" href="../../assets/images/favicon.png">
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <img src="../../assets/images/favicon.png" alt="Logo Finna" class="logo-img">
                </a>
                <a href="index.php" style="text-decoration: none"> <span class="logo-text">Finna</span> </a>
            </div>
            <button class="mobile-menu-toggle"><i class="fas fa-bars"></i></button>
            <nav class="nav-links">
                <a href="index.php">Accueil</a>
                <a href="inscription.view.php">S'inscrire</a>
            </nav>
        </div>
    </header>

    <h1 class="main-title" style="margin-top: 5%">
        Bienvenue sur <span class="highlight-green">Finna</span>
    </h1>
    <div class="signup-form">
        <div class="container2">
            <div class="content2">

                <h2>Inscription Client</h2>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error">
                        <?php
                        if ($_GET['error'] == 'login_taken') {
                            echo "Ce login est déjà utilisé.";
                        } elseif ($_GET['error'] == 'email_taken') {
                            echo "Un compte existe déjà avec cette adresse e-mail.";
                        } elseif ($_GET['error'] == 'phone_taken') {
                            echo "Un compte existe déjà avec ce numéro de téléphone.";
                        }
                        ?>
                    </p>
                <?php endif; ?>


                <form action="../controleur/inscription.php" method="POST">
                    <div class="form-row">
                        <label for="login">Login :</label>
                        <input type="text" id="login" name="login" required>
                    </div>

                    <div class="form-row">
                        <label for="civ_cli">Civilité :</label>
                        <select id="civ_cli" name="civ_cli" required>
                            <option value="M">M</option>
                            <option value="Mme">Mme</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="nom_cli">Nom :</label>
                        <input type="text" id="nom_cli" name="nom_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="prenom_cli">Prénom :</label>
                        <input type="text" id="prenom_cli" name="prenom_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="tel_cli">Téléphone :</label>
                        <input type="tel" id="tel_cli" name="tel_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="mel_cli">E-mail :</label>
                        <input type="email" id="mel_cli" name="mel_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="adr_cli">Adresse :</label>
                        <input type="text" id="adr_cli" name="adr_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="cp_cli">Code Postal :</label>
                        <input type="text" id="cp_cli" name="cp_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="commune_cli">Commune :</label>
                        <input type="text" id="commune_cli" name="commune_cli" required>
                    </div>

                    <div class="form-row">
                        <label for="mot_de_passe">Mot de Passe :</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                    </div>

                    <button type="submit">Créer un compte</button>

                </form>


                <a href="../vue/login.view.php">Déjà un compte ? Connectez-vous ici.</a>
            </div>
        </div>
    </div>

</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.querySelector('.mobile-menu-toggle');
        const nav = document.querySelector('.nav-links');
        if (btn && nav) btn.addEventListener('click', () => nav.classList.toggle('show'));
    });
</script>

</html>