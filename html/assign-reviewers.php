<?php
// html/assign-reviewers.php
session_start();

// 1) enforce login
require_once __DIR__ . '/../php/session_check.php';

// 2) load your PDO connection
require_once __DIR__ . '/../php/config.php';

// 3) figure out which template/request we're editing
$requestId = isset($_GET['template_id']) ? (int)$_GET['template_id'] : 0;

// 4) fetch all request templates
try {
    $stmt       = $pdo->query("SELECT id, title FROM request_templates ORDER BY title");
    $templates  = $stmt->fetchAll();
} catch (PDOException $e) {
    die("DB error fetching templates: " . $e->getMessage());
}
// fetch only users with type = 3 (evaluators)
try {
  $stmt = $pdo->query(
    "SELECT 
       id, 
       CONCAT(first_name,' ',last_name) AS name 
     FROM users 
     WHERE type_of_user = 3 
     ORDER BY name"
  );
  $reviewers = $stmt->fetchAll();
} catch (PDOException $e) {
  die("DB error fetching evaluators: " . $e->getMessage());
}

// 6) fetch already-assigned reviewers for this request
$assignedIds = [];
if ($requestId) {
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM evaluators WHERE request_id = ?");
        $stmt->execute([$requestId]);
        // FETCH_COLUMN gives us a flat array of user_id values
        $assignedIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        die("DB error fetching assigned reviewers: " . $e->getMessage());
    }
}

// 7) success flag (for SweetAlert)
$showSuccess = isset($_GET['success']) && $_GET['success'] === '1';
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
</head>
<body>
  <!-- Spinner -->
  <div id="spinner" class="show bg-white position-fixed translate-middle
              w-100 vh-100 top-50 start-50 d-flex
              align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width:3rem; height:3rem;" role="status">
      <span class="sr-only">Loading…</span>
    </div>
  </div>

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
        <a href="../index.php" class="nav-item nav-link">Home</a>
        <a href="../html/about.html" class="nav-item nav-link">About</a>
        <a href="../html/courses.php" class="nav-item nav-link">Courses</a>
        <a href="../html/departments.php" class="nav-item nav-link">Departments</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="assign-reviewers.php" class="dropdown-item active">Ανάθεση Αξιολογητών</a>
            <a href="../html/submission_period.php" class="dropdown-item">Submission Period</a>
            <a href="../html/testimonial.html" class="dropdown-item">Testimonial</a>
            <a href="../html/404.html" class="dropdown-item">404 Page</a>
          </div>
        </div>
        <a href="../php/settings.php" class="nav-item nav-link">Settings</a>
      </div>
      <div class="d-flex align-items-center">
        <span class="nav-item nav-link me-3">
          <?= htmlspecialchars($_SESSION['username']) ?>
        </span>
        <a href="../php/logout.php" class="btn btn-danger py-2 px-4">Logout</a>
      </div>
    </div>
  </nav>
  <!-- Navbar End -->

  <!-- Header Start -->
  <div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5 text-center">
      <h1 class="display-3 text-white">Ανάθεση Αξιολογητών</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
          <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
          <li class="breadcrumb-item text-white active" aria-current="page">Ανάθεση Αξιολογητών</li>
        </ol>
      </nav>
    </div>
  </div>
  <!-- Header End -->

  <!-- Form Start -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <form id="assignReviewersForm"
                method="POST"
                action="../php/assign-reviewers.php">
            <!-- Template Dropdown -->
            <div class="form-floating mb-3">
              <select class="form-select"
                      id="templateSelect"
                      name="template_id"
                      required
                      onchange="if(this.value) window.location.href='?template_id='+this.value;">
                <option selected disabled value="">Επιλέξτε Αίτηση</option>
                <?php foreach ($templates as $t): ?>
                  <option
                    value="<?= $t['id'] ?>"
                    <?= $t['id'] === $requestId ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($t['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="templateSelect">Αίτηση Προτύπου</label>
            </div>

            <!-- Reviewers Checkboxes -->
            <div class="mb-4">
              <div class="border rounded p-3 shadow-sm">
                <div class="mb-2">
                  <strong class="d-block">Αξιολογητές</strong>
                  <small class="text-muted">Επιλέξτε τουλάχιστον έναν</small>
                </div>
                <div id="reviewersCheckboxes">
                <?php foreach($reviewers as $r): ?>
  <div class="form-check">
    <input class="form-check-input"
           type="checkbox"
           name="reviewers[]"
           value="<?= $r['id'] ?>"
           id="rev<?= $r['id'] ?>">
    <label class="form-check-label" for="rev<?= $r['id'] ?>">
      <?= htmlspecialchars($r['name']) ?>
    </label>
  </div>
<?php endforeach; ?>

</div>

              </div>
              <div id="reviewersError" class="text-danger mt-2"></div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-3">Ανάθεση</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Form End -->

  <!-- Footer Start -->
  <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
    <div class="container py-5">
      <div class="row g-5">
        <!-- your footer columns here -->
      </div>
    </div>
  </div>
  <!-- Footer End -->

  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">
    <i class="bi bi-arrow-up"></i>
  </a>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>

  <!-- SweetAlert2 Success Popup -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      if (params.get('success') === '1') {
        Swal.fire({
          title: 'Επιτυχία!',
          text: 'Οι αξιολογητές ανατέθηκαν με επιτυχία.',
          icon: 'success',
          confirmButtonText: 'Το κατάλαβα'
        });
      }
    });
  </script>

  <!-- Your form-validation & AJAX (if any) -->
  <script src="../assets/js/assign-reviewers.js"></script>
</body>
</html>
