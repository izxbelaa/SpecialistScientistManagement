// assets/js/assign-reviewers.js
document.addEventListener('DOMContentLoaded', ()=>{
  const form   = document.getElementById('assignReviewersForm');
  const select = document.getElementById('requestSelect');
  const box    = document.getElementById('reviewersCheckboxes');
  const err    = document.getElementById('reviewersError');

  function validate(){
    let ok = true;
    if(!select.value){
      select.classList.add('is-invalid');
      ok = false;
    } else select.classList.remove('is-invalid');

    const any = Array.from(box.querySelectorAll('input[type=checkbox]'))
                     .some(cb=>cb.checked);
    if(!any){
      err.textContent = 'Παρακαλώ επιλέξτε τουλάχιστον έναν αξιολογητή.';
      ok = false;
    } else err.textContent='';

    return ok;
  }

  select.addEventListener('change', validate);
  box.addEventListener('change', validate);

  form.addEventListener('submit', e=>{
    e.preventDefault();
    if(!validate()) return;
    fetch(form.action,{
      method:'POST',
      body:new FormData(form),
      headers:{'Accept':'application/json'}
    })
    .then(r=>r.json())
    .then(data=>{
      Swal.fire(
        data.success?'Επιτυχία':'Σφάλμα',
        data.message,
        data.success?'success':'error'
      );
    })
    .catch(_=>{
      Swal.fire('Σφάλμα','Σφάλμα δικτύου.','error');
    });
  });
});
