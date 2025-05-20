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
      <img src="../assets/img/logocut.png" alt="Tepak Logo" width="150" height="60">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="../index.php" class="nav-item nav-link">Home</a>
        <a href="./about.html" class="nav-item nav-link">About</a>
        <a href="./courses.html" class="nav-item nav-link">Courses</a>
        <a href="./html/departments.php" class="nav-item nav-link">Departments</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="./team.html" class="dropdown-item">Our Team</a>
            <a href="./testimonial.html" class="dropdown-item">Testimonial</a>
            <a href="./404.html" class="dropdown-item">404 Page</a>
            <a href="./departments.php" class="dropdown-item">Departments</a>
          </div>
        </div>
        <a href="../php/settings.php" class="nav-item nav-link">Settings</a>
      </div>
      <!-- (Optional) You can remove or adjust the Join Now button if needed -->
     <?php if (isset($_SESSION['username'])): ?>
  <div class="dropdown pe-4">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <?php echo htmlspecialchars($_SESSION['username']); ?>
    </a>
    <ul class="dropdown-menu" aria-labelledby="userDropdown">
      <li><a class="dropdown-item" href="../php/logout.php">Αποσύνδεση</a></li>
      <li><a class="dropdown-item" href="../html/edit_user.php">Επεξεργασία Προφίλ</a></li>
    </ul>
  </div>
<?php else: ?>
  <a href="../html/auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
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
          <h1 class="display-3 text-white animated slideInDown">Επιθεώρηση Αιτήσεων</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a class="text-white" href="../index.php">Ταμπλό</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">Επιθεώρηση</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
      <!-- Header End -->

<!-- Πίνακας Αιτήσεων Start -->
  <div class="container mb-5">
    <h2 class="mb-4 text-center">Λίστα Αιτήσεων</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-striped text-center">
        <thead class="table-primary">
  <tr>
    <th>Α/Α</th>
    <th>Ονοματεπώνυμο Αιτούντα</th>
    <th>Όνομα Αίτησης</th>
    <th>Περιγραφή</th>
    <th>Ενέργειες</th>
  </tr>
</thead>
        <tbody id="applications-table-body">
          <!-- JS will fill this -->
        </tbody>
      </table>
    </div>
  </div>

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
        const tbody = document.getElementById('applications-table-body');
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
        document.getElementById('applications-table-body').innerHTML =
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


  <!-- Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>

</body>
</html>
