<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Login - Special Scientists - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Login page" name="keywords">
  <meta content="Login page for Cyprus University of Technology" name="description">

  <!-- Favicon -->
  <link href="../../assets/img/logo.png" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap"
    rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="../../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="../../assets/css/style.css.php" rel="stylesheet">
</head>

<body class="login-page">
  <!-- Spinner Start -->
  <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->

  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="../../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <img src="../../assets/img/logocut.png" alt="Tepak Logo" width="150" height="60" class="d-inline-block align-top">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
       
          <a href="../../index.php" class="nav-item nav-link">Αρχικη</a>
          <a href="../about.php" class="nav-item nav-link">Σχετικα</a>
          <a href="login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Σύνδεση <i class="fa fa-arrow-right ms-3"></i></a>
        
      </div>
      <?php if (isset($_SESSION['username'])): ?>
  <div class="dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <?php echo htmlspecialchars($_SESSION['username']); ?>
    </a>
    <ul class="dropdown-menu" aria-labelledby="userDropdown">
      <li><a class="dropdown-item" href="../../php/logout.php">Logout</a></li>
      <!-- You can add more items here if needed -->
    </ul>
  </div>
<?php endif; ?>

    </div>
  </nav>
  <!-- Navbar End -->

  <!-- Header Start -->
  <div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <h1 class="display-3 text-white animated slideInDown">Σύνδεση</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a class="text-white" href="../../index.php">Αρχική</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">Σύνδεση</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- Header End -->

  <!-- Login Form Start -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <!-- The form submits to the backend processing script located at ../../php/login.php -->
          <form id="login-form" method="POST" action="../../php/login.php">
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="login-email" name="email" placeholder="Email">
              <label for="login-email">Ηλεκτρονική Διεύθυνση</label>
              <div class="invalid-feedback">Το email είναι υποχρεωτικό ή μη έγκυρο.</div>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="login-password" name="password" placeholder="Password">
              <label for="login-password">Κωδικός</label>
              <div class="invalid-feedback">Ο κωδικός είναι υποχρεωτικός.</div>
              <button type="button" class="btn btn-link position-absolute text-decoration-none pe-3" 
                      id="togglePassword" style="z-index: 5; right: 10px; top: 13px;">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            <div class="text-end mb-3">
              <a href="/html/forgot-password.php" class="text-primary">Ξεχάσατε τον κωδικό;</a>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3">Σύνδεση</button>
            <p class="mt-3 text-center">
              Δεν έχετε λογαριασμό? <a href="register.html">Εγγραφή</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Login Form End -->


  <!-- Footer Start -->
<div class="container-fluid bg-dark text-light pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-4">
        <div class="row g-4 justify-content-between align-items-start">

            <!-- TEPAK Logo -->
            <div class="col-md-3 d-flex align-items-start">
                <img src="../../assets/img/logocut.png" alt="Tepak Logo" width="250" class="me-2 mt-1">
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
  <script src="../../lib/wow/wow.min.js"></script>
  <script src="../../lib/easing/easing.min.js"></script>
  <script src="../../lib/waypoints/waypoints.min.js"></script>
  <script src="../../lib/owlcarousel/owl.carousel.min.js"></script>
  <!-- Bootstrap Bundle JS -->
  <script src="../../assets/js/bootstrap.bundle.min.js"></script>
  <!-- Template Javascript -->
  <script src="../../assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Login Javascript (for client-side validation) -->
  <script src="../../assets/js/login.js"></script>
</body>

</html>
