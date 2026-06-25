<?php
// models/Payment.php
require_once __DIR__ . '/../db.php';

class Payment {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Payments WHERE payment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByStudent($studentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Payments WHERE student_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public static function getByParent($parentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT p.*, s.full_name AS student_name, s.registration_no 
                               FROM Payments p
                               JOIN Students s ON p.student_id = s.student_id
                               WHERE s.parent_id = ?
                               ORDER BY p.created_at DESC");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public static function hasUnpaidDues($parentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Payments p
                               JOIN Students s ON p.student_id = s.student_id
                               WHERE s.parent_id = ? AND p.status = 'Unpaid'");
        $stmt->execute([$parentId]);
        return $stmt->fetchColumn() > 0;
    }

    public static function createInvoice($studentId, $amount) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Payments (student_id, amount, status) VALUES (?, ?, 'Unpaid')");
        return $stmt->execute([$studentId, $amount]);
    }

    public static function markAsPaid($paymentId, $trxId) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Payments SET status = 'Paid', jazzcash_trx_id = ?, payment_date = NOW() WHERE payment_id = ?");
        return $stmt->execute([$trxId, $paymentId]);
    }

    public static function getAllPayments() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT p.*, s.full_name AS student_name, s.registration_no, u.name AS parent_name
                               FROM Payments p
                               JOIN Students s ON p.student_id = s.student_id
                               JOIN Users u ON s.parent_id = u.user_id
                               ORDER BY p.payment_date DESC, p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getFinancialSummary() {
        global $pdo;
        // Total earnings
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM Payments WHERE status = 'Paid'");
        $stmt->execute();
        $totalEarnings = (float)($stmt->fetchColumn() ?: 0.00);

        // Load actual expenses from Expenses table
        require_once __DIR__ . '/Expenses.php';
        $expenses = (float)Expenses::getTotalExpenses();
        $netProfit = $totalEarnings - $expenses;

        return [
            'total_earnings' => $totalEarnings,
            'simulated_expenses' => $expenses,
            'net_profit' => $netProfit
        ];
    }
}
?>
