document.addEventListener("DOMContentLoaded", function () {
    const courses = [
      { id: 1, title: "Προγραμματισμός Ι", hasInstructor: true },
      { id: 2, title: "Αντικειμενοστραφής Σχεδίαση", hasInstructor: false },
      { id: 3, title: "Ανάλυση Αλγορίθμων", hasInstructor: true },
      { id: 4, title: "Ασφάλεια Πληροφοριών", hasInstructor: false }
    ];
  
    const coursesContainer = document.getElementById("coursesContainer");
    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("filterSelect");
    const noResultsMessage = document.getElementById("noResultsMessage");
  
    function renderCourses() {
      const keyword = searchInput.value.toLowerCase();
      const filter = filterSelect.value;
  
      const filtered = courses.filter(course => {
        const matchesSearch = course.title.toLowerCase().includes(keyword);
        const matchesFilter =
          filter === "all" ||
          (filter === "with" && course.hasInstructor) ||
          (filter === "without" && !course.hasInstructor);
  
        return matchesSearch && matchesFilter;
      });
  
      coursesContainer.innerHTML = "";
  
      if (filtered.length === 0) {
        noResultsMessage.classList.remove("d-none");
        return;
      } else {
        noResultsMessage.classList.add("d-none");
      }
  
      filtered.forEach(course => {
        const card = document.createElement("div");
        card.className = "col-md-6 col-lg-4";
        card.innerHTML = `
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">${course.title}</h5>
              <span class="status-badge ${course.hasInstructor ? "with-instructor" : "no-instructor"}">
                ${course.hasInstructor ? "Με Διδάσκοντα" : "Χωρίς Διδάσκοντα"}
              </span>
            </div>
          </div>
        `;
        coursesContainer.appendChild(card);
      });
    }
  
    searchInput.addEventListener("input", renderCourses);
    filterSelect.addEventListener("change", renderCourses);
  
    renderCourses();
  });