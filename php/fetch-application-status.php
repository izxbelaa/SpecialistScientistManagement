<?php
$applications = [];

function getStatusText($status) {
    return match ((int)$status) {
        1   => 'Εγκρίθηκε',
        -1  => 'Απορρίφθηκε',
        default => 'Σε εξέλιξη',
    };
}

require_once __DIR__ . '/config.php'; // Ensure PDO is loaded

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    // Optionally handle not logged in
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT rt.title AS request_name, cu.status
    FROM candidate_users cu
    JOIN requests r ON cu.request_id = r.id
    JOIN request_templates rt ON r.template_id = rt.id
    WHERE cu.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $applications[] = [
        'request_name' => $row['request_name'],
        'katastasi'    => getStatusText($row['status']),
    ];
}