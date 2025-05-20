document.addEventListener("DOMContentLoaded", function () {
  const templateSelect = document.getElementById("templateSelect");
  const description = document.getElementById("description");
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");
  const courses = document.getElementById("courses");
  const academyInfo = document.getElementById("academyInfo");

  if (!templateSelect || !description || !startDate || !endDate || !courses || !academyInfo) {
    console.error("One or more elements not found in the DOM. Check your HTML.");
    return;
  }

  fetch("../php/application-filter.php") // must return { templates, userApplications, success: true }
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
      const filteredTemplates = templates.filter(t => {
        const endDate = t.date_end?.split(" ")[0];
        return endDate >= today && !excludedTemplateIds.includes(t.id);
      });

      // Render options
      templateSelect.innerHTML = '<option value="">-- Επιλέξτε --</option>';
      filteredTemplates.forEach(t => {
        const opt = document.createElement("option");
        opt.value = t.id;
        opt.textContent = t.title;
        templateSelect.appendChild(opt);
      });

      // On select, fill data
      templateSelect.addEventListener("change", () => {
        const selected = filteredTemplates.find(t => t.id == templateSelect.value);
        if (!selected) return;

        description.value = selected.description || "";
        startDate.value = (selected.date_start || "").split(" ")[0];
        endDate.value = (selected.date_end || "").split(" ")[0];

        courses.innerHTML = "";
        (selected.courses || []).forEach(cid => {
          const opt = document.createElement("option");
          opt.value = cid;
          opt.textContent = "Μάθημα #" + cid;
          courses.appendChild(opt);
        });

        academyInfo.innerHTML = "<strong>Ακαδημίες:</strong> " +
          (selected.academies || []).map(a => "Ακαδημία #" + a).join(", ");
      });
    })
    .catch(err => {
      console.error("Fetch error:", err);
      alert("Αποτυχία φόρτωσης αιτήσεων.");
    });
});
