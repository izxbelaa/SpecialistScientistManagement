<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['general' => 'Unauthorized access']]);
    exit;
}

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $old_password = trim($_POST['old_password'] ?? '');
    $password = trim($_POST['password'] ?? '');
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

    // Get current user data for password verification
    try {
        $stmt = $pdo->prepare("SELECT email, password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors['general'] = "Σφάλμα στη βάση: " . $e->getMessage();
    }

    // Verify current password for email changes or password changes
    if (empty($errors) && ($email !== $current_user['email'] || !empty($password))) {
        if (empty($old_password)) {
            $errors['old_password'] = "Ο τρέχων κωδικός απαιτείται για την αλλαγή email ή κωδικού.";
        } else if (!password_verify($old_password, $current_user['password'])) {
            $errors['old_password'] = "Ο τρέχων κωδικός είναι λανθασμένος.";
        }
    }

    // Check if email is already taken by another user
    if (empty($errors) && $email !== $current_user['email']) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors['email'] = "Το email χρησιμοποιείται ήδη.";
            }
        } catch (PDOException $e) {
            $errors['general'] = "Σφάλμα στη βάση: " . $e->getMessage();
        }
    }

    // Password validation if new password is provided
    if (!empty($password)) {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
            $errors['password'] = "Ο κωδικός πρέπει να περιλαμβάνει τουλάχιστον 8 χαρακτήρες, ένα κεφαλαίο, ένα πεζό και ένα σύμβολο.";
        }

        if ($password !== $confirm_pass) {
            $errors['confirm_password'] = "Οι κωδικοί δεν ταιριάζουν.";
        }
    }

    // Update user in database
    if (empty($errors)) {
        try {
            if (!empty($password)) {
                // Update with password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET first_name = ?, middle_name = ?, last_name = ?, email = ?, password = ?
                    WHERE id = ?
                ");
                $stmt->execute([$first_name, $middle_name, $last_name, $email, $hashed_password, $user_id]);
            } else {
                // Update without password
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET first_name = ?, middle_name = ?, last_name = ?, email = ?
                    WHERE id = ?
                ");
                $stmt->execute([$first_name, $middle_name, $last_name, $email, $user_id]);
            }

            // Update session username if first name changed
            $_SESSION['username'] = $first_name;

            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'errors' => ['general' => "Σφάλμα κατά την ενημέρωση: " . $e->getMessage()]]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
}
?> 