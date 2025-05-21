document.addEventListener("DOMContentLoaded", function () {
    // Fetch user data and populate the table
    fetch("../php/fetch_users.php")
        .then(response => response.json())
        .then(users => {
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
let allUsers = [];
let filteredUsers = [];
let currentPage = 1;

function setPagination(users) {
    // Determine the number of entries to show per page
    const entriesPerPage = parseInt(document.getElementById("entriesSelect").value, 10) || 5;

    // Slice the filtered users based on the current page and entries per page
    const start = (currentPage - 1) * entriesPerPage;
    const end = currentPage * entriesPerPage;
    const usersToDisplay = users.slice(start, end);

    // Rebuild the table with the current slice of users
    const tableBody = document.querySelector("#usersTable tbody");
    tableBody.innerHTML = ""; // Clear the existing rows

    usersToDisplay.forEach((user, idx) => {
        const row = document.createElement("tr");

        if (user.disabled_user == 1) {
            row.classList.add("disabled-user");
        }

        row.innerHTML = `
            <td>${start + idx + 1}</td>
            <td>${user.first_name}</td>
            <td>${user.last_name}</td>
            <td>${user.middle_name || 'N/A'}</td>
            <td>${user.email}</td>
            <td>${user.type_of_user}</td>
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
            row.querySelector("td:nth-child(5)").textContent = updatedUser.userType;
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
