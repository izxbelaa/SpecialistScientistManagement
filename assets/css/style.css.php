<?php
include '../../php/config.php';

$stmt = $pdo->query("SELECT site_color, light_color, dark_color FROM colors LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Set color variables; provide default values if the query doesn't return results.
if ($settings) {
    $primary = $settings['site_color'];
    $light   = $settings['light_color'];
    $dark    = $settings['dark_color'];
} else {
    $primary = "#06BBCC";    // default primary color
    $light   = "#F0FBFC";    // default light color
    $dark    = "#181d38";    // default dark color
}

header('Content-Type: text/css; charset=utf-8');
?>

.page-item .page-link {
    min-width: 40px;
    text-align: center;
    display: inline-block;
    /* Προαιρετικά: */
    padding: 0.375rem 0.75rem;
}

/********** Template CSS **********/
:root {
    --primary: <?php echo $primary; ?>;
    --light: <?php echo $light; ?>;
    --dark: <?php echo $dark; ?>;
}

.fw-medium {
    font-weight: 600 !important;
}

.fw-semi-bold {
    font-weight: 700 !important;
}

.back-to-top {
    position: fixed;
    display: none;
    right: 45px;
    bottom: 45px;
    z-index: 99;
}


/*** Spinner ***/
#spinner {
    opacity: 0;
    visibility: hidden;
    transition: opacity .5s ease-out, visibility 0s linear .5s;
    z-index: 99999;
}

#spinner.show {
    transition: opacity .5s ease-out, visibility 0s linear 0s;
    visibility: visible;
    opacity: 1;
}


/*** Button ***/
.btn {
    font-family: 'Nunito', sans-serif;
    font-weight: 600;
    transition: .5s;
}

.btn.btn-primary,
.btn.btn-secondary {
    color: #FFFFFF;
}

.btn-square {
    width: 38px;
    height: 38px;
}

.btn-sm-square {
    width: 32px;
    height: 32px;
}

.btn-lg-square {
    width: 48px;
    height: 48px;
}

.btn-square,
.btn-sm-square,
.btn-lg-square {
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: normal;
    border-radius: 0px;
}


/*** Navbar ***/
.navbar .dropdown-toggle::after {
    border: none;
    content: "\f107";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    vertical-align: middle;
    margin-left: 8px;
}

.navbar-light .navbar-nav .nav-link {
    margin-right: 30px;
    padding: 25px 0;
    color: #FFFFFF;
    font-size: 15px;
    text-transform: uppercase;
    outline: none;
}

.navbar-light .navbar-nav .nav-link:hover,
.navbar-light .navbar-nav .nav-link.active {
    color: var(--primary);
}

@media (max-width: 991.98px) {
    .navbar-light .navbar-nav .nav-link  {
        margin-right: 0;
        padding: 10px 0;
    }

    .navbar-light .navbar-nav {
        border-top: 1px solid #EEEEEE;
    }
}

.navbar-light .navbar-brand,
.navbar-light a.btn {
    height: 75px;
}

.navbar-light .navbar-nav .nav-link {
    color: var(--dark);
    font-weight: 500;
}

.navbar-light.sticky-top {
    top: -100px;
    transition: .5s;
}

@media (min-width: 992px) {
    .navbar .nav-item .dropdown-menu {
        display: block;
        margin-top: 0;
        opacity: 0;
        visibility: hidden;
        transition: .5s;
    }

    .navbar .dropdown-menu.fade-down {
        top: 100%;
        transform: rotateX(-75deg);
        transform-origin: 0% 0%;
    }

    .navbar .nav-item:hover .dropdown-menu {
        top: 100%;
        transform: rotateX(0deg);
        visibility: visible;
        transition: .5s;
        opacity: 1;
    }
}


