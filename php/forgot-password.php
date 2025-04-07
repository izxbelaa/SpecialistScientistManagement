<?php
include 'config.php';
header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρο email.']);
    exit;
}

// Έλεγχος αν υπάρχει χρήστης με αυτό το email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Το email δεν βρέθηκε.']);
    exit;
}

// Δημιουργία token και αποθήκευση
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token=?, expires_at=?");
$stmt->bind_param("sssss", $email, $token, $expires, $token, $expires);
$stmt->execute();

// Σύνδεσμος επαναφοράς
$resetLink = "http://localhost/SpecialistScientistManagement/php/reset-password.php?token=$token";

// Εδώ θα έστελνες email. Προς το παρόν μόνο εμφανίζουμε:
echo json_encode([
    'success' => true,
    'message' => 'Ο σύνδεσμος επαναφοράς έχει σταλεί στο email σας.',
    'debug_link' => $resetLink // μόνο για development
]);
