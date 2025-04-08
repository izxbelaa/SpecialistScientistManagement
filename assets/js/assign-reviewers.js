document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("assignReviewersForm");
    const applicationSelect = document.getElementById("application_id");
    const reviewersContainer = document.getElementById("reviewersCheckboxes");
    const resultMessage = document.getElementById("resultMessage");
    const reviewersError = document.getElementById("reviewersError");
  
    // 🔄 Φόρτωση αιτήσεων
    fetch("../php/load-requests.php")
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          data.requests.forEach(req => {
            const option = document.createElement("option");
            option.value = req.id;
            option.textContent = `Αίτηση #${req.id}`;
            applicationSelect.appendChild(option);
          });
        } else {
          console.error("Απέτυχε η φόρτωση των αιτήσεων");
        }
      });
  
    // 🔄 Φόρτωση αξιολογητών με όνομα
    fetch("../php/assign-evaloators.php")
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          data.evaluators.forEach(ev => {
            const checkbox = document.createElement("div");
            checkbox.classList.add("form-check");
  
            checkbox.innerHTML = `
              <input class="form-check-input" type="checkbox" name="reviewers[]" value="${ev.user_id}" id="reviewer-${ev.user_id}">
              <label class="form-check-label" for="reviewer-${ev.user_id}">
                ${ev.first_name} ${ev.last_name}
              </label>
            `;
  
            reviewersContainer.appendChild(checkbox);
          });
        } else {
          console.error("Απέτυχε η φόρτωση των αξιολογητών");
        }
      });
  
    // ✅ Validation
    function validateForm() {
      let isValid = true;
  
      if (!applicationSelect.value) {
        applicationSelect.classList.add("is-invalid");
        isValid = false;
      } else {
        applicationSelect.classList.remove("is-invalid");
      }
  
      const checkedReviewers = reviewersContainer.querySelectorAll("input[type='checkbox']:checked");
      if (checkedReviewers.length === 0) {
        reviewersError.textContent = "Παρακαλώ επιλέξτε τουλάχιστον έναν αξιολογητή.";
        isValid = false;
      } else {
        reviewersError.textContent = "";
      }
  
      return isValid;
    }
  
    applicationSelect.addEventListener("change", validateForm);
    reviewersContainer.addEventListener("change", validateForm);
  
    // 🚀 Υποβολή
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!validateForm()) return;
  
      const formData = new FormData(form);
  
      fetch(form.action, {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            resultMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            form.reset();
            applicationSelect.classList.remove("is-invalid");
            reviewersError.textContent = "";
          } else {
            resultMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
          }
        })
        .catch(() => {
          resultMessage.innerHTML = `<div class="alert alert-danger">Σφάλμα σύνδεσης με τον server.</div>`;
        });
    });
  });
  