let applications = [];
let filteredApplications = [];
let currentPage = 1;
let sortColumn = null;
let sortDirection = 1; // 1 = asc, -1 = desc
let entriesPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    loadApplications();
    document.getElementById('searchInput').addEventListener('input', searchApplications);
    document.getElementById('entriesPerPage').addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderApplicationsTable(filteredApplications.length ? filteredApplications : applications);
    });
});

function compareValues(a, b) {
    if (typeof a === 'string' && typeof b === 'string') {
        return a.localeCompare(b, undefined, { sensitivity: 'base' });
    }
    return a < b ? -1 : a > b ? 1 : 0;
}

function sortApplications(applicationsList) {
    if (sortColumn === null) return applicationsList;
    return [...applicationsList].sort((a, b) => {
        let valA, valB;
        switch (sortColumn) {
            case 0: valA = a.serial; valB = b.serial; break;
            case 1: valA = a.requester_name; valB = b.requester_name; break;
            case 2: valA = a.request_title; valB = b.request_title; break;
            case 3: valA = a.description; valB = b.description; break;
            default: return 0;
        }
        return compareValues(valA, valB) * sortDirection;
    });
}

function renderPagination(total) {
    const paginationControls = document.getElementById("paginationControls");
    paginationControls.innerHTML = "";
    const totalPages = Math.ceil(total / entriesPerPage);
    if (totalPages <= 1) return;
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement("li");
        li.className = "page-item" + (i === currentPage ? " active" : "");
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener("click", function (e) {
            e.preventDefault();
            currentPage = i;
            renderApplicationsTable(filteredApplications.length ? filteredApplications : applications);
        });
        paginationControls.appendChild(li);
    }
}

function renderApplicationsTable(applicationsList = applications) {
    const tbody = document.getElementById('inspectionTableBody');
    tbody.innerHTML = '';
    const start = (currentPage - 1) * entriesPerPage;
    const end = start + entriesPerPage;
    const sorted = sortApplications(applicationsList);
    const paginated = sorted.slice(start, end);
    paginated.forEach((app, idx) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${app.serial}</td>
            <td>${app.requester_name || 'Άγνωστος'}</td>
            <td>${app.request_title || '-'}</td>
            <td>${app.description || '-'}</td>
            <td>
                <button class="btn btn-success btn-sm me-2" onclick="acceptApplication(${app.candidate_user_id})">Αποδοχή</button>
                <button class="btn btn-danger btn-sm me-2" onclick="rejectApplication(${app.candidate_user_id})">Απόρριψη</button>
                <button class="btn btn-info btn-sm text-white" onclick="downloadCV(${app.request_id})">Λήψη Βιογραφικού</button>
            </td>
        `;
        tbody.appendChild(row);
    });
    renderPagination(applicationsList.length);
    setupApplicationsTableSorting();
}

function setupApplicationsTableSorting() {
    const ths = document.querySelectorAll("#inspectionTable thead th");
    ths.forEach((th, idx) => {
        if (idx === 4) { // Skip Ενέργειες column
            th.innerHTML = th.textContent.replace(/[\u25B2\u25BC\u2193\u2191]/g, '').trim();
            th.style.cursor = "default";
            th.onclick = null;
            return;
        }
        if (!th.dataset.label) th.dataset.label = th.textContent.replace(/[\u25B2\u25BC\u2193\u2191]/g, '').trim();
        th.style.cursor = "pointer";
        let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
        if (sortColumn === idx) {
            arrow = sortDirection === 1
                ? '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▲</span>'
                : '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▼</span>';
        }
        th.innerHTML = th.dataset.label + arrow;
        th.onclick = function () {
            if (sortColumn === idx) {
                sortDirection *= -1;
            } else {
                sortColumn = idx;
                sortDirection = 1;
            }
            ths.forEach((t, i) => {
                if (i === 4) { // Skip Ενέργειες column
                    t.innerHTML = t.textContent.replace(/[\u25B2\u25BC\u2193\u2191]/g, '').trim();
                    t.style.cursor = "default";
                    t.onclick = null;
                    return;
                }
                let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
                if (i === sortColumn) {
                    arrow = sortDirection === 1
                        ? '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▲</span>'
                        : '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▼</span>';
                }
                t.innerHTML = t.dataset.label + arrow;
            });
            renderApplicationsTable(filteredApplications.length ? filteredApplications : applications);
        };
    });
}

function searchApplications() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    filteredApplications = applications.filter(app =>
        (String(app.serial).includes(searchValue)) ||
        (app.requester_name && app.requester_name.toLowerCase().includes(searchValue)) ||
        (app.request_title && app.request_title.toLowerCase().includes(searchValue)) ||
        (app.description && app.description.toLowerCase().includes(searchValue))
    );
    currentPage = 1;
    renderApplicationsTable(filteredApplications);
}

function loadApplications() {
    fetch('../php/get-requests.php')
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0 || data.error) {
                applications = [];
                renderApplicationsTable([]);
                if (data.error) console.error(data.error);
                return;
            }
            applications = data.map((row, idx) => ({ ...row, serial: idx + 1 }));
            filteredApplications = [];
            renderApplicationsTable(applications);
        })
        .catch(error => {
            console.error('Σφάλμα κατά τη φόρτωση:', error);
            document.getElementById('inspectionTableBody').innerHTML =
                '<tr><td colspan="5">Σφάλμα κατά τη φόρτωση των αιτήσεων.</td></tr>';
        });
}

// Accept/Reject/Download actions are still handled by global functions in the page 