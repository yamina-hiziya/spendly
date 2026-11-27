<?php

/**
 * Vue de la page Inscription
 * variables envoyées par inscription.php :
 * @var array $errors
 */
?>
<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <h1>Inscription</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endforeach; ?>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8">
                <div class="form-text">8 caractères minimum</div>
            </div>
            <input type="submit" value="S'inscrire" class="btn btn-primary">
        </form>

        <p class="mt-3">Déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
    </div>
</div>