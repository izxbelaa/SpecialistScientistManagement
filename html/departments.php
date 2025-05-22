<?php include '../php/session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Configure Submission Period - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Submission period page" name="keywords">
  <meta content="Submission period page for Cyprus University of Technology" name="description">
  <!-- Favicon -->
  <link href="../assets/img/logo.png" rel="icon">
  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Libraries Stylesheet -->
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <!-- Customized Bootstrap Stylesheet -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- Template Stylesheet -->
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

<body class="departments-page">
  <!-- Spinner Start -->
  <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->

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
        <a href="../index.php" class="nav-item nav-link">Αρχικη</a>
        <a href="about.php" class="nav-item nav-link">Σχετικα</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Αιτησεις</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="application.php" class="dropdown-item">Συμπλήρωση Αιτήσεων</a>
            <a href="application-status.php" class="dropdown-item">Κατάσταση Αιτήσεων</a>
            <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Διαχειριστής' || $_SESSION['user_type'] == 'Επιθεωρητής')): ?>
            <a href="application_inspection.php" class="dropdown-item">Επιθεώρηση Αιτήσεων</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Moodle</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="lms_sync.php" class="dropdown-item">Πρόσβαση στο Moodle</a>
            <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Διαχειριστής'  || $_SESSION['user_type'] == 'Προϊστάμενος Ανθρώπινου Δυναμικού')): ?>
            <a href="lms-reports.php" class="dropdown-item">Αναφορές LMS</a>
            <?php endif; ?>
          </div>
        </div>
        <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'Διαχειριστής' || $_SESSION['user_type'] == 5)): ?>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρισεις</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="courses.php" class="dropdown-item">Μαθήματα</a>
            <a href="departments.php" class="dropdown-item active">Τμήματα</a>
            <a href="academies.php" class="dropdown-item">Σχολές</a>
          </div>
        </div>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Σελιδες Διαχειριστη</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="assign-reviewers.php" class="dropdown-item">Ανάθεση Αξιολογητών</a>
            <a href="tables.php" class="dropdown-item">Πινακας Χρηστων</a>
            <a href="requests-admin.php" class="dropdown-item">Διαχειριση Αιτησεων</a>
            <a href="statistics.php" class="dropdown-item">Στατιστικά</a>
          </div>
        </div>
        <a href="admin-settings.php" class="nav-item nav-link">Ρυθμισεις Διαχειριστη</a>
        <?php endif; ?>
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

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header departments-header">
        <div class="container py-5">
          <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
              <h1 class="display-3 text-white animated slideInDown">Διαχείριση Τμημάτων</h1>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                  <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                  <li class="breadcrumb-item text-white active" aria-current="page">Διαχείριση Τμημάτων</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <!-- Header End -->

<div class="container mt-4">
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0">Λιστα Τμημάτων</h2>
        <button class="btn btn-info text-white" id="addDepartmentBtn" data-bs-toggle="modal" data-bs-target="#departmentModal">
          <i class="fas fa-plus me-2"></i>Προσθήκη Νέου Τμήματος
        </button>
      </div>
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <input type="text" class="form-control" id="searchInput" style="max-width:350px;min-width:200px;" placeholder="Αναζήτηση Τμημάτων..." oninput="searchDepartments()">
        <select id="entriesPerPage" class="form-control" style="max-width:150px;min-width:120px;" onchange="loadDepartments()">
          <option value="5">5 Τμήματα</option>
          <option value="10" selected>10 Τμήματα</option>
          <option value="20">20 Τμήματα</option>
          <option value="100">100 Τμήματα</option>
        </select>
      </div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th style="width: 70px; min-width: 60px;">Α/Α</th>
              <th>Όνομα Τμήματος</th>
              <th>Κωδικός</th>
              <th>Ακαδημία</th>
              <th>Ενέργειες</th>
            </tr>
          </thead>
          <tbody id="departmentsTableBody">
          </tbody>
        </table>
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="paginationControls">
          <!-- Pagination will be loaded here -->
        </ul>
      </nav>
    </div>
  </div>
