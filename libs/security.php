<?php

/**
 * Renvoie le jeton CSRF de la session (le crée s'il n'existe pas encore).
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // 32 octets aléatoires convertis en texte (64 caractères)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie que le jeton envoyé par le formulaire correspond à celui de la session.
 */
function checkCsrfToken(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    // Comparaison en temps constant : ne laisse rien deviner du jeton
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Échappe un texte avant de l'afficher dans le HTML (protection XSS).
 */
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
