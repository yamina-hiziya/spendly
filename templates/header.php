<?php
require_once __DIR__ . '/../libs/config.php';
require_once __DIR__ . '/../libs/pdo.php';
require_once __DIR__ . '/../libs/security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mainMenu = [
    'index.php' => 'Accueil',
    'propos.php' => 'À propos'
];

// le menu "Mes Dépenses" n'apparaît que si on est connecté
if (isset($_SESSION['user'])) {
    $mainMenu['depenses.php'] = 'Mes Dépenses';
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="icon" type="image/svg+xml" href="/assets/images/spendly-icon.svg">
    <link rel="stylesheet" href="/assets/css/override-bootstrap.css?v=3">
</head>

<body>

    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-md-3 mb-2 mb-md-0">
                <a href="/" class="d-inline-flex text-decoration-none">
                    <img src="/assets/images/spendly-icon.svg" alt="" width="44" height="44">
                    <span class="fs-3 fw-bold ms-2 align-self-center" style="color: var(--spendly-primary);">Spendly</span>
                </a>
            </div>

            <ul class="nav col-12 col-md-auto mb-2 mb-md-0 justify-content-center">
                <?php foreach ($mainMenu as $page => $title): ?>
                    <li><a href="<?= e($page) ?>" class="nav-link px-2 link-secondary"><?= e($title) ?></a></li>
                <?php endforeach; ?>
            </ul>

            <div class="col-md-3 text-end">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="d-flex flex-column align-items-center">
                        <p class="small text-body-secondary mb-1">Connecté : <?= e($_SESSION['user']['email']) ?></p>
                        <!-- on se déconnecte avec un formulaire POST + jeton CSRF, pas avec un simple lien -->
                        <form action="deconnexion.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Déconnexion</button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="connexion.php" class="btn btn-outline-primary me-2">Connexion</a>
                    <a href="inscription.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
        </header>
    </div>
    <main>