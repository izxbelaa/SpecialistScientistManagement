<?php
session_start();
require_once __DIR__ . '/../php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

$type = $currentUser['type_of_user'];

// Moodle API setup
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'http://localhost/SpecialistScientistManagement/moodle/webservice/rest/server.php';

function checkMoodleAccess($email, $token, $baseUrl) {
    $url = $baseUrl . '?' . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email
    ]);
    $resp = json_decode(file_get_contents($url), true);
    return !empty($resp[0]['id']) ? $resp[0] : false;
}

$ees = $pdo->query("SELECT * FROM users WHERE type_of_user = 2")->fetchAll(PDO::FETCH_ASSOC);
$rows = [];
foreach ($ees as $i => $ee) {
    $moodleUser = checkMoodleAccess($ee['email'], $token, $baseUrl);
    $isEnabled = $moodleUser ? true : false;
    $rows[] = [
        'aa' => $i + 1,
        'name' => htmlspecialchars($ee['first_name'] . ' ' . $ee['last_name']),
        'email' => htmlspecialchars($ee['email']),
        'lmsStatus' => $isEnabled ? 'Έχει πρόσβαση' : 'Χωρίς πρόσβαση',
        'actions' =>
            '<button class="btn btn-sm ' . ($isEnabled ? 'btn-warning' : 'btn-primary') . ' me-1 toggle-lms-access" data-user-id="' . $ee['id'] . '" data-enabled="' . ($isEnabled ? '1' : '0') . '" title="' . ($isEnabled ? 'Απενεργοποίηση πρόσβασης' : 'Ενεργοποίηση πρόσβασης') . '">' .
                '<i class="fas ' . ($isEnabled ? 'fa-user-slash' : 'fa-user-check') . '"></i>' .
            '</button>' .
            '<button class="btn btn-sm btn-success" title="Πρόσβαση σε άλλο μάθημα"><i class="fas fa-plus"></i></button>'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>LMS Sync Management - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="LMS sync management page" name="keywords">
    <meta content="LMS sync management for Cyprus University of Technology" name="description">
    <link href="../assets/img/logo.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css.php" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"/>
    <style>
        html, body {
            height: 100%;
            min-height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1 0 auto;
        }
        .site-footer {
            flex-shrink: 0;
        }
        /* DataTables Styling */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 1em;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: auto;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
        }
        .dataTables_info {
            padding-top: 0.85em;
        }
        .dataTables_paginate {
            text-align: center;
            padding: 1em 0;
        }
        .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #0d6efd;
            cursor: pointer;
            min-width: 30px;
            display: inline-block;
            text-align: center;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }
        .dataTables_paginate .paginate_button:hover:not(.current) {
            background-color: #e9ecef;
            color: #0a58ca;
            border-color: #dee2e6;
            text-decoration: none;
        }
        table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }
        table.dataTable thead th.sorting:after,
        table.dataTable thead th.sorting_asc:after,
        table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #6c757d;
        }
        table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
        }
        table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }
        table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="../assets/js/lms-table.js"></script>
</head>
<body>
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
                    <a href="lms_sync.php" class="dropdown-item active">Πρόσβαση στο Moodle</a>
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
<div class="container-fluid bg-primary py-5 mb-5 page-header" style="background: url('../assets/img/lms.jpg') center center/cover no-repeat;">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        <h1 class="display-3 text-white animated slideInDown">Πρόσβαση στο Moodle</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page">Πρόσβαση στο Moodle</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2 class="mb-0">Πρόσβαση στο Moodle</h2>
              <!-- Add action button here if needed -->
            </div>
            <?php if ($type == 2): // EE user ?>
              <h4>Η πρόσβασή μου στο Moodle</h4>
              <?php $moodleUser = checkMoodleAccess($currentUser['email'], $token, $baseUrl); ?>
              <?php if ($moodleUser): ?>
                <div class="alert alert-success">Έχετε ενεργή πρόσβαση στο Moodle. <a href="http://localhost/SpecialistScientistManagement/moodle/" target="_blank">Μετάβαση στο LMS</a></div>
              <?php else: ?>
                <div class="alert alert-danger">Δεν έχετε ενεργή πρόσβαση στο Moodle.</div>
              <?php endif; ?>
            <?php elseif (in_array($type, [4,5])): ?>
              <h4>Λίστα EE με πρόσβαση στο Moodle</h4>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group" style="max-width: 400px;">
                  <span class="input-group-text bg-white border-end-0" id="search-icon">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Αναζήτηση..." aria-label="Αναζήτηση" aria-describedby="search-icon">
                </div>
                <select id="entriesSelect" class="form-select form-select-sm ms-2" style="width: 120px;">
                  <option value="5">5 εγγραφές</option>
                  <option value="10">10 εγγραφές</option>
                  <option value="25">25 εγγραφές</option>
                </select>
              </div>
              <div class="table-responsive">
                <table id="eesTable" class="table table-striped align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Α/Α</th>
                      <th>Όνομα</th>
                      <th>Email</th>
                      <th>Κατάσταση LMS</th>
                      <th>Ενέργειες</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Rows will be rendered by JS -->
                  </tbody>
                </table>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div><span id="entriesCount">Εμφάνιση 0 έως 0 από 0 εγγραφές</span></div>
              </div>
              <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="paginationControls"></ul>
              </nav>
              <script>
              const lmsData = <?php echo json_encode($rows, JSON_UNESCAPED_UNICODE); ?>;
              </script>
              <script src="../assets/js/lms-sync-table.js"></script>
              <script src="../assets/js/lms-sync-actions.js"></script>
              <script src="../assets/js/lms-sync-add-teacher.js"></script>
            <?php else: ?>
              <div class="alert alert-info">Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- Scripts -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../lib/wow/wow.min.js"></script>
<script src="../lib/easing/easing.min.js"></script>
<script src="../lib/waypoints/waypoints.min.js"></script>
<script src="../lib/owlcarousel/owl.carousel.min.js"></script>
<script src="../assets/js/main.js"></script>

<!-- Footer Start -->
<div class="container-fluid bg-dark text-light pt-5 mt-5 wow fadeIn site-footer" data-wow-delay="0.1s">
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

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTeacherModalLabel">Προσθήκη ως Διδάσκων σε Μάθημα</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="coursesList">
          <div class="text-center my-3">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            <div>Φόρτωση μαθημάτων...</div>
          </div>
        </div>
        <div id="addTeacherResult" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
      </div>
    </div>
  </div>
</div>
<!-- End Add Teacher Modal -->
</body>
</html> 