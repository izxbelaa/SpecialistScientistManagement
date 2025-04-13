<?php
require_once 'config.php';

// Get the updated user data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if all required fields are set
if (!isset($data['id']) || !isset($data['firstName']) || !isset($data['lastName']) || !isset($data['email']) || !isset($data['userType'])) {
    echo json_encode(["error" => "Missing required fields."]);
    exit;
}

$id = $data['id'];
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$middleName = $data['middleName'];
$email = $data['email'];
$userType = $data['userType'];
$disabledUser = $data['disabledUser'];

try {
    // Prepare the update query
    $query = "UPDATE users SET first_name = :firstName, last_name = :lastName, middle_name = :middleName, email = :email, type_of_user = :userType, disabled_user = :disabledUser WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':middleName', $middleName, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':userType', $userType, PDO::PARAM_STR);
    $stmt->bindParam(':disabledUser', $disabledUser, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();

    echo json_encode(["success" => "User updated successfully."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to update user: " . $e->getMessage()]);
}
?>
