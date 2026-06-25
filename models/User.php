<?php
// models/User.php
require_once __DIR__ . '/../db.php';

class User {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($name, $email, $password, $role, $status = 'Active') {
        global $pdo;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role, $status]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $name, $email, $password = null) {
        global $pdo;
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE Users SET name = ?, email = ?, password = ? WHERE user_id = ?");
            return $stmt->execute([$name, $email, $hashedPassword, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE Users SET name = ?, email = ? WHERE user_id = ?");
            return $stmt->execute([$name, $email, $id]);
        }
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }

    public static function getByRole($role) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE role = ? ORDER BY name ASC");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public static function getPendingParents() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT u.*, s.student_id, s.full_name AS student_name, s.registration_no, s.class_name, s.dob 
                               FROM Users u
                               LEFT JOIN Students s ON u.user_id = s.parent_id
                               WHERE u.role = 'Parent' AND u.status = 'Pending'
                               ORDER BY u.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateStatus($id, $status) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Users SET status = ? WHERE user_id = ?");
        return $stmt->execute([$status, $id]);
    }
}
?>
