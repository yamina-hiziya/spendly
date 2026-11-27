<?php
// on démarre la session avant tout affichage pour pouvoir rediriger
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'libs/pdo.php';
require_once 'libs/user.php';
require_once 'libs/security.php';

// si on est déjà connecté, on va directement aux dépenses
if (isset($_SESSION['user'])) {
    header('Location: depenses.php');
    exit;
}

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
    }
    if ($password === '') {
        $errors[] = 'Le mot de passe est obligatoire';
    }

    if (count($errors) === 0) {
        $user = verifyUserLogin($pdo, $email, $password);

        if ($user) {
            // on change l'id de session à la connexion (contre le vol de session)
            session_regenerate_id(true);
            $_SESSION['user'] = ['id_user' => $user['id_user'], 'email' => $user['email']];

            header('Location: depenses.php');
            exit;
        } else {
            // même message si l'email n'existe pas ou si le mot de passe est faux :
            // on ne donne pas d'indice sur les comptes existants
            $errors[] = 'Email ou mot de passe incorrect';
        }
    }
}

// affichage : header, vue de la page, footer
require_once 'templates/header.php';
require_once 'templates/pages/connexion.php';
require_once 'templates/footer.php';
