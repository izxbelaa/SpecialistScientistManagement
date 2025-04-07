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
      // Real-time validation on typing
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

      // Removed automatic validation on page load:
      // input.dispatchEvent(new Event("input"));
    }
  });

  form.addEventListener("submit", (e) => {
    let hasErrors = false;

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
      e.preventDefault();
    }
  });
});
