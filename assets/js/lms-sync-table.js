// Example data: Replace with dynamic data if needed
// const lmsData = [
//   // ...
// ];

let currentPage = 1;
let entriesPerPage = 5;
let filteredData = lmsData;
let sortColumn = null;
let sortDirection = 1; // 1 = asc, -1 = desc
let columnFilters = { aa: '', name: '', email: '', lmsStatus: '' };
let globalFilter = '';

function compareValues(a, b) {
  if (typeof a === 'string' && typeof b === 'string') {
    return a.localeCompare(b, undefined, { sensitivity: 'base' });
  }
  return a < b ? -1 : a > b ? 1 : 0;
}

function sortLmsData(data) {
  if (sortColumn === null) return data;
  return [...data].sort((a, b) => {
    let valA = a[sortColumn];
    let valB = b[sortColumn];
    return compareValues(valA, valB) * sortDirection;
  });
}

function filterLmsData(data) {
  return data.filter(row => {
    // Global filter: match any column
    if (globalFilter) {
      const search = globalFilter.toLowerCase();
      const values = [row.aa, row.name, row.email, row.lmsStatus];
      if (!values.some(val => String(val).toLowerCase().includes(search))) {
        return false;
      }
    }
    // Per-column filters
    return Object.keys(columnFilters).every(col => {
      if (!columnFilters[col]) return true;
      return String(row[col]).toLowerCase().includes(columnFilters[col].toLowerCase());
    });
  });
}

function renderTable() {
  const tbody = document.querySelector('#eesTable tbody');
  tbody.innerHTML = '';
  filteredData = filterLmsData(lmsData);
  const sorted = sortLmsData(filteredData);
  const start = (currentPage - 1) * entriesPerPage;
  const end = Math.min(start + entriesPerPage, sorted.length);
  for (let i = start; i < end; i++) {
    const row = sorted[i];
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.aa}</td>
      <td>${row.name}</td>
      <td>${row.email}</td>
      <td><span class="badge bg-success">${row.lmsStatus}</span></td>
      <td class="text-center">${row.actions}</td>
    `;
    tbody.appendChild(tr);
  }
  updateInfoText(sorted.length);
  renderPagination(sorted.length);
}

function updateInfoText(total) {
  const info = document.getElementById('entriesCount');
  const start = total === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
  const end = Math.min(currentPage * entriesPerPage, total);
  info.textContent = `Εμφάνιση ${start} έως ${end} από ${total} εγγραφές`;
}

function renderPagination(total) {
  const controls = document.getElementById('paginationControls');
  controls.innerHTML = '';
  const totalPages = Math.ceil(total / entriesPerPage);
  for (let i = 1; i <= totalPages; i++) {
    const li = document.createElement('li');
    li.className = 'page-item' + (i === currentPage ? ' active' : '');
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href = '#';
    a.textContent = i;
    a.onclick = (e) => {
      e.preventDefault();
      currentPage = i;
      renderTable();
    };
    li.appendChild(a);
    controls.appendChild(li);
  }
}

// Universal/global search
const globalSearchInput = document.getElementById('searchInput');
if (globalSearchInput) {
  globalSearchInput.addEventListener('input', function() {
    globalFilter = this.value;
    currentPage = 1;
    renderTable();
  });
}

// Per-column search
Array.from(document.querySelectorAll('#eesTable thead input[data-col]')).forEach(input => {
  input.addEventListener('input', function() {
    columnFilters[this.dataset.col] = this.value;
    currentPage = 1;
    renderTable();
  });
});

// Sorting
const ths = Array.from(document.querySelectorAll('#eesTable thead tr:first-child th'));
ths.forEach((th, idx) => {
  if (idx === 4) {
    th.innerHTML = th.textContent.replace(/[▲▼]/g, '').trim();
    th.style.cursor = 'default';
    th.onclick = null;
    return;
  }
  if (!th.dataset.label) th.dataset.label = th.textContent.replace(/[▲▼]/g, '').trim();
  th.style.cursor = 'pointer';
  let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
  if (sortColumn === ['aa', 'name', 'email', 'lmsStatus'][idx]) {
    arrow = sortDirection === 1
      ? '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▲</span>'
      : '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▼</span>';
  }
  th.innerHTML = th.dataset.label + arrow;
  th.onclick = function () {
    const colKeys = ['aa', 'name', 'email', 'lmsStatus'];
    if (sortColumn === colKeys[idx]) {
      sortDirection *= -1;
    } else {
      sortColumn = colKeys[idx];
      sortDirection = 1;
    }
    ths.forEach((t, i) => {
      if (i === 4) {
        t.innerHTML = t.textContent.replace(/[▲▼]/g, '').trim();
        t.style.cursor = 'default';
        t.onclick = null;
        return;
      }
      if (!t.dataset.label) t.dataset.label = t.textContent.replace(/[▲▼]/g, '').trim();
      let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
      if (colKeys[i] === sortColumn) {
        arrow = sortDirection === 1
          ? '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▲</span>'
          : '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▼</span>';
      }
      t.innerHTML = t.dataset.label + arrow;
    });
    renderTable();
  };
});

document.getElementById('entriesSelect').addEventListener('change', function() {
  entriesPerPage = parseInt(this.value, 10);
  currentPage = 1;
  renderTable();
});

// Initial render
renderTable(); 