<?php
header('Content-Type: application/json');
include 'config.php';
include '../php_classes/Users.php'; // Path to your class
$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name   = trim($_POST['first_name'] ?? '');
    $middle_name  = trim($_POST['middle_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = "Το όνομα είναι υποχρεωτικό.";
    }

    if (empty($last_name)) {
        $errors['last_name'] = "Το επώνυμο είναι υποχρεωτικό.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Μη έγκυρο email.";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
        $errors['password'] = "Ο κωδικός πρέπει να περιλαμβάνει τουλάχιστον 8 χαρακτήρες, ένα κεφαλαίο, ένα πεζό και ένα σύμβολο.";
    }

    if ($password !== $confirm_pass) {
        $errors['confirm_password'] = "Οι κωδικοί δεν ταιριάζουν.";
    }

    // Check for duplicate email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors['email'] = "Το email χρησιμοποιείται ήδη.";
            }
        } catch (PDOException $e) {
            $errors['general'] = "Σφάλμα στη βάση: " . $e->getMessage();
        }
    }

    // Insert into DB using Users class values
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $user = new Users(
                null,               // id
                $first_name,
                $last_name,
                $middle_name,
                $email,
                $hashed_password,
                0,                  // type_of_user
                0                   // disabled_user
            );

            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, middle_name, last_name, email, password, type_of_user, disabled_user)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user->first_name,
                $user->middle_name,
                $user->last_name,
                $user->email,
                $user->password,
                $user->type_of_user,
                $user->disabled_user
            ]);

            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'errors' => ['general' => "Σφάλμα κατά την εγγραφή: " . $e->getMessage()]]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
}
?>
