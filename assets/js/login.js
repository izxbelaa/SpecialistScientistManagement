document.addEventListener('DOMContentLoaded', function () {
  const emailInput = document.getElementById('login-email');
  const passwordInput = document.getElementById('login-password');
  const form = document.getElementById('login-form');

  emailInput.addEventListener('input', function () {
    validateEmail();
  });

  passwordInput.addEventListener('input', function () {
    validatePassword();
  });

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();

    if (!isEmailValid || !isPasswordValid) return;

    const formData = new FormData(form);

    // 🔄 SweetAlert loading
    Swal.fire({
      title: 'Επιτυχής Σύνδεση',
      allowOutsideClick: false,
      timer: 5000,
      didOpen: () => Swal.showLoading()
    });

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: formData
      });
      const data = await response.json();

      Swal.close();

      if (data.success) {
        // Επιτυχής σύνδεση → Redirect
        window.location.href = data.redirect || '../../dashboard.php';
      } else {
        // Αποτυχία → SweetAlert μήνυμα
        Swal.fire({
          icon: 'error',
          title: 'Αποτυχία σύνδεσης',
          text: data.message || 'Λάθος email ή κωδικός.'
        });
      }
    } catch (err) {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Σφάλμα',
        text: 'Δεν ήταν δυνατή η σύνδεση με τον server.'
      });
    }
  });

  function validateEmail() {
    const email = emailInput.value.trim();
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const errorDiv = emailInput.nextElementSibling;

    if (email === '') {
      emailInput.classList.add('is-invalid');
      errorDiv.textContent = 'Το email είναι υποχρεωτικό.';
      return false;
    } else if (!valid) {
      emailInput.classList.add('is-invalid');
      errorDiv.textContent = 'Το email δεν είναι έγκυρο.';
      return false;
    } else {
      emailInput.classList.remove('is-invalid');
      return true;
    }
  }

  function validatePassword() {
    const password = passwordInput.value.trim();
    const errorDiv = passwordInput.nextElementSibling;

    if (password === '') {
      passwordInput.classList.add('is-invalid');
      errorDiv.textContent = 'Ο κωδικός είναι υποχρεωτικός.';
      return false;
    } else {
      passwordInput.classList.remove('is-invalid');
      return true;
    }
  }
});
