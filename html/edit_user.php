<?php
include '../php/config.php';
session_start();

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
                <a href="../index.php" class="nav-item nav-link">Home</a>
                <a href="../about.html" class="nav-item nav-link">About</a>
                <a href="../courses.html" class="nav-item nav-link">Courses</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="../team.html" class="dropdown-item">Our Team</a>
                        <a href="../testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="../404.html" class="dropdown-item">404 Page</a>
                    </div>
                </div>
                <a href="../php/settings.php" class="nav-item nav-link">Settings</a>
            </div>
            <div class="d-flex align-items-center">
                <span class="nav-item nav-link me-3"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="../index.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
                    <i class="fa fa-arrow-left me-3"></i>Back
                </a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Επεξεργασία Προφίλ</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
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
                    <form id="editProfileForm" method="POST" action="../php/edit_user.php">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" placeholder="First Name" required>
                            <label for="first_name">Όνομα *</label>
                            <div id="first_name_error" class="error-message"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                   value="<?php echo htmlspecialchars($user['middle_name']); ?>" placeholder="Middle Name">
                            <label for="middle_name">Μεσαίο Όνομα</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" placeholder="Last Name" required>
                            <label for="last_name">Επώνυμο *</label>
                            <div id="last_name_error" class="error-message"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
                            <label for="email">Email *</label>
                            <div class="form-text">Απαιτείται ο τρέχων κωδικός για την αλλαγή του email.</div>
                            <div id="email_error" class="error-message"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Current Password">
                            <label for="old_password">Τρέχων Κωδικός *</label>
                            <div class="form-text">Απαιτείται για την αλλαγή του email ή του κωδικού.</div>
                            <div id="old_password_error" class="error-message"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="New Password">
                            <label for="password">Νέος Κωδικός</label>
                            <div class="form-text">Αφήστε κενό αν δεν θέλετε αλλαγή κωδικού.</div>
                            <div id="password_error" class="error-message"></div>
                            <div id="password_strength" class="mt-2"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                            <label for="confirm_password">Επιβεβαίωση Νέου Κωδικού</label>
                            <div id="confirm_password_error" class="error-message"></div>
                            <div id="password_match" class="mt-2"></div>
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
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Quick Link</h4>
                    <a class="btn btn-link" href="#">About Us</a>
                    <a class="btn btn-link" href="#">Contact Us</a>
                    <a class="btn btn-link" href="#">Privacy Policy</a>
                    <a class="btn btn-link" href="#">Terms & Condition</a>
                    <a class="btn btn-link" href="#">FAQs & Help</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@example.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Gallery</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="../assets/img/course-1.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right Reserved.
                    </div>
                </div>
            </div>
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
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                errors.push("Ο κωδικός πρέπει να περιέχει τουλάχιστον ένα ειδικό σύμβολο (!@#$%^&*(),.?\":{}|<>)");
            }
            return errors;
        }

        // Function to update password strength indicator
        function updatePasswordStrength(password) {
            const strengthIndicator = document.getElementById('password_strength');
            const errors = validatePassword(password);
            
            if (password.length === 0) {
                strengthIndicator.innerHTML = '';
                return;
            }

            if (errors.length > 0) {
                strengthIndicator.innerHTML = errors.map(error => 
                    `<div class="text-danger"><i class="fas fa-times-circle"></i> ${error}</div>`
                ).join('');
            } else {
                strengthIndicator.innerHTML = '<div class="text-success"><i class="fas fa-check-circle"></i> Ο κωδικός πληροί όλες τις απαιτήσεις</div>';
            }
        }

        // Function to check if passwords match
        function checkPasswordMatch(password, confirmPassword) {
            const matchIndicator = document.getElementById('password_match');
            if (confirmPassword.length === 0) {
                matchIndicator.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchIndicator.innerHTML = '<div class="text-success"><i class="fas fa-check-circle"></i> Οι κωδικοί ταιριάζουν</div>';
            } else {
                matchIndicator.innerHTML = '<div class="text-danger"><i class="fas fa-times-circle"></i> Οι κωδικοί δεν ταιριάζουν</div>';
            }
        }

        // Add event listeners for password validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            // Add real-time validation
            passwordInput.addEventListener('input', function() {
                updatePasswordStrength(this.value);
                checkPasswordMatch(this.value, confirmPasswordInput.value);
            });

            confirmPasswordInput.addEventListener('input', function() {
                checkPasswordMatch(passwordInput.value, this.value);
            });
        });

        // Form submission handler
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            // Validate password if provided
            if (password) {
                const errors = validatePassword(password);
                if (errors.length > 0) {
                    document.getElementById('password_error').textContent = errors[0];
                    return;
                }
                
                if (password !== confirmPassword) {
                    document.getElementById('confirm_password_error').textContent = "Οι κωδικοί δεν ταιριάζουν";
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
                    alert('Οι αλλαγές αποθηκεύτηκαν επιτυχώς!');
                    window.location.href = '../index.php';
                } else {
                    // Display errors
                    Object.entries(data.errors).forEach(([field, message]) => {
                        const errorElement = document.getElementById(`${field}_error`);
                        if (errorElement) {
                            errorElement.textContent = message;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Παρουσιάστηκε σφάλμα κατά την αποθήκευση των αλλαγών.');
            });
        });
    </script>
</body>
</html> 