document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#login-form");
  
    const validators = {
      email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
      password: (value) => value.trim() !== "",
    };
  
    const messages = {
      email: "Το email είναι υποχρεωτικό ή μη έγκυρο.",
      password: "Ο κωδικός είναι υποχρεωτικός.",
    };
  
    form.querySelectorAll("input").forEach((input) => {
      input.addEventListener("input", () => {
        const name = input.name;
        const feedback = input.parentElement.querySelector(".invalid-feedback");
        const isValid = validators[name](input.value);
  
        if (!isValid) {
          input.classList.add("is-invalid");
          if (feedback) feedback.textContent = messages[name];
        } else {
          input.classList.remove("is-invalid");
          if (feedback) feedback.textContent = "";
        }
      });
    });
  
    form.addEventListener("submit", (e) => {
      let hasErrors = false;
      form.querySelectorAll("input").forEach((input) => {
        const name = input.name;
        const feedback = input.parentElement.querySelector(".invalid-feedback");
        const isValid = validators[name](input.value);
  
        if (!isValid) {
          input.classList.add("is-invalid");
          if (feedback) feedback.textContent = messages[name];
          hasErrors = true;
        } else {
          input.classList.remove("is-invalid");
          if (feedback) feedback.textContent = "";
        }
      });
  
      if (hasErrors) {
        e.preventDefault();
      }
    });
  });
  