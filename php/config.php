<?php
$host = 'localhost';          // Or the internal IP, if required
$db   = 'cei326omada2';
$user = 'cei326omada2user';
$pass = 'Vp5!2BNFh!cHiN!U';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Connected to database successfully!";
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>