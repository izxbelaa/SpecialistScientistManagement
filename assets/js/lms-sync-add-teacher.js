document.addEventListener('DOMContentLoaded', function() {
  let currentUserId = null;

  // Open modal on Add button click
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.btn-success[title="Πρόσβαση σε άλλο μάθημα"]')) {
      const btn = e.target.closest('.btn-success');
      currentUserId = btn.closest('tr').querySelector('.toggle-lms-access').getAttribute('data-user-id');
      document.getElementById('coursesList').innerHTML = '<div class="text-center my-3"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><div>Φόρτωση μαθημάτων...</div></div>';
      document.getElementById('addTeacherResult').innerHTML = '';
      const modal = new bootstrap.Modal(document.getElementById('addTeacherModal'));
      modal.show();
      fetch('../php/fetch_moodle_courses.php')
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const categories = data.categories;
            let html = '<div class="accordion" id="coursesAccordion">';
            categories.forEach((cat, i) => {
              const catId = 'cat' + cat.id;
              html += `
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading${catId}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${catId}" aria-expanded="false" aria-controls="collapse${catId}">
                      ${cat.name}
                    </button>
                  </h2>
                  <div id="collapse${catId}" class="accordion-collapse collapse" aria-labelledby="heading${catId}" data-bs-parent="#coursesAccordion">
                    <div class="accordion-body p-0">
                      <ul class="list-group list-group-flush mb-0">`;
              cat.courses.forEach(course => {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>${course.fullname}</span>
                  <button class="btn btn-primary btn-sm add-as-teacher" data-course-id="${course.id}">Προσθήκη</button>
                </li>`;
              });
              html += `</ul>
                    </div>
                  </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('coursesList').innerHTML = html;
          } else {
            document.getElementById('coursesList').innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης μαθημάτων.</div>';
          }
        })
        .catch(() => {
          document.getElementById('coursesList').innerHTML = '<div class="alert alert-danger">Σφάλμα σύνδεσης με τον διακομιστή.</div>';
        });
    }
  });

  // Handle add as teacher button
  document.getElementById('coursesList').addEventListener('click', function(e) {
    if (e.target.classList.contains('add-as-teacher')) {
      const courseId = e.target.getAttribute('data-course-id');
      e.target.disabled = true;
      fetch('../php/add_teacher_to_course.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: currentUserId, course_id: courseId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('addTeacherResult').innerHTML = '<div class="alert alert-success">Ο χρήστης προστέθηκε ως διδάσκων στο μάθημα!</div>';
        } else {
          document.getElementById('addTeacherResult').innerHTML = '<div class="alert alert-danger">' + (data.message || 'Σφάλμα προσθήκης.') + '</div>';
        }
      })
      .catch(() => {
        document.getElementById('addTeacherResult').innerHTML = '<div class="alert alert-danger">Σφάλμα σύνδεσης με τον διακομιστή.</div>';
      })
      .finally(() => { e.target.disabled = false; });
    }
  });
}); 