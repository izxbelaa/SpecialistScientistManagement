<?php
session_start();
header('Content-Type: application/json');

require __DIR__.'/config.php';
require __DIR__.'/../vendor/autoload.php';  // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1) parse & validate
$body = json_decode(file_get_contents('php://input'), true);
$email = filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
  echo json_encode(['success'=>false,'message'=>'Έγκυρο email απαιτείται.']);
  exit;
}

// 2) (opt) check user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user) {
  // still pretend we sent, for security
  echo json_encode(['success'=>true,'message'=>'Έχουμε στείλει έναν κωδικό αν υπάρχει λογαριασμός.']);
  exit;
}

// 3) make & store code
$code = random_int(100000,999999);
$_SESSION['verification_code'] = (string)$code;
$_SESSION['verification_email']= $email;

// 4) send via Gmail SMTP
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
  $mail->Subject = 'CUT | Κωδικός Επαλήθευσης';
  $html  = "<p>Ο κωδικός επαλήθευσης είναι:</p>";
  $html .= "<h2 style='color:#0054a6;'>$code</h2>";
  $mail->Body    = $html;
  $mail->AltBody = "Ο κωδικός επαλήθευσης είναι: $code";

  $mail->send();
  echo json_encode(['success'=>true,'message'=>'Έχουμε στείλει τον κωδικό στο email.']);
} catch (Exception $e) {
  echo json_encode(['success'=>false,'message'=>'Σφάλμα αποστολής: '.$mail->ErrorInfo]);
}
