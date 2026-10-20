<?php
session_start();
require_once 'libs/pdo.php';
require_once 'libs/user.php';

$errors = [];
if (isset($_POST['email']) && isset($_POST['password'])) {
    if (empty($_POST['email'])) {
        $errors[] = 'Email est requis';
    }
    if (empty($_POST['password'])) {
        $errors[] = 'Password est requis';
    }
    if (count($errors) === 0) {
        $user = verifyUserLogin($pdo, $_POST['email'], $_POST['password']);
        if ($user) {
            //on veut le connecter avec une session
            session_regenerate_id(true);
            $_SESSION["user"] = ["id_user" => $user["id_user"], "email" => $user["email"]];
            header('location: index.php');
            exit;
        } else {
            //on va généré une erreur
            $errors[] = 'Email ou mot de passe incorrect';
        }
    }
}
require_once 'templates/header.php';
?>


<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 ">
        <h1>Connexion</h1>

        <?php foreach ($errors as $error) : ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endforeach ?>

        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <input type="submit" value="Se connecter" class="btn btn-primary">
        </form>
    </div>
</div>
<?php
require_once 'templates/footer.php'; ?>