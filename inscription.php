<?php
// on démarre la session avant tout affichage pour pouvoir rediriger
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'libs/pdo.php';
require_once 'libs/user.php';
require_once 'libs/security.php';

$errors = [];

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // on vérifie que le formulaire vient bien de notre site
    if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Le formulaire a expiré, merci de réessayer';
    }

    if ($email === '') {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (mb_strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
    }

    if (count($errors) === 0) {
        try {
            addUser($pdo, $email, $password);

            header('Location: connexion.php?registered=1');
            exit;
        } catch (PDOException $e) {
            // 1062 = doublon : l'email est unique dans la table user
            if ($e->errorInfo[1] === 1062) {
                $errors[] = 'Cet email est déjà utilisé';
            } else {
                // on garde le détail dans les logs, pas à l'écran
                error_log('[Spendly] ' . $e->getMessage());
                $errors[] = "Une erreur s'est produite lors de votre inscription";
            }
        }
    }
}

// affichage : header, vue de la page, footer
require_once 'templates/header.php';
require_once 'templates/pages/inscription.php';
require_once 'templates/footer.php';
