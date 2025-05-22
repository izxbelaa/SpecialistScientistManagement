<?php
require_once 'config.php';
header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
file_put_contents('php_error.log', print_r($input, true), FILE_APPEND); // Debug log

// Validate required fields
$required = ['firstName', 'lastName', 'email', 'userType', 'password'];
foreach ($required as $field) {
    if (!isset($input[$field]) || $input[$field] === '') {
        echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
        exit;
    }
}

$firstName = $input['firstName'];
$lastName = $input['lastName'];
$middleName = isset($input['middleName']) ? $input['middleName'] : null;
$email = $input['email'];
$userType = (int)$input['userType'];
$disabledUser = isset($input['disabledUser']) ? (int)$input['disabledUser'] : 0;
$password = $input['password'];

// Check if email already exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Email already exists']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, middle_name, email, type_of_user, disabled_user, password) VALUES (?, ?, ?, ?, ?, ?, ?)');
try {
    $stmt->execute([$firstName, $lastName, $middleName, $email, $userType, $disabledUser, $hashedPassword]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 