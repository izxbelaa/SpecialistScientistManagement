let academies = [];
let filteredAcademies = [];
let currentPage = 1;
let sortColumn = null;
let sortDirection = 1; // 1 = asc, -1 = desc
let entriesPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    loadAcademies();
    
    // Add event listeners for search and entries per page
    document.getElementById('searchInput').addEventListener('input', searchAcademies);
    document.getElementById('entriesPerPage').addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderAcademiesTable(filteredAcademies.length ? filteredAcademies : academies);
    });
});

function compareValues(a, b) {
    if (typeof a === 'string' && typeof b === 'string') {
        return a.localeCompare(b, undefined, { sensitivity: 'base' });
    }
    return a < b ? -1 : a > b ? 1 : 0;
}

function sortAcademies(academiesList) {
    if (sortColumn === null) return academiesList;
    return [...academiesList].sort((a, b) => {
        let valA, valB;
        switch (sortColumn) {
            case 0: valA = a.id; valB = b.id; break;
            case 1: valA = a.academy_name; valB = b.academy_name; break;
            case 2: valA = a.academy_code; valB = b.academy_code; break;
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
            renderAcademiesTable(filteredAcademies.length ? filteredAcademies : academies);
        });
        paginationControls.appendChild(li);
    }
}

function renderAcademiesTable(academiesList = academies) {
    const tbody = document.getElementById('academiesTableBody');
    tbody.innerHTML = '';
    
    const start = (currentPage - 1) * entriesPerPage;
    const end = start + entriesPerPage;
    
    // Sort before paginating
    const sorted = sortAcademies(academiesList);
    const paginated = sorted.slice(start, end);
    
    paginated.forEach((academy) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${academy.id}</td>
            <td>${academy.academy_name}</td>
            <td>${academy.academy_code}</td>
            <td>
                <button class="btn btn-sm btn-primary me-2" onclick="editAcademy(${academy.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteAcademy(${academy.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    renderPagination(academiesList.length);
    setupAcademiesTableSorting();
}

function setupAcademiesTableSorting() {
    const ths = document.querySelectorAll("#academiesTable thead th");
    ths.forEach((th, idx) => {
        if (idx === 3) { // Skip Ενέργειες column
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
                if (i === 3) { // Skip Ενέργειες column
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
            renderAcademiesTable(filteredAcademies.length ? filteredAcademies : academies);
        };
    });
}

function searchAcademies() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    filteredAcademies = academies.filter(academy =>
        academy.academy_name.toLowerCase().includes(searchValue) ||
        academy.academy_code.toLowerCase().includes(searchValue)
    );
    currentPage = 1; // Reset to first page when searching
    renderAcademiesTable(filteredAcademies);
}

function loadAcademies() {
    fetch('../php/academies.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            academies = data.data.map((academy, idx) => ({ ...academy, originalIndex: idx + 1 }));
            filteredAcademies = [];
            renderAcademiesTable(academies);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Edit academy
function editAcademy(id) {
    fetch('../php/academies.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const academy = data.data;
            document.getElementById('academyId').value = academy.id;
            document.getElementById('academyName').value = academy.academy_name;
            document.getElementById('academyCode').value = academy.academy_code;
            document.getElementById('academyModalLabel').textContent = 'Edit Academy';
            new bootstrap.Modal(document.getElementById('academyModal')).show();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Delete academy
function deleteAcademy(id) {
    if (confirm('Are you sure you want to delete this academy?')) {
        fetch('../php/academies.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Academy deleted successfully');
                loadAcademies();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Save academy
document.getElementById('saveAcademy').addEventListener('click', function() {
    const form = document.getElementById('academyForm');
    const formData = new FormData(form);
    const id = formData.get('id');
    
    const params = new URLSearchParams();
    params.append('action', id ? 'update' : 'create');
    params.append('name', formData.get('name'));
    params.append('code', formData.get('code'));
    if (id) {
        params.append('id', id);
    }
    
    fetch('../php/academies.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Academy saved successfully');
            bootstrap.Modal.getInstance(document.getElementById('academyModal')).hide();
            form.reset();
            loadAcademies();
        } else {
            alert(data.message || 'An unknown error occurred');
            console.error('Server error:', data);
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        alert('A network error occurred. Please check the console for details.');
    });
});

// Reset form when modal is closed
document.getElementById('academyModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('academyForm').reset();
    document.getElementById('academyId').value = '';
    document.getElementById('academyModalLabel').textContent = 'Add New Academy';
});
 