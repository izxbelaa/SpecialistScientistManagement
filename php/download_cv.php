<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['request_id']) || empty($_GET['request_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing request_id']);
    exit;
}

$request_id = intval($_GET['request_id']);

try {
    $stmt = $pdo->prepare("SELECT file FROM request_uploadedfile WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $fileRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fileRow || empty($fileRow['file'])) {
        echo json_encode(['success' => false, 'message' => 'Το βιογραφικό δεν βρέθηκε.']);
        exit;
    }

    // Encode the file content in base64 to send as JSON
    $base64File = base64_encode($fileRow['file']);
    echo json_encode([
        'success' => true,
        'filename' => "cv_request_{$request_id}.pdf",
        'file' => $base64File
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
