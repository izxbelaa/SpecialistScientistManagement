<?php
include 'config.php';
header('Content-Type: application/json');
session_start();


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    try {
      // Φέρνουμε τα Τμήματα
      $stmt = $pdo->query("SELECT * FROM departments");
      $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Φέρνουμε και τις Σχολές
      $academyStmt = $pdo->query("SELECT id, academy_name FROM academies ORDER BY academy_name");
      $academies = $academyStmt->fetchAll(PDO::FETCH_ASSOC);

      echo json_encode([
        'success' => true,
        'departments' => $departments,
        'academies' => $academies
      ]);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    break;

  case 'POST':
    $id = $_POST['department_id'] ?? null;
    $academy_id = $_POST['academy_id'] ?? null;
    $name = $_POST['department_name'] ?? null;
    $code = $_POST['department_code'] ?? null;

    if (!$academy_id || !$name || !$code) {
      echo json_encode(['success' => false, 'message' => 'Όλα τα πεδία είναι υποχρεωτικά.']);
      exit;
    }

    if ($id) {
      $stmt = $pdo->prepare("UPDATE departments SET academy_id = ?, department_name = ?, department_code = ? WHERE id = ?");
      $success = $stmt->execute([$academy_id, $name, $code, $id]);
    } else {
      $stmt = $pdo->prepare("INSERT INTO departments (academy_id, department_name, department_code) VALUES (?, ?, ?)");
      $success = $stmt->execute([$academy_id, $name, $code]);
    }

    echo json_encode(['success' => $success]);
    break;

  case 'DELETE':
    parse_str(file_get_contents("php://input"), $deleteData);
    $id = $deleteData['id'] ?? null;

    if (!$id) {
      echo json_encode(['success' => false, 'message' => 'Δεν δόθηκε ID.']);
      exit;
    }

    $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success]);
    break;

  default:
    echo json_encode(['success' => false, 'message' => 'Μη υποστηριζόμενη μέθοδος']);
}
?>