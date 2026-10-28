<?php
require_once 'libs/config.php';
require_once 'libs/pdo.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mainMenu = [
    'index.php' => 'Accueil',
    'propos.php' => 'À propos'
];

if (isset($_SESSION['user'])) {
    $mainMenu['depenses.php'] = ' Mes Dépenses';
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/css/override-bootstrap.css">
</head>

<body>

    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-md-3 mb-2 mb-md-0"><a href="/" class="d-inline-flex text-decoration-none">
                    <img src="/assets/images/spendly.svg" alt="Logo spendly" width="150">
                </a></div>
            <ul class="nav col-12 col-md-auto mb-2 mb-md-0 justify-content-center">
                <?php foreach ($mainMenu as $page => $title) : ?>
                    <li><a href="<?= $page ?>" class="nav-link px-2 link-secondary"><?= $title ?></a></li>
                <?php endforeach ?>
            </ul>
            <div class="col-md-3 text-end">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="d-flex flex-column align-items-center">
                        <p>Connecté en tant que <?= $_SESSION['user']['email'] ?></p>
                        <a href="deconnexion.php" class="btn btn-outline-primary 
                    me-2">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="connexion.php" class="btn btn-outline-primary me-2">Connexion</a>
                    <a href="inscription.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
        </header>
    </div>
    <main>