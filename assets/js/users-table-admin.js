let allUsers = [];
let filteredUsers = [];
let currentPage = 1;
let sortColumn = null;
let sortDirection = 1; // 1 = asc, -1 = desc

function compareValues(a, b) {
  if (typeof a === 'string' && typeof b === 'string') {
    return a.localeCompare(b, undefined, { sensitivity: 'base' });
  }
  return a < b ? -1 : a > b ? 1 : 0;
}

function sortUsers(data) {
  if (sortColumn === null) return data;
  if (sortColumn === 'index') {
    return [...data].sort((a, b) => (a.originalIndex - b.originalIndex) * sortDirection);
  }
  return [...data].sort((a, b) => {
    let valA = a[sortColumn];
    let valB = b[sortColumn];
    return compareValues(valA, valB) * sortDirection;
  });
}

document.addEventListener("DOMContentLoaded", function () {
    // Fetch user data and populate the table
    fetch("../php/fetch_users.php")
        .then(response => response.json())
        .then(users => {
            // Assign originalIndex to each user
            users.forEach((user, i) => user.originalIndex = i + 1);
            console.log("Fetched Users:", users);
            const tableBody = document.querySelector("#usersTable tbody");

            if (!tableBody) {
                console.error("Table body not found!");
                return;
            }

            // Populate the table with rows of users
            allUsers = users; // Store all users
            filteredUsers = users; // Initialize with all users
            setPagination(filteredUsers); // Initialize pagination
        })
        .catch(error => console.error("Error fetching users:", error));

    // Event listener for search input
    document.getElementById("searchInput").addEventListener("input", filterTable);

    // Event listener for entries per page select
    document.getElementById("entriesSelect").addEventListener("change", function () {
        setPagination(filteredUsers); // Recalculate pagination when entries per page changes
    });

    // Add sorting event listeners to table headers
    const ths = Array.from(document.querySelectorAll('#usersTable thead th'));
    const colKeys = ['index', 'first_name', 'last_name', 'middle_name', 'email', 'type_of_user', 'disabled_user'];
    ths.forEach((th, idx) => {
      if (!th.dataset.label) th.dataset.label = th.textContent.replace(/[▲▼]/g, '').trim();
      th.style.cursor = idx === 7 ? 'default' : 'pointer';
      let arrow = '';
      if (idx !== 7) {
        arrow = '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#888; vertical-align:middle;">▼</span>';
        if (sortColumn === colKeys[idx]) {
          arrow = sortDirection === 1
            ? '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#0099ff; vertical-align:middle;">▲</span>'
            : '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#0099ff; vertical-align:middle;">▼</span>';
        }
      }
      th.innerHTML = `<span style='display:inline-flex;align-items:center'>${th.dataset.label}${arrow}</span>`;
      if (idx !== 7) {
        th.onclick = function() {
          if (sortColumn === colKeys[idx]) {
            sortDirection *= -1;
          } else {
            sortColumn = colKeys[idx];
            sortDirection = 1;
          }
          ths.forEach((t, i) => {
            if (!t.dataset.label) t.dataset.label = t.textContent.replace(/[▲▼]/g, '').trim();
            let arrow = '';
            if (i !== 7) {
              arrow = '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#888; vertical-align:middle;">▼</span>';
              if (colKeys[i] === sortColumn) {
                arrow = sortDirection === 1
                  ? '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#0099ff; vertical-align:middle;">▲</span>'
                  : '<span class="sort-arrow" style="margin-left:6px; min-width:18px; display:inline-block; color:#0099ff; vertical-align:middle;">▼</span>';
              }
            }
            t.innerHTML = `<span style='display:inline-flex;align-items:center'>${t.dataset.label}${arrow}</span>`;
          });
          setPagination(filteredUsers);
        };
      } else {
        th.onclick = null;
      }
    });

    // Add User modal submit handler
    document.getElementById("addUserForm").onsubmit = function(event) {
        event.preventDefault();
        if (!validateUserForm('add')) return;
        const userType = document.getElementById("addUserType").value;
        if (!userType) {
            alert("Παρακαλώ επιλέξτε τύπο χρήστη.");
            return;
        }
        const newUser = {
            firstName: document.getElementById("addFirstName").value,
            lastName: document.getElementById("addLastName").value,
            middleName: document.getElementById("addMiddleName").value,
            email: document.getElementById("addEmail").value,
            userType: userType,
            disabledUser: document.getElementById("addDisabledUser").value === '1' ? 1 : 0,
            password: document.getElementById("addPassword").value
        };
        console.log(newUser);
        fetch("../php/add_user.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(newUser)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Failed to add user: " + (data.error || "Unknown error"));
            }
        })
        .catch(error => alert("Error adding user: " + error));
    };
});

