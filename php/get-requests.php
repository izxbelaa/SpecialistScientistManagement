<?php
session_start();
include 'config.php'; // this should define $pdo

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['type_of_user'])) {
    $stmt = $pdo->prepare("SELECT type_of_user FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['type_of_user'] = $row ? $row['type_of_user'] : null;
}
$type_of_user = $_SESSION['type_of_user'];

if ($type_of_user == 5) {
    // Admin: see all
$sql = "SELECT 
    c.id AS candidate_user_id,
    c.request_id,
    CONCAT(u.first_name, ' ', u.last_name) AS requester_name,
    t.title AS request_title,
    t.description
FROM candidate_users c
LEFT JOIN users u ON c.user_id = u.id
LEFT JOIN requests r ON c.request_id = r.id
LEFT JOIN request_templates t ON r.template_id = t.id
WHERE c.status = 0
ORDER BY c.id ASC";
    $params = [];
} elseif ($type_of_user == 3) {
    // Inspector: only assigned requests
    $sql = "SELECT 
        c.id AS candidate_user_id,
        c.request_id,
        CONCAT(u.first_name, ' ', u.last_name) AS requester_name,
        t.title AS request_title,
        t.description
    FROM candidate_users c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN requests r ON c.request_id = r.id
    LEFT JOIN request_templates t ON r.template_id = t.id
    INNER JOIN evaluators e ON e.request_id = c.request_id
    WHERE c.status = 0 AND e.user_id = ?
    ORDER BY c.id ASC";
    $params = [$user_id];
} else {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>