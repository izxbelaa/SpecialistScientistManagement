<?php
require_once __DIR__ . '/config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch all templates with end date >= today
    $stmt = $pdo->prepare("SELECT * FROM request_templates WHERE date_end >= CURDATE()");
    $stmt->execute();
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($templates as &$template) {
        $tid = $template['id'];

        // Fetch associated courses
        $stmtC = $pdo->prepare("SELECT course_id FROM request_template_course WHERE template_id = ?");
        $stmtC->execute([$tid]);
        $template['courses'] = array_column($stmtC->fetchAll(PDO::FETCH_ASSOC), 'course_id');

        // Fetch associated academies
        $stmtA = $pdo->prepare("SELECT academy_id FROM request_template_academy WHERE template_id = ?");
        $stmtA->execute([$tid]);
        $template['academies'] = array_column($stmtA->fetchAll(PDO::FETCH_ASSOC), 'academy_id');
    }

    // 2. Fetch all requests the user has made
    $stmt = $pdo->prepare("SELECT r.template_id, cu.status 
                           FROM requests r 
                           JOIN candidate_users cu ON cu.request_id = r.id 
                           WHERE cu.user_id = ?");
    $stmt->execute([$user_id]);
    $userApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'templates' => $templates,
        'userApplications' => $userApplications
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
