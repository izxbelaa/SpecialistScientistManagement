<?php
require_once 'config.php';
header('Content-Type: application/json');

// 🧠 Action dispatcher (GET and POST)
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// ✅ GET departments + academies (used on page load)
if ($action === 'fetch_all') {
    try {
        $stmt1 = $pdo->query("SELECT * FROM Departments");
        $departments = $stmt1->fetchAll();

        $stmt2 = $pdo->query("SELECT * FROM Academies");
        $academies = $stmt2->fetchAll();

        echo json_encode([
            'success' => true,
            'departments' => $departments,
            'academies' => $academies
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// ✅ SAVE or UPDATE department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'save' || $action === 'edit')) {
    $id = $_POST['department_id'] ?? null;
    $academy_id = $_POST['academy_id'] ?? null;
    $department_name = trim($_POST['department_name'] ?? '');
    $department_code = trim($_POST['department_code'] ?? '');

    if ( !$department_name || !$department_code) {
        echo json_encode(['success' => false, 'message' => 'Συμπληρώστε όλα τα πεδία.']);
        exit;
    }

    try {
        if ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("UPDATE Departments SET academy_id = ?, department_name = ?, department_code = ? WHERE id = ?");
            $stmt->execute([$academy_id, $department_name, $department_code, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO Departments (academy_id, department_name, department_code) VALUES (?, ?, ?)");
            $stmt->execute([$academy_id, $department_name, $department_code]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Σφάλμα βάσης: ' . $e->getMessage()]);
    }
    exit;
}

// ✅ DELETE department
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Convert DELETE body to POST-style array
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing ID.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM Departments WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Σφάλμα κατά τη διαγραφή.']);
    }
    exit;
}

// ❌ Unknown action
echo json_encode(['success' => false, 'message' => 'Μη έγκυρη ενέργεια.']);
exit;
