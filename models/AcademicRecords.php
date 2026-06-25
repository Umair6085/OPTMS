<?php
// models/AcademicRecords.php
require_once __DIR__ . '/../db.php';

class AcademicRecords {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT ar.*, s.full_name AS student_name, u.name AS teacher_name 
                               FROM AcademicRecords ar
                               JOIN Students s ON ar.student_id = s.student_id
                               JOIN Users u ON ar.teacher_id = u.user_id
                               WHERE ar.record_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($studentId, $teacherId, $recordType, $title, $description = null, $totalMarks = null, $obtainedMarks = null, $filePath = null) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO AcademicRecords (student_id, teacher_id, record_type, title, description, total_marks, obtained_marks, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $teacherId, $recordType, $title, $description, $totalMarks, $obtainedMarks, $filePath]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $studentId, $recordType, $title, $description = null, $totalMarks = null, $obtainedMarks = null, $filePath = null) {
        global $pdo;
        if ($filePath !== null) {
            $stmt = $pdo->prepare("UPDATE AcademicRecords SET student_id = ?, record_type = ?, title = ?, description = ?, total_marks = ?, obtained_marks = ?, file_path = ? WHERE record_id = ?");
            return $stmt->execute([$studentId, $recordType, $title, $description, $totalMarks, $obtainedMarks, $filePath, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE AcademicRecords SET student_id = ?, record_type = ?, title = ?, description = ?, total_marks = ?, obtained_marks = ? WHERE record_id = ?");
            return $stmt->execute([$studentId, $recordType, $title, $description, $totalMarks, $obtainedMarks, $id]);
        }
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM AcademicRecords WHERE record_id = ?");
        return $stmt->execute([$id]);
    }

    public static function getByStudent($studentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT ar.*, u.name AS teacher_name 
                               FROM AcademicRecords ar
                               JOIN Users u ON ar.teacher_id = u.user_id
                               WHERE ar.student_id = ? 
                               ORDER BY ar.created_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public static function getByTeacherAndStudent($teacherId, $studentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT ar.*, u.name AS teacher_name 
                               FROM AcademicRecords ar
                               JOIN Users u ON ar.teacher_id = u.user_id
                               WHERE ar.teacher_id = ? AND ar.student_id = ? 
                               ORDER BY ar.created_at DESC");
        $stmt->execute([$teacherId, $studentId]);
        return $stmt->fetchAll();
    }
}
?>
