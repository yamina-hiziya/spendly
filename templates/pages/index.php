<?php

/**
 * Vue de la page d'accueil
 * pas de variable envoyée : on regarde seulement si l'utilisateur est connecté
 */
$isLogged = isset($_SESSION['user']);
?>

<!-- bandeau d'accueil -->
<section class="bg-spendly-light py-5 mb-5">
    <div class="container col-xxl-8 px-4">
        <div class="row flex-lg-row-reverse align-items-center g-5">
            <div class="col-8 col-sm-6 col-lg-5 mx-auto">
                <img src="/assets/images/spendly-logo.svg" class="d-block mx-auto img-fluid" alt="" width="420" loading="lazy">
            </div>
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold lh-1 mb-3">Gardez le contrôle de vos dépenses</h1>
                <p class="lead mb-4">Notez vos dépenses en quelques secondes, classez-les par catégorie et voyez tout de suite où part votre argent.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <?php if ($isLogged): ?>
                        <a href="depenses.php" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-wallet2 me-2" aria-hidden="true"></i>Voir mes dépenses
                        </a>
                    <?php else: ?>
                        <a href="inscription.php" class="btn btn-primary btn-lg px-4 me-md-2">Créer mon compte gratuit</a>
                        <a href="connexion.php" class="btn btn-outline-primary btn-lg px-4">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- fonctionnalités -->
<section class="container col-xxl-8 px-4 mb-5">
    <h2 class="text-center fw-bold mb-4">Tout ce qu'il faut pour suivre votre budget</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-spendly h-100 p-4 text-center">
                <div class="feature-icon mx-auto mb-3"><i class="bi bi-plus-circle" aria-hidden="true"></i></div>
                <h3 class="h5 fw-bold">Ajout rapide</h3>
                <p class="mb-0">Un titre, un montant, une date : votre dépense est enregistrée en quelques secondes.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-spendly h-100 p-4 text-center">
                <div class="feature-icon mx-auto mb-3"><i class="bi bi-tags" aria-hidden="true"></i></div>
                <h3 class="h5 fw-bold">Catégories</h3>
                <p class="mb-0">Classez vos dépenses (courses, loisirs, transport…) et créez vos propres catégories.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-spendly h-100 p-4 text-center">
                <div class="feature-icon mx-auto mb-3"><i class="bi bi-calculator" aria-hidden="true"></i></div>
                <h3 class="h5 fw-bold">Total automatique</h3>
                <p class="mb-0">Votre total est calculé pour vous, au global ou pour une seule catégorie.</p>
            </div>
        </div>
    </div>
</section>

<!-- comment ça marche -->
<section class="bg-spendly-light py-5 mb-5">
    <div class="container col-xxl-8 px-4">
        <h2 class="text-center fw-bold mb-4">Comment ça marche ?</h2>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <span class="step-number mb-3">1</span>
                <h3 class="h5 fw-bold">Créez votre compte</h3>
                <p class="mb-0">Un email et un mot de passe suffisent.</p>
            </div>
            <div class="col-md-4">
                <span class="step-number mb-3">2</span>
                <h3 class="h5 fw-bold">Ajoutez vos dépenses</h3>
                <p class="mb-0">Au fil de l'eau, depuis votre ordinateur ou votre téléphone.</p>
            </div>
            <div class="col-md-4">
                <span class="step-number mb-3">3</span>
                <h3 class="h5 fw-bold">Suivez votre budget</h3>
                <p class="mb-0">Filtrez par catégorie et voyez votre total en un coup d'œil.</p>
            </div>
        </div>
    </div>
</section>

<!-- confiance -->
<section class="container col-xxl-8 px-4 mb-5">
    <div class="card card-spendly p-4">
        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="feature-icon flex-shrink-0"><i class="bi bi-shield-lock" aria-hidden="true"></i></div>
            <div>
                <h2 class="h4 fw-bold">Vos données restent privées</h2>
                <p class="mb-0">Chaque compte ne voit que ses propres dépenses. Votre mot de passe est chiffré et n'est jamais stocké en clair.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!$isLogged): ?>
    <!-- appel à l'inscription -->
    <section class="bg-spendly-primary text-white py-5">
        <div class="container col-xxl-8 px-4 text-center">
            <h2 class="fw-bold text-white mb-3">Prête à reprendre la main sur votre budget ?</h2>
            <a href="inscription.php" class="btn btn-gold btn-lg px-4">Commencer gratuitement</a>
        </div>
    </section>
<?php endif; ?>