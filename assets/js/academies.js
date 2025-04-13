
        // Load academies on page load
        function loadAcademies() {
            fetch('../php/academies.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('academiesTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(academy => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${academy.id}</td>
                                <td>${academy.academy_name}</td>
                                <td>${academy.academy_code}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary me-2" onclick="editAcademy(${academy.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteAcademy(${academy.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Edit academy
        function editAcademy(id) {
            fetch('../php/academies.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const academy = data.data;
                    document.getElementById('academyId').value = academy.id;
                    document.getElementById('academyName').value = academy.academy_name;
                    document.getElementById('academyCode').value = academy.academy_code;
                    document.getElementById('academyModalLabel').textContent = 'Edit Academy';
                    new bootstrap.Modal(document.getElementById('academyModal')).show();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Delete academy
        function deleteAcademy(id) {
            if (confirm('Are you sure you want to delete this academy?')) {
                fetch('../php/academies.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Academy deleted successfully');
                        loadAcademies();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Save academy
        document.getElementById('saveAcademy').addEventListener('click', function() {
            const form = document.getElementById('academyForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            
            // Create a URLSearchParams object to properly format the data
            const params = new URLSearchParams();
            params.append('action', id ? 'update' : 'create');
            params.append('name', formData.get('name'));
            params.append('code', formData.get('code'));
            if (id) {
                params.append('id', id);
            }
            
            fetch('../php/academies.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Academy saved successfully');
                    bootstrap.Modal.getInstance(document.getElementById('academyModal')).hide();
                    form.reset();
                    loadAcademies();
                } else {
                    // Show the actual error message from the server
                    alert(data.message || 'An unknown error occurred');
                    console.error('Server error:', data);
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                alert('A network error occurred. Please check the console for details.');
            });
        });

        // Reset form when modal is closed
        document.getElementById('academyModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('academyForm').reset();
            document.getElementById('academyId').value = '';
            document.getElementById('academyModalLabel').textContent = 'Add New Academy';
        });

        // Load academies when page loads
        document.addEventListener('DOMContentLoaded', loadAcademies);
 