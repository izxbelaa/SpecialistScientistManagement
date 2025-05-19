<?php
session_start();

// Έλεγχος αν είναι διαχειριστής
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "Διαχειριστής") {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit;
}

// Κάνε εδώ το sync logic σου (π.χ. database update, API call, κ.λπ.)
$success = true; // Βάλε εδώ την πραγματική λογική

if ($success) {
    echo json_encode(["status" => "success", "message" => "Full sync completed successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Full sync failed"]);
}
?>