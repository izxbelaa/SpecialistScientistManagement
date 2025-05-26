<?php
// Load Composer packages
require __DIR__ . '/../vendor/autoload.php';

// Load your config (PDO + MAIL constants)
require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    // Debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    // SMTP setup
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_ENCRYPTION;
    $mail->Port       = MAIL_PORT;

    // Recipients
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress('your-real-email@example.com', 'Your Name');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from CUT';
    $mail->Body    = '<p>Είναι ένα δοκιμαστικό email από <b>CUT</b>.</p>';
    $mail->AltBody = 'Είναι ένα δοκιμαστικό email από CUT.';

    $mail->send();
    echo '<p style="color:green">Το μήνυμα στάλθηκε με επιτυχία!</p>';
} catch (Exception $e) {
    echo "<p style='color:red'>Σφάλμα αποστολής: {$mail->ErrorInfo}</p>";
}
