<?php
// models/TimeSlots.php
require_once __DIR__ . '/../db.php';

class TimeSlots {
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT ts.*, u.name AS teacher_name 
                               FROM TimeSlots ts
                               JOIN Users u ON ts.teacher_id = u.user_id
                               WHERE ts.slot_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($teacherId, $slotTime, $duration = 15, $status = 'Available') {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO TimeSlots (teacher_id, slot_time, duration, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$teacherId, $slotTime, $duration, $status]);
        return $pdo->lastInsertId();
    }

    public static function update($slotId, $teacherId, $slotTime, $duration, $status) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE TimeSlots SET teacher_id = ?, slot_time = ?, duration = ?, status = ? WHERE slot_id = ?");
        return $stmt->execute([$teacherId, $slotTime, $duration, $status, $slotId]);
    }

    public static function delete($slotId) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM TimeSlots WHERE slot_id = ?");
        return $stmt->execute([$slotId]);
    }

    public static function getByTeacher($teacherId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM TimeSlots WHERE teacher_id = ? ORDER BY slot_time ASC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public static function getAllSlots() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT ts.*, u.name AS teacher_name 
                               FROM TimeSlots ts
                               JOIN Users u ON ts.teacher_id = u.user_id
                               ORDER BY ts.slot_time ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAvailableByTeacher($teacherId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM TimeSlots WHERE teacher_id = ? AND status = 'Available' ORDER BY slot_time ASC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public static function updateStatus($slotId, $status) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE TimeSlots SET status = ? WHERE slot_id = ?");
        return $stmt->execute([$status, $slotId]);
    }

    public static function getByTeacherAndTime($teacherId, $slotTime) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM TimeSlots WHERE teacher_id = ? AND slot_time = ?");
        $stmt->execute([$teacherId, $slotTime]);
        return $stmt->fetch();
    }

    // Auto-generates slots for all active teachers in system for a given event date and range
    public static function generateFromEvent($date, $startTime, $endTime, $durationMinutes) {
        global $pdo;
        // Fetch active teachers
        $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE role = 'Teacher' AND status = 'Active'");
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $startEpoch = strtotime($date . ' ' . $startTime);
        $endEpoch = strtotime($date . ' ' . $endTime);
        $durationSeconds = $durationMinutes * 60;

        $generatedCount = 0;
        foreach ($teachers as $teacherId) {
            for ($time = $startEpoch; $time < $endEpoch; $time += $durationSeconds) {
                $slotFormatted = date('Y-m-d H:i:s', $time);
                
                // Avoid double insertion
                $check = pdo_query_value("SELECT COUNT(*) FROM TimeSlots WHERE teacher_id = ? AND slot_time = ?", [$teacherId, $slotFormatted]);
                if ($check == 0) {
                    self::create($teacherId, $slotFormatted, $durationMinutes, 'Available');
                    $generatedCount++;
                }
            }
        }
        return $generatedCount;
    }
}

// Helper to get scalar value easily if not defined elsewhere
if (!function_exists('pdo_query_value')) {
    function pdo_query_value($query, $params = []) {
        global $pdo;
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}
?>
