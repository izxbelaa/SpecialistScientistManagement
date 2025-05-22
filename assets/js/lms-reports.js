let eeMoodleChart, coursesNoInstructorChart, coursesWithInstructorChart;

function hideAllSections() {
    document.getElementById('ee-moodle-section').style.display = 'none';
    document.getElementById('courses-no-instructor-section').style.display = 'none';
    document.getElementById('courses-with-instructor-section').style.display = 'none';
}

function showSection(sectionId) {
    hideAllSections();
    document.getElementById(sectionId).style.display = '';
}

// Utility: CSV export
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = [];
    for (let row of table.rows) {
        let rowData = [];
        for (let cell of row.cells) {
            rowData.push('"' + cell.innerText.replace(/"/g, '""') + '"');
        }
        csv.push(rowData.join(','));
    }
    const csvString = csv.join('\n');
    // Add UTF-8 BOM for Excel compatibility
    const BOM = "\uFEFF";
    const blob = new Blob([BOM + csvString], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('btn-ee-moodle').addEventListener('click', function () {
        showSection('ee-moodle-section');
        const statsDiv = document.getElementById('ee-moodle-stats');
        statsDiv.innerHTML = '<div class="text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Φόρτωση...</div>';
        fetch('../php/lms-reports.php?report=ee_stats')
            .then(res => res.json())
            .then(data => {
                if (data.ee_stats) {
                    statsDiv.innerHTML = `
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Με πρόσβαση στο Moodle: <strong>${data.ee_stats.with_access}</strong></li>
                            <li class="list-group-item">Χωρίς πρόσβαση στο Moodle: <strong>${data.ee_stats.without_access}</strong></li>
                        </ul>
                    `;
                    // Populate table with user info
                    const tbody = document.getElementById('ee-moodle-table').querySelector('tbody');
                    tbody.innerHTML = '';
                    if (Array.isArray(data.ee_users) && data.ee_users.length > 0) {
                        data.ee_users.forEach(u => {
                            let tr = document.createElement('tr');
                            tr.innerHTML = `<td>${u.email}</td><td>${u.first_name}</td><td>${u.last_name}</td><td>${u.access}</td>`;
                            tbody.appendChild(tr);
                        });
                    }
                    document.getElementById('ee-moodle-summary').innerText = `Σύνολο ΕΕ: ${data.ee_users.length}`;
                    // Chart
                    if (eeMoodleChart) eeMoodleChart.destroy();
                    eeMoodleChart = new Chart(document.getElementById('eeMoodleChart').getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Με πρόσβαση', 'Χωρίς πρόσβαση'],
                            datasets: [{
                                data: [data.ee_stats.with_access, data.ee_stats.without_access],
                                backgroundColor: ['#198754', '#dc3545']
                            }]
                        },
                        options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
                    });
                } else {
                    statsDiv.innerHTML = '<div class="text-danger">Σφάλμα φόρτωσης στατιστικών.</div>';
                }
            })
            .catch(() => {
                statsDiv.innerHTML = '<div class="text-danger">Σφάλμα σύνδεσης.</div>';
            });
    });

    document.getElementById('btn-courses-no-instructor').addEventListener('click', function () {
        showSection('courses-no-instructor-section');
        const coursesDiv = document.getElementById('courses-no-instructor');
        coursesDiv.innerHTML = '<div class="text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Φόρτωση...</div>';
        fetch('../php/lms-reports.php?report=courses_no_instructor')
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data.courses_no_instructor)) {
                    document.getElementById('courses-no-instructor-summary').innerText = `Σύνολο μαθημάτων χωρίς διδάσκοντα: ${data.courses_no_instructor.length}`;
                    const tbody = document.getElementById('courses-no-instructor-table').querySelector('tbody');
                    tbody.innerHTML = '';
                    data.courses_no_instructor.forEach(row => {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `<td>${row.course_code}</td><td>${row.course_name}</td>`;
                        tbody.appendChild(tr);
                    });
                    if (data.courses_no_instructor.length === 0) {
                        coursesDiv.innerHTML = '<div class="text-success">Όλα τα μαθήματα έχουν διδάσκοντα.</div>';
                    } else {
                        coursesDiv.innerHTML = '';
                    }
                    // Chart
                    if (coursesNoInstructorChart) coursesNoInstructorChart.destroy();
                    coursesNoInstructorChart = new Chart(document.getElementById('coursesNoInstructorChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: data.courses_no_instructor.map(c => c.course_code),
                            datasets: [{
                                label: 'Μαθήματα χωρίς διδάσκοντα',
                                data: data.courses_no_instructor.map(() => 1),
                                backgroundColor: '#ffc107'
                            }]
                        },
                        options: {responsive: true, plugins: {legend: {display: false}}}
                    });
                } else {
                    coursesDiv.innerHTML = '<div class="text-danger">Σφάλμα φόρτωσης μαθημάτων.</div>';
                }
            })
            .catch(() => {
                coursesDiv.innerHTML = '<div class="text-danger">Σφάλμα σύνδεσης.</div>';
            });
    });

    document.getElementById('btn-courses-with-instructor').addEventListener('click', function () {
        showSection('courses-with-instructor-section');
        const coursesDiv = document.getElementById('courses-with-instructor');
        coursesDiv.innerHTML = '<div class="text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Φόρτωση...</div>';
        fetch('../php/lms-reports.php?report=courses_with_instructor')
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data.courses_with_instructor)) {
                    document.getElementById('courses-with-instructor-summary').innerText = `Σύνολο μαθημάτων με διδάσκοντα: ${data.courses_with_instructor.length}`;
                    const tbody = document.getElementById('courses-with-instructor-table').querySelector('tbody');
                    tbody.innerHTML = '';
                    data.courses_with_instructor.forEach(row => {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `<td>${row.course_code}</td><td>${row.course_name}</td>`;
                        tbody.appendChild(tr);
                    });
                    if (data.courses_with_instructor.length === 0) {
                        coursesDiv.innerHTML = '<div class="text-warning">Δεν υπάρχουν μαθήματα με διδάσκοντα.</div>';
                    } else {
                        coursesDiv.innerHTML = '';
                    }
                    // Chart
                    if (coursesWithInstructorChart) coursesWithInstructorChart.destroy();
                    const instructorCounts = {};
                    data.courses_with_instructor.forEach(c => {
                        const key = `${c.first_name ? c.first_name : ''} ${c.last_name ? c.last_name : ''}`.trim();
                        if (key) instructorCounts[key] = (instructorCounts[key] || 0) + 1;
                    });
                    coursesWithInstructorChart = new Chart(document.getElementById('coursesWithInstructorChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: Object.keys(instructorCounts),
                            datasets: [{
                                label: 'Αριθμός μαθημάτων ανά διδάσκοντα',
                                data: Object.values(instructorCounts),
                                backgroundColor: '#0d6efd'
                            }]
                        },
                        options: {responsive: true, plugins: {legend: {display: false}}}
                    });
                } else {
                    coursesDiv.innerHTML = '<div class="text-danger">Σφάλμα φόρτωσης μαθημάτων.</div>';
                }
            })
            .catch(() => {
                coursesDiv.innerHTML = '<div class="text-danger">Σφάλμα σύνδεσης.</div>';
            });
    });

    // Add event listeners for CSV export buttons
    if (document.getElementById('export-ee-moodle-csv')) {
        document.getElementById('export-ee-moodle-csv').addEventListener('click', function() {
            exportTableToCSV('ee-moodle-table', 'ee_moodle.csv');
        });
    }
    if (document.getElementById('export-courses-no-instructor-csv')) {
        document.getElementById('export-courses-no-instructor-csv').addEventListener('click', function() {
            exportTableToCSV('courses-no-instructor-table', 'courses_no_instructor.csv');
        });
    }
    if (document.getElementById('export-courses-with-instructor-csv')) {
        document.getElementById('export-courses-with-instructor-csv').addEventListener('click', function() {
            exportTableToCSV('courses-with-instructor-table', 'courses_with_instructor.csv');
        });
    }
}); 