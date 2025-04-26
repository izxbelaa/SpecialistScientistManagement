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
</head>
<body>

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

            <button type="submit" class="btn btn-success w-100 py-3">Ανάθεση</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- … your footer here … -->

  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/assign-reviewers.js"></script>
</body>
</html>
