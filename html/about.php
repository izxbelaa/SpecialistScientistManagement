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

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css.php" rel="stylesheet">
</head>

<body class="about-page">
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
                <?php if (!isset($_SESSION['username'])): ?>
                    <a href="../index.php" class="nav-item nav-link">Αρχικη</a>
                    <a href="about.php" class="nav-item nav-link active">Σχετικα</a>
                    <a href="auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Σύνδεση <i class="fa fa-arrow-right ms-3"></i></a>
                <?php else: ?>
                <a href="../index.php" class="nav-item nav-link">Αρχικη</a>
                <a href="about.php" class="nav-item nav-link active">Σχετικα</a>
                    <!-- Full nav for logged-in users -->
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
                        <a href="departments.php" class="dropdown-item">Τμήματα</a>
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
                    <!-- User dropdown -->
                    <div class="dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <?php echo htmlspecialchars($_SESSION['username']); ?>
    </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
      <li><a class="dropdown-item" href="../php/logout.php">Αποσύνδεση</a></li>
                            <li><a class="dropdown-item" href="edit_user.php">Επεξεργασία Προφίλ</a></li>
    </ul>
                    </div>
                    <?php endif; ?>
<?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->



    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Σχετικά</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Σχετικά</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->



   <!-- About Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
        <!-- Section 1: About the System -->
        <div class="row g-5 align-items-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.3s">
                <h6 class="section-title bg-white text-start text-primary pe-3">Σχετικα με το Συστημα</h6>
                <h1 class="mb-4">Εφαρμογή Διαχείρισης Ειδικών Επιστημόνων</h1>
                <p class="mb-3">
                    Η <strong>Εφαρμογή Διαχείρισης Ειδικών Επιστημόνων</strong> είναι μια διαδικτυακή πλατφόρμα που αναπτύχθηκε για τη διαχείριση της πρόσληψης και ενσωμάτωσης των Ειδικών Επιστημόνων (ΕΕ) στο <strong>ΤΕΠΑΚ</strong>.
                </p>
                <p class="mb-3">
                    Η πλατφόρμα αυτοματοποιεί τη διαδικασία από την αίτηση μέχρι την καταχώριση στο <strong>Moodle</strong> και υποστηρίζει όλες τις βασικές λειτουργίες του κύκλου πρόσληψης.
                </p>
                        </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.4s">
                <img class="img-fluid rounded" src="..\assets\img\about page 1.jpg" alt="About Image 1">
                    </div>
                </div>
        <!-- Project Team Info Box -->
        <div class="p-4 rounded shadow-sm mb-4 wow fadeInUp" data-wow-delay="0.5s" style="background-color: #f8f9fa;">
            <div class="text-center">
                <div class="d-flex justify-content-center mb-3 wow fadeIn" data-wow-delay="0.6s">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <p class="fs-6 mb-1 wow fadeIn" data-wow-delay="0.7s">
                    Το σύστημα αναπτύχθηκε από τους φοιτητές 
                    <strong>Άντρεα Παπαδοπούλου</strong>, 
                    <strong>Κατερίνα Γιώργαλλου</strong>, 
                    <strong>Ιζαμπέλα Νεδιάλκοβα</strong>, 
                    <strong>Χρίστος Αριστοτέλους</strong>, 
                    <strong>Κωνσταντίνος Χρίστου</strong>, 
                    <strong>Χρήστος Ζαβός</strong>,
                    τριτοετείς φοιτητές του Τμήματος Ηλεκτρολόγων Μηχανικών και Μηχανικών Ηλεκτρονικών Υπολογιστών και Πληροφορικής του 
                    <strong>Τεχνολογικού Πανεπιστημίου Κύπρου</strong>, υπό την επίβλεψη του Καθηγητή 
                    <strong>Χρίστου Ροδοσθένους</strong> στα πλαίσια του μαθήματος 
                    <em>"CEI326 – Μηχανική Ιστού"</em>.
                </p>
                <p class="fw-bold text-muted mt-2 mb-0 wow fadeIn" data-wow-delay="0.8s">Λεμεσός, Μάιος 2025</p>
            </div>
        </div>
        <!-- End Team Box -->
        <!-- Section 2: Modules & Technology -->
        <div class="row g-5 align-items-center wow fadeInUp" data-wow-delay="0.9s">
            <div class="col-lg-6 order-lg-2 wow fadeInRight" data-wow-delay="1.0s">
                <h4 class="mb-4">Δομή & Τεχνολογίες</h4>
                <ul>
                    <li><strong>Admin Module:</strong> Διαχείριση χρηστών, αιτήσεων και στατιστικών.</li>
                    <li><strong>Recruitment Module:</strong> Προφίλ και αιτήσεις υποψηφίων.</li>
                    <li><strong>Enrollment Module:</strong> Συγχρονισμός με Moodle & αναφορές.</li>
                </ul>
                <p>
                    Η εφαρμογή αναπτύχθηκε με <strong>PHP, MySQL/MariaDB, HTML, CSS, JavaScript</strong> και υποστηρίζει σύνδεση με Moodle μέσω API.
                </p>
    </div>
            <div class="col-lg-6 order-lg-1 wow fadeInLeft" data-wow-delay="1.1s">
                <img class="img-fluid rounded" src="..\assets\img\about page 2.jpg" alt="About Image 2">
            </div>
            </div>
        </div>
    </div>
    <!-- About End -->
        

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
                    <li class="mb-2"><i class="fa fa-chevron-right me-2 text-primary"></i><a href="https://cei326-omada2.cut.ac.cy/moodle/" class="text-light text-decoration-none" target="_blank">eLearning (Moodle)</a></li>

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
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <script>
      new WOW().init();
    </script>
</body>

</html>