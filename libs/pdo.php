<?php
require_once __DIR__ . '/config.php';

try {
    $dbSettings = parse_ini_file(_APP_ENV_);
    $pdo = new PDO(
        "mysql:dbname={$dbSettings['db_name']};host={$dbSettings['db_host']};port={$dbSettings['db_port']};charset=utf8mb4",
        $dbSettings['db_user'],
        $dbSettings['db_password']
    );
    // on veut une exception en cas d'erreur SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // on garde le détail dans les logs, l'utilisateur voit un message neutre
    error_log('[Spendly] connexion à la base impossible : ' . $e->getMessage());
    http_response_code(500);
    die('Le service est momentanément indisponible, merci de réessayer plus tard.');
}
