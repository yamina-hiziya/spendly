<?php
session_start();
require_once __DIR__ . '/../libs/security.php';

// on accepte seulement le formulaire du header (POST + jeton valide)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !checkCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: index.php');
    exit;
}

// on vide et on détruit la session
session_unset();
session_destroy();

header('Location: index.php');
exit;
