<?php
include 'config.php';
include '../php_classes/Users.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/auth/login.php");
}

// Skip if user type is already in session
if (isset($_SESSION['user_type'])) {
    return;
}

$userId = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$row = $stmt->fetch();

if ($row) {
    $user = new Users(
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['middle_name'],
        $row['email'],
        $row['password'],
        $row['type_of_user'],
        $row['logged_in'],
        $row['disabled_user']
    );

    $_SESSION['user_type'] = $user->getUserTypeName(); // Save readable role

} else {
    $_SESSION['user_type'] = 'Άγνωστος'; // Unknown
}
