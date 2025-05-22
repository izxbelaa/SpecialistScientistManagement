<?php
require_once 'config.php';
session_start();

if (!isset($_POST['template_id']) || !isset($_SESSION['user_id'])) {
    header("Location: ../html/application.php?status=error&message=missing");
    exit;
}

$template_id = $_POST['template_id'];
$user_id = $_SESSION['user_id'];
$courses = $_POST['courses'] ?? [];
$fileUploaded = $_FILES['cv'] ?? null;

try {
    // 1. Insert into requests
    $stmt = $pdo->prepare("INSERT INTO requests (template_id) VALUES (?)");
    $stmt->execute([$template_id]);
    $request_id = $pdo->lastInsertId();

    // 2. Insert into request_course (many-to-many)
    $stmtCourse = $pdo->prepare("INSERT INTO request_course (request_id, course_id) VALUES (?, ?)");
    foreach ($courses as $course_id) {
        $stmtCourse->execute([$request_id, $course_id]);
    }

    // 3. Insert CV into request_uploadedfile
    if ($fileUploaded && $fileUploaded['error'] === UPLOAD_ERR_OK) {
        $fileData = file_get_contents($fileUploaded['tmp_name']);
        $stmtFile = $pdo->prepare("INSERT INTO request_uploadedfile (request_id, file) VALUES (?, ?)");
        $stmtFile->bindParam(1, $request_id, PDO::PARAM_INT);
        $stmtFile->bindParam(2, $fileData, PDO::PARAM_LOB);
        $stmtFile->execute();
    }

    // 4. Insert into candidate_users
    $stmtCandidate = $pdo->prepare("INSERT INTO candidate_users (user_id, request_id, status) VALUES (?, ?, 0)");
    $stmtCandidate->execute([$user_id, $request_id]);

    header("Location: ../html/application.php?status=success");
    exit;
} catch (Exception $e) {
    header("Location: ../html/application.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
