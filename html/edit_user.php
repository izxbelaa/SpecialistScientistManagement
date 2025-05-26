<?php include '../php/session_check.php'; ?>
<?php
include '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Get current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Profile - Special Scientists - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Edit Profile page" name="keywords">
    <meta content="Edit Profile page for Cyprus University of Technology" name="description">

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
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="edituser-page">
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
                        <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../php/logout.php">Αποσύνδεση</a></li>
                            <li><a class="dropdown-item active" href="edit_user.php">Επεξεργασία Προφίλ</a></li>
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
    <div class="container-fluid bg-primary py-5 mb-5 page-header edituser-header">
      <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Επεξεργασία Προφίλ</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Επεξεργασία Προφίλ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
     </div>
    <!-- Header End -->

    <!-- Edit Profile Form Start -->
    <div class="container-xxl py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6">
            <form id="editProfileForm" method="POST" action="../php/edit_user.php" autocomplete="off">
              <h5 class="mt-5 mb-3 text-primary">Προσωπικά Στοιχεία Αιτητή</h5>

              <div class="form-floating mb-3">
                <input type="date" class="form-control" id="dob" name="dob"
                       value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                <label for="dob">Ημερομηνία Γέννησης*</label>
                <div class="error-message" id="dob_error"></div>
              </div>

              <div class="form-floating mb-3">
                <select class="form-select" id="gender" name="gender">
                  <option value="" disabled selected>-- Επιλέξτε --</option>
                  <option value="M" <?= ($user['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Άρρεν</option>
                  <option value="F" <?= ($user['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Θήλυ</option>
                </select>
                <label for="gender">Φύλο*</label>
                <div class="error-message" id="gender_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="social_security_number" name="social_security_number"
                       value="<?= htmlspecialchars($user['social_security_number'] ?? '') ?>" placeholder="ΑΚΑ">
                <label for="social_security_number">Αριθμός Κοινωνικής Ασφάλισης (ΑΚΑ)*</label>
                <div class="error-message" id="social_security_number_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text"
       class="form-control"
       id="cypriot_id"
       name="cypriot_id"
       autocomplete="national-id"
       value="<?= htmlspecialchars($user['cypriot_id'] ?? '') ?>"
       placeholder="ΑΔΤ">
                <label for="cypriot_id">Αριθμός Ταυτότητας*</label>
                <div class="error-message" id="cypriot_id_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="postal_code" name="postal_code"
                       value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" placeholder="Τ.Κ.">
                <label for="postal_code">Ταχυδρομικός Κώδικας*</label>
                <div class="error-message" id="postal_code_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="street_address" name="street_address"
                       value="<?= htmlspecialchars($user['street_address'] ?? '') ?>" placeholder="Οδός και Αριθμός">
                <label for="street_address">Οδός και Αριθμός*</label>
                <div class="error-message" id="street_address_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="city" name="city"
                       value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="Πόλη">
                <label for="city">Πόλη*</label>
                <div class="error-message" id="city_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="country" name="country"
                       value="<?= htmlspecialchars($user['country'] ?? '') ?>" placeholder="Χώρα">
                <label for="country">Χώρα*</label>
                <div class="error-message" id="country_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="municipality" name="municipality"
                       value="<?= htmlspecialchars($user['municipality'] ?? '') ?>" placeholder="Δήμος">
                <label for="municipality">Δήμος</label>
                <div class="error-message" id="municipality_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="community" name="community"
                       value="<?= htmlspecialchars($user['community'] ?? '') ?>" placeholder="Κοινότητα">
                <label for="community">Κοινότητα</label>
                <div class="error-message" id="community_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nationality" name="nationality"
                       value="<?= htmlspecialchars($user['nationality'] ?? '') ?>" placeholder="Εθνικότητα">
                <label for="nationality">Εθνικότητα*</label>
                <div class="error-message" id="nationality_error"></div>
              </div>

              <h5 class="mt-5 mb-3 text-primary">Στοιχεία Επικοινωνίας</h5>

              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Email">
                <label for="email">Email*</label>
                <div class="error-message" id="email_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="university_email" name="university_email"
                       value="<?= htmlspecialchars($user['university_email'] ?? '') ?>" placeholder="University Email">
                <label for="university_email">University Email</label>
                <div class="error-message" id="university_email_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="mobile_phone" name="mobile_phone"
                       value="<?= htmlspecialchars($user['mobile_phone'] ?? '') ?>" placeholder="Κινητό Τηλέφωνο">
                <label for="mobile_phone">Κινητό Τηλέφωνο*</label>
                <div class="error-message" id="mobile_phone_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="landline_phone" name="landline_phone"
                       value="<?= htmlspecialchars($user['landline_phone'] ?? '') ?>" placeholder="Σταθερό Τηλέφωνο">
                <label for="landline_phone">Σταθερό Τηλέφωνο</label>
                <div class="error-message" id="landline_phone_error"></div>
              </div>

              <h5 class="mt-5 mb-3 text-primary">Ασφάλεια</h5>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Τρέχων Κωδικός">
                <label for="old_password">Τρέχων Κωδικός</label>
                <div class="error-message" id="old_password_error"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Νέος Κωδικός">
                <label for="password">Νέος Κωδικός</label>
                <div class="error-message" id="password_error"></div>
                <div id="password_strength" class="mt-1"></div>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Επιβεβαίωση Κωδικού">
                <label for="confirm_password">Επιβεβαίωση Κωδικού</label>
                <div class="error-message" id="confirm_password_error"></div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary w-100 py-3">Αποθήκευση Αλλαγών</button>
                <a href="../index.php" class="btn btn-secondary w-100 py-3">Ακύρωση</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Edit Profile Form End -->

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

    <!-- Real-time Validations -->
    <script>
    const validators = {
      dob: v => v ? '' : 'Η ημερομηνία γέννησης είναι υποχρεωτική.',
      gender: v => v ? '' : 'Το φύλο είναι υποχρεωτικό.',
      social_security_number: v => /^\d{9}$/.test(v) ? '' : 'Ο ΑΚΑ πρέπει να έχει 9 αριθμούς.',
      cypriot_id: v => /^\d{10}$/.test(v)? '': 'Ο Αριθμός Ταυτότητας πρέπει να αποτελείται από 10 ψηφία.',

      postal_code: v => /^\d{4}$/.test(v) ? '' : 'Ο ΤΚ πρέπει να έχει 4 ψηφία.',
      street_address: v => v.trim() ? '' : 'Η οδός & αριθμός είναι υποχρεωτικά.',
      city: v => v.trim() ? '' : 'Η πόλη είναι υποχρεωτική.',
      country: v => v.trim() ? '' : 'Η χώρα είναι υποχρεωτική.',
      municipality: _ => '',  // προαιρετικό
      community: _ => '',     // προαιρετικό
      nationality: v => v.trim() ? '' : 'Η εθνικότητα είναι υποχρεωτική.',
      email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Εισάγετε έγκυρο email.',
      university_email: v => !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Εισάγετε έγκυρο πανεπιστημιακό email.',
      mobile_phone: v => /^(?:\+357)?9\d{7}$/.test(v) ? '' : 'Το κινητό πρέπει να ξεκινά με 9 και να έχει 8 ψηφία.',
      landline_phone: v => !v || /^(?:\+357)?2\d{7}$/.test(v) ? '' : 'Το σταθερό πρέπει να ξεκινά με 2 και να έχει 8 ψηφία.',
      old_password: _ => '',   // optional
      password: v => {
        if (!v) return '';
        const errs = [];
        if (v.length<8) errs.push('>=8 χαρακτήρες');
        if (!/[A-Z]/.test(v)) errs.push('1 κεφαλαίο');
        if (!/[a-z]/.test(v)) errs.push('1 πεζό');
        if (!/\d/.test(v)) errs.push('1 αριθμό');
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(v)) errs.push('1 ειδικό σύμβολο');
        return errs.length ? 'Ο κωδικός πρέπει: '+errs.join(', ') : '';
      },
      confirm_password: v => v===document.getElementById('password').value ? '' : 'Οι κωδικοί δεν ταιριάζουν.'
    };

    function validateField(id) {
      const val = document.getElementById(id).value;
      const err = validators[id](val);
      document.getElementById(id+'_error').textContent = err;
      return !err;
    }

    document.addEventListener('DOMContentLoaded', () => {
      Object.keys(validators).forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', () => {
          validateField(id);
          if (id==='password') {
            const msg = validators.password(el.value);
            const strength = document.getElementById('password_strength');
            strength.textContent = msg
              ? ''
              : 'Ο κωδικός πληροί όλες τις απαιτήσεις';
            strength.className = msg ? 'text-danger' : 'text-success';
          }
        });
      });

      document.getElementById('editProfileForm').addEventListener('submit', e => {
        let ok = true;
        Object.keys(validators).forEach(id => {
          if (!validateField(id)) ok = false;
        });
        if (!ok) {
          e.preventDefault();
          return false;
        }
      });
    });
    </script>
    <script>
    // Password validation function
    function validatePassword(password) {
        const errors = [];
        if (password.length < 8) {
            errors.push("Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες");
        }
        if (!/[A-Z]/.test(password)) {
            errors.push("Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα κεφαλαίο γράμμα");
        }
        if (!/[a-z]/.test(password)) {
            errors.push("Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα πεζό γράμμα");
        }
        if (!/[0-9]/.test(password)) {
            errors.push("Ο κωδικός πρέπει να περιέχει τουλάχιστον έναν αριθμό");
        }
        if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
            errors.push("Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα ειδικό σύμβολο (!@#$%^&*(),.?\":{}|<>)");
        }
        return errors;
    }

    function updatePasswordStrength(password) {
        const strengthIndicator = document.getElementById('password_strength');
        const errors = validatePassword(password);

        if (!strengthIndicator) return;

        if (password.length === 0) {
            strengthIndicator.innerHTML = '';
            return;
        }

        strengthIndicator.innerHTML = errors.length > 0
            ? errors.map(e => `<div class="text-danger"><i class="fas fa-times-circle"></i> ${e}</div>`).join('')
            : '<div class="text-success"><i class="fas fa-check-circle"></i> Ο κωδικός πληροί όλες τις απαιτήσεις</div>';
    }

    function checkPasswordMatch(password, confirmPassword) {
        const matchIndicator = document.getElementById('password_match');
        if (!matchIndicator) return;

        matchIndicator.innerHTML = confirmPassword.length === 0
            ? ''
            : (password === confirmPassword
                ? '<div class="text-success"><i class="fas fa-check-circle"></i> Οι κωδικοί ταιριάζουν</div>'
                : '<div class="text-danger"><i class="fas fa-times-circle"></i> Οι κωδικοί δεν ταιριάζουν</div>');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('input', function () {
                updatePasswordStrength(this.value);
                checkPasswordMatch(this.value, confirmPasswordInput.value);
            });

            confirmPasswordInput.addEventListener('input', function () {
                checkPasswordMatch(passwordInput.value, this.value);
            });
        }
    });

    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
        e.preventDefault();

        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        const formData = new FormData(this);
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');

        if (password) {
            const errors = validatePassword(password);
            if (errors.length > 0) {
                const errEl = document.getElementById('password_error');
                if (errEl) errEl.textContent = errors[0];
                return;
            }
            if (password !== confirmPassword) {
                const matchEl = document.getElementById('confirm_password_error');
                if (matchEl) matchEl.textContent = "Οι κωδικοί δεν ταιριάζουν";
                return;
            }
        }

        fetch('../php/edit_user.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Επιτυχία',
                        text: 'Οι αλλαγές αποθηκεύτηκαν επιτυχώς!',
                        confirmButtonText: 'ΟΚ',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = '../index.php';
                    });
                } else {
                    for (const [field, msg] of Object.entries(data.errors)) {
                        const errorEl = document.getElementById(`${field}_error`);
                        if (errorEl) errorEl.textContent = msg;
                    }

                    if (data.errors.general) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Σφάλμα',
                            text: data.errors.general
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Σφάλμα',
                    text: 'Απέτυχε η αποθήκευση των αλλαγών.'
                });
            });
    });
</script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
</html> 