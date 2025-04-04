<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Special Scientists Dashboard</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <script src="assets/js/script.js"></script>

  <style>
    /* Define a base and larger minimum height for the dashboard cards */
    .dashboard-card {
      min-height: 180px; /* Base height for small screens */
    }
    @media (min-width: 992px) {
      .dashboard-card {
        min-height: 220px; /* Slightly larger on bigger screens */
      }
    }
    /* Center content within the card */
    .card-body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
  </style>
</head>
<body>

  <!-- Navbar Inclusion (loaded from navbar.html) -->
  <div id="navbar-placeholder"></div>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar Column: visible on medium and larger screens -->
      <div class="col-md-2 d-none d-md-block p-0">
        <div id="sidebar-placeholder" class="bg-light vh-100"></div>
      </div>

      <!-- Main Content Column: full width on small screens, reduced width on larger screens -->
      <div class="col-12 col-md-10">
        <div class="container-fluid py-4">
          <h3 class="mb-4 text-center"> Dashboard</h3>
          <div class="row g-4 justify-content-center">
            <!-- Card: Electronic Applications -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
              <div class="card h-100 dashboard-card">
                <div class="card-body">
                  <i class="fa fa-file-alt fa-2x mb-3"></i>
                  <h5 class="card-title">Electronic Applications</h5>
                </div>
              </div>
            </div>
            <!-- Card: Process Monitoring -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
              <div class="card h-100 dashboard-card">
                <div class="card-body">
                  <i class="fa fa-eye fa-2x mb-3"></i>
                  <h5 class="card-title">Process Monitoring</h5>
                </div>
              </div>
            </div>
            <!-- Card: Reports & Statistics -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
              <div class="card h-100 dashboard-card">
                <div class="card-body">
                  <i class="fa fa-chart-bar fa-2x mb-3"></i>
                  <h5 class="card-title">Reports & Statistics</h5>
                </div>
              </div>
            </div>
            <!-- Card: Communication & Announcements -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
              <div class="card h-100 dashboard-card">
                <div class="card-body">
                  <i class="fa fa-bullhorn fa-2x mb-3"></i>
                  <h5 class="card-title">Annoucements</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END: Dashboard Content -->
      </div>
    </div>
  </div>

  <!-- Footer Inclusion (loaded from footer.html) -->
  <div id="footer-placeholder"></div>

  <!-- Bootstrap JS (including Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome JS (if needed) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
