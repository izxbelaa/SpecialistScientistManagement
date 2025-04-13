document.addEventListener("DOMContentLoaded", function () {
    const departmentForm = document.getElementById("departmentForm");
    const departmentsTableBody = document.getElementById("departmentsTableBody");
    const addBtn = document.getElementById("addDepartmentBtn");
    const modalTitle = document.getElementById("departmentModalLabel");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const academySelect = document.getElementById("academy_id");
  
    let deleteId = null;
  
    let academies = [
      { id: 1, academy_name: "Σχολή Επιστημών" },
      { id: 2, academy_name: "Σχολή Μηχανικής" }
    ];
  
    let departments = [
      { id: 1, department_name: "Τμήμα Πληροφορικής", department_code: "INF01", academy_id: 1 },
      { id: 2, department_name: "Τμήμα Ηλεκτρολόγων", department_code: "ENG02", academy_id: 2 }
    ];
  
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
  
    departmentForm.addEventListener("submit", function (e) {
      e.preventDefault();
  
      const isNameValid = validateField(document.getElementById("department_name"), "Το όνομα τμήματος είναι υποχρεωτικό.");
      const isCodeValid = validateField(document.getElementById("department_code"), "Ο κωδικός τμήματος είναι υποχρεωτικός.");
      const isAcademyValid = validateField(document.getElementById("academy_id"), "Πρέπει να επιλέξετε σχολή.");
  
      if (!isNameValid || !isCodeValid || !isAcademyValid) return;
  
      const id = document.getElementById("department_id").value;
      const name = document.getElementById("department_name").value;
      const code = document.getElementById("department_code").value;
      const academy_id = parseInt(document.getElementById("academy_id").value);
  
      if (id) {
        const dep = departments.find(d => d.id == id);
        dep.department_name = name;
        dep.department_code = code;
        dep.academy_id = academy_id;
      } else {
        const newId = departments.length ? departments[departments.length - 1].id + 1 : 1;
        departments.push({ id: newId, department_name: name, department_code: code, academy_id });
      }
  
      renderDepartments();
      bootstrap.Modal.getInstance(document.getElementById("departmentModal")).hide();
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
        deleteId = id;
        new bootstrap.Modal(document.getElementById("confirmDeleteModal")).show();
      }
    });
  
    confirmDeleteBtn.addEventListener("click", () => {
      if (deleteId) {
        departments = departments.filter(d => d.id != deleteId);
        renderDepartments();
        deleteId = null;
        bootstrap.Modal.getInstance(document.getElementById("confirmDeleteModal")).hide();
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
  
    document.getElementById("academy_id").addEventListener("change", function () {
      validateField(this, "Πρέπει να επιλέξετε σχολή.");
    });
  
    renderAcademyOptions();
    renderDepartments();
  });
  