/*** Header carousel ***/
@media (max-width: 768px) {
    .header-carousel .owl-carousel-item {
        position: relative;
        min-height: 500px;
    }
    
    .header-carousel .owl-carousel-item img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.header-carousel .owl-nav {
    position: absolute;
    top: 50%;
    right: 8%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
}

.header-carousel .owl-nav .owl-prev,
.header-carousel .owl-nav .owl-next {
    margin: 7px 0;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    background: transparent;
    border: 1px solid #FFFFFF;
    font-size: 22px;
    transition: .5s;
}

.header-carousel .owl-nav .owl-prev:hover,
.header-carousel .owl-nav .owl-next:hover {
    background: var(--primary);
    border-color: var(--primary);
}

.page-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/carousel-1.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

.page-header-inner {
    background: rgba(15, 23, 43, .7);
}

.breadcrumb-item + .breadcrumb-item::before {
    color: var(--light);
}


/*** Section Title ***/
.section-title {
    position: relative;
    display: inline-block;
    text-transform: uppercase;
}

.section-title::before {
    position: absolute;
    content: "";
    width: calc(100% + 80px);
    height: 2px;
    top: 4px;
    left: -40px;
    background: var(--primary);
    z-index: -1;
}

.section-title::after {
    position: absolute;
    content: "";
    width: calc(100% + 120px);
    height: 2px;
    bottom: 5px;
    left: -60px;
    background: var(--primary);
    z-index: -1;
}

.section-title.text-start::before {
    width: calc(100% + 40px);
    left: 0;
}

.section-title.text-start::after {
    width: calc(100% + 60px);
    left: 0;
}


/*** Service ***/
.service-item {
    background: var(--light);
    transition: .5s;
}

.service-item:hover {
    margin-top: -10px;
    background: var(--primary);
}

.service-item * {
    transition: .5s;
}

.service-item:hover * {
    color: var(--light) !important;
}


/*** Categories & Courses ***/
.category img,
.course-item img {
    transition: .5s;
}

.category a:hover img,
.course-item:hover img {
    transform: scale(1.1);
}


/*** Team ***/
.team-item img {
    transition: .5s;
}

.team-item:hover img {
    transform: scale(1.1);
}


/*** Testimonial ***/
.testimonial-carousel::before {
    position: absolute;
    content: "";
    top: 0;
    left: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(to right, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
    z-index: 1;
}

.testimonial-carousel::after {
    position: absolute;
    content: "";
    top: 0;
    right: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(to left, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
    z-index: 1;
}

@media (min-width: 768px) {
    .testimonial-carousel::before,
    .testimonial-carousel::after {
        width: 200px;
    }
}

@media (min-width: 992px) {
    .testimonial-carousel::before,
    .testimonial-carousel::after {
        width: 300px;
    }
}

.testimonial-carousel .owl-item .testimonial-text,
.testimonial-carousel .owl-item.center .testimonial-text * {
    transition: .5s;
}

.testimonial-carousel .owl-item.center .testimonial-text {
    background: var(--primary) !important;
}

.testimonial-carousel .owl-item.center .testimonial-text * {
    color: #FFFFFF !important;
}

.testimonial-carousel .owl-dots {
    margin-top: 24px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.testimonial-carousel .owl-dot {
    position: relative;
    display: inline-block;
    margin: 0 5px;
    width: 15px;
    height: 15px;
    border: 1px solid #CCCCCC;
    transition: .5s;
}

.testimonial-carousel .owl-dot.active {
    background: var(--primary);
    border-color: var(--primary);
}


/*** Footer ***/
.footer .btn.btn-social {
    margin-right: 5px;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--light);
    font-weight: normal;
    border: 1px solid #FFFFFF;
    border-radius: 35px;
    transition: .3s;
}

.footer .btn.btn-social:hover {
    color: var(--primary);
}

.footer .btn.btn-link {
    display: block;
    margin-bottom: 5px;
    padding: 0;
    text-align: left;
    color: #FFFFFF;
    font-size: 15px;
    font-weight: normal;
    text-transform: capitalize;
    transition: .3s;
}

.footer .btn.btn-link::before {
    position: relative;
    content: "\f105";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 10px;
}

.footer .btn.btn-link:hover {
    letter-spacing: 1px;
    box-shadow: none;
}

.footer .copyright {
    padding: 25px 0;
    font-size: 15px;
    border-top: 1px solid rgba(256, 256, 256, .1);
}

.footer .copyright a {
    color: var(--light);
}

.footer .footer-menu a {
    margin-right: 15px;
    padding-right: 15px;
    border-right: 1px solid rgba(255, 255, 255, .1);
}

.footer .footer-menu a:last-child {
    margin-right: 0;
    padding-right: 0;
    border-right: none;
}




/********** user table CSS **********/
:root {
    --primary: <?php echo $primary; ?>;
    --light: <?php echo $light; ?>;
    --dark: <?php echo $dark; ?>;
}

/* Existing styles remain unchanged */

/* ---------------- New User Table Styling ---------------- */

/* Reduce overall card width */
.card {
    max-width: 1200px; /* Adjust based on preference */
    margin: auto; /* Center the card */
}

/* Table Styling */
#usersTable {
    width: 100%;
    border-collapse: collapse;
}

/* Reduce Table Cell Padding & Font Size */
#usersTable th, #usersTable td {
    padding: 6px 8px; /* Less padding for a compact look */
    font-size: 14px; /* Reduce font size */
    white-space: nowrap; /* Prevent text from wrapping */
}

/* Reduce Header Background & Text Size */
#usersTable thead th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Adjust Specific Column Widths */
#usersTable th:nth-child(1),  
#usersTable td:nth-child(1) { width: 15%; } /* Name */

#usersTable th:nth-child(2),  
#usersTable td:nth-child(2) { width: 15%; } /* Surname */

#usersTable th:nth-child(4),  
#usersTable td:nth-child(4) { width: 25%; } /* Email */

#usersTable th:last-child,
#usersTable td:last-child { width: 10%; } /* Actions */

/* Reduce Form Input Size */
#entriesSelect, #searchInput {
    font-size: 14px;
    padding: 6px 8px;
}

