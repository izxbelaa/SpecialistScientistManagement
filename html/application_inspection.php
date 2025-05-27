<?php
session_start();
require_once __DIR__ . '/../php/session_check.php';
require_once __DIR__ . '/../php/config.php';
include '../php/config.php';

$needsProfileCompletion = false;
$userData = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($_SESSION['username']) && isset($userData['name'])) {
    $_SESSION['username'] = $userData['name'];
}


    $requiredFields = [
        'dob', 'gender', 'social_security_number', 'cypriot_id', 'postal_code',
        'street_address', 'city', 'country', 'nationality', 'mobile_phone', 'email'
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
  <title>Configure Submission Period - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Submission period page" name="keywords">
  <meta content="Submission period page for Cyprus University of Technology" name="description">
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
    .search-header {
      background-color: #ffffff;
      padding: 2rem 0;
      margin-bottom: 2rem;
    }
    .search-box-wrapper {
      max-width: 700px;
      margin: 0 auto;
      position: relative;
    }
    .search-box-wrapper input {
      padding-right: 40px;
      border-radius: 8px;
    }
    .search-box-wrapper i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #4c8ad5;
    }
    .btn-orange {
      background-color: #0099ff;
      color: white;
      border: none;
    }
    .btn-orange:hover {
      background-color: #f3ece7;
    }
    /* Add sorting styles */
    #inspectionTable th {
      position: relative;
      cursor: pointer;
      user-select: none;
      padding-right: 25px;
    }
    #inspectionTable th:hover {
      background-color: #f8f9fa;
    }
    .sort-arrow {
      display: inline-block;
      margin-left: 15px;
      transition: color 0.2s;
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
    }
    #inspectionTable th[data-sort="asc"] .sort-arrow,
    #inspectionTable th[data-sort="desc"] .sort-arrow {
      color: #0099ff;
    }
  </style>
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
            <a href="../index.php" class="nav-item nav-link">Αρχικη</a>
            <a href="about.php" class="nav-item nav-link">Σχετικα</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Αιτησεις</a>
                <div class="dropdown-menu fade-down m-0">
                    <a href="application.php" class="dropdown-item">Συμπλήρωση Αιτήσεων</a>
                    <a href="application-status.php" class="dropdown-item">Κατάσταση Αιτήσεων</a>
                    <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Διαχειριστής' || $_SESSION['user_type'] == 'Επιθεωρητής')): ?>
                    <a href="application_inspection.php" class="dropdown-item active">Επιθεώρηση Αιτήσεων</a>
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
  <div class="container-fluid bg-primary py-5 mb-5 page-header" style="background: url('../assets/img/inspection.jpeg') center center/cover no-repeat;">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <h1 class="display-3 text-white animated slideInDown">Επιθεώρηση Αιτήσεων</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">Επιθεώρηση</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
      <!-- Header End -->

<!-- Πίνακας Αιτήσεων Start -->
<div class="container mt-4">
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0">Λίστα Αιτήσεων</h2>
      </div>
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <input type="text" class="form-control" id="searchInput" style="max-width:350px;min-width:200px;" placeholder="Αναζήτηση Αιτήσεων...">
        <select id="entriesPerPage" class="form-control" style="max-width:150px;min-width:120px;">
          <option value="5">5 Αιτήσεις</option>
          <option value="10" selected>10 Αιτήσεις</option>
          <option value="20">20 Αιτήσεις</option>
          <option value="100">100 Αιτήσεις</option>
        </select>
      </div>
      <div class="table-responsive">
        <table id="inspectionTable" class="table table-striped">
          <thead>
            <tr>
              <th style="width: 70px; min-width: 60px;">Α/Α</th>
              <th>Ονοματεπώνυμο Αιτούντα</th>
              <th>Όνομα Αίτησης</th>
              <th>Περιγραφή</th>
              <th>Ενέργειες</th>
            </tr>
          </thead>
          <tbody id="inspectionTableBody">
            <!-- JS will fill this -->
          </tbody>
        </table>
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="paginationControls">
          <!-- Pagination will be loaded here -->
        </ul>
      </nav>
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
                    <li class="mb-2"><i class="fa fa-chevron-right me-2 text-primary"></i><a href="https://cei326-omada2.cut.ac.cy/moodle" class="text-light text-decoration-none" target="_blank">eLearning (Moodle)</a></li>
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    
  function updateApplicationStatus(id, status) {
  fetch('../php/update_application_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({ id: id, status: status })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      Swal.fire('Επιτυχία!', 'Η αίτηση #' + id + ' ενημερώθηκε.', 'success')
        .then(() => location.reload());
    } else {
      Swal.fire('Σφάλμα!', data.error || 'Κάτι πήγε στραβά.', 'error');
    }
  })
  .catch(() => {
    Swal.fire('Σφάλμα!', 'Δεν ήταν δυνατή η ενημέρωση.', 'error');
  });
}

function acceptApplication(id) {
  Swal.fire({
    title: 'Επιβεβαίωση',
    text: 'Θέλεις να αποδεχτείς την αίτηση #' + id + ';',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ναι, αποδοχή'
  }).then((result) => {
    if (result.isConfirmed) {
      updateApplicationStatus(id, 1);
    }
  });
}

function rejectApplication(id) {
  Swal.fire({
    title: 'Επιβεβαίωση',
    text: 'Θέλεις να απορρίψεις την αίτηση #' + id + ';',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ναι, απόρριψη'
  }).then((result) => {
    if (result.isConfirmed) {
      updateApplicationStatus(id, -1);
    }
  });
}


  document.addEventListener('DOMContentLoaded', () => {
    fetch('../php/get-requests.php')
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById('inspectionTableBody');
        tbody.innerHTML = '';

        if (!data || data.length === 0 || data.error) {
          tbody.innerHTML = '<tr><td colspan="5">Δεν υπάρχουν αιτήσεις ή παρουσιάστηκε σφάλμα.</td></tr>';
          if (data.error) console.error(data.error);
          return;
        }

     data.forEach((row, index) => {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>${index + 1}</td>
    <td>${row.requester_name || 'Άγνωστος'}</td>
    <td>${row.request_title || '-'}</td>
    <td>${row.description || '-'}</td>
    <td>
      <button class="btn btn-success btn-sm me-2" onclick="acceptApplication(${row.candidate_user_id})">Αποδοχή</button>
      <button class="btn btn-danger btn-sm me-2" onclick="rejectApplication(${row.candidate_user_id})">Απόρριψη</button>
     <button class="btn btn-info btn-sm text-white" onclick="downloadCV(${row.request_id})">Λήψη Βιογραφικού</button>
    </td>
  `;
  tbody.appendChild(tr);
});
      })
      .catch(error => {
        console.error('Σφάλμα κατά τη φόρτωση:', error);
        document.getElementById('inspectionTableBody').innerHTML =
          '<tr><td colspan="5">Σφάλμα κατά τη φόρτωση των αιτήσεων.</td></tr>';
      });
  });
  function downloadCV(requestId) {
    fetch(`../php/download_cv.php?request_id=${requestId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Create a Blob from the base64-encoded file
                const byteCharacters = atob(data.file);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'application/pdf' });

                // Create a link to download the Blob
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = data.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                Swal.fire('Σφάλμα!', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Σφάλμα!', 'Δεν ήταν δυνατή η λήψη του βιογραφικού.', 'error');
        });
}
</script>

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/application-inspection.js"></script>

</body>
</html>
