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
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../index.html");
                exit;
            } else {
                $errors[] = "Λανθασμένο email ή κωδικός.";
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα βάσης: " . $e->getMessage();
        }
    }
}
?>
