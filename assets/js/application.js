document.addEventListener("DOMContentLoaded", function () {
    const applicationsContainer = document.getElementById("applicationsContainer");
  
    const mockApplications = [
      { id: 1, title: "Αίτηση Υποψηφίου", description: "Για υποψήφιους ειδικών επιστημόνων." },
      { id: 2, title: "Αίτηση Ερευνητή", description: "Για θέσεις σε ερευνητικά έργα του ΤΕΠΑΚ." }
    ];
  
    renderApplications(mockApplications);
  
    function renderApplications(applications) {
      applicationsContainer.innerHTML = "";
      applications.forEach(app => {
        const card = document.createElement("div");
        card.className = "col-lg-4 col-md-6";
        card.innerHTML = `
          <div class="card h-100 shadow-sm">
            <div class="card-body text-start">
              <h5 class="card-title">${app.title}</h5>
              <p class="card-text">${app.description}</p>
              <button class="btn btn-orange w-100 select-application-btn" data-id="${app.id}" data-title="${app.title}">Ξεκίνα</button>
            </div>
          </div>
        `;
        applicationsContainer.appendChild(card);
      });
    }
  
    applicationsContainer.addEventListener("click", function (e) {
      if (e.target.classList.contains("select-application-btn")) {
        const appId = e.target.dataset.id;
        const title = e.target.dataset.title;
  
        // Αποθήκευση προσωρινού προφίλ στο localStorage
        localStorage.setItem("selected_application", JSON.stringify({
          id: appId,
          title: title,
          status: "draft",
          created_at: new Date().toISOString()
        }));
  
        // Redirect σε φόρμα συμπλήρωσης
        window.location.href = "fill-application.php";
      }
    });
  });
  