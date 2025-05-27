<?php include '../php/session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Academies - Special Scientists - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Manage Academies page" name="keywords">
    <meta content="Manage Academies page for Cyprus University of Technology" name="description">

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="courses-page">
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
                <div class="nav-item dropdown active">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρισεις</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="courses.php" class="dropdown-item active">Μαθήματα</a>
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

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header courses-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Διαχείριση Μαθημάτων</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Διαχείριση Μαθημάτων</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    
    <!-- Courses Management Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0">Λίστα Μαθημάτων</h2>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#CourseModal">
                                <i class="fa fa-plus me-2"></i>Προσθήκη Νέου Μαθήματος
                            </button>
                        </div>

                        <!-- Search bar and entries per page -->
                        <div class="d-flex justify-content-between mb-3">
                            <input type="text" id="searchInput" class="form-control w-50" placeholder="Αναζήτηση Μαθημάτων..." oninput="searchCourses()">
                            <select id="entriesPerPage" class="form-control w-25" onchange="loadCourses()">
                                <option value="5">5 Μαθήματα</option>
                                <option value="10" selected>10 Μαθήματα</option>
                                <option value="20">20 Μαθήματα</option>
                                <option value="100">100 Μαθήματα</option>
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table id="CourseTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Α/Α</th>
                                        <th>Τμήμα</th>
                                        <th>Μάθημα</th>
                                        <th>Κωδικός</th>
                                        <th>Ενέργειες</th>
                                    </tr>
                                </thead>
                                <tbody id="CourseTableBody">
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination controls -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="paginationControls">
                                <!-- Pagination will be loaded here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Course Management End -->


    <!-- Course Modal -->
    <div class="modal fade" id="CourseModal" tabindex="-1" aria-labelledby="CourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CourseModalLabel">Προσθήκη Νέου Μαθήματος</h5>
                    <input type="hidden" id="isEditing" value="0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
                </div>
                <div class="modal-body">
                    <form id="academyForm">
                        <input type="hidden" id="CourseId" name="id">
                        <div class="mb-3">
                            <label for="DepartmentName" class="form-label">Όνομα Τμήματος <span>*</span></label>
                            <select class="form-select" id="DepartmentName" name="departmentname" required>
                                <option value="" disabled selected>Επιλέξτε Τμήμα</option>
                                <!-- Options will be populated by JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="CourseName" class="form-label">Όνομα Μαθήματος <span>*</span></label>
                            <input type="text" class="form-control" id="CourseName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="CourseCode" class="form-label">Κωδικός Μαθήματος <span>*</span></label>
                            <input type="text" class="form-control" id="CourseCode" name="code" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ακύρωση</button>
                    <button type="button" class="btn btn-primary" id="saveCourse">Αποθήκευση</button>
                </div>
            </div>
        </div>
    </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="DeleteModal" tabindex="-1" aria-labelledby="DeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="DeleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this course?
        <input type="hidden" id="deleteCourseId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/courses.js"></script>

</body>
</html> 