document.addEventListener('DOMContentLoaded', function () {
  const fullSyncSwitch = document.getElementById('fullSyncSwitch');
  if (!fullSyncSwitch) return;

  // Fetch current state on page load
  fetch('../php/get_full_sync_status.php')
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        fullSyncSwitch.checked = data.enabled === 1;
      }
    });

  // Listener for toggle switch
  fullSyncSwitch.addEventListener('change', function () {
    const isChecked = this.checked;
    const toggle = this;

    if (isChecked) {
      Swal.fire({
        title: 'Είσαι σίγουρος;',
        text: "Θέλεις να κάνεις Full Sync;",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ναι, συγχρονισμός!',
        cancelButtonText: 'Ακύρωση'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('../php/full_sync.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'enabled=1'
          })
            .then(res => res.json())
            .then(data => {
              if (data.status === "success") {
                Swal.fire('Ολοκληρώθηκε!', data.message, 'success');
              } else {
                Swal.fire('Σφάλμα!', data.message, 'error');
                toggle.checked = false;
              }
            })
            .catch(() => {
              Swal.fire('Σφάλμα!', 'Απέτυχε η σύνδεση με τον server.', 'error');
              toggle.checked = false;
            });
        } else {
          toggle.checked = false;
        }
      });
    } else {
      // Directly disable without confirmation
      fetch('../php/full_sync.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'enabled=0'
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") {
            Swal.fire('Απενεργοποιήθηκε!', data.message, 'success');
          } else {
            Swal.fire('Σφάλμα!', data.message, 'error');
            toggle.checked = true;
          }
        })
        .catch(() => {
          Swal.fire('Σφάλμα!', 'Απέτυχε η σύνδεση με τον server.', 'error');
          toggle.checked = true;
        });
    }
  });
});
