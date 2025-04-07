<?php
include 'config.php';
session_start();


$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρο email.";
    }

    if (empty($errors)) {
        try {
            // Adjust the query to also fetch the username for the greeting.
            // Make sure your "users" table has a "username" column.
            $stmt = $pdo->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['first_name'];  // Store the username for the greeting
                header("Location: ../index.php"); // Redirect to homepage or dashboard
                exit;
            } else {
                $errors[] = "Λανθασμένο email ή κωδικός.";
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα βάσης: " . $e->getMessage();
        }
    }
}

// If there are errors, store them in session and redirect back to the login page.
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header("Location: ../html/auth/login.php");
    exit;
}
?>
