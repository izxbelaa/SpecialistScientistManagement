<?php
// login.php (backend μόνο)
session_start();
include '../php/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρη διεύθυνση email.";
    }

    if (empty($password)) {
        $errors[] = "Ο κωδικός είναι υποχρεωτικός.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];

                header("Location: ../public/index.html");
                exit();
            } else {
                $errors[] = "Μη έγκυρο email ή κωδικός πρόσβασης.";
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα σύνδεσης με βάση: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header("Location: ../public/login.html");
        exit();
    }
} else {
    header("Location: ../public/login.html");
    exit();
}
?>
