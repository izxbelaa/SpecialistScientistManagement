<?php
session_start();
include 'config.php'; // $pdo

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$id = intval($input['id']);
$status = intval($input['status']);

try {
    // Ενημέρωση status στον πίνακα candidate_users
    $stmt = $pdo->prepare("UPDATE candidate_users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // Αν έγινε αποδοχή της αίτησης, ενημέρωσε το type_of_user στον πίνακα users
    if ($status === 1) {
        // Βρες το user_id που αντιστοιχεί στον συγκεκριμένο candidate_user
        $stmt = $pdo->prepare("SELECT user_id FROM candidate_users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['user_id'])) {
            // Κάνε update το type_of_user στον πίνακα users
            $updateUserStmt = $pdo->prepare("UPDATE users SET type_of_user = 2 WHERE id = ?");
            $updateUserStmt->execute([$user['user_id']]);
        }
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
