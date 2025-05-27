<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php'; // $pdo is created here

function getStatusText($status) {
    return match ((int)$status) {
        1   => 'Εγκρίθηκε',
        -1  => 'Απορρίφθηκε',
        default => 'Σε εξέλιξη',
    };
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    // $pdo is already created in config.php
    $stmt = $pdo->prepare("
        SELECT rt.title AS request_name, cu.status
        FROM candidate_users cu
        JOIN requests r ON cu.request_id = r.id
        JOIN request_templates rt ON r.template_id = rt.id
        WHERE cu.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $applications = [];
    foreach ($rows as $row) {
        $applications[] = [
            'request_name' => $row['request_name'],
            'katastasi'    => getStatusText($row['status']),
        ];
    }

    echo json_encode(['success' => true, 'data' => $applications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}