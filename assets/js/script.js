      // Load navbar.html dynamically
      document.addEventListener("DOMContentLoaded", function () {
        fetch('components/navbar.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('navbar-placeholder').innerHTML = data;
            })
            .catch(error => console.error('Error loading navbar:', error));
    });

   // Load sidebar.html dynamically into the placeholder
   fetch('components/sidebar.html')
   .then(response => response.text())
   .then(data => {
       document.getElementById('sidebar-placeholder').innerHTML = data;
   });
    // Load footer.html dynamically
    document.addEventListener("DOMContentLoaded", function () {
        fetch('components/footer.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('footer-placeholder').innerHTML = data;
            })
            .catch(error => console.error('Error loading footer:', error));
    });

  