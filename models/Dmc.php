<?php
// models/Dmc.php
require_once __DIR__ . '/../db.php';

class Dmc {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM DMCs WHERE dmc_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByStudent($studentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT d.*, u.name AS teacher_name 
                               FROM DMCs d
                               JOIN Users u ON d.teacher_id = u.user_id
                               WHERE d.student_id = ? 
                               ORDER BY d.created_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public static function save($studentId, $teacherId, $totalMarks, $obtainedMarks, $pdfFilePath = null) {
        global $pdo;
        // Check if DMC record already exists
        $stmt = $pdo->prepare("SELECT dmc_id, pdf_file_path FROM DMCs WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Keep existing PDF path if not overwriting
            $finalPdfPath = $pdfFilePath !== null ? $pdfFilePath : $existing['pdf_file_path'];
            $stmt = $pdo->prepare("UPDATE DMCs SET total_marks = ?, obtained_marks = ?, pdf_file_path = ?, teacher_id = ? WHERE dmc_id = ?");
            $stmt->execute([$totalMarks, $obtainedMarks, $finalPdfPath, $teacherId, $existing['dmc_id']]);
            return $existing['dmc_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO DMCs (student_id, teacher_id, total_marks, obtained_marks, pdf_file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $teacherId, $totalMarks, $obtainedMarks, $pdfFilePath]);
            return $pdo->lastInsertId();
        }
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM DMCs WHERE dmc_id = ?");
        return $stmt->execute([$id]);
    }
}
?>
