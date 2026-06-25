<?php
// models/Student.php
require_once __DIR__ . '/../db.php';

class Student {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Students WHERE student_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByRegistrationNo($regNo) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Students WHERE registration_no = ?");
        $stmt->execute([$regNo]);
        return $stmt->fetch();
    }

    public static function getByParent($parentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Students WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public static function create($parentId, $fullName, $regNo, $className, $dob) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Students (parent_id, full_name, registration_no, class_name, dob) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$parentId, $fullName, $regNo, $className, $dob]);
        return $pdo->lastInsertId();
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM Students WHERE student_id = ?");
        return $stmt->execute([$id]);
    }
}
?>
