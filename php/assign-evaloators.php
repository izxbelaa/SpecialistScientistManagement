<?php
include 'config.php';
include '../php_classes/evaluators.php'; // Adjust path as needed
session_start();

try {
    $stmt = $pdo->query("SELECT * FROM evaluators");
    $results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $evaluator = new Evaluators(
            $row['id'],
            $row['user_id'],
            $row['request_id']
        );
        $results[] = $evaluator;
    }

    echo json_encode([
        'success' => true,
        'evaluators' => $results
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Σφάλμα βάσης: ' . $e->getMessage()
    ]);
}
?>
