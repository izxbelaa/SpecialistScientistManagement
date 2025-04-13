<?php
session_start();
header('Content-Type: application/json');

// Include database connection (PDO)
require 'config.php';

// Include PHPMailer classes
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get and sanitize input
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρο email.']);
    exit;
}

// Check if user exists (using PDO)
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Το email δεν βρέθηκε.']);
    exit;
}

// Generate verification code and store in session
$code = mt_rand(100000, 999999);
$_SESSION['verification_code'] = (string)$code;
$_SESSION['verification_email'] = $email;

// Send verification email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = '290fe509a58d86'; // Your Mailtrap username
    $mail->Password   = '74cdc42485d2e0'; // Your Mailtrap password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 2525;

    $mail->setFrom('noreply@example.com', 'CUT - Special Scientists');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset';

    $mail->Body = "
    <html>
  <body style='font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;'>
    <div style='background: white; border-radius: 8px; padding: 30px; max-width: 600px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>
      <h2 style='color: #0054a6;'>Hello,</h2>
      <p>We received a request to reset your password.</p>
      <p>Your verification code is:</p>
      <h1 style='color: #0054a6; letter-spacing: 2px;'>$code</h1>
      <p>Please enter this code in the form to continue.</p>
      <p>If you did not make this request, you can safely ignore this email.</p>
      <br>
      <p style='font-size: 12px; color: #999;'>CUT - Special Scientists Platform</p>
    </div>
  </body>
</html>

    ";

    $mail->AltBody = "Ο κωδικός επαλήθευσης είναι: $code";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Ο κωδικός έχει σταλεί στο email σας.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Σφάλμα κατά την αποστολή: ' . $mail->ErrorInfo]);
}
?>
