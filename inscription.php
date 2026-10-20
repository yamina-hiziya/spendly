<?php
require_once 'libs/pdo.php';
require_once 'libs/user.php';
require_once 'templates/header.php';

$errors = [];
if (isset($_POST['email']) && isset($_POST['password'])) {
    if (empty($_POST['email'])) {
        $errors[] = 'Email est requis';
    }
    if (empty($_POST['password'])) {
        $errors[] = 'Password est requis';
    }
    if (count($errors) === 0) {
        $res = addUser($pdo, $_POST['email'], $_POST['password']);
        if ($res) {
            header('Location: connexion.php');
        } else {
            $errors[] = 'Une erreur s\'est produite lors de votre inscription';
        }
    }
}
?>


<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <h1>Inscription</h1>

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
            <input type="submit" value="S'inscrire" class="btn btn-primary">
        </form>
    </div>
</div>
<?php
require_once 'templates/footer.php'; ?>