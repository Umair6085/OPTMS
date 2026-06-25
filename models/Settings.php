<?php
// models/Settings.php
require_once __DIR__ . '/../db.php';

class Settings {
    public static function get($key, $default = null) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT setting_value FROM Settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    }

    public static function set($key, $value) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        return $stmt->execute([$key, $value, $value]);
    }
}
?>
