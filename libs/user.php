<?php

function addUser(PDO $pdo, string $email, string $password): bool
{
    // on prepare la requête sql pour inséré un utilisateur en base de donnée
    $query = $pdo->prepare("INSERT INTO user(email,password) VALUES (:email,:password)");

    // on hash le mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // on passe les données
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':password', $passwordHash, PDO::PARAM_STR);

    //on exectute la requête
    return $query->execute();
}

function verifyUserLogin(PDO $pdo, string $email, string $password): bool|array
{
    $query = $pdo->prepare("SELECT id_user, email, password FROM user WHERE email = :email");
    $query->bindvalue(":email", $email, PDO::PARAM_STR);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return false;
    }
}
