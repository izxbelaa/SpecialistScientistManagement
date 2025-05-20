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

  fetch("../php/application-fetch.php?fetch=templates")
    .then(res => {
      if (!res.ok) throw new Error("Network response was not ok");
      return res.json();
    })
    .then(data => {
      if (!data.success || !Array.isArray(data.templates)) {
        throw new Error("Invalid data received from backend");
      }

      const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
      const templates = data.templates.filter(t => {
        const end = t.date_end?.split(" ")[0];
        return end && end >= today; // keep only not-expired
      });

      templateSelect.innerHTML = '<option value="">-- Επιλέξτε --</option>';

      templates.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.title;
        templateSelect.appendChild(opt);
      });

      templateSelect.addEventListener("change", () => {
        const selected = templates.find(t => t.id == templateSelect.value);
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
      alert("Αποτυχία φόρτωσης των δεδομένων. Δοκιμάστε ξανά.");
    });
});
