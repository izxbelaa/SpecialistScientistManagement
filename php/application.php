<?php
include 'config.php';
header('Content-Type: application/json');
session_start();

try {
    $stmt = $conn->prepare("SELECT id, course_selection_id FROM requests");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $applications = array_map(function ($row) {
        return [
            "id" => $row['id'],
            "title" => "Αίτηση #" . $row['id'],
            "description" => "Μάθημα επιλογής: " . $row['course_selection_id']
        ];
    }, $rows);

    echo json_encode([
        "success" => true,
        "applications" => $applications
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Σφάλμα: " . $e->getMessage()
    ]);
}