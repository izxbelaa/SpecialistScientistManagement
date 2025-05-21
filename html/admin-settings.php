<?php 
include '../php/session_check.php';
require_once '../php/config.php';
// Get the full sync status directly
try {
    $stmt = $pdo->query("SELECT enabled FROM full_sync LIMIT 1");
    $row = $stmt->fetch();
    $fullSyncEnabled = $row ? (int)$row['enabled'] : 0;
} catch (Exception $e) {
    $fullSyncEnabled = 0; // or handle error as needed
}
?>
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
</head>
<style>
  .custom-switch {
    display: flex;
    align-items: center;
    gap: 10px; /* spacing between switch and text */
    cursor: pointer;
  }

  .custom-switch input[type="checkbox"] {
    display: none;
  }

  .slider {
    position: relative;
    width: 50px;
    height: 26px;
    background-color: #ccc;
    border-radius: 34px;
    transition: 0.3s;
  }

  .slider::before {
    content: "";
    position: absolute;
    height: 22px;
    width: 22px;
    left: 2px;
    top: 2px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
  }

  input:checked + .slider {
    background-color: #0d9488; /* teal green */
  }

  input:checked + .slider::before {
    transform: translateX(24px);
  }

  .switch-label {
    font-size: 1rem;
    user-select: none;
  }
</style>

<body class="adminsettings-page">
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
                <a href="../index.php" class="nav-item nav-link">ΑΡΧΙΚΗ</a>
                <a href="about.php" class="nav-item nav-link">ΣΧΕΤΙΚΑ</a>
                <a href="application.php" class="nav-item nav-link">APPLICATIONS</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ΚΑΤΑΧΩΡΙΣΕΙΣ</a>
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
                    </div>
                </div>
                <a href="admin-settings.php" class="nav-item nav-link active">Ρυθμισεις Διαχειριστη</a>
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
    <div class="container-fluid bg-primary py-5 mb-5 page-header adminsettings-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Ρυθμίσεις Διαχειριστή</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Ρυθμίσεις Διαχειριστή</a></li>
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
                    <br>
                    <label class="custom-switch">
  <input type="checkbox" id="fullSyncSwitch">
  <span class="slider"></span>
  <span class="switch-label">Πλήρης Συγχρονισμός</span>
</label>


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

    <script src="../assets/js/full-sync.js"></script>

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

