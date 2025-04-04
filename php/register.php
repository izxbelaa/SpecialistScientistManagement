<?php
// register.php
include '../php/config.php';

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($first_name)) $errors[] = "Το όνομα είναι υποχρεωτικό.";
    if (empty($last_name)) $errors[] = "Το επώνυμο είναι υποχρεωτικό.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Μη έγκυρο email.";
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
        $errors[] = "Ο κωδικός δεν πληροί τα κριτήρια ασφαλείας.";
    }

    if (empty($errors)) {
        try {
            // Έλεγχος αν υπάρχει ήδη email
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Το email χρησιμοποιείται ήδη.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, type_of_user) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashed_password, 0]);
                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα κατά την εγγραφή: " . $e->getMessage();
        }
    }
}
?>