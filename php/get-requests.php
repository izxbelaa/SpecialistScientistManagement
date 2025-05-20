<?php
session_start();
include 'config.php'; // this should define $pdo

header('Content-Type: application/json');

// Full JOIN to get name of requester, request title and description
$sql = "SELECT 
            r.id, 
            CONCAT(u.first_name, ' ', u.last_name) AS requester_name,
            t.title AS request_title,
            t.description
        FROM requests r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN request_templates t ON r.template_id = t.id
        ORDER BY r.id ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
