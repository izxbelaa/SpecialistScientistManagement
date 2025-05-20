<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // All available templates
    $stmt = $pdo->query("SELECT * FROM request_templates");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all applications the user has submitted
    $stmt = $pdo->prepare("
        SELECT r.template_id, cu.status
        FROM candidate_users cu
        JOIN requests r ON cu.request_id = r.id
        WHERE cu.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $userApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'templates' => $templates,
        'userApplications' => $userApplications
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
