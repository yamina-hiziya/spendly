<?php
// on démarre la session avant tout affichage pour pouvoir rediriger
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'libs/pdo.php';
require_once 'libs/expense.php';
require_once 'libs/category.php';
require_once 'libs/security.php';

// page réservée aux utilisateurs connectés
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

$idUser = $_SESSION['user']['id_user'];
$categories = getCategories($pdo);

// on garde les id des catégories pour vérifier celle qui est envoyée
$categoryIds = array_column($categories, 'id_category');

$showModalAddExpense = false;
$errors = [];

if (isset($_POST['saveExpense'])) {
    $title = trim($_POST['title'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $idCategory = (int)($_POST['category'] ?? 0);
    $expenseDate = $_POST['date'] ?? '';
    $newCategoryName = trim($_POST['new_category'] ?? '');

    // on vérifie que le formulaire vient bien de notre site
    if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Le formulaire a expiré, merci de réessayer';
    }

    if ($title === '') {
        $errors['title'] = 'Le titre est obligatoire';
    } elseif (mb_strlen($title) > 255) {
        $errors['title'] = 'Le titre ne doit pas dépasser 255 caractères';
    }

    if ($amount <= 0) {
        $errors['amount'] = 'Le montant doit être supérieur à 0';
    }

    // on vérifie que la date existe vraiment (format AAAA-MM-JJ)
    $date = DateTime::createFromFormat('Y-m-d', $expenseDate);
    if (!$date || $date->format('Y-m-d') !== $expenseDate) {
        $errors['date'] = 'La date est obligatoire';
    }

    // -1 = nouvelle catégorie, sinon la catégorie doit exister en base
    if ($idCategory === -1) {
        if ($newCategoryName === '') {
            $errors['new_category'] = 'Le nom de la catégorie est obligatoire';
        } elseif (mb_strlen($newCategoryName) > 100) {
            $errors['new_category'] = 'Le nom de la catégorie ne doit pas dépasser 100 caractères';
        }
    } elseif (!in_array($idCategory, $categoryIds)) {
        $errors['category'] = 'La catégorie est obligatoire';
    }

    if (!$errors) {
        if ($idCategory === -1) {
            $idCategory = addCategory($pdo, $newCategoryName);
        }
        saveExpense($pdo, $title, $amount, $expenseDate, $idCategory, $idUser);

        // on redirige pour éviter de renvoyer le formulaire si on rafraîchit la page
        header('Location: depenses.php?added=1');
        exit;
    } else {
        $showModalAddExpense = true;
    }
}

// filtre par catégorie
$categoryId = null;
if (isset($_GET['category']) && $_GET['category'] !== '') {
    $categoryId = (int)$_GET['category'];
}

$expenses = getExpenses($pdo, $idUser, $categoryId);
$expenseTotal = getExpenseTotal($pdo, $idUser, $categoryId);

// le header affiche du HTML, il vient seulement maintenant
require_once 'templates/header.php';
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1 class="mb-3">Mes Dépenses</h1>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">La dépense a bien été ajoutée</div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
        Ajouter une dépense
    </button>

    <!-- Modal ajout d'une dépense -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="expenseModalLabel">Dépense</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(getCsrfToken()) ?>">

                    <div class="modal-body">
                        <?php if (isset($errors['form'])): ?>
                            <div class="alert alert-danger"><?= e($errors['form']) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" name="title" id="title" class="form-control" maxlength="255" value="<?= e($_POST['title'] ?? '') ?>">
                        </div>
                        <?php if (isset($errors['title'])): ?>
                            <div class="alert alert-danger"><?= e($errors['title']) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant</label>
                            <input type="number" name="amount" id="amount" step=".01" min="0.01" class="form-control" value="<?= e($_POST['amount'] ?? '') ?>">
                        </div>
                        <?php if (isset($errors['amount'])): ?>
                            <div class="alert alert-danger"><?= e($errors['amount']) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="<?= e($_POST['date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <?php if (isset($errors['date'])): ?>
                            <div class="alert alert-danger"><?= e($errors['date']) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select name="category" id="category" class="form-control form-select">
                                <option value="">Choisir une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category['id_category'] ?>" <?= (isset($_POST['category']) && (int)$_POST['category'] === (int)$category['id_category']) ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="-1" <?= (isset($_POST['category']) && (int)$_POST['category'] === -1) ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                        <?php if (isset($errors['category'])): ?>
                            <div class="alert alert-danger"><?= e($errors['category']) ?></div>
                        <?php endif; ?>

                        <div class="mb-3" style="display: <?= (isset($_POST['category']) && (int)$_POST['category'] === -1) ? 'block' : 'none' ?>" id="newCategoryDiv">
                            <label for="new_category" class="form-label">Nouvelle catégorie</label>
                            <input type="text" name="new_category" id="new_category" class="form-control" maxlength="100" value="<?= e($_POST['new_category'] ?? '') ?>">
                            <?php if (isset($errors['new_category'])): ?>
                                <div class="alert alert-danger mt-3"><?= e($errors['new_category']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <input name="saveExpense" type="submit" class="btn btn-primary" value="Enregistrer">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- fin modal -->

    <form method="get">
        <table class="table mt-4">
            <thead>
                <tr class="table-dark">
                    <th scope="col">Titre</th>
                    <th scope="col">Montant</th>
                    <th scope="col">Date</th>
                    <th scope="col">
                        <select onchange="this.form.submit()" name="category" id="filter_category" class="form-control form-select" aria-label="Filtrer par catégorie">
                            <option value="">-- Catégorie --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id_category'] ?>" <?= ($categoryId === (int)$category['id_category']) ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= e($expense['title']) ?></td>
                            <td><?= e(formatPrice($expense['amount'])) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($expense['expense_date']))) ?></td>
                            <td><?= e($expense['category_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Aucune dépense</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-dark">
                    <th>Total</th>
                    <th><?= e(formatPrice($expenseTotal)) ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<script>
    // on affiche le champ "nouvelle catégorie" seulement si on choisit "Autre"
    const categorySelect = document.getElementById('category');
    const newCategoryDiv = document.getElementById('newCategoryDiv');
    if (categorySelect && newCategoryDiv) {
        categorySelect.addEventListener('change', function() {
            if (categorySelect.value === '-1') {
                newCategoryDiv.style.display = 'block';
            } else {
                newCategoryDiv.style.display = 'none';
            }
        });
    }

    <?php if ($showModalAddExpense): ?>
        // s'il y a des erreurs, on rouvre la modal pour les afficher
        document.addEventListener('DOMContentLoaded', () => {
            const expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));
            expenseModal.show();
        });
    <?php endif; ?>
</script>

<?php require_once 'templates/footer.php'; ?>



<?php
require_once 'templates/footer.php';
?>