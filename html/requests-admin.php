<?php include '../php/session_check.php'; ?>
<?php
include '../php/get-user-type.php';

// Only allow access if user is "Διαχειριστής" (Admin in Greek)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "Διαχειριστής") {
    header("Location: ./auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Special Scientists - CUT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
    <link href="../assets/css/requests-admin.css" rel="stylesheet">

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
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="courses.html" class="nav-item nav-link">Courses</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="team.html" class="dropdown-item">Our Team</a>
                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="404.html" class="dropdown-item">404 Page</a>
                        <a href="admin-settings.php" class="dropdown-item">Admin Settings</a>
                        <a href="requests-admin.php" class="dropdown-item">Requests</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Settings</a>
            </div>
            <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="../php/logout.php">Logout</a></li>
                <!-- You can add more items here if needed -->
                </ul>
            </div>
            <?php else: ?>
            <a href="./auth/login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
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
                    <h1 class="display-3 text-white animated slideInDown">Requests</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Requests</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

<!-- Search + Add Request Row -->
<div class="container mb-4">
    <div class="row align-items-center">
      <!-- Search Box -->
      <div class="col-md-8">
        <div class="search-box-wrapper position-relative">
          <input type="text" class="form-control" id="searchInput" placeholder="Search requests...">
          <i class="fas fa-search"></i>
        </div>
      </div>
      <!-- Add Request Button -->
      <div class="col-md-4 text-end">
        <button class="btn btn-orange" id="addRequestBtn" data-bs-toggle="modal" data-bs-target="#addRequestModal">
          <i class="fas fa-plus me-2"></i>Add Request
        </button>
      </div>
    </div>
  </div>

  <!-- Requests Table -->
  <div class="container mt-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3"><i class="fas fa-list me-2"></i>Requests List</h5>
        <div class="table-responsive p-3">
          <table id="requestsTable" class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>A/A</th>
                <th>Template Title</th>
                <th>Description</th>
                <th>Start Date/Time</th>
                <th>End Date/Time</th>
                <th>Academies</th>
                <th>Departments</th>
                <th>Courses</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Table rows will be populated dynamically -->
            </tbody>
          </table>
          <!-- Pagination Controls (optional) -->
          <div class="d-flex justify-content-between">
            <div>
              <label for="entriesSelect">Show entries: </label>
              <select id="entriesSelect" class="form-select" style="width: 100px;">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
              </select>
            </div>
            <div>
              <span id="entriesCount">Showing 1 to 5 of 50 entries</span>
            </div>
          </div>
          <div id="paginationControls" class="d-flex justify-content-center mt-3">
            <!-- Pagination buttons go here if needed -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End of Requests Table -->

  <!-- Modal for Add/Edit Request Template -->
  <div class="modal fade" id="addRequestModal" tabindex="-1" aria-labelledby="addRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addRequestModalLabel">Add New Request Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addRequestForm">
            <input type="hidden" name="action" value="save_template">
            <input type="hidden" id="templateId" name="template_id" value="">
            <div class="mb-3">
              <label for="templateTitle" class="form-label">Template Title</label>
              <input type="text" class="form-control" id="templateTitle" name="templateTitle" required>
            </div>
            <div class="mb-3">
              <label for="templateDescription" class="form-label">Description</label>
              <textarea class="form-control" id="templateDescription" name="templateDescription" rows="3" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="startDate" class="form-label">Start Date and Time</label>
                <div class="d-flex gap-2">
                  <input type="date" class="form-control" id="startDate" name="startDate" required>
                  <input type="time" class="form-control" id="startTime" name="startTime" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="endDate" class="form-label">End Date and Time</label>
                <div class="d-flex gap-2">
                  <input type="date" class="form-control" id="endDate" name="endDate" required>
                  <input type="time" class="form-control" id="endTime" name="endTime" required>
                </div>
              </div>
            </div>
            <div id="academyContainer">
              <div class="mb-3">
                <label class="form-label">Academy</label>
                <div class="d-flex gap-2 academy-row">
                  <select class="form-select academy-select" name="academies[]" required>
                  </select>
                  <button type="button" class="btn btn-primary add-academy">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>

            <div id="departmentContainer">
              <div class="mb-3">
                <label class="form-label">Department</label>
                <div class="d-flex gap-2 department-row">
                  <select class="form-select department-select" name="departments[]" id="department_select_1" required>
                    <option value="">Select Department</option>
                  </select>
                  <button type="button" class="btn btn-primary add-department" data-max-selections="2">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Courses</label>
              <div id="coursesContainer">
                <!-- Courses will be populated here as checkboxes -->
              </div>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Save Template</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End of Modal -->

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
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a><br><br>
                        Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="#">Home</a>
                            <a href="#">Cookies</a>
                            <a href="#">Help</a>
                            <a href="#">FQAs</a>
                        </div>
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
    <script src="../assets/js/settings.js"></script>
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- Custom Javascript -->
    <script src="../assets/js/request-templates.js"></script>
</body>

</html>
