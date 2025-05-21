<?php
// application_status.php
session_start();
require_once __DIR__ . '/../php/session_check.php';
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/fetch-application-status.php';

?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <title>Κατάσταση Αιτήσεων - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Application status page" name="keywords">
  <meta content="Page to display the status of user applications" name="description">
  <link href="../assets/img/logo.png" rel="icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css.php" rel="stylesheet">
</head>
<body>
  <!-- Spinner -->
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
          <h1 class="display-3 text-white animated slideInDown">Κατάσταση Αιτήσεων</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a class="text-white" href="../index.php">Ταμπλό</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">Κατάσταση Αιτήσεων</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Content -->
 <div class="container my-5">
  <div class="card border shadow-sm">
    <div class="card-header bg-light d-flex align-items-center">
      <i class="fas fa-list me-2 text-primary"></i>
      <h5 class="mb-0 fw-bold">Λίστα Αιτήσεων</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle mb-0 text-center">
        <thead style="background-color: #e6f2ff;">
          <tr>
            <th style="width: 10%;">#</th>
            <th>Τίτλος Αίτησης</th>
            <th>Κατάσταση</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($applications)): ?>
            <?php foreach ($applications as $index => $app): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($app['request_name']) ?></td>
                <td>
                  <?php
                    $status = $app['katastasi'];
                    $badgeClass = match ($status) {
                      'Εγκρίθηκε'    => 'bg-success',
                      'Απορρίφθηκε'  => 'bg-danger',
                      default        => 'bg-warning text-dark',
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-muted">Δεν υπάρχουν αιτήσεις.</td>
            </tr>
          <?php endif; ?>
</tbody>

      </table>
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
</body>
</html>
