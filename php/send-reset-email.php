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
  // 1) Tell PHPMailer to embed the image:
$mail->addEmbeddedImage(
  __DIR__ . '/../assets/img/whitelogo.png',
  'logo_cid'
);
  $mail->Host       = MAIL_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = MAIL_USERNAME;
  $mail->Password   = MAIL_PASSWORD;
  $mail->SMTPSecure = MAIL_ENCRYPTION;
  $mail->Port       = MAIL_PORT;

  $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
  $mail->addAddress($email);
  $mail->Subject = 'Special Scientist System CUT | Κωδικός Επαλήθευσης';
 // Build the HTML email
$html = '
<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8">
    <title>Κωδικός Επαλήθευσης</title>
  </head>
  <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
      <tr>
        <td align="center">
          <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
            <!-- Header with logo -->
            <tr>
              <td align="center" style="padding:30px 0;background:#0054a6;">
                 <img src="cid:logo_cid" width="180" alt="CUT Logo">
              </td>
            </tr>
            <!-- Title -->
            <tr>
              <td style="padding:40px 30px 20px;">
                <h1 style="margin:0;color:#333333;font-size:24px;text-align:center;">Επαναφορά Κωδικού</h1>
              </td>
            </tr>
            <!-- Body text -->
            <tr>
              <td style="padding:0 30px 20px;">
                <p style="margin:0 0 15px;color:#555555;font-size:16px;line-height:1.5;">
                  Γεια σας,
                </p>
                <p style="margin:0 0 15px;color:#555555;font-size:16px;line-height:1.5;">
                  Ο κωδικός επαλήθευσης για να αλλάξετε τον κωδικό πρόσβασής σας είναι:
                </p>
                <div style="text-align:center;margin:20px 0;">
                  <span style="
                    display:inline-block;
                    padding:15px 25px;
                    font-size:28px;
                    font-weight:bold;
                    color:#0054a6;
                    background:#e6f7ff;
                    border:2px dashed #00b8d4;
                    border-radius:4px;
                    letter-spacing:4px;
                  ">
                    ' . htmlspecialchars($code) . '
                  </span>
                </div>
                <p style="margin:0 0 15px;color:#555555;font-size:16px;line-height:1.5;">
                  Εισάγετε αυτόν τον κωδικό στην αντίστοιχη φόρμα για να συνεχίσετε.
                </p>
                <p style="margin:0;color:#999999;font-size:14px;line-height:1.4;">
                  Αν δεν ζητήσατε εσείς αυτήν την ενέργεια, μπορείτε να αγνοήσετε αυτό το μήνυμα.
                </p>
              </td>
            </tr>
            <!-- Footer -->
            <tr>
              <td style="background:#f4f4f4;padding:20px 30px;text-align:center;font-size:12px;color:#888888;">
                © ' . date('Y') . ' Cyprus University of Technology – Special Scientists Platform
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>';

// Plain-text fallback
$alt = "Κωδικός Επαλήθευσης: $code\n\n"
     . "Εισάγετε το στη φόρμα για να συνεχίσετε.\n"
     . "Αν δεν το ζητήσατε, αγνοήστε αυτό το email.";

$mail->isHTML(true);
$mail->Body    = $html;
$mail->AltBody = $alt;


  $mail->send();
  echo json_encode(['success'=>true,'message'=>'Έχουμε στείλει τον κωδικό στο email.']);
} catch (Exception $e) {
  echo json_encode(['success'=>false,'message'=>'Σφάλμα αποστολής: '.$mail->ErrorInfo]);
}
