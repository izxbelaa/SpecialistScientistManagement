document.addEventListener('DOMContentLoaded', function() {
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.toggle-lms-access')) {
      const btn = e.target.closest('.toggle-lms-access');
      const userId = btn.getAttribute('data-user-id');
      const isEnabled = btn.getAttribute('data-enabled') === '1';
      btn.disabled = true;
      fetch('../php/toggle_lms_access.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, enable: !isEnabled })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Update button and status badge in the same row
          btn.setAttribute('data-enabled', data.enabled ? '1' : '0');
          btn.className = 'btn btn-sm ' + (data.enabled ? 'btn-warning' : 'btn-primary') + ' me-1 toggle-lms-access';
          btn.title = data.enabled ? 'Απενεργοποίηση πρόσβασης' : 'Ενεργοποίηση πρόσβασης';
          btn.innerHTML = '<i class="fas ' + (data.enabled ? 'fa-user-slash' : 'fa-user-check') + '"></i>';
          // Update status badge
          const statusCell = btn.closest('tr').querySelector('td:nth-child(4) span');
          if (statusCell) {
            statusCell.className = 'badge ' + (data.enabled ? 'bg-success' : 'bg-danger');
            statusCell.textContent = data.enabled ? 'Έχει πρόσβαση' : 'Χωρίς πρόσβαση';
          }
        } else {
          alert('Σφάλμα: ' + (data.message || 'Δεν ήταν δυνατή η ενημέρωση.'));
        }
      })
      .catch(() => alert('Σφάλμα σύνδεσης με τον διακομιστή.'))
      .finally(() => { btn.disabled = false; });
    }
  });
}); 