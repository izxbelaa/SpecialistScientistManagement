document.addEventListener('DOMContentLoaded', function () {
  const emailInput = document.getElementById('login-email');
  const passwordInput = document.getElementById('login-password');
  const form = document.getElementById('login-form');

  // Έλεγχος email
  emailInput.addEventListener('input', function () {
    const email = emailInput.value.trim();
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    if (email === '' || !valid) {
      emailInput.classList.add('is-invalid');
    } else {
      emailInput.classList.remove('is-invalid');
    }
  });

  // Έλεγχος κωδικού
  passwordInput.addEventListener('input', function () {
    if (passwordInput.value.trim() === '') {
      passwordInput.classList.add('is-invalid');
    } else {
      passwordInput.classList.remove('is-invalid');
    }
  });

  // Έλεγχος πριν την υποβολή
  form.addEventListener('submit', function (e) {
    let valid = true;

    if (emailInput.classList.contains('is-invalid') || emailInput.value.trim() === '') {
      emailInput.classList.add('is-invalid');
      valid = false;
    }

    if (passwordInput.value.trim() === '') {
      passwordInput.classList.add('is-invalid');
      valid = false;
    }

    if (!valid) {
      e.preventDefault(); // Ακύρωση υποβολής
    }
  });
});