// Function to filter the table based on search input
function filterTable() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    filteredUsers = allUsers.filter(user => {
        return Object.values(user).some(value =>
            value.toString().toLowerCase().includes(searchInput)
        );
    });
    setPagination(filteredUsers); // Recalculate pagination with the filtered users
}

// Function to set the pagination based on user selection and filtered data
function setPagination(users) {
    // Determine the number of entries to show per page
    const entriesPerPage = parseInt(document.getElementById("entriesSelect").value, 10) || 5;

    // Sort users before paginating
    let sortedUsers = users;
    if (sortColumn !== null) {
      sortedUsers = sortUsers(users);
    }

    // Slice the filtered users based on the current page and entries per page
    const start = (currentPage - 1) * entriesPerPage;
    const end = currentPage * entriesPerPage;
    const usersToDisplay = sortedUsers.slice(start, end);

    // Rebuild the table with the current slice of users
    const tableBody = document.querySelector("#usersTable tbody");
    tableBody.innerHTML = ""; // Clear the existing rows

    usersToDisplay.forEach((user, idx) => {
        const row = document.createElement("tr");

        if (user.disabled_user == 1) {
            row.classList.add("disabled-user");
        }

        row.innerHTML = `
            <td>${user.originalIndex}</td>
            <td>${user.first_name}</td>
            <td>${user.last_name}</td>
            <td>${user.middle_name || 'N/A'}</td>
            <td>${user.email}</td>
            <td>${getUserTypeName(user.type_of_user)}</td>
            <td>${user.disabled_user ? 'Yes' : 'No'}</td>
            <td>
                <button class="btn btn-info btn-action me-1" title="Edit" onclick="editUser(${user.id}, '${user.first_name}', '${user.last_name}', '${user.middle_name}', '${user.email}', '${user.type_of_user}', ${user.disabled_user})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-action" title="Delete" onclick="deleteUser(${user.id}, this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    // Update the pagination controls
    updatePaginationControls(users.length, entriesPerPage);
    // Update the "Showing X to Y of Z entries" text
    updateEntriesCount(users.length, entriesPerPage);
}

// Function to update the "Showing X to Y of Z entries" text dynamically
function updateEntriesCount(totalItems, itemsPerPage) {
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, totalItems);
    const entriesCountText = `Showing ${start} to ${end} of ${totalItems} entries`;
    document.getElementById("entriesCount").textContent = entriesCountText;
}

// Function to update pagination controls (next/prev buttons, page numbers)
function updatePaginationControls(totalItems, itemsPerPage) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationControls = document.getElementById("paginationControls");
    paginationControls.innerHTML = "";  // Clear the existing controls

    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button");
        pageButton.textContent = i;
        pageButton.classList.add("btn", "btn-outline-primary", "m-1");
        pageButton.addEventListener("click", function () {
            currentPage = i;
            setPagination(filteredUsers);
        });
        paginationControls.appendChild(pageButton);
    }
}

// Function to open the modal and prefill the user data
function editUser(id, firstName, lastName, middleName, email, userType, disabledUser) {
    document.getElementById("editFirstName").value = firstName;
    document.getElementById("editLastName").value = lastName;
    document.getElementById("editMiddleName").value = middleName;
    document.getElementById("editEmail").value = email;
    document.getElementById("editUserType").value = userType;
    document.getElementById("editDisabledUser").value = disabledUser;

    var myModal = new bootstrap.Modal(document.getElementById('editModal'));
    myModal.show();

    document.getElementById("editUserForm").onsubmit = function (event) {
        event.preventDefault();
        if (!validateUserForm('edit')) return;
        
        const updatedUser = {
            id,
            firstName: document.getElementById("editFirstName").value,
            lastName: document.getElementById("editLastName").value,
            middleName: document.getElementById("editMiddleName").value,
            email: document.getElementById("editEmail").value,
            userType: document.getElementById("editUserType").value,
            disabledUser: document.getElementById("editDisabledUser").value === '1' ? 1 : 0,
        };

        fetch("../php/update_user.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(updatedUser)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTableRow(id, updatedUser);
                myModal.hide();
            } else {
                console.error("Failed to update user:", data.error);
            }
        })
        .catch(error => console.error("Error updating user:", error));
    };
}

// Function to update the table row with updated user information
function updateTableRow(id, updatedUser) {
    const rows = document.querySelectorAll("#usersTable tbody tr");
    rows.forEach(row => {
        const userId = row.dataset.userId;
        if (userId == id) {
            row.querySelector("td:nth-child(1)").textContent = updatedUser.firstName;
            row.querySelector("td:nth-child(2)").textContent = updatedUser.lastName;
            row.querySelector("td:nth-child(3)").textContent = updatedUser.middleName || 'N/A';
            row.querySelector("td:nth-child(4)").textContent = updatedUser.email;
            row.querySelector("td:nth-child(5)").textContent = getUserTypeName(updatedUser.userType);
            row.querySelector("td:nth-child(6)").textContent = updatedUser.disabledUser == '1' ? 'Yes' : 'No';
        }
    });
}

// Function to handle user deletion (disabling user)
function deleteUser(id, row) {
    fetch("../php/disable_user.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tableRow = row.closest("tr");
            tableRow.classList.add("disabled-user");
            tableRow.querySelector("td:nth-child(6)").innerText = "Yes";
        } else {
            console.error("Failed to disable user:", data.error);
        }
    })
    .catch(error => console.error("Error disabling user:", error));
}

function getUserTypeName(type) {
    const types = {
        0: "Χρήστης",
        1: "Υποψήφιος",
        2: "Ειδικός Επιστήμονας",
        3: "Επιθεωρητής",
        4: "Προϊστάμενος Ανθρώπινου Δυναμικού",
        5: "Διαχειριστής"
    };
    return types[type] ?? type;
}

// Add User modal submit handler
function validateUserForm(formPrefix) {
  let valid = true;
  const requiredFields = [
    `${formPrefix}FirstName`,
    `${formPrefix}LastName`,
    `${formPrefix}Email`,
    `${formPrefix}UserType`,
  ];
  let missingField = false;
  requiredFields.forEach(id => {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      el.classList.add('is-invalid');
      valid = false;
      missingField = true;
    } else {
      el.classList.remove('is-invalid');
    }
  });
  // Name fields should not contain numbers
  let nameHasNumber = false;
  ['FirstName', 'LastName', 'MiddleName'].forEach(field => {
    const el = document.getElementById(`${formPrefix}${field}`);
    if (el && /\d/.test(el.value)) {
      el.classList.add('is-invalid');
      valid = false;
      nameHasNumber = true;
    } else if (el) {
      el.classList.remove('is-invalid');
    }
  });
  // Password only for add form
  if (formPrefix === 'add') {
    const pwd = document.getElementById('addPassword');
    if (!pwd.value.trim()) {
      pwd.classList.add('is-invalid');
      valid = false;
      missingField = true;
    } else {
      pwd.classList.remove('is-invalid');
    }
  }
  // Email format check
  const emailEl = document.getElementById(`${formPrefix}Email`);
  if (emailEl && emailEl.value && !/^\S+@\S+\.\S+$/.test(emailEl.value)) {
    emailEl.classList.add('is-invalid');
    valid = false;
    missingField = true;
  }
  // Show SweetAlert2 error if needed
  if (missingField) {
    Swal.fire({
      icon: 'error',
      title: 'Σφάλμα',
      text: 'Όλα τα πεδία είναι υποχρεωτικά και πρέπει να είναι έγκυρα.'
    });
  } else if (nameHasNumber) {
    Swal.fire({
      icon: 'error',
      title: 'Σφάλμα',
      text: 'Τα ονόματα δεν πρέπει να περιέχουν αριθμούς.'
    });
  }
  return valid;
}

// Add validation feedback CSS if not present
if (!document.getElementById('user-form-validation-style')) {
  const style = document.createElement('style');
  style.id = 'user-form-validation-style';
  style.innerHTML = `.is-invalid { border-color: #dc3545 !important; } .is-invalid:focus { box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25) !important; } .invalid-feedback { color: #dc3545; font-size: 0.9em; display: block; }`;
  document.head.appendChild(style);
}
