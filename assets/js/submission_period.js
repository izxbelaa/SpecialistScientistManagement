document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("submissionForm");
  const cancelBtn = document.getElementById("cancelBtn");
  const startDateInput = document.getElementById("startDate");
  const endDateInput = document.getElementById("endDate");
  const schoolSelect = document.getElementById("schoolSelect");
  const currentSettings = document.getElementById("currentSettings");

  // Δημιουργία error span για το σφάλμα
  const dateError = document.createElement("div");
  dateError.classList.add("text-danger", "mt-1");
  dateError.style.fontSize = "0.875rem";
  endDateInput.parentElement.appendChild(dateError);

  // Μορφοποίηση ημερομηνίας για εμφάνιση
  function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString("el-GR", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit"
    });
  }

  // Ενημέρωση preview
  function updateCurrentSettings() {
    const school = schoolSelect.options[schoolSelect.selectedIndex]?.text || "";
    const start = startDateInput.value;
    const end = endDateInput.value;

    if (school && start && end) {
      currentSettings.textContent = `Σχολή: ${school}, Έναρξη: ${formatDateTime(start)}, Λήξη: ${formatDateTime(end)}`;
    } else {
      currentSettings.textContent = "Οι τρέχουσες ρυθμίσεις θα εμφανιστούν εδώ.";
    }
  }

  // Real-time έλεγχος εγκυρότητας
  function validateDates() {
    const start = new Date(startDateInput.value);
    const end = new Date(endDateInput.value);

    if (startDateInput.value && endDateInput.value && end <= start) {
      endDateInput.classList.add("is-invalid");
      dateError.textContent = "Η ημερομηνία λήξης πρέπει να είναι μετά την ημερομηνία έναρξης.";
      return false;
    } else {
      endDateInput.classList.remove("is-invalid");
      dateError.textContent = "";
      return true;
    }
  }

  // Υποβολή φόρμας
  form.addEventListener("submit", function (e) {
    if (!validateDates()) {
      e.preventDefault(); // Μην επιτρέψεις την υποβολή
    }
  });

  // Cancel button καθαρίζει τη φόρμα
  cancelBtn.addEventListener("click", function () {
    form.reset();
    dateError.textContent = "";
    endDateInput.classList.remove("is-invalid");
    currentSettings.textContent = "Οι τρέχουσες ρυθμίσεις θα εμφανιστούν εδώ.";
  });

  // Συνδέουμε τα real-time validation events
  [startDateInput, endDateInput].forEach((input) => {
    input.addEventListener("input", function () {
      validateDates();
      updateCurrentSettings();
    });
  });

  schoolSelect.addEventListener("change", updateCurrentSettings);
});
