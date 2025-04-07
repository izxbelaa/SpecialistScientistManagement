document.getElementById('forgotPasswordForm').addEventListener('submit', async function (e) {
    e.preventDefault();
  
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const email = emailInput.value.trim();
    const msg = document.getElementById('responseMsg');
  
    // Καθαρισμός μηνυμάτων και styling
    msg.innerHTML = '';
    emailInput.classList.remove('is-invalid', 'is-valid');
    emailError.classList.add('d-none');
  
    // Έλεγχος εγκυρότητας email
    const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  
    if (!isValidEmail) {
      emailInput.classList.add('is-invalid');
      emailError.classList.remove('d-none');
      return;
    } else {
      emailInput.classList.add('is-valid');
    }
  
    // Αποστολή request
    try {
      const response = await fetch('../../php/forgot-password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email })
      });
  
      const result = await response.json();
  
      if (result.success) {
        msg.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
      } else {
        msg.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
      }
    } catch (error) {
      msg.innerHTML = `<div class="alert alert-danger">Σφάλμα κατά την αποστολή. Προσπαθήστε ξανά.</div>`;
    }
  });
  