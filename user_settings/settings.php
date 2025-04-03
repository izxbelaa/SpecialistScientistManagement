<?php
include '../config.php'; // σύνδεση με τη βάση

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($first_name)) $errors[] = "Το όνομα είναι υποχρεωτικό.";
    if (empty($last_name)) $errors[] = "Το επώνυμο είναι υποχρεωτικό.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Μη έγκυρο email.";
    if (!preg_match('/^\d+$/', $phone)) $errors[] = "Ο αριθμός πρέπει να περιέχει μόνο αριθμούς.";
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
        $errors[] = "Ο κωδικός δεν πληροί τα κριτήρια ασφαλείας.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO Users (first_name, last_name, email, mobile_phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hashed_password);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Αποτυχία αποθήκευσης: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
</head>
<body>
    <!-- Navbar Inclusion -->
    <?php include '../components/navbar.html'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0">
                <!-- Sidebar Inclusion -->
                <?php include '../components/sidebar.html'; ?>
            </div>

            <div class="col-md-10 p-4">
                <h2 class="mb-4">User Settings</h2>

                <!-- Alert messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success">Οι αλλαγές αποθηκεύτηκαν με επιτυχία.</div>
                <?php endif; ?>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endforeach; ?>

 <!--User Settings-->               
 <div class="card shadow-sm ms-3 mb-4" style="max-width: 900px;">
  <div class="card-body">
    <h5 class="card-title mb-3">Επεξεργασία Προφίλ</h5>
    <form method="POST" action="">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Όνομα</label>
            <input type="text" class="form-control" name="first_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Κωδικός Πρόσβασης</label>
            <input type="password" class="form-control" name="password">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Επώνυμο</label>
            <input type="text" class="form-control" name="last_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Αριθμός επικοινωνίας</label>
            <input type="text" class="form-control" name="phone" required>
          </div>
          <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Αποθήκευση</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="card shadow-sm ms-3" style="max-width: 900px;">
  <div class="card-body">
    <h5 class="card-title mb-3">Ενημέρωση Κωδικού Πρόσβασης</h5>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Τρέχων Κωδικός Πρόσβασης</label>
        <input type="password" class="form-control" name="current_password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Νέος Κωδικός Πρόσβασης</label>
        <input type="password" class="form-control" name="new_password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Επιβεβαίωση Κωδικού</label>
        <input type="password" class="form-control" name="confirm_password" required>
      </div>
      <div class="text-end">
        <button type="submit" name="change_password" class="btn btn-primary">Αποθήκευση</button>
      </div>
    </form>
 </div>
 </div>
</div>
</body>
</html>
