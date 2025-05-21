document.addEventListener("DOMContentLoaded", function () {
  const departmentForm = document.getElementById("departmentForm");
  const departmentsTableBody = document.getElementById("departmentsTableBody");
  const addBtn = document.getElementById("addDepartmentBtn");
  const modalTitle = document.getElementById("departmentModalLabel");
  const academySelect = document.getElementById("academy_id");

  let departments = [];
  let academies = [];
  let entriesPerPage = 10;
  let currentPage = 1;
  let filteredDepartments = [];

  async function fetchDepartments() {
    try {
      const response = await fetch("../php/departments.php");
      const data = await response.json();

      if (data.success) {
        departments = data.departments;
        academies = data.academies;

        renderAcademyOptions();
        renderDepartments();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Σφάλμα',
          text: data.message || 'Σφάλμα απόκρισης backend.'
        });
      }
    } catch (error) {
      console.error("Σφάλμα φόρτωσης:", error);
      Swal.fire({
        icon: 'error',
        title: 'Σφάλμα',
        text: 'Αποτυχία φόρτωσης τμημάτων.'
      });
    }
  }

  function renderAcademyOptions() {
    academySelect.innerHTML = '<option value="">Επιλέξτε Σχολή</option>';
    academies.forEach(academy => {
      const option = document.createElement("option");
      option.value = academy.id;
      option.textContent = academy.academy_name;
      academySelect.appendChild(option);
    });
  }

  function renderDepartments(list = departments) {
  // Αν υπάρχει search, να το κρατάς στο filteredDepartments
  filteredDepartments = list;

  departmentsTableBody.innerHTML = "";

  // Pagination logic
  const start = (currentPage - 1) * entriesPerPage;
  const end = start + entriesPerPage;
  const paginated = list.slice(start, end);

  paginated.forEach((dep, index) => {
    const academy = academies.find(a => a.id == dep.academy_id);
    const academyName = academy ? academy.academy_name : "—";

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${start + index + 1}</td>
      <td>${dep.department_name}</td>
      <td>${dep.department_code}</td>
      <td>${academyName}</td>
      <td>
        <div style="display:flex;gap:0.5rem;">
          <button class="btn btn-sm btn-primary me-2 edit-btn" data-id="${dep.id}"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger delete-btn" data-id="${dep.id}"><i class="fas fa-trash-alt"></i></button>
        </div>
      </td>
    `;
    departmentsTableBody.appendChild(row);
  });

  renderPagination(list.length);
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
      renderDepartments(filteredDepartments.length ? filteredDepartments : departments);
    });
    paginationControls.appendChild(li);
  }
}

  addBtn.addEventListener("click", () => {
    modalTitle.textContent = "Προσθήκη Τμήματος";
    departmentForm.reset();
    document.getElementById("department_id").value = "";
    resetValidation();
  });

  departmentForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const isNameValid = validateField(document.getElementById("department_name"), "Το όνομα τμήματος είναι υποχρεωτικό.");
    const isCodeValid = validateField(document.getElementById("department_code"), "Ο κωδικός τμήματος είναι υποχρεωτικός.");
    const isAcademyValid = validateField(document.getElementById("academy_id"), "Πρέπει να επιλέξετε σχολή.");

    if (!isNameValid || !isCodeValid || !isAcademyValid) return;

    const formData = new FormData(departmentForm);

    try {
      const response = await fetch("../php/departments.php", {
        method: "POST",
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        await fetchDepartments();
        bootstrap.Modal.getInstance(document.getElementById("departmentModal")).hide();
        Swal.fire({
          icon: 'success',
          title: 'Επιτυχία!',
          text: 'Το τμήμα αποθηκεύτηκε.',
          timer: 1500,
          showConfirmButton: false
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Σφάλμα',
          text: data.message || "Αποτυχία αποθήκευσης."
        });
      }
    } catch (err) {
      console.error("Σφάλμα κατά την υποβολή:", err);
      Swal.fire({
        icon: 'error',
        title: 'Σφάλμα',
        text: 'Αποτυχία σύνδεσης με τον server.'
      });
    }
  });

  departmentsTableBody.addEventListener("click", function (e) {
    const id = e.target.closest("button")?.dataset.id;
    if (e.target.closest(".edit-btn")) {
      const dep = departments.find(d => d.id == id);
      modalTitle.textContent = "Επεξεργασία Τμήματος";
      document.getElementById("department_id").value = dep.id;
      document.getElementById("academy_id").value = dep.academy_id;
      document.getElementById("department_name").value = dep.department_name;
      document.getElementById("department_code").value = dep.department_code;
      resetValidation();
      new bootstrap.Modal(document.getElementById("departmentModal")).show();
    } else if (e.target.closest(".delete-btn")) {
      const dep = departments.find(d => d.id == id);
      Swal.fire({
        title: `Διαγραφή "${dep.department_name}"`,
        text: "Είστε σίγουροι ότι θέλετε να το διαγράψετε;",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ναι, διαγραφή',
        cancelButtonText: 'Άκυρο'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch("../php/departments.php", {
              method: "DELETE",
              body: new URLSearchParams({ id }),
            });
            const data = await response.json();

            if (data.success) {
              await fetchDepartments();
              Swal.fire({
                icon: 'success',
                title: 'Διαγράφηκε!',
                text: 'Το τμήμα διαγράφηκε.',
                timer: 1500,
                showConfirmButton: false
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Σφάλμα',
                text: 'Αποτυχία διαγραφής.'
              });
            }
          } catch (err) {
            console.error("Σφάλμα κατά τη διαγραφή:", err);
            Swal.fire({
              icon: 'error',
              title: 'Σφάλμα',
              text: 'Αποτυχία Διαγραφής Λόγω Ανοικτής Αίτησης.'
            });
          }
        }
      });
    }
  });

document.getElementById("entriesPerPage").addEventListener("change", function (e) {
  entriesPerPage = parseInt(e.target.value, 10);
  currentPage = 1;
  renderDepartments(filteredDepartments.length ? filteredDepartments : departments);
});

document.getElementById("searchInput").addEventListener("input", function (e) {
  const searchValue = e.target.value.toLowerCase();
  const filtered = departments.filter(dep =>
    dep.department_name.toLowerCase().includes(searchValue) ||
    dep.department_code.toLowerCase().includes(searchValue) ||
    (academies.find(a => a.id == dep.academy_id)?.academy_name.toLowerCase().includes(searchValue) || "")
  );
  currentPage = 1; // reset page
  renderDepartments(filtered);
});

  function validateField(input, message) {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      input.classList.remove("is-valid");
      input.nextElementSibling.textContent = message;
      return false;
    } else {
      input.classList.remove("is-invalid");
      input.classList.add("is-valid");
      return true;
    }
  }

  function resetValidation() {
    ["department_name", "department_code", "academy_id"].forEach(id => {
      const el = document.getElementById(id);
      el.classList.remove("is-invalid", "is-valid");
    });
  }

  document.getElementById("department_name").addEventListener("input", function () {
    validateField(this, "Το όνομα τμήματος είναι υποχρεωτικό.");
  });

  document.getElementById("department_code").addEventListener("input", function () {
    validateField(this, "Ο κωδικός τμήματος είναι υποχρεωτικός.");
  });

  document.getElementById("academy_id").addEventListener("change", function () {
    validateField(this, "Πρέπει να επιλέξετε σχολή.");
  });

  fetchDepartments();
});
