let applications = [];
let filteredApplications = [];
let currentPage = 1;
let sortColumn = null;
let sortDirection = 1; // 1 = asc, -1 = desc
let entriesPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    loadApplications();
    
    // Add event listeners for search and entries per page
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
            case 0: valA = a.id; valB = b.id; break;
            case 1: valA = a.request_name; valB = b.request_name; break;
            case 2: valA = a.katastasi; valB = b.katastasi; break;
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
    const tbody = document.getElementById('applicationsTableBody');
    tbody.innerHTML = '';
    
    const start = (currentPage - 1) * entriesPerPage;
    const end = start + entriesPerPage;
    
    // Sort before paginating
    const sorted = sortApplications(applicationsList);
    const paginated = sorted.slice(start, end);
    
    paginated.forEach((app) => {
        const row = document.createElement('tr');
        const badgeClass = app.katastasi === 'Εγκρίθηκε' ? 'bg-success' : 
                          app.katastasi === 'Απορρίφθηκε' ? 'bg-danger' : 
                          'bg-warning text-dark';
        
        row.innerHTML = `
            <td>${app.id}</td>
            <td>${app.request_name}</td>
            <td><span class="badge ${badgeClass}">${app.katastasi}</span></td>
        `;
        tbody.appendChild(row);
    });
    
    renderPagination(applicationsList.length);
    setupApplicationsTableSorting();
}

function setupApplicationsTableSorting() {
    const ths = document.querySelectorAll("#applicationsTable thead th");
    ths.forEach((th, idx) => {
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
            ths.forEach((t) => {
                let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
                if (t === th) {
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
    filteredApplications = applications.filter((app, idx) =>
        (String(idx + 1).includes(searchValue)) || // A/A column
        (app.request_name && app.request_name.toLowerCase().includes(searchValue)) ||
        (app.katastasi && app.katastasi.toLowerCase().includes(searchValue))
    );
    currentPage = 1; // Reset to first page when searching
    renderApplicationsTable(filteredApplications);
}

function loadApplications() {
    fetch('../php/fetch-application-status.php', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            applications = data.data.map((app, idx) => ({ 
                ...app, 
                id: idx + 1,
                katastasi: app.katastasi || 'Σε εξέλιξη' // Default status if none provided
            }));
            filteredApplications = [];
            renderApplicationsTable(applications);
        } else {
            console.error('Error loading applications:', data.message);
            // Show error message to user
            const tbody = document.getElementById('applicationsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Error loading applications'}
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message to user
        const tbody = document.getElementById('applicationsTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error loading applications. Please try again later.
                </td>
            </tr>
        `;
    });
} 