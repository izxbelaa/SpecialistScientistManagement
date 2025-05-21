<?php
// html/assign-reviewers.php
session_start();
require_once __DIR__ . '/../php/session_check.php';
require_once __DIR__ . '/../php/config.php';

// Which request_id are we editing?
$requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

// Fetch all request_templates
try {
    $stmt      = $pdo->query("SELECT id, title FROM request_templates ORDER BY title");
    $templates = $stmt->fetchAll();
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

// Fetch all evaluators (users of type 3)
try {
    $stmt      = $pdo->query("
      SELECT id, CONCAT(first_name,' ',last_name) AS name
      FROM users
      WHERE type_of_user = 3
      ORDER BY name
    ");
    $reviewers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

// Fetch already‐assigned for pre‐checking
$assigned = [];
if ($requestId) {
    $stmt = $pdo->prepare("SELECT user_id FROM evaluators WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $assigned = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <title>Ανάθεση Αξιολογητών – CUT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../assets/img/logo.png" rel="icon">

  <!-- Google Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&
          family=Nunito:wght@600;700;800&display=swap"
    rel="stylesheet"
  >
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css"
    rel="stylesheet"
  >
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"
    rel="stylesheet"
  >

  <!-- Bootstrap & Template CSS -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css.php" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="evaluators-page">

  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <img src="../assets/img/logocut.png" width="150" height="60" alt="CUT Logo">
    </a>
    <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="../index.php" class="nav-item nav-link">Αρχικη</a>
        <a href="about.php" class="nav-item nav-link">Σχετικα</a>
        <a href="application.php" class="nav-item nav-link">Applications</a>
        <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'Διαχειριστής' || $_SESSION['user_type'] == 5)): ?>
        <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Καταχωρίσεις</a>
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
  <div class="container-fluid bg-primary py-5 mb-5 page-header evaluators-header wow fadeIn">
    <div class="container py-5 text-center wow fadeInUp">
        <h1 class="display-3 text-white">Ανάθεση Αξιολογητών</h1>
        <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.2s">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Ανάθεση Αξιολογητών</li>
            </ol>
        </nav>
    </div>
  </div>
  <!-- Header End -->

  <!-- Form Start -->
  <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.2s">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <form id="assignReviewersForm"
                method="POST"
                action="../php/assign-evaluators.php">
            
            <!-- REQUEST dropdown -->
            <div class="form-floating mb-3">
              <select class="form-select"
                      id="requestSelect"
                      name="request_id"
                      data-selected="<?= $requestId ?>"
                      required
                      onchange="if(this.value) location.search='?request_id='+this.value;">
                <option selected disabled value="">Επιλέξτε Αίτηση</option>
                <?php foreach($templates as $t): ?>
                  <option value="<?= $t['id'] ?>"
                    <?= $t['id']===$requestId?'selected':''?>>
                    <?= htmlspecialchars($t['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="requestSelect">Αίτηση</label>
            </div>

            <!-- REVIEWERS checkboxes -->
            <div class="mb-4">
              <div class="border rounded p-3 shadow-sm">
                <strong>Αξιολογητές</strong>
                <small class="text-muted d-block mb-2">Επιλέξτε τουλάχιστον έναν</small>
                <div id="reviewersCheckboxes">
                  <?php foreach($reviewers as $r): ?>
                    <div class="form-check">
                      <input class="form-check-input"
                             type="checkbox"
                             name="reviewers[]"
                             value="<?= $r['id'] ?>"
                             id="rev<?= $r['id'] ?>"
                             <?= in_array($r['id'],$assigned)?'checked':''?>>
                      <label class="form-check-label" for="rev<?= $r['id'] ?>">
                        <?= htmlspecialchars($r['name']) ?>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div id="reviewersError" class="text-danger mt-2"></div>
              </div>
            </div>

            <button type="submit" class="btn btn-assign w-100 py-3">Ανάθεση</button>
          </form>
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

  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/assign-reviewers.js"></script>
  <script>
    new WOW().init();
  </script>
</body>
</html>
