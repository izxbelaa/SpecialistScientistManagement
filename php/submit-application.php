<?php
require_once 'config.php';
session_start();

if (!isset($_POST['template_id']) || !isset($_SESSION['user_id'])) {
    header("Location: ../html/application.php?status=error&message=missing");
    exit;
}

$template_id = $_POST['template_id'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO requests (template_id, consent_forms_id) VALUES (?, NULL)");
    $stmt->execute([$template_id]);

    header("Location: ../html/application.php?status=success");
    exit;
} catch (Exception $e) {
    header("Location: ../html/application.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
