document.addEventListener("DOMContentLoaded", () => {
    // Fetch and fill current settings on load
    fetch('../php/get-admin-settings.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('site_color').value = data.site_color;
            document.getElementById('light_color').value = data.light_color;
            document.getElementById('dark_color').value = data.dark_color;
            document.getElementById('logoPreview').src = data.logo_path;
            document.getElementById('logocutPreview').src = data.logocut_path;

            // Update CSS variables dynamically if needed
            document.documentElement.style.setProperty('--primary', data.site_color);
            document.documentElement.style.setProperty('--light', data.light_color);
            document.documentElement.style.setProperty('--dark', data.dark_color);
        })
        .catch(error => console.error("Error fetching initial settings:", error));

    // Handle Save button click with fetch
    document.getElementById('saveBtn').addEventListener('click', (e) => {
        e.preventDefault();
    
        const form = document.getElementById('settingsForm');
        const formData = new FormData(form);
    
        fetch('../php/save-admin-settings.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            // Show SweetAlert2 toast on success
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Οι ρυθμίσεις αποθηκεύτηκαν επιτυχώς!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            
            // Re-fetch and update the settings
            fetch('../php/get-admin-settings.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('site_color').value = data.site_color;
                    document.getElementById('light_color').value = data.light_color;
                    document.getElementById('dark_color').value = data.dark_color;
                    document.getElementById('logoPreview').src = data.logo_path;
                    document.getElementById('logocutPreview').src = data.logocut_path;
                    
                    // Also update CSS variables dynamically
                    document.documentElement.style.setProperty('--primary', data.site_color);
                    document.documentElement.style.setProperty('--light', data.light_color);
                    document.documentElement.style.setProperty('--dark', data.dark_color);
                })
                .catch(error => console.error("Error re-fetching settings:", error));
        })
        .catch(error => {
            console.error("Error saving settings:", error);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Σφάλμα κατά την αποθήκευση.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    });
    });
