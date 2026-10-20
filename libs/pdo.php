<?php
require_once 'libs/config.php';

try {

    $dbSettings = parse_ini_file(_APP_ENV_);
    $pdo = new PDO("mysql:dbname={$dbSettings["db_name"]};host={$dbSettings["db_host"]};port={$dbSettings["db_port"]};charset=utf8mb4", $dbSettings["db_user"], $dbSettings["db_password"]);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
