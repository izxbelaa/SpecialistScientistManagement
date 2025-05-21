// Example data: Replace with dynamic data if needed
// const lmsData = [
//   // ...
// ];

let currentPage = 1;
let entriesPerPage = 5;
let filteredData = lmsData;

function renderTable() {
  const tbody = document.querySelector('#eesTable tbody');
  tbody.innerHTML = '';
  const start = (currentPage - 1) * entriesPerPage;
  const end = Math.min(start + entriesPerPage, filteredData.length);
  for (let i = start; i < end; i++) {
    const row = filteredData[i];
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
  updateInfoText();
  renderPagination();
}

function updateInfoText() {
  const info = document.getElementById('entriesCount');
  const start = filteredData.length === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
  const end = Math.min(currentPage * entriesPerPage, filteredData.length);
  info.textContent = `Εμφάνιση ${start} έως ${end} από ${filteredData.length} εγγραφές`;
}

function renderPagination() {
  const controls = document.getElementById('paginationControls');
  controls.innerHTML = '';
  const totalPages = Math.ceil(filteredData.length / entriesPerPage);

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

document.getElementById('searchInput').addEventListener('input', function() {
  const value = this.value.toLowerCase();
  filteredData = lmsData.filter(row =>
    row.name.toLowerCase().includes(value) ||
    row.email.toLowerCase().includes(value)
  );
  currentPage = 1;
  renderTable();
});

document.getElementById('entriesSelect').addEventListener('change', function() {
  entriesPerPage = parseInt(this.value, 10);
  currentPage = 1;
  renderTable();
});

// Initial render
renderTable(); 