<?php
require_once 'config.php';

// Get the user data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if the user ID is provided
if (!isset($data['id'])) {
    echo json_encode(["error" => "User ID is missing."]);
    exit;
}

$id = $data['id'];

try {
    // Update the user to be disabled
    $query = "UPDATE users SET disabled_user = 1 WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => "User disabled successfully."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to disable user: " . $e->getMessage()]);
}
?>
