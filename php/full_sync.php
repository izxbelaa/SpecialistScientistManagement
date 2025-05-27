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
        // Temporarily enable sync to allow sync_to_moodle.php to run
        $stmt = $pdo->prepare("UPDATE full_sync SET enabled = 1");
        $stmt->execute();

        // Start output buffering
        ob_start();
        require_once 'sync_to_moodle.php';
        ob_end_clean(); // Discard all output

        // Now disable sync
        $stmt = $pdo->prepare("UPDATE full_sync SET enabled = 0");
        $stmt->execute();
        } else {
        // Just enable sync without running it
        $stmt = $pdo->prepare("UPDATE full_sync SET enabled = 1");
        $stmt->execute();
    }

    echo json_encode([
        "status" => "success",
        "message" => $enabled ? "Full sync enabled" : "Full sync disabled and sync completed"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
