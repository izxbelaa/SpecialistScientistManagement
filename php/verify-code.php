<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$codeSubmitted = trim($data['code'] ?? '');

if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] === $codeSubmitted) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Λανθασμένος κωδικός.'
    ]);
}
?>
