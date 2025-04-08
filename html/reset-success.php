<?php
session_start();
$message = $_SESSION['reset_success'] ?? null;
if (!$message) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Επιτυχία Επαναφοράς</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <meta http-equiv="refresh" content="2;url=../index.php">
</head>
<body>
  <div class="container mt-5">
    <div class="alert alert-success text-center" role="alert">
      <?php echo htmlspecialchars($message); ?>
    </div>
  </div>
</body>
</html>
