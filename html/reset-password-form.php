<?php
session_start();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Special Scientists - CUT | Επαναφορά Κωδικού</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../assets/img/logo.png" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/lib/animate/animate.min.css" rel="stylesheet">
    <link href="/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css.php" rel="stylesheet">
</head>
<body>

<!-- Spinner -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="/index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="../assets/img/logocut.png" alt="Tepak Logo" width="150" height="60">
    </a>
</nav>

<!-- Header -->
<div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h1 class="display-3 text-white animated slideInDown">Επαναφορά Κωδικού</h1>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Form -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Ορίστε νέο κωδικό</h3>

                        <?php if (isset($_SESSION['reset_error'])): ?>
                          <div class="alert alert-danger text-center">
                            <?php echo $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?>
                          </div>
                        <?php endif; ?>

                        <form action="../php/process-reset.php" method="post" id="reset-password-form">
                            <input type="hidden" name="code" value="<?php echo htmlspecialchars($_GET['code'] ?? ''); ?>">

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="new-password" name="new_password"
                                       placeholder="Νέος Κωδικός" required>
                                <label for="new-password">Νέος Κωδικός</label>
                                <div class="invalid-feedback">Ο κωδικός πρέπει να πληροί τις απαιτήσεις.</div>
                            </div>

                            <div id="password-rules" class="mb-3 small text-muted"></div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="confirm-password" name="confirm_password"
                                       placeholder="Επιβεβαίωση Κωδικού" required>
                                <label for="confirm-password">Επιβεβαίωση Κωδικού</label>
                                <div class="invalid-feedback">Οι κωδικοί δεν ταιριάζουν.</div>
                            </div>

                            <div id="match-message" class="mb-3 small"></div>

                            <button type="submit" class="btn btn-primary w-100 py-3">Επαναφορά Κωδικού</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const passwordInput = document.getElementById('new-password');
  const confirmInput = document.getElementById('confirm-password');
  const form = document.getElementById('reset-password-form');
  const rulesContainer = document.getElementById('password-rules');
  const matchMessage = document.getElementById('match-message');

  const rules = {
    length: { regex: /.{8,}/, label: "Τουλάχιστον 8 χαρακτήρες" },
    upper: { regex: /[A-Z]/, label: "Τουλάχιστον ένα κεφαλαίο γράμμα" },
    lower: { regex: /[a-z]/, label: "Τουλάχιστον ένα πεζό γράμμα" },
    number: { regex: /\d/, label: "Τουλάχιστον έναν αριθμό" },
    symbol: { regex: /[\W_]/, label: "Τουλάχιστον ένα σύμβολο (π.χ. !@#)" }
  };

  function updateRules(password) {
    rulesContainer.innerHTML = '';
    Object.entries(rules).forEach(([key, rule]) => {
      const valid = rule.regex.test(password);
      const color = valid ? 'text-success' : 'text-danger';
      const item = `<div class="${color}">${rule.label}</div>`;
      rulesContainer.insertAdjacentHTML('beforeend', item);
    });
  }

  function validateConfirm() {
    const match = passwordInput.value === confirmInput.value && confirmInput.value !== '';
    matchMessage.textContent = match ? 'Οι κωδικοί ταιριάζουν' : 'Οι κωδικοί δεν ταιριάζουν';
    matchMessage.className = match ? 'text-success small' : 'text-danger small';
    return match;
  }

  passwordInput.addEventListener('input', function () {
    updateRules(passwordInput.value);
    validateConfirm();
  });

  confirmInput.addEventListener('input', validateConfirm);

  form.addEventListener('submit', function (e) {
    const allValid = Object.values(rules).every(rule => rule.regex.test(passwordInput.value));
    const match = validateConfirm();

    if (!allValid || !match) {
      e.preventDefault();
    }
  });
});
</script>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/lib/wow/wow.min.js"></script>
<script src="/lib/easing/easing.min.js"></script>
<script src="/lib/waypoints/waypoints.min.js"></script>
<script src="/lib/owlcarousel/owl.carousel.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
