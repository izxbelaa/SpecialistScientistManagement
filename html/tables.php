<?php include '../php/session_check.php'; ?>
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css.php" rel="stylesheet">

    <link href="../assets/css/requests-admin.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</head>

<body class="userstable-page">
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
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Διαχειριστής'): ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρισεις</a>
                        <div class="dropdown-menu fade-down m-0">
                            <a href="courses.php" class="dropdown-item">Μαθήματα</a>
                            <a href="departments.php" class="dropdown-item">Τμήματα</a>
                            <a href="academies.php" class="dropdown-item">Σχολές</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown active">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Σελιδες Διαχειριστη </a>
                        <div class="dropdown-menu fade-down m-0">
                            <a href="assign-reviewers.php" class="dropdown-item">Ανάθεση Αξιολογητών</a>
                            <a href="tables.php" class="dropdown-item active">Πινακας Χρηστων</a>
                            <a href="requests-admin.php" class="dropdown-item">Διαχειριση Αιτησεων</a>
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
    <div class="container-fluid bg-primary py-5 mb-5 page-header userstable-header wow fadeIn">
        <div class="container py-5">
            <div class="row justify-content-center wow fadeInUp">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Πίνακας Χρηστών</h1>
                    <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.2s">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Πίνακας Χρηστών</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- User List Start -->


<!-- Users Table -->
<div class="container mt-4 wow fadeInUp" data-wow-delay="0.2s">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title mb-3"><i class="fas fa-list me-2"></i>Λίστα Χρηστών</h5>
      <div class="table-responsive p-3">
        <div class="row align-items-center mb-3">
          <div class="col-md-8">
            <div class="search-box-wrapper position-relative">
              <input type="text" id="searchInput" class="form-control" placeholder="Αναζήτηση χρηστών...">
              <i class="fas fa-search"></i>
            </div>
          </div>
          <div class="col-md-4 text-end">
            <button class="btn btn-orange" id="addUserBtn" data-bs-toggle="modal" data-bs-target="#editModal">
              <i class="fas fa-plus me-2"></i>Προσθήκη Χρήστη
            </button>
          </div>
        </div>
        <table id="usersTable" class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>A/A</th>
              <th>Όνομα</th>
              <th>Επώνυμο</th>
              <th>Μεσαίο Όνομα</th>
              <th>Email</th>
              <th>Τύπος Χρήστη</th>
              <th>Απενεργοποιημένος</th>
              <th>Ενέργειες</th>
            </tr>
          </thead>
          <tbody>
            <!-- Rows will be added here dynamically -->
          </tbody>
        </table>
        <div class="d-flex justify-content-between">
          <div>
            <label for="entriesSelect">Εμφάνιση εγγραφών: </label>
            <select id="entriesSelect" class="form-select" style="width: 100px;">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
            </select>
          </div>
          <div>
            <span id="entriesCount">Εμφάνιση 1 έως 5 από 50 εγγραφών</span>
          </div>
        </div>
        <div id="paginationControls" class="d-flex justify-content-center mt-3">
          <!-- Pagination buttons go here if needed -->
        </div>
      </div>
    </div>
  </div>
</div>




<!-- Modal for editing user information -->
<div class="modal" tabindex="-1" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Επεξεργασία Χρήστη</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm">
          <div class="mb-3">
            <label for="editFirstName" class="form-label">Όνομα</label>
            <input type="text" class="form-control" id="editFirstName" required>
          </div>
          <div class="mb-3">
            <label for="editLastName" class="form-label">Επώνυμο</label>
            <input type="text" class="form-control" id="editLastName" required>
          </div>
          <div class="mb-3">
            <label for="editMiddleName" class="form-label">Μεσαίο Όνομα</label>
            <input type="text" class="form-control" id="editMiddleName">
          </div>
          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" required>
          </div>
          <div class="mb-3">
            <label for="editUserType" class="form-label">Τύπος Χρήστη</label>
            <input type="text" class="form-control" id="editUserType" required>
          </div>
          <div class="mb-3">
            <label for="editDisabledUser" class="form-label">Απενεργοποιημένος Χρήστης</label>
            <select class="form-select" id="editDisabledUser">
              <option value="0">Όχι</option>
              <option value="1">Ναι</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Αποθήκευση</button>
        </form>
      </div>
    </div>
  </div>
</div>
    <!--  User List End -->

    


   

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
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <!-- JavaScript for User List -->
    <script src="../assets/js/users-table-admin.js"></script>
    <script>
      new WOW().init();
    </script>
</body>

</html>