document.addEventListener("DOMContentLoaded", function () {
    loadCourses();
    loadDepartments();

    document.getElementById("saveCourse").addEventListener("click", function () {
        const isEditing = document.getElementById("isEditing").value === "1";
        const id = document.getElementById("CourseId").value;
        const dept = document.getElementById("DepartmentName").value.trim();
        const name = document.getElementById("CourseName").value.trim();
        const code = document.getElementById("CourseCode").value.trim();
    
        if (!dept || !name || !code) {
            alert("All fields are required.");
            return;
        }
    
        const formData = new FormData();
        formData.append("action", isEditing ? "update_course" : "add_course");
        formData.append("departmentname", dept);
        formData.append("name", name);
        formData.append("code", code);
        if (isEditing) formData.append("id", id);
    
        fetch("../php/courses.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCourses();
                document.getElementById("academyForm").reset();
                document.getElementById("isEditing").value = "0";
                document.getElementById("CourseModalLabel").textContent = "Add New Course";
                bootstrap.Modal.getInstance(document.getElementById("CourseModal")).hide();
            } else {
                alert("Failed to save course.");
            }
        });
    });
    
});

function loadDepartments() {
    fetch("../php/courses.php", {
        method: "POST",
        body: new URLSearchParams({ action: "fetch_departments" })
    })
    .then(res => res.json())
    .then(data => {
        const deptInput = document.getElementById("DepartmentName");
        deptInput.setAttribute("list", "departmentList");

        const dataList = document.createElement("datalist");
        dataList.id = "departmentList";

        data.forEach(d => {
            const option = document.createElement("option");
            option.value = d.department_name;
            dataList.appendChild(option);
        });

        deptInput.parentNode.appendChild(dataList);
    });
}

function loadCourses() {
    fetch("../php/courses.php", {
        method: "POST",
        body: new URLSearchParams({ action: "fetch_courses" })
    })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("CourseTableBody");
        tbody.innerHTML = "";

        data.forEach(course => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${course.id}</td>
                <td>${course.department_name}</td>
                <td>${course.course_name}</td>
                <td>${course.course_code}</td>
                <td>
                    <button class="btn btn-sm btn-info text-white me-1" onclick="editCourse(${course.id})">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    });
}

function deleteCourse(id) {
    document.getElementById("deleteCourseId").value = id;
    new bootstrap.Modal(document.getElementById("DeleteModal")).show();
}

document.getElementById("confirmDelete").addEventListener("click", function () {
    const id = document.getElementById("deleteCourseId").value;

    const formData = new FormData();
    formData.append("action", "delete_course");
    formData.append("id", id);

    fetch("../php/courses.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadCourses();
            bootstrap.Modal.getInstance(document.getElementById("DeleteModal")).hide();
        } else {
            alert("Delete failed.");
        }
    });
});


// Placeholder for editCourse(id)
function editCourse(id) {
    fetch("../php/courses.php", {
        method: "POST",
        body: new URLSearchParams({ action: "fetch_courses" })
    })
    .then(res => res.json())
    .then(data => {
        const course = data.find(c => c.id == id);
        if (!course) return alert("Course not found.");

        document.getElementById("CourseModalLabel").textContent = "Edit Course";
        document.getElementById("CourseId").value = course.id;
        document.getElementById("DepartmentName").value = course.department_name;
        document.getElementById("CourseName").value = course.course_name;
        document.getElementById("CourseCode").value = course.course_code;
        document.getElementById("isEditing").value = "1";

        new bootstrap.Modal(document.getElementById("CourseModal")).show();
    });
}

document.getElementById("CourseModal").addEventListener("hidden.bs.modal", function () {
    document.getElementById("academyForm").reset();
    document.getElementById("isEditing").value = "0";
    document.getElementById("CourseModalLabel").textContent = "Add New Course";
});



// ===== ENHANCEMENT: Pagination + Search + Entries per page =====

let allCourses = [];
let currentPage = 1;

function getRowsPerPage() {
    return parseInt(document.getElementById("entriesPerPage").value) || 10;
}

function renderPaginatedCourses(courses, page = 1) {
    const rowsPerPage = getRowsPerPage();
    const tbody = document.getElementById("CourseTableBody");
    tbody.innerHTML = "";

    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedItems = courses.slice(start, end);

    paginatedItems.forEach(course => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${course.id}</td>
            <td>${course.department_name}</td>
            <td>${course.course_name}</td>
            <td>${course.course_code}</td>
            <td>
                <button class="btn btn-sm btn-info text-white me-1" onclick="editCourse(${course.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });

    renderPaginationControls(courses.length, page);
}

function renderPaginationControls(totalItems, currentPage) {
    const rowsPerPage = getRowsPerPage();
    const totalPages = Math.ceil(totalItems / rowsPerPage);
    const pagination = document.getElementById("paginationControls");
    pagination.innerHTML = "";

    if (totalPages <= 1) return;

    const maxPagesToShow = 7; // Πόσα κουμπιά να δείχνει συνολικά
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (currentPage <= 3) {
        endPage = Math.min(totalPages, maxPagesToShow - 2);
    }
    if (currentPage >= totalPages - 2) {
        startPage = Math.max(1, totalPages - (maxPagesToShow - 3));
    }

    // Πρώτη σελίδα
    if (startPage > 1) {
        addPageBtn(1);
        if (startPage > 2) addEllipsis();
    }

    // Ενδιάμεσες σελίδες
    for (let i = startPage; i <= endPage; i++) {
        addPageBtn(i);
    }

    // Τελευταία σελίδα
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) addEllipsis();
        addPageBtn(totalPages);
    }

    function addPageBtn(i) {
        const li = document.createElement("li");
        li.className = `page-item${i === currentPage ? " active" : ""}`;

        const btn = document.createElement("button");
        btn.className = "page-link";
        btn.textContent = i;
        btn.addEventListener("click", () => {
            if (currentPage !== i) {
                currentPage = i;
                renderPaginatedCourses(filteredCourses(), currentPage);
            }
        });

        li.appendChild(btn);
        pagination.appendChild(li);
    }

    function addEllipsis() {
        const li = document.createElement("li");
        li.className = "page-item disabled";
        li.innerHTML = `<span class="page-link">...</span>`;
        pagination.appendChild(li);
    }
}

function filteredCourses() {
    const query = document.getElementById("searchInput").value.toLowerCase().trim();
    return allCourses.filter(c =>
        c.course_name.toLowerCase().includes(query) ||
        c.course_code.toLowerCase().includes(query) ||
        c.department_name.toLowerCase().includes(query)
    );
}

function searchCourses() {
    currentPage = 1;
    renderPaginatedCourses(filteredCourses(), currentPage);
}

// Overriding loadCourses to store allCourses
const originalLoadCoursesFn = loadCourses;
loadCourses = function () {
    fetch("../php/courses.php", {
        method: "POST",
        body: new URLSearchParams({ action: "fetch_courses" })
    })
    .then(res => res.json())
    .then(data => {
        allCourses = data;
        currentPage = 1;
        renderPaginatedCourses(filteredCourses(), currentPage);
    });
};

