<?php
session_start();
require __DIR__.'/config.php';
require __DIR__.'/../vendor/autoload.php';  // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Get & validate the email
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['reset_error'] = 'Εισάγετε έγκυρο email.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

// 2. Lookup user by email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user) {
    // Don’t reveal whether the email exists
    $_SESSION['reset_info'] = 'Εάν υπάρχει λογαριασμός, έχετε λάβει email.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

// 3. Generate & store the one-time code
$code = random_int(100000, 999999);
$_SESSION['verification_code'] = (string)$code;
$_SESSION['reset_user_id']      = $user['id'];

// 4. Send the reset link via Gmail SMTP
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_ENCRYPTION;
    $mail->Port       = MAIL_PORT;

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress($email);
    $mail->Subject = 'CUT | Επαναφορά Κωδικού';
    $link = "https://{$_SERVER['HTTP_HOST']}/pages/reset-password-form.php?code=$code";
    $body  = "Γεια σας,\n\n";
    $body .= "Κάντε κλικ στον παρακάτω σύνδεσμο για να ορίσετε νέο κωδικό:\n\n";
    $body .= "$link\n\n";
    $body .= "Αν δεν ζητήσατε εσείς αυτήν την ενέργεια, αγνοήστε αυτό το μήνυμα.";
    $mail->Body    = nl2br(htmlspecialchars($body));
    $mail->AltBody = $body;
    $mail->send();

    $_SESSION['reset_info'] = 'Απεστάλη email με οδηγίες επαναφοράς.';
} catch (Exception $e) {
    $_SESSION['reset_error'] = 'Σφάλμα αποστολής: ' . $mail->ErrorInfo;
}

header('Location: ../pages/forgot-password.php');
exit;
