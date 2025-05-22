<?php
include 'config.php';
include '../php_classes/Users.php';
session_start();

header('Content-Type: application/json');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Μη έγκυρο email.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {
                $user = new Users(
                    $row['id'], $row['first_name'], $row['last_name'], $row['middle_name'],
                    $row['email'], $row['password'], $row['type_of_user'],
                    $row['disabled_user']
                );

                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->first_name;
                $_SESSION['user_type'] = $user->getUserTypeName();

                echo json_encode([
                    "success" => true,
                    "redirect" => "../../index.php",
                     ]);
                exit;
            } else {
                $errors[] = "Λανθασμένο email ή κωδικός.";
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα βάσης: " . $e->getMessage();
        }
    }
}

echo json_encode([
    "success" => false,
    "message" => implode(" ", $errors)
]);
exit;
