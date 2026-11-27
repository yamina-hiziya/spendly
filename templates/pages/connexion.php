<?php

/**
 * Vue de la page Connexion
 * variables envoyées par connexion.php :
 * @var array $errors
 */
?>
<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5">
        <h1>Connexion</h1>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Inscription réussie, vous pouvez vous connecter</div>
        <?php endif; ?>

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
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <input type="submit" value="Se connecter" class="btn btn-primary">
        </form>

        <p class="mt-3">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
    </div>
</div>