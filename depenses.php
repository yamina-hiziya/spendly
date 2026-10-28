<?php
require_once 'templates/header.php';
require_once 'libs/expense.php';
require_once 'libs/category.php';

if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

$showModalAddExpense = false;

$errors = [];
if (isset($_POST['saveExpense'])) {
    $title = trim($_POST['title']);
    $amount = (float)$_POST['amount'];
    $IdCategory = isset($_POST['category']) ? (int)$_POST['category'] : 0;
    $expenseDate = $_POST['date'];
    $newCategoryName = isset($_POST['new_category']) ? $_POST['new_category'] : '';

    if (!is_numeric($amount) || $amount <= 0) {
        $errors["amount"] = 'Le montant doit être supérieur à 0';
    }
    if ($title === '') {
        $errors["title"] = 'Le titre est obligatoire';
    }
    if ($IdCategory < -1) {
        $errors["category"] = 'La catégorie est obligatoire';
    }
    if ($IdCategory === -1 && $newCategoryName === '') {
        $errors["new_category"] = 'Le nom de la catégorie est obligatoire';
    }

    if (!$errors) {
        if ($IdCategory === -1) {
            $IdCategory = addCategory($pdo, $newCategoryName);
        }
        //on peut enregistrer la dépense
        $res = saveExpense($pdo, $title, $amount, $expenseDate, $IdCategory, $_SESSION['user']['id_user']);
    } else {
        $showModalAddExpense = true;
    }
}

$categoryId = null;
if (isset($_GET["category"]) && $_GET["category"] !== "") {
    $categoryId = (int)$_GET["category"];
}
$categories = getCategories($pdo);
$expenses = getExpenses($pdo, $_SESSION['user']['id_user'], $categoryId);
$expenseTotal = getExpenseTotal($pdo, $_SESSION['user']['id_user'], $categoryId);
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1 class='mb-3'>Mes Dépenses</h1>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
        Ajouter une dépense
    </button>

    <!-- Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="expenseModalLabel">Dépense</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" name="title" id="title" class="form-control">
                        </div>
                        <?php if (isset($errors['title'])): ?>
                            <div class="alert alert-danger">
                                <?= $errors['title'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant</label>
                            <input type="number" name="amount" id="amount" step=".01" class="form-control">
                        </div>
                        <?php if (isset($errors["amount"])): ?>
                            <div class="alert alert-danger">
                                <?= $errors["amount"]  ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="expense_date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select name="category" id="category" class="form-control form-select">
                                <option value=""> Choisir une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                    <option<?= (isset($_POST["category"]) && (int)$_POST["category"] === $category['id_category']) ? 'selected' : '' ?> value="<?= $category['id_category'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                    <option value="-1" <?= (isset($_POST["category"]) && (int)$_POST["category"] === -1) ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                        <?php if (isset($errors["category"])): ?>
                            <div class="alert alert-danger">
                                <?= $errors["category"]  ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3" style="display: <?= (isset($_POST["category"]) && (int)$_POST["category"] === -1) ? '' : 'none' ?>" id="newCategoryDiv">
                            <label for="new_category" class="form-label">Nouvelle catégorie</label>
                            <input type="text" name="new_category" id="new_category" class="form-control">
                            <?php if (isset($errors["new_category"])): ?>
                                <div class="alert alert-danger mt-3">
                                    <?= $errors["new_category"]  ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <input name="saveExpense" type="submit" class="btn btn-primary" value="Enregistrer"></input>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--Modal-->
    <form method="get">
        <table class="table mt-4">
            <thead>
                <tr class=table-dark>
                    <th scope="col">Titre</th>
                    <th scope="col">Montant</th>
                    <th scope="col">Date</th>
                    <th scope="col">
                        <select onchange="this.form.submit()" name="category" id="category" class="form-control form-select" aria-label="Catégorie">
                            <option value="">-- Catégorie --</option>
                            <?php foreach ($categories as $category): ?>
                                <option <?= (isset($_GET["category"]) && (int)$_GET["category"] === $category['id_category']) ? 'selected' : '' ?> value="<?= $category['id_category'] ?>"><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses): ?>
                    <?php foreach ($expenses as $expense) : ?>
                        <tr>
                            <td><?= htmlspecialchars($expense['title']) ?></td>
                            <td><?= htmlspecialchars(formatPrice($expense['amount'])) ?></td>
                            <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                            <td><?= htmlspecialchars($expense['category_name']) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Aucune dépense</td>
                    </tr>
                <?php endif; ?>
            <tfoot>
                <tr class=table-dark>
                    <th>Total</th>
                    <th><?= htmlspecialchars(formatPrice($expenseTotal)) ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
            </tbody>
        </table>
    </form>
</div>

<script>
    const categorySelect = document.getElementById('category');
    const newCategoryDiv = document.getElementById('newCategoryDiv');
    if (categorySelect && newCategoryDiv) {
        categorySelect.addEventListener('change',
            function() {
                if (categorySelect.value === '-1') {
                    newCategoryDiv.style.display = 'block';
                } else {
                    newCategoryDiv.style.display = 'none';
                }
            });
    }

    <?php if ($showModalAddExpense): ?>
        document.addEventListener('DOMContentLoaded', (event) => {
            const expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));
            expenseModal.show();
        });
    <?php endif; ?>
</script>



<?php
require_once 'templates/footer.php';
?>