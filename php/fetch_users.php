<?php
require_once 'config.php';  // Correct path to config.php (inside /php/ folder)
require_once '../php_classes/Users_admin.php';  // Correct path to Users.php (go up one directory to /php_classes/)
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

// Ensure the database connection exists
if (!isset($pdo)) {
    echo json_encode(["error" => "Database connection not established."]);
    exit;
}

// Create Users object and fetch data
$usersObj = new Users($pdo);
$users = $usersObj->getAllUsers();

// Handle cases where no users are returned
if (!$users || isset($users['error'])) {  // Check if an error exists
    echo json_encode(["error" => "No users found"]);
    exit;
}

// Ensure that `id` is included in every user entry
foreach ($users as &$user) {
    if (!isset($user['id'])) {
        $user['id'] = null; // Set to null if missing
    }
}

echo json_encode($users);  // Return the fetched users in JSON format
?>