/* Adjust Dropdown Menu */
#usersTable .dropdown-menu {
    font-size: 13px;
    min-width: 120px;
}

/* Reduce Button & Dropdown Sizes (without affecting global .btn) */
#usersTable .btn {
    padding: 4px 8px;
    font-size: 13px;
}

/* Reduce Pagination Button Size */
#paginationControls button {
    padding: 4px 8px;
    font-size: 13px;
}

/* Adjust Spacing of Pagination & Entries Info */
.d-flex {
    gap: 10px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    #usersTable th, #usersTable td {
        font-size: 12px;
        padding: 4px 6px;
    }
    #usersTable .btn, 
    #usersTable .dropdown-menu, 
    #entriesSelect, 
    #searchInput {
        font-size: 12px;
        padding: 4px 6px;
    }
}

/* Login page header background */
.login-page .page-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/login.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* About page header background */
.about-page .page-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/tepak.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

.btn-social {
    width: 40px;
    height: 40px;
    border-radius: 50%; /* Κάνει τα κουμπιά κυκλικά */
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ffffff; /* Άσπρο περίγραμμα */
    color: #ffffff;
    transition: background-color 0.3s, color 0.3s;
}

.btn-social:hover {
    background-color: #ffffff;
    color: #0d6efd; /* ή κάποιο μπλε */
}

/* Prevent user dropdown from overflowing the right edge */
.navbar .dropdown-menu-end[aria-labelledby="userDropdown"] {
    right: 0;
    left: auto;
    min-width: 180px;
    transform: translateX(0);
}
@media (min-width: 992px) {
    .navbar .dropdown-menu-end[aria-labelledby="userDropdown"] {
        right: 0;
        left: auto;
    }
}

/* Courses page header background */
.courses-page .courses-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/courses.webp);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Departments page header background */
.departments-page .departments-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/departments.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Academies page header background */
.academies-page .academies-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/academies.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Evaluators page header background */
.evaluators-page .evaluators-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/evaluators.webp);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Users Table page header background */
.userstable-page .userstable-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/userstable.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Admin Applications page header background */
.adminapps-page .adminapps-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/application-admin.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Admin Settings page header background */
.adminsettings-page .adminsettings-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/admin-settings.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Edit User page header background */
.edituser-page .edituser-header {
    background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url(../img/edit-profile.jpg);
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Assign button custom color */
.btn-assign {
    background-color: #0cc3d2 !important;
    color: #fff !important;
    border: none;
    font-weight: bold;
}
.btn-assign:hover, .btn-assign:focus {
    background-color: #0aa9b6 !important;
    color: #fff !important;
}

#paginationControls.pagination {
  gap: 0 !important;
}
#paginationControls.pagination .page-item {
  margin: 0 2px;
}
#paginationControls.pagination .page-item .page-link {
  border-radius: 6px !important;
  background: none !important;
  border: none !important;
  color: #17c1e8 !important;
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  box-shadow: none !important;
  transition: background 0.2s, color 0.2s;
}
#paginationControls.pagination .page-item.active .page-link {
  background-color: #17c1e8 !important;
  color: #fff !important;
  font-weight: bold;
}
#paginationControls.pagination .page-item .page-link:focus {
  outline: none;
  box-shadow: none !important;
}

table.lms-table thead th {
  background-color: #fff !important;
  color: #222 !important;
}
table.lms-table td, table.lms-table th {
  vertical-align: middle !important;
  color: #444 !important;
}

/* Force LMS Sync table header to white */
#eesTable thead th,
.lms-table thead th,
table.lms-table thead th,
#eesTable thead tr,
.lms-table thead tr,
table.lms-table thead tr {
  background-color: #fff !important;
  color: #222 !important;
}

/* Remove requests-table custom colors to use Bootstrap defaults */
/* .requests-table thead th, .requests-table tbody tr:nth-of-type(odd), .requests-table tbody tr:nth-of-type(even), .requests-table td, .requests-table th { } */

.sort-arrow {
  display: inline-block;
  min-width: 1.2em;
  text-align: right;
  vertical-align: middle;
  font-size: 1em;
  line-height: 1;
  margin-left: 8px;
}

#CourseTable th {
  min-width: 150px;
  padding-right: 36px;
  position: relative;
}
