<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT enabled FROM full_sync LIMIT 1");
    $row = $stmt->fetch();

    if ($row) {
        echo json_encode(["status" => "success", "enabled" => (int)$row['enabled']]);
    } else {
        echo json_encode(["status" => "error", "message" => "No row found"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
