<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Forgot Password - Special Scientists - CUT</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <link href="../assets/img/logo.png" rel="icon" />
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="../../index.php" class="navbar-brand px-4 px-lg-5">
      <img src="../../assets/img/logocut.png" width="150" height="60" alt="Logo" />
    </a>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="../../index.php" class="nav-item nav-link">Home</a>
        <a href="../about.html" class="nav-item nav-link">About</a>
        <a href="../courses.html" class="nav-item nav-link">Courses</a>

        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
          <div class="dropdown-menu fade-down m-0">
            <a href="../team.html" class="dropdown-item">Our Team</a>
            <a href="html/assign-reviewers.html" class="dropdown-item">Ανάθεση Αξιολογητών</a>
            <a href="../testimonial.html" class="dropdown-item">Testimonial</a>
            <a href="../404.html" class="dropdown-item">404 Page</a>
          </div>
        </div>

        <a href="../../php/settings.php" class="nav-item nav-link">Settings</a>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <h1 class="display-3 text-white">Forgot Password</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a class="text-white" href="auth/login.php">Login</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">Forgot Password</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Forgot Password Form -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <form id="forgot-password-form">
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="email" name="email" placeholder="Ηλεκτρονική Διεύθυνση" required>
              <label for="email">Ηλεκτρονική Διεύθυνση *</label>
            </div>


            <button type="submit" class="btn btn-primary w-100 py-3">Αποστολή Συνδέσμου Επαναφοράς</button>
            <p class="mt-3 text-center">
              Θυμήθηκες τον κωδικό σου; <a href="auth/login.php">Σύνδεση</a>
            </p>
          </form>
          <div id="message" class="mt-3 text-center"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Verification Code Modal -->
  <div class="modal fade" id="codeModal" tabindex="-1" aria-labelledby="codeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="codeModalLabel">Εισάγετε τον Κωδικό Επαλήθευσης</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="codeForm">
            <div class="mb-3">
              <label for="verificationCode" class="form-label">Κωδικός Επαλήθευσης</label>
              <input type="text" class="form-control" id="verificationCode" placeholder="Εισάγετε τον κωδικό σας" required>
            </div>
            <div id="codeError" class="text-danger mb-3" style="display: none;"></div>
            <button type="submit" class="btn btn-primary">Υποβολή Κωδικού</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('forgot-password-form').addEventListener('submit', function (e) {
      e.preventDefault();
      const email = document.getElementById('email').value.trim();
      const messageDiv = document.getElementById('message');

      fetch('../php/send-reset-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
          new bootstrap.Modal(document.getElementById('codeModal')).show();
        } else {
          messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
      })
      .catch(() => {
        messageDiv.innerHTML = `<div class="alert alert-danger">Σφάλμα αποστολής. Προσπαθήστε ξανά.</div>`;
      });
    });

    document.getElementById('codeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const code = document.getElementById('verificationCode').value.trim();
      const codeError = document.getElementById('codeError');

      fetch('../php/verify-code.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: code })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = "reset-password-form.php";
        } else {
          codeError.style.display = 'block';
          codeError.textContent = data.message || "Λανθασμένος κωδικός. Προσπαθήστε ξανά.";
        }
      })
      .catch(() => {
        codeError.style.display = 'block';
        codeError.textContent = "Σφάλμα επικοινωνίας. Προσπαθήστε ξανά.";
      });
    });
  </script>
</body>

</html>
