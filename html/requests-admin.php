<?php include '../php/session_check.php'; ?>
<?php
include '../php/get-user-type.php';

// Only allow access if user is "Διαχειριστής" (Admin in Greek)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "Διαχειριστής") {
    header("Location: ./auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Special Scientists - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
    <link href="../assets/css/requests-admin.css" rel="stylesheet">

</head>

<body class="adminapps-page">
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
                <a href="application.php" class="nav-item nav-link">Applications</a>
                <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'Διαχειριστής' || $_SESSION['user_type'] == 5)): ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρισεις</a>
                        <div class="dropdown-menu fade-down m-0">
                                                    <a href="courses.php" class="dropdown-item">Μαθήματα</a>

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
    <div class="container-fluid bg-primary py-5 mb-5 page-header adminapps-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Διαχείρηση Αιτήσεων</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Διαχείρηση Αιτήσεων</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

<!-- Search + Add Request Row -->
<div class="container mb-4">
    <div class="row align-items-center">
      <!-- Search Box -->
      <div class="col-md-8">
        <div class="search-box-wrapper position-relative">
          <input type="text" class="form-control" id="searchInput" placeholder="Αναζήτηση Αιτήσεων...">
          <i class="fas fa-search"></i>
        </div>
      </div>
      <!-- Add Request Button -->
      <div class="col-md-4 text-end">
        <button class="btn btn-orange" id="addRequestBtn" data-bs-toggle="modal" data-bs-target="#addRequestModal">
          <i class="fas fa-plus me-2"></i>Προσθήκη Αιτήσεων
        </button>
      </div>
    </div>
  </div>

  <!-- Requests Table -->
  <div class="container mt-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3"><i class="fas fa-list me-2"></i>Λίστα Αιτήσεων</h5>
        <div class="table-responsive">
          <table id="requestsTable" class="table table-striped align-middle">
            <thead>
              <tr>
                <th>A/A</th>
                <th>Τίτλος Αίτησης</th>
                <th>Περιγραφή</th>
                <th>Ημερομηνία Έναρξης</th>
                <th>Ημερομηνία Λήξης</th>
                <th>Σχολές</th>
                <th>Τμήματα</th>
                <th>Μαθήματα</th>
                <th>Ενέργειες</th>
              </tr>
            </thead>
            <tbody>
              <!-- Table rows will be populated dynamically -->
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
          <div>
            <label for="entriesSelect">Εμφάνιση</label>
            <select id="entriesSelect" class="form-select form-select-sm ms-2" style="width: 100px; display: inline-block;">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
            </select>
            <span>εγγραφών</span>
          </div>
          <div>
            <span id="entriesCount">Εμφάνιση 1 έως 5 από 50 εγγραφών</span>
          </div>
        </div>
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center" id="paginationControls"></ul>
        </nav>
      </div>
    </div>
  </div>
  <!-- End of Requests Table -->

  <!-- Modal for Add/Edit Request Template -->
  <div class="modal fade" id="addRequestModal" tabindex="-1" aria-labelledby="addRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addRequestModalLabel">Προσθήκη Νέας Αίτησης</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addRequestForm">
            <input type="hidden" name="action" value="save_template">
            <input type="hidden" id="templateId" name="template_id" value="">
            <div class="mb-3">
              <label for="templateTitle" class="form-label">Τίτλος Αίτησης</label>
              <input type="text" class="form-control" id="templateTitle" name="templateTitle" required>
            </div>
            <div class="mb-3">
              <label for="templateDescription" class="form-label">Περιγραφή</label>
              <textarea class="form-control" id="templateDescription" name="templateDescription" rows="3" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="startDate" class="form-label">Ημερομηνία Έναρξης και Ώρα Έναρξης</label>
                <div class="d-flex gap-2">
                  <input type="date" class="form-control" id="startDate" name="startDate" required>
                  <input type="time" class="form-control" id="startTime" name="startTime" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="endDate" class="form-label">Ημερομηνία Λήξης και Ώρα Λήξης</label>
                <div class="d-flex gap-2">
                  <input type="date" class="form-control" id="endDate" name="endDate" required>
                  <input type="time" class="form-control" id="endTime" name="endTime" required>
                </div>
              </div>
            </div>
            <div id="academyContainer">
              <div class="mb-3">
                <label class="form-label">Σχολή</label>
                <div class="d-flex gap-2 academy-row">
                  <select class="form-select academy-select" name="academies[]" required>
                  </select>
                  <button type="button" class="btn btn-primary add-academy">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>

            <div id="departmentContainer">
              <div class="mb-3">
                <label class="form-label">Τμήμα</label>
                <div class="d-flex gap-2 department-row">
                  <select class="form-select department-select" name="departments[]" id="department_select_1" required>
                    <option value="">Επιλέξτε Τμήμα</option>
                  </select>
                  <button type="button" class="btn btn-primary add-department" data-max-selections="2">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Μαθήματα</label>
              <div id="coursesContainer">
                <!-- Courses will be populated here as checkboxes -->
              </div>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Αποθήκευση Αίτησης</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of Modal -->

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../assets/js/settings.js"></script>
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- Custom Javascript -->
    <script src="../assets/js/request-templates.js"></script>
</body>

</html>
