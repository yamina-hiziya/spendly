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

// affichage : header, vue de la page, footer
require_once 'templates/header.php';
require_once 'templates/pages/depenses.php';
require_once 'templates/footer.php';
