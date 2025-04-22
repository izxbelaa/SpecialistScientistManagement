<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "Διαχειριστής") {
    http_response_code(403);
    echo "Unauthorized.";
    exit;
}

try {
    $site_color = $_POST['site_color'] ?? '#06BBCC';
    $light_color = $_POST['light_color'] ?? '#F0FBFC';
    $dark_color = $_POST['dark_color'] ?? '#181d38';

    // Only update (assuming exactly one row exists)
    $stmt = $pdo->prepare("UPDATE colors SET site_color = ?, light_color = ?, dark_color = ? LIMIT 1");
    $stmt->execute([$site_color, $light_color, $dark_color]);

    // Update logos if uploaded
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        move_uploaded_file($_FILES['logo']['tmp_name'], '../assets/img/logo.png');
    }

    if (isset($_FILES['logocut']) && $_FILES['logocut']['error'] === 0) {
        move_uploaded_file($_FILES['logocut']['tmp_name'], '../assets/img/logocut.png');
    }

    echo "Success";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error saving colors: " . $e->getMessage();
}
