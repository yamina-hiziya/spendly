<?php

/**
 * Vue de la page Mes Dépenses
 * variables envoyées par depenses.php :
 * @var array $errors
 * @var array $categories
 * @var int|null $categoryId
 * @var array $expenses
 * @var float $expenseTotal
 * @var bool $showModalAddExpense
 */
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