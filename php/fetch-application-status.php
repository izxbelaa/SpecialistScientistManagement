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

$stmt = $pdo->prepare("
    SELECT rt.title AS request_name, cu.status
    FROM candidate_users cu
    JOIN request_templates rt ON cu.request_id = rt.id
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $applications[] = [
        'request_name' => $row['request_name'],
        'katastasi'    => getStatusText($row['status']),
    ];
}
