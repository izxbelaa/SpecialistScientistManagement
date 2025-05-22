<?php include '../php/session_check.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Αναφορές LMS - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="LMS Reports page" name="keywords">
    <meta content="LMS Reports page for Cyprus University of Technology" name="description">
    <link href="../assets/img/logo.png" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css.php" rel="stylesheet">
</head>
<body class="lms-reports-page">
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
                <div class="nav-item dropdown active">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Moodle</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="lms_sync.php" class="dropdown-item">Πρόσβαση στο Moodle</a>
                        <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Διαχειριστής'  || $_SESSION['user_type'] == 'Προϊστάμενος Ανθρώπινου Δυναμικού')): ?>
                        <a href="lms-reports.php" class="dropdown-item active">Αναφορές LMS</a>
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

    <!-- Header -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header" style="background: url('../assets/img/lms-statistics.jpg') center center/cover no-repeat;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Αναφορές LMS</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Αναφορές LMS</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-3"><i class="bi bi-bar-chart-line me-2"></i>Στατιστικά & Αναφορές</h4>
                            <div class="mb-4">
                                <button class="btn btn-primary me-2" id="btn-ee-moodle">ΕΕ με/χωρίς πρόσβαση στο Moodle</button>
                                <button class="btn btn-primary me-2" id="btn-courses-no-instructor">Μαθήματα χωρίς διδάσκοντα</button>
                                <button class="btn btn-primary" id="btn-courses-with-instructor">Μαθήματα με διδάσκοντα</button>
                            </div>
                            <div id="ee-moodle-section" class="mb-4" style="display:none;">
                                <canvas id="eeMoodleChart" height="80"></canvas>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div id="ee-moodle-summary" class="fw-bold"></div>
                                    <button class="btn btn-outline-secondary btn-sm" id="export-ee-moodle-csv">Εξαγωγή CSV</button>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="ee-moodle-table">
                                        <thead><tr><th>Email</th><th>Όνομα</th><th>Επώνυμο</th><th>Πρόσβαση</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div id="ee-moodle-stats"></div>
                            </div>
                            <div id="courses-no-instructor-section" class="mb-4" style="display:none;">
                                <canvas id="coursesNoInstructorChart" height="120"></canvas>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div id="courses-no-instructor-summary" class="fw-bold"></div>
                                    <button class="btn btn-outline-secondary btn-sm" id="export-courses-no-instructor-csv">Εξαγωγή CSV</button>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="courses-no-instructor-table">
                                        <thead><tr><th>Κωδικός</th><th>Όνομα Μαθήματος</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div id="courses-no-instructor"></div>
                            </div>
                            <div id="courses-with-instructor-section" class="mb-4" style="display:none;">
                                <canvas id="coursesWithInstructorChart" height="120"></canvas>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div id="courses-with-instructor-summary" class="fw-bold"></div>
                                    <button class="btn btn-outline-secondary btn-sm" id="export-courses-with-instructor-csv">Εξαγωγή CSV</button>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="courses-with-instructor-table">
                                        <thead><tr><th>Κωδικός</th><th>Όνομα Μαθήματος</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div id="courses-with-instructor"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mt-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Σημειώσεις</h5>
                            <ul>
                                <li>Τα στατιστικά ενημερώνονται αυτόματα από το σύστημα LMS/Moodle.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row g-4 justify-content-between align-items-start">
                <div class="col-md-3 d-flex align-items-start">
                    <img src="../assets/img/logocut.png" alt="Tepak Logo" width="250" class="me-2 mt-1">
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-white mb-3 border-bottom pb-1">ΧΡΗΣΙΜΟΙ ΣΥΝΔΕΣΜΟΙ</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fa fa-chevron-right me-2 text-primary"></i><a href="http://localhost/SpecialistScientistManagement/moodle" class="text-light text-decoration-none" target="_blank">eLearning (Moodle)</a></li>
                        <li><i class="fa fa-chevron-right me-2 text-primary"></i><a href="https://www.cut.ac.cy/" class="text-light text-decoration-none" target="_blank">Ιστοσελίδα ΤΕΠΑΚ</a></li>
                    </ul>
                </div>
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
            <div class="container text-center mt-4 pt-3 border-top border-secondary">
                <p class="mb-0 small text-muted">© Cyprus University of Technology. All rights reserved.</p>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/lms-reports.js"></script>
</body>
</html> 