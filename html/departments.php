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

<body>
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
        <a href="../index.php" class="nav-item nav-link">Home</a>
        <a href="./about.html" class="nav-item nav-link">About</a>
        <a href="./courses.html" class="nav-item nav-link">Courses</a>
        <a href="./html/departments.php" class="nav-item nav-link">Departments</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="./team.html" class="dropdown-item">Our Team</a>
            <a href="./testimonial.html" class="dropdown-item">Testimonial</a>
            <a href="./404.html" class="dropdown-item">404 Page</a>
            <a href="./departments.php" class="dropdown-item">Departments</a>
          </div>
        </div>
        <a href="../php/settings.php" class="nav-item nav-link">Settings</a>
      </div>
      <!-- (Optional) You can remove or adjust the Join Now button if needed -->
      <a href="./html/auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Login<i class="fa fa-arrow-right ms-3"></i></a>
    </div>
  </nav>
  <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
          <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
              <h1 class="display-3 text-white animated slideInDown">Διαχείριση Τμημάτων</h1>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                  <li class="breadcrumb-item"><a class="text-white" href="../../index.php">Ταμπλό</a></li>
                  <li class="breadcrumb-item text-white active" aria-current="page">Διαχείριση Τμημάτων</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <!-- Header End -->

<!-- Search + Add Department Row -->
<div class="container mb-4">
    <div class="row align-items-center">
      <!-- Αναζήτηση -->
      <div class="col-md-8">
        <div class="search-box-wrapper position-relative">
          <input type="text" class="form-control" id="searchInput" placeholder="Αναζήτηση τμημάτων...">
          <i class="fas fa-search position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); color: #4c8ad5;"></i>
        </div>
      </div>
      <!-- Κουμπί Προσθήκης -->
      <div class="col-md-4 text-end">
        <button class="btn btn-orange" id="addDepartmentBtn" data-bs-toggle="modal" data-bs-target="#departmentModal">
          <i class="fas fa-plus me-2"></i>ΠΡΟΣΘΗΚΗ ΤΜΗΜΑΤΟΣ
        </button>
      </div>
    </div>
  </div>


  
  <!-- Table for Departments -->
<div class="container mt-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3"><i class="fas fa-list me-2"></i>Λίστα Τμημάτων</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Α/Α</th>
                <th>Όνομα Τμήματος</th>
                <th>Κωδικός</th>
                <th>Ακαδημία</th>
                <th>Ενέργειες</th>
              </tr>
            </thead>
            <tbody id="departmentsTableBody">
              <!-- JS-Generated Rows Here -->
            </tbody>
          </table>
        </div>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ΑΚΥΡΩΣΗ</button>
          <button type="submit" class="btn btn-primary" form="departmentForm">ΥΠΟΒΟΛΗ</button>
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
  <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h4 class="text-white mb-3">Quick Link</h4>
          <a class="btn btn-link" href="#">About Us</a>
          <a class="btn btn-link" href="#">Contact Us</a>
          <a class="btn btn-link" href="#">Privacy Policy</a>
          <a class="btn btn-link" href="#">Terms & Condition</a>
          <a class="btn btn-link" href="#">FAQs & Help</a>
        </div>
        <div class="col-lg-3 col-md-6">
          <h4 class="text-white mb-3">Contact</h4>
          <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
          <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
          <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@example.com</p>
          <div class="d-flex pt-2">
            <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
            <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
            <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
       
        <div class="col-lg-3 col-md-6">
          <h4 class="text-white mb-3">Newsletter</h4>
          <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
          <div class="position-relative mx-auto" style="max-width: 400px;">
            <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="copyright">
        <div class="row">
          <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right Reserved.
            Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a><br><br>
            Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <div class="footer-menu">
              <a href="#">Home</a>
              <a href="#">Cookies</a>
              <a href="#">Help</a>
              <a href="#">FQAs</a>
            </div>
          </div>
        </div>
      </div>
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
  <script src="../assets/js/bootstrap.bundle.min.js"></script>!
  <!-- Template Javascript -->
  <script src="../assets/js/departments.js"></script>

<!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
  <script src="../assets/js/main.js"></script>
</body>

</html>
