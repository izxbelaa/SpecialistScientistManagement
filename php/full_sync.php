<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "Διαχειριστής") {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit;
}

require_once 'config.php';

$enabled = isset($_POST['enabled']) ? (int)$_POST['enabled'] : null;

if (!in_array($enabled, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid value"]);
    exit;
}

try {
    if ($enabled === 0) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows path (XAMPP)
            $phpPath = 'C:\\xampp\\php\\php.exe';
            $scriptPath = 'C:\\xampp\\htdocs\\SpecialistScientistManagement\\php\\sync_to_moodle.php';
            exec("\"$phpPath\" \"$scriptPath\"");
        } else {
            // Linux path (server)
            $scriptPath = 'sync_to_moodle.php';
            exec("php \"$scriptPath\" > /dev/null 2>&1 &");
        }
    }

    $stmt = $pdo->prepare("UPDATE full_sync SET enabled = ?");
    $stmt->execute([$enabled]);

    echo json_encode([
        "status" => "success",
        "message" => $enabled ? "Full sync enabled" : "Full sync disabled"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
