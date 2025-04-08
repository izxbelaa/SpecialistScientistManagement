document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('new-password');
    const confirmInput = document.getElementById('confirm-password');
    const form = document.getElementById('reset-password-form');
    const rulesContainer = document.getElementById('password-rules');
    const matchMessage = document.getElementById('match-message');
  
    const rules = {
      length: { regex: /.{8,}/, label: "Τουλάχιστον 8 χαρακτήρες" },
      upper: { regex: /[A-Z]/, label: "Τουλάχιστον ένα κεφαλαίο γράμμα" },
      lower: { regex: /[a-z]/, label: "Τουλάχιστον ένα πεζό γράμμα" },
      number: { regex: /\d/, label: "Τουλάχιστον έναν αριθμό" },
      symbol: { regex: /[\W_]/, label: "Τουλάχιστον ένα σύμβολο (π.χ. !@#)" }
    };
  
    function updateRules(password) {
      rulesContainer.innerHTML = '';
      Object.entries(rules).forEach(([key, rule]) => {
        const valid = rule.regex.test(password);
        const color = valid ? 'text-success' : 'text-danger';
        const item = `<div class="${color}">${rule.label}</div>`;
        rulesContainer.insertAdjacentHTML('beforeend', item);
      });
    }
  
    function validateConfirm() {
      const match = passwordInput.value === confirmInput.value && confirmInput.value !== '';
      matchMessage.textContent = match ? 'Οι κωδικοί ταιριάζουν' : 'Οι κωδικοί δεν ταιριάζουν';
      matchMessage.className = match ? 'text-success small' : 'text-danger small';
      return match;
    }
  
    passwordInput.addEventListener('input', function () {
      updateRules(passwordInput.value);
      validateConfirm();
    });
  
    confirmInput.addEventListener('input', validateConfirm);
  
    form.addEventListener('submit', function (e) {
      const allValid = Object.values(rules).every(rule => rule.regex.test(passwordInput.value));
      const match = validateConfirm();
  
      if (!allValid || !match) {
        e.preventDefault();
        alert('Βεβαιωθείτε ότι ο κωδικός πληροί όλους τους κανόνες και ότι οι κωδικοί ταιριάζουν.');
      }
    });
  });
  