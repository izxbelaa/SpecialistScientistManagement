<?php
include '../php/session_check.php';
include '../php/config.php';

// Only allow access if user is admin (type_of_user = 5)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Διαχειριστής') {
    header('Location: ../index.php');
    exit;
}

// 1. Requests per application type (template)
$appTypeStats = $pdo->query('
    SELECT rt.title, COUNT(r.id) as count
    FROM request_templates rt
    LEFT JOIN requests r ON r.template_id = rt.id
    GROUP BY rt.id
')->fetchAll(PDO::FETCH_ASSOC);

// 2b. Requests per department (for pie chart)
$departmentStats = $pdo->query('
    SELECT d.department_name, COUNT(rc.request_id) as count
    FROM departments d
    LEFT JOIN course c ON c.department_id = d.id
    LEFT JOIN request_course rc ON rc.course_id = c.id
    GROUP BY d.id
')->fetchAll(PDO::FETCH_ASSOC);

// 2c. Number of different courses in open requests only (for pie chart)
$openCoursesStats = $pdo->query('
    SELECT c.course_name, COUNT(DISTINCT rc.request_id) as count
    FROM course c
    JOIN request_course rc ON rc.course_id = c.id
    JOIN candidate_users cu ON cu.request_id = rc.request_id
    GROUP BY c.id
')->fetchAll(PDO::FETCH_ASSOC);

// 3. Total requests
$totalStats = $pdo->query('SELECT COUNT(*) as total FROM requests')->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Στατιστικά Αιτήσεων – CUT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/img/logo.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css.php" rel="stylesheet">
    <link href="../assets/css/requests-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="adminapps-page">
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
                <div class="nav-item dropdown active">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Σελιδες Διαχειριστη</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="assign-reviewers.php" class="dropdown-item">Ανάθεση Αξιολογητών</a>
                        <a href="tables.php" class="dropdown-item">Πίνακας Χρηστών</a>
                        <a href="requests-admin.php" class="dropdown-item">Διαχείριση Αιτήσεων</a>
                        <a href="statistics.php" class="dropdown-item active">Στατιστικά</a>
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
    <div class="container-fluid bg-primary py-5 mb-5 page-header adminapps-header wow fadeIn statistics-header">
        <div class="container py-5 text-center wow fadeInUp">
            <h1 class="display-3 text-white">Στατιστικά Αιτήσεων</h1>
            <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.2s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a class="text-white" href="../index.php">Αρχική</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Στατιστικά</li>
                </ol>
            </nav>
        </div>
    </div>
    <style>
    .statistics-header {
        background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('../assets/img/statistics.jpg') center center/cover no-repeat !important;
    }
    </style>
    <div class="container my-5 wow animate__animated animate__fadeInUp" data-wow-delay="0.2s">
        <!-- Total Requests at the Top -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow p-4 mb-4">
                    <h5 class="card-title text-center">Συνολικές Αιτήσεις που εχουν συμπληρωθεί</h5>
                    <div class="display-2 text-center text-primary" style="font-weight: bold;"><?php echo $totalStats['total']; ?></div>
                </div>
            </div>
        </div>
        <!-- Cyprus University of Technology Logo (for print) -->
        <img id="cutLogoForPrint" src="../assets/img/logocut.png" alt="Cyprus University of Technology" style="display:none;" />
        <!-- 1st Report Print Button -->
        <button class="btn btn-primary mb-3" id="printReport1Btn">Εκτύπωση Αναφοράς 1</button>
        <!-- First Graph: Requests per Application Type -->
        <div class="row justify-content-center mb-5 wow animate__animated animate__fadeInUp" data-wow-delay="0.3s">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow p-4 mb-4">
                    <h5 class="card-title text-center">Σύνολο Αιτήσεων που συμπληρώθηκαν ανά Τύπο Αίτησης</h5>
                    <div class="d-flex justify-content-center">
                        <canvas id="appTypeChart" width="900" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- 2nd Report Print Button -->
        <button class="btn btn-primary mb-3" id="printReport2Btn">Εκτύπωση Αναφοράς 2</button>
        <!-- Second Graph: Requests per Department (Pie Chart) -->
        <style>
        .pie-flex-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            gap: 32px;
        }
        .pie-legend-box {
            max-height: 350px;
            overflow-y: auto;
            min-width: 260px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 18px;
            font-size: 15px;
        }
        .pie-legend-box ul { list-style: none; padding: 0; margin: 0; }
        .pie-legend-box li { display: flex; align-items: center; margin-bottom: 8px; }
        .pie-legend-color {
            display: inline-block;
            width: 18px; height: 18px;
            margin-right: 10px;
            border-radius: 3px;
        }
        </style>
        <div class="row justify-content-center mb-2 mt-2 wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
            <div class="col-12">
                <div class="card shadow p-3 mb-2">
                    <h5 class="card-title text-center mb-3">Αιτήσεις ανά Τμήμα (Pie Chart)</h5>
                    <div class="pie-flex-row">
                        <canvas id="departmentChart" width="350" height="350"></canvas>
                        <div id="departmentLegend" class="pie-legend-box"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 3rd Report Print Button -->
        <button class="btn btn-primary mb-3" id="printReport3Btn">Εκτύπωση Αναφοράς 3</button>
        <!-- Third Graph: Different Courses in Open Requests (Pie Chart) -->
        <style>
        .pie-legend-box-courses {
            max-height: 350px;
            overflow-y: auto;
            min-width: 260px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 18px;
            font-size: 15px;
        }
        .pie-legend-box-courses ul { list-style: none; padding: 0; margin: 0; }
        .pie-legend-box-courses li { display: flex; align-items: center; margin-bottom: 8px; }
        .pie-legend-color-courses {
            display: inline-block;
            width: 18px; height: 18px;
            margin-right: 10px;
            border-radius: 3px;
        }
        </style>
        <div class="row justify-content-center mb-2 mt-2 wow animate__animated animate__fadeInUp" data-wow-delay="0.5s">
            <div class="col-12">
                <div class="card shadow p-3 mb-2">
                    <h5 class="card-title text-center mb-3">Διαφορετικά Μαθήματα σε Ανοικτές Αιτήσεις (Pie Chart)</h5>
                    <div class="pie-flex-row">
                        <canvas id="openCoursesChart" width="350" height="350"></canvas>
                        <div id="openCoursesLegend" class="pie-legend-box-courses"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Chart for Applications per Application Type
        const appTypeCtx = document.getElementById('appTypeChart').getContext('2d');
        const appTypeColors = [
            '#0099ff', '#06BBCC', '#4fc3f7', '#1976d2', '#64b5f6', '#0288d1', '#00bcd4', '#2196f3', '#40c4ff', '#01579b', '#039be5', '#81d4fa'
        ];
        new Chart(appTypeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($appTypeStats, 'title')); ?>,
                datasets: [{
                    label: 'Αιτήσεις',
                    data: <?php echo json_encode(array_column($appTypeStats, 'count')); ?>,
                    backgroundColor: appTypeColors.slice(0, <?php echo count($appTypeStats); ?>),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {legend: {display: false}},
                scales: {
                    x: {ticks: {font: {size: 16}}},
                    y: {ticks: {font: {size: 16}}}
                }
            }
        });
        // Chart for Applications per Department (Pie Chart)
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentColors = [
            '#06BBCC', '#0099ff', '#f39c12', '#e74c3c', '#8e44ad', '#27ae60', '#d35400', '#2980b9', '#c0392b', '#16a085',
            '#2ecc71', '#f1c40f', '#e67e22', '#34495e', '#7f8c8d', '#9b59b6', '#1abc9c', '#2c3e50', '#95a5a6', '#bdc3c7',
            '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'
        ];
        const departmentData = {
            labels: <?php echo json_encode(array_column($departmentStats, 'department_name')); ?>,
            datasets: [{
                label: 'Αιτήσεις',
                data: <?php echo json_encode(array_column($departmentStats, 'count')); ?>,
                backgroundColor: departmentColors,
            }]
        };
        const departmentChart = new Chart(departmentCtx, {
            type: 'pie',
            data: departmentData,
            options: {
                responsive: false,
                plugins: {legend: {display: false}},
            },
            plugins: [{
                id: 'custom-legend',
                afterRender: function(chart) {
                    const legendBox = document.getElementById('departmentLegend');
                    if (!legendBox) return;
                    let html = '<ul>';
                    chart.data.labels.forEach(function(label, i) {
                        const color = chart.data.datasets[0].backgroundColor[i % departmentColors.length];
                        html += `<li><span class='pie-legend-color' style='background:${color}'></span>${label}</li>`;
                    });
                    html += '</ul>';
                    legendBox.innerHTML = html;
                }
            }]
        });
        // Chart for Different Courses in Open Requests (Pie Chart)
        const openCoursesCtx = document.getElementById('openCoursesChart').getContext('2d');
        const openCoursesColors = [
            '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#4bc0c0', '#9966ff', '#f67019', '#f53794', '#acc236', '#166a8f',
            '#00a950', '#58595b', '#8549ba', '#b2dfdb', '#ffd54f', '#ffb300', '#8d6e63', '#d84315', '#43a047', '#1e88e5',
            '#c62828', '#ad1457', '#6d4c41', '#00897b', '#c0ca33', '#fbc02d', '#0288d1', '#7e57c2', '#388e3c', '#fdd835', '#ff7043'
        ];
        const openCoursesData = {
            labels: <?php echo json_encode(array_column($openCoursesStats, 'course_name')); ?>,
            datasets: [{
                label: 'Αιτήσεις',
                data: <?php echo json_encode(array_column($openCoursesStats, 'count')); ?>,
                backgroundColor: openCoursesColors,
            }]
        };
        const openCoursesChart = new Chart(openCoursesCtx, {
            type: 'pie',
            data: openCoursesData,
            options: {
                responsive: false,
                plugins: {legend: {display: false}},
            },
            plugins: [{
                id: 'custom-legend-courses',
                afterRender: function(chart) {
                    const legendBox = document.getElementById('openCoursesLegend');
                    if (!legendBox) return;
                    let html = '<ul>';
                    chart.data.labels.forEach(function(label, i) {
                        const color = chart.data.datasets[0].backgroundColor[i % openCoursesColors.length];
                        html += `<li><span class='pie-legend-color-courses' style='background:${color}'></span>${label}</li>`;
                    });
                    html += '</ul>';
                    legendBox.innerHTML = html;
                }
            }]
        });
        // Print Chart logic
        document.addEventListener('DOMContentLoaded', function() {
          // 1st Report
          document.getElementById('printReport1Btn').addEventListener('click', function() {
            // Generate detailed explanation for Report 1
            const appTypeData = <?php echo json_encode($appTypeStats); ?>;
            let explanation = 'Αναλυτική Αναφορά Αιτήσεων ανά Τύπο:\n\n';
            appTypeData.forEach(item => {
              explanation += `• ${item.title}: ${item.count} αιτήσεις\n`;
            });

            const total = <?php echo json_encode($totalStats['total']); ?>;
            const appTypeCanvas = document.getElementById('appTypeChart');
            const appTypeImg = new Image();
            appTypeImg.src = appTypeCanvas.toDataURL();
            appTypeImg.style.maxWidth = '100%';
            appTypeImg.style.display = 'block';
            appTypeImg.style.margin = '0 auto 24px auto';
            const logoSrc = document.getElementById('cutLogoForPrint').src;
            const printWindow = window.open('', '', 'width=900,height=900');
            printWindow.document.write('<html><head><title>Αναφορά 1</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;padding:24px;} h2{text-align:center;} .extra-details{margin:24px 0;white-space:pre-wrap;} .legend-print{margin:0 auto 24px auto;max-width:400px;} .legend-print ul{list-style:none;padding:0;} .legend-print li{display:flex;align-items:center;margin-bottom:8px;} .legend-color{display:inline-block;width:18px;height:18px;margin-right:10px;border-radius:3px;} .logo-print{text-align:center;margin-bottom:24px;} .big-number{font-size:2.5rem;color:#0d6efd;text-align:center;font-weight:bold;margin-bottom:18px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="logo-print"><img src="' + logoSrc + '" alt="Cyprus University of Technology" height="60"></div>');
            printWindow.document.write('<h2>Συνολικές Αιτήσεις που εχουν συμπληρωθεί</h2>');
            printWindow.document.write('<div class="big-number">' + total + '</div>');
            printWindow.document.write('<h2>Σύνολο Αιτήσεων που συμπληρώθηκαν ανά Τύπο Αίτησης</h2>');
            printWindow.document.body.appendChild(appTypeImg);
            printWindow.document.write('<div class="extra-details" style="margin-top:32px;"><strong>Αναλυτική Αναφορά:</strong><br>' + explanation.replace(/\n/g, '<br>') + '</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(() => { 
              printWindow.focus(); 
              printWindow.print(); 
              // Close the print window after printing or cancelling
              printWindow.onafterprint = function() {
                printWindow.close();
              };
              setTimeout(function() {
                if (!printWindow.closed) printWindow.close();
              }, 2000);
            }, 500);
          });

          // 2nd Report
          document.getElementById('printReport2Btn').addEventListener('click', function() {
            // Generate detailed explanation for Report 2
            const departmentData = <?php echo json_encode($departmentStats); ?>;
            let explanation = 'Αναλυτική Αναφορά Αιτήσεων ανά Τμήμα:\n\n';
            departmentData.forEach(item => {
              explanation += `• ${item.department_name}: ${item.count} αιτήσεις\n`;
            });
            explanation += `\nΣυνολικά έχουν καταχωρηθεί αιτήσεις σε ${departmentData.length} τμήματα.`;

            const departmentCanvas = document.getElementById('departmentChart');
            const departmentImg = new Image();
            departmentImg.src = departmentCanvas.toDataURL();
            departmentImg.style.maxWidth = '350px';
            departmentImg.style.display = 'block';
            departmentImg.style.margin = '0 auto 24px auto';
            const logoSrc = document.getElementById('cutLogoForPrint').src;
            // Build legend HTML with colors (grid below the pie)
            const departmentColors = [
              '#06BBCC', '#0099ff', '#f39c12', '#e74c3c', '#8e44ad', '#27ae60', '#d35400', '#2980b9', '#c0392b', '#16a085',
              '#2ecc71', '#f1c40f', '#e67e22', '#34495e', '#7f8c8d', '#9b59b6', '#1abc9c', '#2c3e50', '#95a5a6', '#bdc3c7',
              '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'
            ];
            let legendHtml = '<div class="legend-grid-print"><ul>';
            departmentData.forEach(function(item, i) {
              const color = departmentColors[i % departmentColors.length];
              legendHtml += `<li><span class='legend-color-print' style='background:${color}'></span>${item.department_name}</li>`;
            });
            legendHtml += '</ul></div>';
            const printWindow = window.open('', '', 'width=1100,height=900');
            printWindow.document.write('<html><head><title>Αναφορά 2</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;padding:24px;} h2{text-align:center;} .extra-details{margin:24px 0 32px 0;white-space:pre-wrap;} .logo-print{text-align:center;margin-bottom:24px;} .pie-center-print{text-align:center;margin:32px 0 0 0;} .legend-grid-print{margin:32px auto 0 auto;max-width:700px;} .legend-grid-print ul{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:8px;list-style:none;padding:0;margin:0;} .legend-grid-print li{display:flex;align-items:center;font-size:15px;margin-bottom:8px;} .legend-color-print{display:inline-block;width:18px;height:18px;margin-right:10px;border-radius:3px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="logo-print"><img src="' + logoSrc + '" alt="Cyprus University of Technology" height="60"></div>');
            printWindow.document.write('<h2>Αιτήσεις ανά Τμήμα (Pie Chart)</h2>');
            printWindow.document.write('<div class="extra-details"><strong>Αναλυτική Αναφορά:</strong><br>' + explanation.replace(/\n/g, '<br>') + '</div>');
            printWindow.document.write('<div class="pie-center-print"></div>');
            printWindow.document.body.querySelector('.pie-center-print').appendChild(departmentImg);
            printWindow.document.write(legendHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(() => { 
              printWindow.focus(); 
              printWindow.print(); 
              // Close the print window after printing or cancelling
              printWindow.onafterprint = function() {
                printWindow.close();
              };
              setTimeout(function() {
                if (!printWindow.closed) printWindow.close();
              }, 2000);
            }, 500);
          });

          // 3rd Report
          document.getElementById('printReport3Btn').addEventListener('click', function() {
            // Generate detailed explanation for Report 3
            const openCoursesData = <?php echo json_encode($openCoursesStats); ?>;
            let explanation = 'Αναλυτική Αναφορά Ανοικτών Αιτήσεων ανά Μάθημα:\n\n';
            openCoursesData.forEach(item => {
              explanation += `• ${item.course_name}: ${item.count} ανοικτές αιτήσεις\n`;
            });
            explanation += `\nΣυνολικά υπάρχουν ${openCoursesData.length} μαθήματα με ανοικτές αιτήσεις.`;

            const openCoursesCanvas = document.getElementById('openCoursesChart');
            const openCoursesImg = new Image();
            openCoursesImg.src = openCoursesCanvas.toDataURL();
            openCoursesImg.style.maxWidth = '350px';
            openCoursesImg.style.display = 'block';
            openCoursesImg.style.margin = '0 auto 24px auto';
            const logoSrc = document.getElementById('cutLogoForPrint').src;
            // Build legend HTML with colors (grid below the pie)
            const openCoursesColors = [
              '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#4bc0c0', '#9966ff', '#f67019', '#f53794', '#acc236', '#166a8f',
              '#00a950', '#58595b', '#8549ba', '#b2dfdb', '#ffd54f', '#ffb300', '#8d6e63', '#d84315', '#43a047', '#1e88e5',
              '#c62828', '#ad1457', '#6d4c41', '#00897b', '#c0ca33', '#fbc02d', '#0288d1', '#7e57c2', '#388e3c', '#fdd835', '#ff7043'
            ];
            let legendHtml = '<div class="legend-grid-print"><ul>';
            openCoursesData.forEach(function(item, i) {
              const color = openCoursesColors[i % openCoursesColors.length];
              legendHtml += `<li><span class='legend-color-print' style='background:${color}'></span>${item.course_name}</li>`;
            });
            legendHtml += '</ul></div>';
            const printWindow = window.open('', '', 'width=1100,height=900');
            printWindow.document.write('<html><head><title>Αναφορά 3</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;padding:24px;} h2{text-align:center;} .extra-details{margin:24px 0 32px 0;white-space:pre-wrap;} .logo-print{text-align:center;margin-bottom:24px;} .pie-center-print{text-align:center;margin:32px 0 0 0;} .legend-grid-print{margin:32px auto 0 auto;max-width:700px;} .legend-grid-print ul{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:8px;list-style:none;padding:0;margin:0;} .legend-grid-print li{display:flex;align-items:center;font-size:15px;margin-bottom:8px;} .legend-color-print{display:inline-block;width:18px;height:18px;margin-right:10px;border-radius:3px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="logo-print"><img src="' + logoSrc + '" alt="Cyprus University of Technology" height="60"></div>');
            printWindow.document.write('<h2>Διαφορετικά Μαθήματα σε Ανοικτές Αιτήσεις (Pie Chart)</h2>');
            printWindow.document.write('<div class="extra-details"><strong>Αναλυτική Αναφορά:</strong><br>' + explanation.replace(/\n/g, '<br>') + '</div>');
            printWindow.document.write('<div class="pie-center-print"></div>');
            printWindow.document.body.querySelector('.pie-center-print').appendChild(openCoursesImg);
            printWindow.document.write(legendHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(() => { 
              printWindow.focus(); 
              printWindow.print(); 
              // Close the print window after printing or cancelling
              printWindow.onafterprint = function() {
                printWindow.close();
              };
              setTimeout(function() {
                if (!printWindow.closed) printWindow.close();
              }, 2000);
            }, 500);
          });
        });
    </script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>new WOW().init();</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>new WOW().init();</script>
</body>
</html> 