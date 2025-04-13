<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($newPassword !== $confirmPassword) {
        $_SESSION['reset_error'] = "Οι κωδικοί δεν ταιριάζουν.";
        header("Location: ../html/reset-password-form.php");
        exit;
    }

    if (!isset($_SESSION['verification_email'])) {
        $_SESSION['reset_error'] = "Η συνεδρία έληξε. Ξεκινήστε ξανά.";
        header("Location: ../html/forgot-password.php");
        exit;
    }

    $email = $_SESSION['verification_email'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    try {
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Get user info (id + first_name)
        $userStmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch();

        if (!$user) {
            $_SESSION['reset_error'] = "Ο χρήστης δεν βρέθηκε.";
            header("Location: ../html/reset-password-form.php");
            exit;
        }

        // Set login session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['first_name'];

        // Clean up
        unset($_SESSION['verification_email']);
        unset($_SESSION['verification_code']);

        $_SESSION['reset_success'] = "Ο κωδικός σας αλλάχθηκε με επιτυχία!";
        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['reset_error'] = "Σφάλμα βάσης δεδομένων: " . $e->getMessage();
        header("Location: ../html/reset-password-form.php");
        exit;
    }
} else {
    echo "Μη εξουσιοδοτημένο αίτημα.";
}
