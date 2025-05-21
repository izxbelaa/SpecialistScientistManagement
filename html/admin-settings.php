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

    <!-- Google Web Fonts -->x
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
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="courses.html" class="nav-item nav-link">Courses</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="team.html" class="dropdown-item">Our Team</a>
                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="404.html" class="dropdown-item">404 Page</a>
                        <a href="admin-settings.php" class="dropdown-item">Admin Settings</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Settings</a>
            </div>
            <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="../php/logout.php">Logout</a></li>
                <!-- You can add more items here if needed -->
                </ul>
            </div>
            <?php else: ?>
            <a href="./auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                Login <i class="fa fa-arrow-right ms-3"></i>
            </a>
            <?php endif; ?>        
        </div>
    </nav>
    <!-- Navbar End -->



    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Admin Settings</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Admin Settings</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Admin Settings Start -->
    <div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Ρυθμίσεις Διαχειριστή</h3>

                <form id="settingsForm" enctype="multipart/form-data">
                    <!-- Color Pickers -->
                    <div class="mb-3">
                        <label for="site_color" class="form-label">Κύριο Χρώμα</label>
                        <input type="color" class="form-control form-control-color" name="site_color" id="site_color">
                    </div>
                    <div class="mb-3">
                        <label for="light_color" class="form-label">Χρώμα Φόντου (Light)</label>
                        <input type="color" class="form-control form-control-color" name="light_color" id="light_color">
                    </div>
                    <div class="mb-3">
                        <label for="dark_color" class="form-label">Σκούρο Χρώμα (Dark)</label>
                        <input type="color" class="form-control form-control-color" name="dark_color" id="dark_color">
                    </div>

                    <!-- Logo Uploads -->
                    <div class="mb-3">
                        <label for="logo" class="form-label">Ανέβασμα logo.png</label>
                        <input type="file" name="logo" accept="image/*" class="form-control">
                        <img id="logoPreview" src="../assets/img/logo.png" class="img-thumbnail mt-2" width="150">
                    </div>
                    <div class="mb-3">
                        <label for="logocut" class="form-label">Ανέβασμα logocut.png</label>
                        <input type="file" name="logocut" accept="image/*" class="form-control">
                        <img id="logocutPreview" src="../assets/img/logocut.png" class="img-thumbnail mt-2" height="60">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Αποθήκευση</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Backup Section: Only for Admin -->
    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Διαχειριστής'): ?>
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Δημιουργία Αντιγράφου Ασφαλείας</h3>
                <div class="text-center">
                    <div class="d-flex justify-content-center gap-3">
                        <button id="backupBtn" class="btn btn-primary px-4 py-2">
                            <span class="d-inline-flex align-items-center">
                                <span id="backupSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                                <span id="backupText">Δημιουργία Backup</span>
                            </span>
                        </button>
                        <button id="downloadBtn" class="btn btn-success px-4 py-2">
                            <span class="d-inline-flex align-items-center">
                                <i class="fas fa-download me-2"></i>
                                <span>Λήψη Backup</span>
                            </span>
                        </button>
                    </div>
                    <div id="backupStatus" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<!-- Admin Settings End -->
   
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
                    <h4 class="text-white mb-3">Gallery</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-1.jpg" alt="">
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../assets/js/settings.js"></script>
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <!-- Custom JavaScript for Admin Settings -->
    <script src="../assets/js/admin-settings.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const backupBtn = $('#backupBtn');
            const downloadBtn = $('#downloadBtn');
            const backupSpinner = $('#backupSpinner');
            const backupText = $('#backupText');
            const backupStatus = $('#backupStatus');

            backupBtn.click(function() {
                // Disable buttons and show spinner
                backupBtn.prop('disabled', true);
                downloadBtn.prop('disabled', true);
                backupSpinner.removeClass('d-none');
                backupText.text('Δημιουργία...');
                backupStatus.html('');

                $.ajax({
                    url: '../php/backup.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let fileList = '';
                            for (let db in response.files) {
                                fileList += `<br>${db}: ${response.files[db]}`;
                            }
                            Swal.fire({
                                title: 'Επιτυχία!',
                                html: `${response.message}<br>Αρχεία backup:${fileList}<br><br>Τοποθεσία: ${response.path}`,
                                icon: 'success'
                            });
                        } else {
                            Swal.fire('Σφάλμα', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Σφάλμα', 'Σφάλμα κατά τη δημιουργία του backup: ' + error, 'error');
                    },
                    complete: function() {
                        // Re-enable buttons and hide spinner
                        backupBtn.prop('disabled', false);
                        downloadBtn.prop('disabled', false);
                        backupSpinner.addClass('d-none');
                        backupText.text('Δημιουργία Backup');
                    }
                });
            });

            downloadBtn.click(function() {
                // Show loading state
                downloadBtn.prop('disabled', true);
                
                // First check if there are backup files
                $.ajax({
                    url: '../php/download_backup.php',
                    type: 'HEAD',
                    error: function(xhr) {
                        downloadBtn.prop('disabled', false);
                        if (xhr.status === 404) {
                            Swal.fire('Σφάλμα', 'Δεν βρέθηκαν αρχεία backup. Παρακαλώ δημιουργήστε πρώτα ένα backup.', 'error');
                        } else {
                            Swal.fire('Σφάλμα', 'Σφάλμα κατά τη λήψη του backup.', 'error');
                        }
                    },
                    success: function() {
                        // If check passes, trigger download
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        
                        iframe.src = '../php/download_backup.php';
                        
                        // Remove iframe after download starts
                        setTimeout(function() {
                            document.body.removeChild(iframe);
                            downloadBtn.prop('disabled', false);
                        }, 1000);
                    }
                });
            });
        });
    </script>
</body>

</html>

<style>
    .btn {
        transition: all 0.2s ease-in-out;
    }
    .btn:hover {
        transform: translateY(-1px);
    }
    .btn:active {
        transform: translateY(0);
    }
    #backupStatus {
        min-height: 24px;
    }
</style>
