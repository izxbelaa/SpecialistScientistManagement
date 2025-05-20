<?php
session_start();
include 'config.php'; // this should define $pdo

header('Content-Type: application/json');

// Full JOIN to get name of requester, request title and description
$sql = "SELECT 
            c.id AS candidate_user_id,
            CONCAT(u.first_name, ' ', u.last_name) AS requester_name,
            t.title AS request_title,
            t.description
        FROM candidate_users c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN requests r ON c.request_id = r.id
        LEFT JOIN request_templates t ON r.template_id = t.id
        WHERE c.status = 0
        ORDER BY c.id ASC";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>