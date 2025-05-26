<?php
session_start();
if ($_SERVER['REQUEST_METHOD']!=='POST') exit;

require __DIR__.'/config.php';

$code     = trim($_POST['code'] ?? '');
$new      = trim($_POST['new_password'] ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');

// verify code
if (!isset($_SESSION['verification_code'],$_SESSION['verification_email'])
 || $_SESSION['verification_code']!==$code) {
  $_SESSION['reset_error']='Άκυρος κωδικός.';
  header('Location: ../html/reset-password-form.php');
  exit;
}

// validate passwords
if ($new!==$confirm || strlen($new)<8) {
  $_SESSION['reset_error']='Οι κωδικοί πρέπει να ταιριάζουν και ≥8 chars.';
  header('Location: ../html/reset-password-form.php');
  exit;
}

// update
$hash = password_hash($new,PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->execute([$hash,$_SESSION['verification_email']]);

unset($_SESSION['verification_code'], $_SESSION['verification_email']);
$_SESSION['reset_success']='Ο κωδικός άλλαξε με επιτυχία!';
header('Location: ../index.php');
exit;

