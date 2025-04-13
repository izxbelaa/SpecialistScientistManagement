document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");

  const validators = {
    first_name: (value) => value.trim() !== "",
    last_name: (value) => value.trim() !== "",
    email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    password: (value) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/.test(value),
    confirm_password: (value) =>
      value === document.querySelector("#password").value,
  };

  const messages = {
    first_name: "Το όνομα είναι υποχρεωτικό.",
    last_name: "Το επώνυμο είναι υποχρεωτικό.",
    email: "Μη έγκυρο email.",
    password:
      "Ο κωδικός πρέπει να περιλαμβάνει τουλάχιστον 8 χαρακτήρες, ένα κεφαλαίο, ένα πεζό και ένα σύμβολο.",
    confirm_password: "Οι κωδικοί δεν ταιριάζουν.",
  };

  form.querySelectorAll("input").forEach((input) => {
    const name = input.name;
    const feedback = input.parentElement.querySelector(".invalid-feedback");

    if (validators[name]) {
      input.addEventListener("input", () => {
        const isValid = validators[name](input.value);

        if (!isValid && input.value.trim() !== "") {
          input.classList.add("is-invalid");
          if (feedback) feedback.textContent = messages[name];
        } else {
          input.classList.remove("is-invalid");
          if (feedback) feedback.textContent = "";
        }
      });
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent form submission

    let hasErrors = false;
    const formData = new FormData(form);

    form.querySelectorAll("input").forEach((input) => {
      const name = input.name;
      const feedback = input.parentElement.querySelector(".invalid-feedback");

      if (validators[name]) {
        const isValid = validators[name](input.value);

        if (!isValid) {
          input.classList.add("is-invalid");
          if (feedback) feedback.textContent = messages[name];
          hasErrors = true;
        } else {
          input.classList.remove("is-invalid");
          if (feedback) feedback.textContent = "";
        }
      }
    });

    if (hasErrors) {
      showModal("Registration Failed", "Παρακαλώ συμπληρώστε σωστά τα πεδία.");
      return;
    }

    try {
      const response = await fetch("../../php/register.php", {
        method: "POST",
        body: formData, 
      });
      const result = await response.json();

      if (result.success) {
        showModal("Registration Successful", "Η εγγραφή ολοκληρώθηκε με επιτυχία!");
        form.reset(); // Reset the form after successful registration
      } else {
        const errorMessages = Object.values(result.errors).join("<br>");
        showModal("Registration Failed", errorMessages);
      }
    } catch (error) {
      showModal("Error", "Κάτι πήγε στραβά. Παρακαλώ προσπαθήστε ξανά.");
    }
  });

  function showModal(title, message) {
    const modalTitle = document.querySelector("#registerModal .modal-title");
    const modalBody = document.querySelector("#registerModal .modal-body");
    const modal = new bootstrap.Modal(document.getElementById("registerModal"));

    modalTitle.textContent = title;
    modalBody.innerHTML = message;
    modal.show();
  }
});
