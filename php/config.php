<?php
// — Database —
$host    = 'localhost';
$db      = 'cei326omada2';
$user    = 'cei326omada2user';
$pass    = 'Vp5!2BNFh!cHiN!U';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die("DB Connection failed: " . $e->getMessage());
}

// — Mail (Gmail SMTP) —
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'noreplyspecialscientist@gmail.com');
define('MAIL_PASSWORD',   'bavu rxze pjhm ctpv');  // ← your 16-char App Password, no spaces
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM',       'noreplyspecialscientist@gmail.com');
define('MAIL_FROM_NAME',  'CUT Password Reset');
