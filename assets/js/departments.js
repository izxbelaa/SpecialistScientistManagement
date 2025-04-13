document.addEventListener("DOMContentLoaded", function () {
  const departmentForm = document.getElementById("departmentForm");
  const departmentsTableBody = document.getElementById("departmentsTableBody");
  const addBtn = document.getElementById("addDepartmentBtn");
  const modalTitle = document.getElementById("departmentModalLabel");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  

  let deleteId = null;
  let departments = [];
  let academies = [];

  async function fetchDepartments() {
    try {
      const response = await fetch("../php/departments.php?action=fetch_all"); // <-- add action param
      const data = await response.json();
  
      if (data.success) {
        departments = data.departments;
        academies = data.academies;
  
        renderAcademyOptions();
        renderDepartments();
      } else {
        console.error("Σφάλμα απόκρισης backend:", data.message);
      }
    } catch (error) {
      console.error("Σφάλμα φόρτωσης:", error);
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
    departmentsTableBody.innerHTML = "";
    list.forEach((dep, index) => {
      const academy = academies.find(a => a.id == dep.academy_id);
      const academyName = academy ? academy.academy_name : "—";

      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${dep.department_name}</td>
        <td>${dep.department_code}</td>
        <td>${academyName}</td>
        <td>
          <button class="btn btn-sm btn-primary me-2 edit-btn" data-id="${dep.id}"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger delete-btn" data-id="${dep.id}"><i class="fas fa-trash-alt"></i></button>
        </td>
      `;
      departmentsTableBody.appendChild(row);
    });
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
   

    if (!isNameValid || !isCodeValid) return;

    const formData = new FormData(departmentForm);

    try {
      const response = await fetch("../../php/departments.php", {
        method: "POST",
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        await fetchDepartments();
        bootstrap.Modal.getInstance(document.getElementById("departmentModal")).hide();
      } else {
        alert(data.message || "Αποτυχία αποθήκευσης.");
      }
    } catch (err) {
      console.error("Σφάλμα κατά την υποβολή:", err);
    }
  });

  departmentsTableBody.addEventListener("click", function (e) {
    const id = e.target.closest("button")?.dataset.id;
    if (e.target.closest(".edit-btn")) {
      const dep = departments.find(d => d.id == id);
      modalTitle.textContent = "Επεξεργασία Τμήματος";
      document.getElementById("department_id").value = dep.id;
      
      document.getElementById("department_name").value = dep.department_name;
      document.getElementById("department_code").value = dep.department_code;
      resetValidation();
      new bootstrap.Modal(document.getElementById("departmentModal")).show();
    } else if (e.target.closest(".delete-btn")) {
      deleteId = id;
      new bootstrap.Modal(document.getElementById("confirmDeleteModal")).show();
    }
  });

  confirmDeleteBtn.addEventListener("click", async () => {
    if (deleteId) {
      try {
        const response = await fetch("../../php/departments.php", {
          method: "DELETE",
          body: new URLSearchParams({ id: deleteId }),
        });
        const data = await response.json();

        if (data.success) {
          await fetchDepartments();
          bootstrap.Modal.getInstance(document.getElementById("confirmDeleteModal")).hide();
        } else {
          alert("Σφάλμα διαγραφής.");
        }
      } catch (err) {
        console.error("Σφάλμα κατά τη διαγραφή:", err);
      }
    }
  });

  document.getElementById("searchInput").addEventListener("input", function (e) {
    const searchValue = e.target.value.toLowerCase();
    const filtered = departments.filter(dep =>
      dep.department_name.toLowerCase().includes(searchValue) ||
      dep.department_code.toLowerCase().includes(searchValue) ||
      (academies.find(a => a.id == dep.academy_id)?.academy_name.toLowerCase().includes(searchValue) || "")
    );
    renderDepartments(filtered);
  });

  // ✅ Real-time validation
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

 
  fetchDepartments();
});