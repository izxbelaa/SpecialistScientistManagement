<?php
session_start();
if ($_SERVER['REQUEST_METHOD']!=='POST') exit;

require __DIR__.'/config.php';

$code     = trim($_POST['code'] ?? '');
$new      = trim($_POST['new_password'] ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');

// 1) Verify code
if (!isset($_SESSION['verification_code'], $_SESSION['verification_email'])
 || $_SESSION['verification_code'] !== $code) {
  $_SESSION['reset_error'] = 'Άκυρος κωδικός.';
  header('Location: ../html/reset-password-form.php');
  exit;
}

// 2) Validate passwords
if ($new !== $confirm || strlen($new) < 8) {
  $_SESSION['reset_error'] = 'Οι κωδικοί πρέπει να ταιριάζουν και ≥8 chars.';
  header('Location: ../html/reset-password-form.php');
  exit;
}

// 3) Update the password in your users table
$hash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hash, $_SESSION['verification_email']]);

// 4) Fetch the user row so we can log them in
$userStmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
$userStmt->execute([$_SESSION['verification_email']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// 5) Clean up reset session vars
unset($_SESSION['verification_code'], $_SESSION['verification_email']);

// 6) Write login session vars
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['first_name'];    // or email, whichever you show in nav
$_SESSION['reset_success'] = 'Ο κωδικός άλλαξε με επιτυχία!';

// 7) Redirect to home (or dashboard)
header('Location: ../index.php');
exit;