</div>



  
<!-- Modal for Add/Edit Department -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="departmentModalLabel">Προσθήκη Τμήματος</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form id="departmentForm">
  <!-- Add this hidden field -->
  <input type="hidden" name="action" value="save">
  
  <input type="hidden" id="department_id" name="department_id">
  <!-- Other fields -->
  <div class="row g-3">
    <!-- Ακαδημία -->
    <div class="col-md-6">
      <label for="academy_id" class="form-label">Σχολή</label>
      <select id="academy_id" name="academy_id" class="form-select">
        <option value="">Επιλέξτε Σχολή</option>
        <!-- Other options loaded dynamically -->
      </select>
    </div>
    <!-- Όνομα Τμήματος -->
    <div class="col-md-6">
      <label for="department_name" class="form-label">Όνομα Τμήματος *</label>
      <input type="text" class="form-control" id="department_name" name="department_name" required>
    </div>
    <!-- Κωδικός Τμήματος -->
    <div class="col-md-6">
      <label for="department_code" class="form-label">Κωδικός Τμήματος *</label>
      <input type="text" class="form-control" id="department_code" name="department_code" required>
    </div>
  </div>
</form>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ακύρωση</button>
          <button type="submit" class="btn btn-primary" form="departmentForm">Αποθήκευση</button>
        </div>
      </div>
    </div>
  </div>
  
<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Επιβεβαίωση Διαγραφής</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
        </div>
        <div class="modal-body">
          Θέλετε σίγουρα να διαγράψετε αυτό το τμήμα;
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ακύρωση</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Διαγραφή</button>
        </div>
      </div>
    </div>
  </div>
  
    <!-- Footer Start -->
<div class="container-fluid bg-dark text-light pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-4">
        <div class="row g-4 justify-content-between align-items-start">

            <!-- TEPAK Logo -->
            <div class="col-md-3 d-flex align-items-start">
                <img src="../assets/img/logocut.png" alt="Tepak Logo" width="250" class="me-2 mt-1">
            </div>

            <!-- ΧΡΗΣΙΜΟΙ ΣΥΝΔΕΣΜΟΙ -->
            <div class="col-md-4">
                <h6 class="text-uppercase text-white mb-3 border-bottom pb-1">ΧΡΗΣΙΜΟΙ ΣΥΝΔΕΣΜΟΙ</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fa fa-chevron-right me-2 text-primary"></i><a href="http://localhost/SpecialistScientistManagement/moodle" class="text-light text-decoration-none" target="_blank">eLearning (Moodle)</a></li>
                    <li><i class="fa fa-chevron-right me-2 text-primary"></i><a href="https://www.cut.ac.cy/" class="text-light text-decoration-none" target="_blank">Ιστοσελίδα ΤΕΠΑΚ</a></li>
                </ul>
        </div>

            <!-- ΣΤΟΙΧΕΙΑ ΕΠΙΚΟΙΝΩΝΙΑ -->
            <div class="col-md-4">
                <h6 class="text-uppercase text-white mb-3 border-bottom pb-1">ΣΤΟΙΧΕΙΑ ΕΠΙΚΟΙΝΩΝΙΑ</h6>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-2"></i>Αρχ. Κυπριανού 30, 3036 Λεμεσός</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-2"></i>2500 2500</p>
                <p class="mb-2"><i class="fa fa-envelope me-2"></i>administration@cut.ac.cy</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social me-2" href="https://x.com/i/flow/login?redirect_after_login=%2Fcyunitech" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social me-2" href="https://www.facebook.com/CyprusUniversityTechnology/?fref=ts" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social me-2" href="https://www.instagram.com/cyprusuniversitytechnology" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social me-2" href="https://www.youtube.com/channel/UCYgPdWWp7FZguifTCdukDJA" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href="https://www.linkedin.com/school/cyprus-university-of-technology/posts/?feedView=all" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

        </div>
    </div>

    <!-- Copyright -->
    <div class="container text-center mt-4 pt-3 border-top border-secondary">
        <p class="mb-0 small text-muted">© Cyprus University of Technology. All rights reserved.</p>
        </div>
    </div>
    <!-- Footer End -->

    
  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <!-- Bootstrap Bundle JS -->
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <!-- Template Javascript -->
  <script src="../assets/js/departments.js"></script>

<!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
  <script src="../assets/js/main.js"></script>
</body>

</html>
