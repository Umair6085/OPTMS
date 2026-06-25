<?php
// models/Feedback.php
require_once __DIR__ . '/../db.php';

class Feedback {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Feedback WHERE feedback_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByMeeting($meetingId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Feedback WHERE meeting_id = ?");
        $stmt->execute([$meetingId]);
        return $stmt->fetch();
    }

    public static function getByTeacher($teacherId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT f.*, m.slot_time, u_parent.name AS parent_name
                               FROM Feedback f
                               JOIN Meetings m ON f.meeting_id = m.meeting_id
                               JOIN Users u_parent ON m.parent_id = u_parent.user_id
                               WHERE m.teacher_id = ?
                               ORDER BY f.created_at DESC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public static function getFlaggedForAdmin() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT f.*, m.slot_time, u_parent.name AS parent_name, u_teacher.name AS teacher_name
                               FROM Feedback f
                               JOIN Meetings m ON f.meeting_id = m.meeting_id
                               JOIN Users u_parent ON m.parent_id = u_parent.user_id
                               JOIN Users u_teacher ON m.teacher_id = u_teacher.user_id
                               WHERE f.needs_admin_action = TRUE
                               ORDER BY f.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create($meetingId, $ratingStars, $comments) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Feedback (meeting_id, rating_stars, comments) VALUES (?, ?, ?)");
        return $stmt->execute([$meetingId, $ratingStars, $comments]);
    }

    public static function flagForAdmin($feedbackId, $flag = true) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Feedback SET needs_admin_action = ? WHERE feedback_id = ?");
        return $stmt->execute([$flag ? 1 : 0, $feedbackId]);
    }
}
?>
