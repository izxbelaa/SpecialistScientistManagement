document.addEventListener("DOMContentLoaded", function () {
  const templateSelect = document.getElementById("templateSelect");
  const description = document.getElementById("description");
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");
  const coursesContainer = document.getElementById("coursesContainer");
  const academyInfo = document.getElementById("academyInfo");
  const templateModalInput = document.getElementById("templateSelectModalInput");
  const openTemplateModalBtn = document.getElementById("openTemplateModalBtn");
  const templateModal = new bootstrap.Modal(document.getElementById("templateSelectModal"));
  const templateList = document.getElementById("templateList");
  const templateSearchInput = document.getElementById("templateSearchInput");
  const templateIdHidden = document.getElementById("template_id");

  if (!templateModalInput || !openTemplateModalBtn || !templateList || !templateSearchInput || !templateIdHidden || !description || !startDate || !endDate || !coursesContainer || !academyInfo) {
    console.error("One or more elements not found in the DOM. Check your HTML.");
    return;
  }

  let filteredTemplates = [];
  let allTemplates = [];

  fetch("../php/application-filter.php")
    .then(res => res.json())
    .then(data => {
      if (!data.success) throw new Error("Invalid backend response");

      const today = new Date().toISOString().split("T")[0];
      const { templates, userApplications } = data;

      // Get template IDs user already applied to (except rejected)
      const excludedTemplateIds = userApplications
        .filter(app => app.status !== -1)
        .map(app => app.template_id);

      // Filter out expired + already applied (unless rejected)
      filteredTemplates = templates.filter(t => {
        const endDate = t.date_end?.split(" ")[0];
        return endDate >= today && !excludedTemplateIds.includes(t.id);
      });
      allTemplates = filteredTemplates;

      // Modal open logic
      openTemplateModalBtn.addEventListener("click", () => {
        templateSearchInput.value = "";
        renderTemplateList(filteredTemplates);
        templateModal.show();
      });

      // Search logic
      templateSearchInput.addEventListener("input", function() {
        const q = this.value.toLowerCase();
        const filtered = allTemplates.filter(t => t.title.toLowerCase().includes(q) || (t.description && t.description.toLowerCase().includes(q)));
        renderTemplateList(filtered);
      });

      function renderTemplateList(list) {
        templateList.innerHTML = "";
        if (list.length === 0) {
          templateList.innerHTML = '<li class="list-group-item text-center text-muted">Δεν βρέθηκαν αιτήσεις.</li>';
          return;
        }
        list.forEach(t => {
          const li = document.createElement("li");
          li.className = "list-group-item list-group-item-action";
          li.style.cursor = "pointer";
          li.textContent = t.title + (t.description ? ` — ${t.description}` : "");
          li.addEventListener("click", () => {
            templateModalInput.value = t.title;
            templateIdHidden.value = t.id;
            templateModal.hide();
            // Fill the rest of the form as before
            fillTemplateFields(t);
          });
          templateList.appendChild(li);
        });
      }

      // Helper to fill form fields
      function fillTemplateFields(selected) {
        description.value = selected.description || "";
        startDate.value = (selected.date_start || "").split(" ")[0];
        endDate.value = (selected.date_end || "").split(" ")[0];
        coursesContainer.innerHTML = "";
        if (selected.courses && Array.isArray(selected.courses)) {
          selected.courses.forEach((course, idx) => {
            const div = document.createElement("div");
            div.className = "form-check";
            const input = document.createElement("input");
            input.type = "checkbox";
            input.className = "form-check-input";
            input.name = "courses[]";
            input.value = course.course_id;
            input.id = `course_${course.course_id}`;
            const label = document.createElement("label");
            label.className = "form-check-label";
            label.htmlFor = input.id;
            label.textContent = `${course.course_name} (${course.course_code})`;
            div.appendChild(input);
            div.appendChild(label);
            coursesContainer.appendChild(div);
          });
        }
        academyInfo.innerHTML = "<strong>Ακαδημίες:</strong> " +
          (selected.academies || []).map(a => "Ακαδημία #" + a).join(", ");
      }
    })
    .catch(err => {
      console.error("Fetch error:", err);
      alert("Αποτυχία φόρτωσης αιτήσεων.");
    });

  // Add JS validation for at least one course selected
  document.querySelector('form').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="courses[]"]:checked');
    if (checked.length === 0) {
      e.preventDefault();
      alert('Παρακαλώ επιλέξτε τουλάχιστον ένα μάθημα.');
    }
    });
});
