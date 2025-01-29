<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finna - Gestion financière</title>
    <link rel="stylesheet" href="../../style/compte.css?v=1.5">
    <link rel="icon" type="image/png" href="../../assets/images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <img src="../../assets/images/favicon.png" alt="Logo Finna" class="logo-img">
                <span class="logo-text">Finna</span>
            </div>
            <nav class="nav-links">
                <a href="login.view.php">Se connecter</a>
                <a href="inscription.view.php">S'inscrire</a>
            </nav>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="container">
        <div class="content">
            <h1 class="main-title">
                Bienvenue sur <span class="highlight-green">Finna</span>
            </h1>

            <p>
                Votre assistant de gestion financière. Suivez vos comptes, contrôlez vos dépenses et
                maîtrisez vos finances en toute simplicité.
            </p>
            <p>Connectez-vous pour découvrir toutes les fonctionnalités !</p>

            <div class="cta-container">
                <button class="button finna" onclick="window.location.href='login.view.php'">Se Connecter</button>
                <h2 class="services-title">Nos Services :</h2>
            </div>
            <!-- Cartes des fonctionnalités -->
            <div class="cards">
                <div class="card">
                    <i class="skill-icon fa-solid fa-university"></i>
                    <h3>Gestion des comptes bancaires</h3>
                    <p>Ajoutez et gérez vos différents comptes bancaires pour un suivi centralisé.</p>
                </div>
                <div class="card">
                    <i class="skill-icon fa-solid fa-credit-card"></i>
                    <h3>Gestion des transactions</h3>
                    <p>Enregistrez, modifiez et suivez vos dépenses et revenus facilement.</p>
                </div>
                <div class="card">
                    <i class="skill-icon fa-solid fa-tags"></i>
                    <h3>Catégories personnalisées</h3>
                    <p>Organisez vos transactions par catégories pour mieux analyser vos dépenses.</p>
                </div>
            </div>
        </div>
    </main>
</body>
<script src="../../assets/js/button.js"></script>

</html>