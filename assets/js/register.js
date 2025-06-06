document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");
  const togglePassword = document.getElementById("togglePassword");
  const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

  // Password visibility toggle
  togglePassword.addEventListener("click", function () {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    
    // Toggle the eye icon
    const icon = this.querySelector("i");
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  });

  // Confirm password visibility toggle
  toggleConfirmPassword.addEventListener("click", function () {
    const type = confirmPasswordInput.getAttribute("type") === "password" ? "text" : "password";
    confirmPasswordInput.setAttribute("type", type);
    
    // Toggle the eye icon
    const icon = this.querySelector("i");
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  });

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
      showModal("Αποτυχία Εγγραφής", "Παρακαλώ συμπληρώστε σωστά τα πεδία.", "error");
      return;
    }

    try {
      const response = await fetch("../../php/register.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        showModal("Επιτυχής Εγγραφή", "Η εγγραφή ολοκληρώθηκε με επιτυχία!", "success");
        form.reset();
      } else {
        const errorMessages = Object.values(result.errors).join("<br>");
        showModal("Αποτυχία Εγγραφής", errorMessages, "error");
      }
    } catch (error) {
      showModal("Σφάλμα", "Κάτι πήγε στραβά. Παρακαλώ προσπαθήστε ξανά.", "error");
    }
  });

  function showModal(title, message, icon = "info") {
    Swal.fire({
      title: title,
      html: message,
      icon: icon,
      confirmButtonText: "OK",
    });
  }
});
