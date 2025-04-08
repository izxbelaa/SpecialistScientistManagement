<?php
include 'config.php';
include '../php_classes/requests.php'; // Adjust path as needed
session_start();

try {
    $stmt = $pdo->query("SELECT * FROM requests");
    $results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $request = new Requests(
            $row['id'],
            $row['course_selection_id'],
            $row['education_id'],
            $row['professional_title_id'],
            $row['work_experience_id'],
            $row['upload_file_id'],
            $row['consent_forms_id']
        );
        $results[] = $request;
    }

    echo json_encode([
        'success' => true,
        'requests' => $results
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Σφάλμα βάσης: ' . $e->getMessage()
    ]);
}
?>
