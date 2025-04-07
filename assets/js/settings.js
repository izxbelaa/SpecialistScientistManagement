document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
  
    const validators = {
      first_name: (value) => value.trim() !== "",
      last_name: (value) => value.trim() !== "",
      email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
      phone: (value) => /^[0-9]{8,15}$/.test(value), // μπορείς να προσαρμόσεις το range
      new_password: (value) =>
        value === "" || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/.test(value),
      confirm_password: (value) =>
        value === document.querySelector("#new_password").value,
    };
  
    const messages = {
      first_name: "Το όνομα είναι υποχρεωτικό.",
      last_name: "Το επώνυμο είναι υποχρεωτικό.",
      email: "Μη έγκυρο email.",
      phone: "Ο αριθμός τηλεφώνου πρέπει να περιέχει μόνο αριθμούς (8-15 ψηφία).",
      new_password:
        "Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες, ένα κεφαλαίο, ένα πεζό και ένα σύμβολο.",
      confirm_password: "Οι κωδικοί δεν ταιριάζουν.",
    };
  
    form.querySelectorAll("input").forEach((input) => {
      input.addEventListener("input", () => {
        const name = input.name;
        if (validators[name]) {
          const isValid = validators[name](input.value);
          const feedback = input.parentElement.querySelector(".invalid-feedback");
  
          if (!isValid) {
            input.classList.add("is-invalid");
            if (feedback) feedback.textContent = messages[name];
          } else {
            input.classList.remove("is-invalid");
          }
        }
      });
    });
  
    form.addEventListener("submit", (e) => {
      let hasErrors = false;
  
      form.querySelectorAll("input").forEach((input) => {
        const name = input.name;
        if (validators[name]) {
          const isValid = validators[name](input.value);
          const feedback = input.parentElement.querySelector(".invalid-feedback");
  
          if (!isValid) {
            input.classList.add("is-invalid");
            if (feedback) feedback.textContent = messages[name];
            hasErrors = true;
          } else {
            input.classList.remove("is-invalid");
          }
        }
      });
  
      if (hasErrors) {
        e.preventDefault();
      }
    });
  });
  