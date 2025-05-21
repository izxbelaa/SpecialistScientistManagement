
<?php
session_start();
require_once __DIR__ . '/../php/session_check.php';
require_once __DIR__ . '/../php/config.php';
include '../php/config.php';

$needsProfileCompletion = false;
$userData = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($_SESSION['username']) && isset($userData['name'])) {
    $_SESSION['username'] = $userData['name'];
}


    $requiredFields = [
        'dob', 'gender', 'social_security_number', 'cypriot_id', 'postal_code',
        'street_address', 'city', 'country', 'nationality', 'mobile_phone', 'email'
    ];

    foreach ($requiredFields as $field) {
        if (empty($userData[$field])) {
            $needsProfileCompletion = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Applications - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Submission period page" name="keywords">
  <meta content="Submission period page for Cyprus University of Technology" name="description">
  <!-- Favicon -->
  <link href="../assets/img/logo.png" rel="icon">
  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css.php" rel="stylesheet">
  <style>
    .search-header {
      background-color: #ffffff;
      padding: 2rem 0;
      margin-bottom: 2rem;
    }
    .search-box-wrapper {
      max-width: 700px;
      margin: 0 auto;
      position: relative;
    }
    .search-box-wrapper input {
      padding-right: 40px;
      border-radius: 8px;
    }
    .search-box-wrapper i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #4c8ad5;
    }
    .btn-orange {
      background-color: #0099ff;
      color: white;
      border: none;
    }
    .btn-orange:hover {
      background-color: #f3ece7;
    }
  </style>
</head>
<body>
  <!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
  <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
    <span class="sr-only">Loading...</span>
  </div>
</div>


  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <img src="../assets/img/logocut.png" alt="Tepak Logo" width="150" height="60" class="d-inline-block align-top">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="../index.php" class="nav-item nav-link">ΑΡΧΙΚΗ</a>
        <a href="about.php" class="nav-item nav-link">ΣΧΕΤΙΚΑ</a>
        <a href="application.php" class="nav-item nav-link active">APPLICATIONS</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ΚΑΤΑΧΩΡΙΣΕΙΣ</a>
          <div class="dropdown-menu fade-down m-0">
                                    <a href="./html/courses.php" class="dropdown-item">Μαθήματα</a>

            <a href="departments.php" class="dropdown-item">Τμήματα</a>
                                        <a href="academies.php" class="dropdown-item">Σχολές</a>
          </div>
        </div>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Σελιδες Διαχειριστη </a>
          <div class="dropdown-menu fade-down m-0">
            <a href="assign-reviewers.php" class="dropdown-item">Ανάθεση Αξιολογητών</a>
            <a href="tables.php" class="dropdown-item">Πινακας Χρηστων</a>
            <a href="requests-admin.php" class="dropdown-item">Διαχειριση Αιτησεων</a>
          </div>
        </div>
        <a href="admin-settings.php" class="nav-item nav-link">Ρυθμισεις Διαχειριστη</a>
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="../php/logout.php">Αποσύνδεση</a></li>
              <li><a class="dropdown-item" href="edit_user.php">Επεξεργασία Προφίλ</a></li>
            </ul>
          </li>
        <?php else: ?>
          <a href="auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block"> Σύνδεση <i class="fa fa-arrow-right ms-3"></i></a>
        <?php endif; ?>
      </div>

 


  </div>
</nav>
  <!-- Navbar End -->

<!-- Header -->
<div class="container-fluid bg-primary py-5 mb-5 page-header">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        <h1 class="display-3 text-white animated slideInDown">Αίτηση</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a class="text-white" href="../../index.php">Ταμπλό</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page">Αιτήσεις</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- Application Form -->
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h2 class="text-center mb-4">Αίτηση</h2>
        <?php if ($needsProfileCompletion): ?>
  <div class="alert alert-warning text-center" role="alert">
    Παρακαλώ συμπληρώστε πρώτα τα στοιχεία του προφίλ σας πριν υποβάλετε αίτηση.
    <a href="edit_user.php" class="alert-link">Μετάβαση στο προφίλ</a>
  </div>
<?php endif; ?>

      <form action="../php/submit-application.php" method="POST" enctype="multipart/form-data">


          <div class="mb-3">
            <label for="templateSelect" class="form-label">Επιλέξτε Αίτηση</label>
            <select class="form-select" id="templateSelect" name="template_id" required>
              <option value="">-- Επιλέξτε --</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Περιγραφή</label>
            <textarea class="form-control" id="description" name="description" rows="4" readonly></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="startDate" class="form-label">Ημερομηνία Έναρξης</label>
              <input type="date" class="form-control" id="startDate" name="start_date" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label for="endDate" class="form-label">Ημερομηνία Λήξης</label>
              <input type="date" class="form-control" id="endDate" name="end_date" readonly>
            </div>
          </div>
          <div class="mb-4">
            <label for="courses" class="form-label">Μαθήματα</label>
            <select class="form-select" id="courses" name="courses[]" multiple required></select>
            <small class="text-muted">Κρατήστε Ctrl (ή Cmd σε Mac) για πολλαπλή επιλογή.</small>
          </div>
      <div class="mb-3">
  <label for="cv" class="form-label">Επισύναψη Βιογραφικού (CV)</label>
  <input class="form-control" type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
</div>


          <div class="mb-4" id="academyInfo"></div>
          <div class="text-center">
           <button type="submit" class="btn btn-primary px-5" <?= $needsProfileCompletion ? 'disabled' : '' ?>>Υποβολή</button>

          </div>
        </form>
      </div>
    </div>
  </div>




  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../lib/wow/wow.min.js"></script>
<script src="../lib/easing/easing.min.js"></script>
<script src="../lib/waypoints/waypoints.min.js"></script>
<script src="../lib/owlcarousel/owl.carousel.min.js"></script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/application.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/application-filter.js"></script>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("status");

    if (status === "success") {
      Swal.fire({
        icon: "success",
        title: "Η αίτηση καταχωρήθηκε επιτυχώς!",
        showConfirmButton: false,
        timer: 2000
      });
    }

    if (status === "error") {
      const message = urlParams.get("message") || "Η αίτηση δεν καταχωρήθηκε. Προσπαθήστε ξανά.";
      Swal.fire({
        icon: "error",
        title: "Σφάλμα",
        text: decodeURIComponent(message),
        confirmButtonText: "Εντάξει"
      });
    }

    // Remove query string to avoid repeated SweetAlert on refresh
    if (status) {
      const cleanUrl = window.location.origin + window.location.pathname;
      window.history.replaceState({}, document.title, cleanUrl);
    }
  });
</script>

</body>
</html>
