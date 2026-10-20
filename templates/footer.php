<?php
$mainMenu = [
    'index.php' => 'Accueil',
    'propos.php' => 'À propos'
];
?>
</main>


<div class="container">
    <footer class="py-3 my-4">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <?php foreach ($mainMenu as $page => $title) : ?>
                <li class="nav-item"><a href="<?= $page ?>" class="nav-link px-2 text-body-secondary"><?= $title ?></a></li>
            <?php endforeach ?>
        </ul>
        <p class="text-center text-body-secondary">© <?= date('Y') ?> Spendly</p>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>