<?php
// models/Expenses.php
require_once __DIR__ . '/../db.php';

class Expenses {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Expenses WHERE expense_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($title, $amount, $category, $expenseDate, $description = null) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Expenses (title, amount, category, expense_date, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $amount, $category, $expenseDate, $description]);
    }

    public static function update($id, $title, $amount, $category, $expenseDate, $description = null) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Expenses SET title = ?, amount = ?, category = ?, expense_date = ?, description = ? WHERE expense_id = ?");
        return $stmt->execute([$title, $amount, $category, $expenseDate, $description, $id]);
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM Expenses WHERE expense_id = ?");
        return $stmt->execute([$id]);
    }

    public static function getAll() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Expenses ORDER BY expense_date DESC, created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getTotalExpenses() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM Expenses");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0.00;
    }
}
?>
