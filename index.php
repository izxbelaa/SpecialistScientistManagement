<?php
include 'php/session_check.php';
include 'php/config.php';

$needsProfileCompletion = false;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // These fields must be completed in order the warning to dissapear 
    $requiredFields = [
        'dob', 'gender', 'social_security_number', 'cypriot_id', 'postal_code',
        'street_address', 'city', 'country',
        'nationality', 'mobile_phone', 'email'
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
    <title>Dashboard - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="assets/img/logo.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="./lib/animate/animate.min.css" rel="stylesheet">
    <link href="./lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="./assets/css/style.css.php" rel="stylesheet">

    <!-- Category Box Stylesheet -->
    <link rel="stylesheet" href="assets/css/category-box.css">
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
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="./assets/img/logocut.png" alt="Tepak Logo" width="150" height="60" class="d-inline-block align-top">
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">

                <a href="index.php" class="nav-item nav-link active">Αρχικη</a>
                <a href="html/about.php" class="nav-item nav-link">Σχετικα</a>
                <a href="./html/application.php" class="nav-item nav-link">Applications</a>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Διαχειριστής'): ?>
    <div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρισεις</a>
        <div class="dropdown-menu fade-down m-0">
            <a href="./html/courses.php" class="dropdown-item">Μαθήματα</a>
            <a href="./html/departments.php" class="dropdown-item">Τμήματα</a>
            <a href="./html/academies.php" class="dropdown-item">Σχολές</a>
        </div>
    </div> 

    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Σελίδες Διαχειριστή</a>
        <div class="dropdown-menu fade-down m-0">
            <a href="./html/assign-reviewers.php" class="dropdown-item">Ανάθεση Αξιολογητών</a>
            <a href="./html/tables.php" class="dropdown-item">Πινακας Χρηστων</a>
            <a href="./html/requests-admin.php" class="dropdown-item">Ρυθμισεις Διαχειριστη</a>
        </div>
    </div>
    <a href="./html/admin-settings.php" class="nav-item nav-link">Ρυθμισεις Διαχειριστη</a>
<?php endif; ?>
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="./php/logout.php">Αποσύνδεση</a></li>
                            <li><a class="dropdown-item" href="./html/edit_user.php">Επεξεργασία Προφίλ</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <a href="./html/auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block"> Σύνδεση <i class="fa fa-arrow-right ms-3"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->


    
      <?php if ($needsProfileCompletion): ?>
  <div class="alert alert-warning text-center alert-dismissible fade show -mb-4" role="alert" style="z-index: 1;">
    ⚠️ Παρακαλώ συμπληρώστε το προφίλ σας. <a href="html/edit_user.php" class="alert-link">Μετάβαση στην Επεξεργασία Προφίλ</a>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="./assets/img/about-1.jpg" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">ΤΕΠΑΚ - Ειδικοι Επιστημονες</h5>
                                <h1 class="display-3 text-white animated slideInDown">Πύλη Ειδικών Επιστημόνων </h1>
                                <p class="fs-5 text-white mb-4 pb-2">Η "Πύλη Ειδικών Επιστημόνων ΤΕΠΑΚ" είναι ένας φιλικός και εύχρηστος διαδικτυακός χώρος που σας προσφέρει πρόσβαση σε όλες τις πληροφορίες, ενημερώσεις και υπηρεσίες που αφορούν τους εξειδικευμένους επιστήμονες του TEΠΑΚ.</p>
                                <a href="./html/about.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Περισσότερα</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="./assets/img/about-3.jpg" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">ΤΕΠΑΚ - Ειδικοι Επιστημονες</h5>
                                <h1 class="display-3 text-white animated slideInDown">Εγγραφή </h1>
                                <p class="fs-5 text-white mb-4 pb-2">Εάν επιθυμείτε να γίνετε Ειδικός Επιστήμονας του TEΠAΚ, υποβάλετε την αίτησή σας μέσω της εγγραφής. Συμπληρώστε τα στοιχεία σας για να αποκτήσετε πρόσβαση σε εξειδικευμένες υπηρεσίες, εκπαιδευτικά προγράμματα και μοναδικές ευκαιρίες – μαζί, διαμορφώνουμε το μέλλον της επιστήμης!</p>
                                <a href="./html/auth/login.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Περισσότερα</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-sign-in-alt text-primary mb-4"></i>
                            <h5 class="mb-3">Είσοδος / Εγγραφή</h5>
                            <p>Συνδεθείτε ή εγγραφείτε για να δημιουργήσετε τον λογαριασμό σας.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-user-edit text-primary mb-4"></i>
                            <h5 class="mb-3">Συμπλήρωση Προφίλ</h5>
                            <p>Συμπληρώστε όλα τα στοιχεία του προφίλ σας για να διευκολυνθεί η διαδικασία αίτησης.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-file-signature text-primary mb-4"></i>
                            <h5 class="mb-3">Υποβολή Αίτησης</h5>
                            <p>Δείτε τις διαθέσιμες αιτήσεις και υποβάλετε όποια επιθυμείτε.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-clipboard-list text-primary mb-4"></i>
                            <h5 class="mb-3">Παρακολούθηση Αίτησης</h5>
                            <p>Παρακολουθήστε την πορεία της αίτησής σας και ενημερωθείτε όταν βγουν τα αποτελέσματα.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


   <!-- About Start (Home Summary Version) -->
    <div class="container-xxl py-5">
        <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                <img class="img-fluid rounded" src="assets/img/about-2.jpg" alt="About Home Image">
                    </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                <h6 class="section-title bg-white text-start text-primary pe-3">Σχετικα με την Πλατφορμα</h6>
                <h2 class="mb-4">Πύλη Διαχείρισης Ειδικών Επιστημόνων</h2>
                <p class="mb-3">Η εφαρμογή διαχειρίζεται τη διαδικασία πρόσληψης και εγγραφής των Ειδικών Επιστημόνων του ΤΕΠΑΚ. Παρέχει στον χρήστη αυτόματη σύνδεση και συγχρονισμό με το Moodle, προσφέροντας άμεση πρόσβαση στο εκπαιδευτικό περιβάλλον.</p>
                <a class="btn btn-primary py-3 px-5" href="html/about.php">Διαβάστε Περισσότερα</a>
            </div>
            </div>
        </div>
    </div>
    <!-- About End -->



    <!-- Categories Start -->
    
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Σχολες</h6>
                <h1 class="mb-5">Σχολές</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/gem/"target="_blank">
                        <img src="assets\img\30570_100faculties_gebet.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Γεωτεχνικών Επιστημών και Διαχείρισης Περιβάλλοντος</h5>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/fme/"target="_blank" >
                        <img src="assets\img\30570_100faculties_fme.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Διοίκησης και Οικονομίας</h5>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/comm/"target="_blank">
                        <img src="assets\img\30570_100faculties_fepik (1).jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Επικοινωνίας και Μέσων Ενημέρωσης</h5>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/hsc/"target="_blank">
                        <img src="assets\img\30570_100faculties_hsc.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Επιστημών Υγείας</h5>
                                </div>
                            </a>
                        </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/aac/"target="_blank">
                        <img src="assets\img\18370_100comm.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Καλών και Εφαρμοσμένων Τεχνών</h5>
                                </div>
                            </a>
                        </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/fet/"target="_blank">
                        <img src="assets\img\64780_100faculties-meme.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Μηχανικής και Τεχνολογίας</h5>
                                </div>
                            </a>
                        </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/tohe/"target="_blank">
                        <img src="assets\img\64780_100sxoli1.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Σχολή Διοίκησης Τουρισμού, Φιλοξενίας και Επιχειρηματικότητας</h5>
                    </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a class="category-box" href="https://www.cut.ac.cy/faculties/languagecentre/"target="_blank">
                        <img src="assets\img\30570_100faculties_lang.jpg" alt="">
                        <div class="category-overlay">
                            <h5>Κέντρο Γλωσσών</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Categories End -->
        

    <!-- Footer Start -->
<div class="container-fluid bg-dark text-light pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-4">
        <div class="row g-4 justify-content-between align-items-start">

            <!-- TEPAK Logo -->
            <div class="col-md-3 d-flex align-items-start">
                <img src="assets/img/logocut.png" alt="Tepak Logo" width="250" class="me-2 mt-1">
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


    <!-- Bootstrap Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

  
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./lib/wow/wow.min.js"></script>
    <script src="./lib/easing/easing.min.js"></script>
    <script src="./lib/waypoints/waypoints.min.js"></script>
    <script src="./lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="./assets/js/main.js"></script>
    <script>
  window.addEventListener("DOMContentLoaded", () => {
    const alert = document.getElementById("success-alert");
    if (alert) {
      setTimeout(() => {
        alert.style.transition = "opacity 1s ease, transform 1s ease";
        alert.style.opacity = "0";
        alert.style.transform = "translateY(50px)";
        setTimeout(() => alert.remove(), 1000);
      }, 3000);
    }
  });
</script>

<?php if (isset($_SESSION['reset_success'])): ?>
  <div id="success-alert" class="alert alert-success text-center position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-3 shadow rounded" style="z-index: 9999;">
    <?php echo $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?>
  </div>
<?php endif; ?>


</body>

</html>