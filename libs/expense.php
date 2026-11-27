<?php

/**
 * Récupère les dépenses d'un utilisateur.
 *
 * @param PDO $pdo La connexion à la base de données.
 * @param int $id_user L'ID de l'utilisateur.
 * @return array Les dépenses de l'utilisateur.
 */

function getExpenses(PDO $pdo, int $id_user, ?int $categoryId = null): array
{
    $where = '';
    if ($categoryId) {
        $where = 'AND e.id_category = :category_id';
    }
    $sql = "SELECT e.id_expense, e.amount, e.title, e.expense_date, e.id_category, c.name as category_name
            FROM expense e 
            JOIN category c ON e.id_category = c.id_category 
            WHERE e.id_user = :id_user $where
            ORDER BY e.expense_date DESC";

    $query = $pdo->prepare($sql);

    $query->bindValue(':id_user', $id_user, PDO::PARAM_INT);
    if ($categoryId) {
        $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    }
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getExpenseTotal(PDO $pdo, int $userId, ?int $categoryId = null): float
{
    $where = '';
    if ($categoryId) {
        $where = 'AND e.id_category = :category_id';
    }
    $sql = "SELECT SUM(e.amount) as total 
            FROM expense e
            WHERE e.id_user = :id_user $where;";

    $query = $pdo->prepare($sql);
    $query->bindValue(':id_user', $userId, PDO::PARAM_INT);
    if ($categoryId) {
        $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    }
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result["total"] === null) {
        $total = 0;
    } else {
        $total = (float) $result["total"];
    }
    return $total;
}

function saveExpense(PDO $pdo, string $title, float $amount, string $expenseDate, int $idCategory, int $idUser): bool
{
    $query = $pdo->prepare("INSERT INTO expense (title, amount, expense_date, id_category, id_user)
                            VALUES (:title, :amount, :expense_date, :id_category, :id_user)");
    $query->bindValue(':title', $title, PDO::PARAM_STR);
    $query->bindValue(':amount', $amount, PDO::PARAM_STR);
    $query->bindValue(':expense_date', $expenseDate, PDO::PARAM_STR);
    $query->bindValue(':id_category', $idCategory, PDO::PARAM_INT);
    $query->bindValue(':id_user', $idUser, PDO::PARAM_INT);

    return $query->execute();
}

function formatPrice(float|int $price, string $currency = '€', $decimal = 2): string
{
    $formattedPrice = number_format($price, 2);
    return "$formattedPrice €";
}
