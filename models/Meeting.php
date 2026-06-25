<?php
// models/Meeting.php
require_once __DIR__ . '/../db.php';

class Meeting {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Meetings WHERE meeting_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByTeacher($teacherId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT m.*, u.name AS parent_name, s.full_name AS student_name, s.student_id, s.registration_no, s.class_name, d.obtained_marks, d.total_marks, d.pdf_file_path
                               FROM Meetings m
                               JOIN Users u ON m.parent_id = u.user_id
                               LEFT JOIN Students s ON s.parent_id = u.user_id
                               LEFT JOIN DMCs d ON d.student_id = s.student_id
                               WHERE m.teacher_id = ?
                               ORDER BY m.slot_time ASC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public static function getByParent($parentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT m.*, u.name AS teacher_name, u.email AS teacher_email
                               FROM Meetings m
                               JOIN Users u ON m.teacher_id = u.user_id
                               WHERE m.parent_id = ?
                               ORDER BY m.slot_time ASC");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public static function isSlotTaken($teacherId, $slotTime) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Meetings WHERE teacher_id = ? AND slot_time = ? AND status = 'Scheduled'");
        $stmt->execute([$teacherId, $slotTime]);
        return $stmt->fetchColumn() > 0;
    }

    public static function create($teacherId, $parentId, $slotTime, $meetLink, $duration = 15) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Meetings (teacher_id, parent_id, slot_time, duration, meet_link, status) VALUES (?, ?, ?, ?, ?, 'Scheduled')");
        $stmt->execute([$teacherId, $parentId, $slotTime, $duration, $meetLink]);
        return $pdo->lastInsertId();
    }

    public static function updateStatus($meetingId, $status) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Meetings SET status = ? WHERE meeting_id = ?");
        return $stmt->execute([$status, $meetingId]);
    }

    public static function createPtmEvent($date, $startTime, $endTime, $slotDuration = 15) {
        global $pdo;
        // Truncate existing events first or add to list. Let's add to list.
        $stmt = $pdo->prepare("INSERT INTO PtmEvents (event_date, start_time, end_time, slot_duration) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$date, $startTime, $endTime, $slotDuration]);
    }

    public static function getPtmEvents() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM PtmEvents ORDER BY event_date DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function deletePtmEvent($eventId) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM PtmEvents WHERE event_id = ?");
        return $stmt->execute([$eventId]);
    }
}
?>
