<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Συμπλήρωση Αίτησης</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
  .btn-white {
    background-color: #fff;
    color: #000;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
  }

  .btn-white:hover {
    background-color: #007bff; /* Bootstrap μπλε */
    color: #fff;
    border-color: #007bff;
  }
</style>
</head>

<body class="container-fluid px-0">

  <!-- Header Start -->
  <div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <h1 class="display-3 text-white animated slideInDown">Συμπλήρωση Αίτησης</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a class="text-white" href="application.php">Επιλογή Αίτησης</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page">Συμπλήρωση Αίτησης</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- Header End -->

  <div class="container py-4">
    <h2 id="formTitle" class="mb-2">Συμπλήρωση Αίτησης</h2>
    <p id="formDescription" class="text-muted mb-4" style="white-space: pre-wrap;"></p>

    <form id="applicationForm" class="row g-3">
      <div class="col-md-6">
        <label for="firstName" class="form-label">Όνομα *</label>
        <input type="text" class="form-control" id="firstName" required>
      </div>
      <div class="col-md-6">
        <label for="lastName" class="form-label">Επίθετο *</label>
        <input type="text" class="form-control" id="lastName" required>
      </div>
      <div class="col-md-6">
        <label for="email" class="form-label">Email *</label>
        <input type="email" class="form-control" id="email" required>
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">Τηλέφωνο *</label>
        <input type="text" class="form-control" id="phone" required>
      </div>
      <div class="col-12">
        <label for="resume" class="form-label">Βιογραφικό (PDF)</label>
        <input type="file" class="form-control" id="resume" accept=".pdf">
        <div class="preview-box" id="resumePreview">Δεν έχει επιλεγεί αρχείο.</div>
      </div>

      <div class="col-12 d-flex gap-3">
      <button type="button" id="saveDraft" class="btn btn-white">
  Αποθήκευση Πρόχειρης
</button>

        <button type="submit" class="btn btn-success">Τελική Υποβολή</button>
      </div>
    </form>

    <div class="alert alert-success mt-4 d-none" id="successMessage"></div>
  </div>

  <script>
    const selectedApp = JSON.parse(localStorage.getItem("selected_application") || "{}");
    document.getElementById("formTitle").textContent = selectedApp?.title || "Συμπλήρωση Αίτησης";
    document.getElementById("formDescription").textContent = selectedApp?.description || "";

    const form = document.getElementById("applicationForm");
    const successMessage = document.getElementById("successMessage");
    const resumeInput = document.getElementById("resume");
    const resumePreview = document.getElementById("resumePreview");

    resumeInput.addEventListener("change", function () {
      if (resumeInput.files.length > 0) {
        resumePreview.textContent = "Αρχείο: " + resumeInput.files[0].name;
      } else {
        resumePreview.textContent = "Δεν έχει επιλεγεί αρχείο.";
      }
    });

    // Δημιουργία μοναδικού αριθμού αίτησης
    function generateApplicationNumber() {
      const year = new Date().getFullYear();
      const random = Math.floor(100000 + Math.random() * 900000); // 6 ψηφία
      return `APP-${year}-${random}`;
    }

    document.getElementById("saveDraft").addEventListener("click", () => {
      const formData = {
        firstName: form.firstName.value,
        lastName: form.lastName.value,
        email: form.email.value,
        phone: form.phone.value,
        status: "draft"
      };
      localStorage.setItem("application_data", JSON.stringify(formData));
      successMessage.classList.remove("d-none");
      successMessage.textContent = "Η αίτηση αποθηκεύτηκε προσωρινά!";
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const applicationNumber = generateApplicationNumber();

      const formData = {
        firstName: form.firstName.value,
        lastName: form.lastName.value,
        email: form.email.value,
        phone: form.phone.value,
        application_number: applicationNumber,
        status: "submitted"
      };

      localStorage.setItem("application_data", JSON.stringify(formData));
      successMessage.classList.remove("d-none");
      successMessage.innerHTML = `
        Η αίτηση υποβλήθηκε επιτυχώς!<br>
        <strong>Αριθμός Αίτησης:</strong> ${applicationNumber}
      `;
    });
  </script>
</body>
</html>
