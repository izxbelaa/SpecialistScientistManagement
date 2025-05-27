$(document).ready(function() {
    // Initialize datepicker for date inputs
    $('#startDate, #endDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Load academies on page load
    loadAcademies();

    // Load existing templates
    loadTemplates();

    // Handle adding new academy row
    $(document).on('click', '.add-academy', function() {
        const academyRow = $(this).closest('.academy-row');
        if (academyRow.find('select').val()) {
            const newRow = academyRow.clone();
            newRow.find('select').val('');
            newRow.find('.add-academy').removeClass('add-academy').addClass('remove-academy')
                .html('<i class="fas fa-minus"></i>');
            academyRow.after(newRow);
            loadAcademies(newRow.find('select'));
        }
    });

    // Handle removing academy row
    $(document).on('click', '.remove-academy', function() {
        $(this).closest('.academy-row').remove();
        updateCourses();
    });

    // Handle adding new department row
    $(document).on('click', '.add-department', function() {
        const departmentRow = $(this).closest('.department-row');
        if (departmentRow.find('select').val()) {
            const newRow = departmentRow.clone();
            const newId = 'department_select_' + ($('.department-select').length + 1);
            
            newRow.find('select')
                .val('')
                .attr('id', newId);
            
            newRow.find('.add-department')
                .removeClass('add-department')
                .addClass('remove-department')
                .html('<i class="fas fa-minus"></i>');
            
            departmentRow.after(newRow);
            loadDepartmentsForRow(newRow);
        }
    });

    // Handle removing department row
    $(document).on('click', '.remove-department', function() {
        $(this).closest('.department-row').remove();
        updateCourses();
    });

    // Handle academy selection change
    $(document).on('change', '.academy-select', function() {
        loadDepartmentsForRow($('.department-row:first'));
    });

    // Handle department selection change
    $(document).on('change', '.department-select', function() {
        const currentCourses = {};
        
        // Save current course selections
        $('#coursesContainer input[type="checkbox"]').each(function() {
            currentCourses[$(this).val()] = $(this).prop('checked');
        });

        // Get all selected departments
        const departments = [];
        $('.department-select').each(function() {
            const val = $(this).val();
            if (val) departments.push(val);
        });

        if (departments.length > 0) {
            $.ajax({
                url: '../php/get-courses.php',
                type: 'GET',
                data: { department_id: departments },
                success: function(response) {
                    $('#coursesContainer').html(response);
                    
                    // Restore previous selections
                    Object.keys(currentCourses).forEach(courseId => {
                        const checkbox = $('#coursesContainer input[value="' + courseId + '"]');
                        if (checkbox.length) {
                            checkbox.prop('checked', currentCourses[courseId]);
                        }
                    });
                }
            });
        } else {
            $('#coursesContainer').html('<p class="text-muted">Please select at least one department</p>');
        }
    });

    // Handle form submissions
    $('#addRequestForm').submit(function(e) {
        e.preventDefault();
        saveTemplate($(this));
    });

    $('#addAcademyForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../php/add-academy.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Academy added successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#academyModal').modal('hide');
                            $('#addAcademyForm')[0].reset();
                            loadAcademies();
                        });
                    } else {
                        showError(result.message || 'Error adding academy');
                    }
                } catch (e) {
                    showError('Invalid response from server');
                }
            }
        });
    });

    $('#addDepartmentForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../php/add-department.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Department added successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#departmentModal').modal('hide');
                            $('#addDepartmentForm')[0].reset();
                            if ($('#academy').val()) {
                                loadDepartments($('#academy').val());
                            }
                        });
                    } else {
                        showError(result.message || 'Error adding department');
                    }
                } catch (e) {
                    showError('Invalid response from server');
                }
            }
        });
    });

    $('#addCourseForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../php/add-course.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Course added successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#courseModal').modal('hide');
                            $('#addCourseForm')[0].reset();
                            if ($('#department').val()) {
                                loadCourses($('#department').val());
                            }
                        });
                    } else {
                        showError(result.message || 'Error adding course');
                    }
                } catch (e) {
                    showError('Invalid response from server');
                }
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-template', function() {
        const templateId = $(this).data('id');
        deleteTemplate(templateId);
    });

    // Handle edit button click
    $(document).on('click', '.edit-template', function() {
        const templateId = $(this).data('id');
        loadTemplateForEdit(templateId);
    });

    // Handle adding new request button click
    $(document).on('click', '#addRequestBtn', function() {
        // Reset form and clear template ID
        $('#addRequestForm')[0].reset();
        $('#templateId').val('');
        $('#addRequestModalLabel').text('Προσθήκη νέου προτύπου αιτήματος');
        
        // Clear existing rows except first ones
        $('.academy-row:not(:first)').remove();
        $('.department-row:not(:first)').remove();
        
        // Reset selects
        $('.academy-select').val('').trigger('change');
        $('.department-select').val('').trigger('change');
        $('#coursesContainer').empty();
    });

    // Initialize variables for pagination
    let currentPage = 1;
    let entriesPerPage = 5;
    let filteredData = [];
    let allRows = [];
    let sortColumn = null;
    let sortDirection = 1; // 1 = asc, -1 = desc

    function compareValues(a, b) {
        if (typeof a === 'string' && typeof b === 'string') {
            return a.localeCompare(b, undefined, { sensitivity: 'base' });
        }
        return a < b ? -1 : a > b ? 1 : 0;
    }

    function sortRequests(rows) {
        if (sortColumn === null) return rows;
        return [...rows].sort((rowA, rowB) => {
            const tdA = $(rowA).find('td').eq(sortColumn).text().trim();
            const tdB = $(rowB).find('td').eq(sortColumn).text().trim();
            return compareValues(tdA, tdB) * sortDirection;
        });
    }

    function setupRequestsTableSorting() {
        $('#requestsTable thead th').each(function(idx) {
            if (idx === 8) { // Skip actions column
                $(this).css('cursor', 'default').off('click');
                $(this).html($(this).text().replace(/[▲▼]/g, '').trim());
                return;
            }
            if (!$(this).data('label')) $(this).data('label', $(this).text().replace(/[▲▼]/g, '').trim());
            $(this).css('cursor', 'pointer');
            let arrow = '<span class="sort-arrow" style="float:right; margin-left:8px; color:#888;">▼</span>';
            if (sortColumn === idx) {
                arrow = sortDirection === 1
                    ? '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▲</span>'
                    : '<span class="sort-arrow" style="float:right; margin-left:8px; color:#0099ff;">▼</span>';
            }
            $(this).html($(this).data('label') + arrow);
            $(this).off('click').on('click', function() {
                if (sortColumn === idx) {
                    sortDirection *= -1;
                } else {
                    sortColumn = idx;
                    sortDirection = 1;
                }
                setupRequestsTableSorting();
                updateTable();
            });
        });
    }

    // Handle search input
    $('#searchInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        if (!searchText) {
            filteredData = allRows.slice();
        } else {
            filteredData = allRows.filter(function(row) {
                const $row = $(row);
                if (!$row.find('td').length) return false;
                const serial = $row.find('td:eq(0)').text().toLowerCase();
                const title = $row.find('td:eq(1)').text().toLowerCase();
                const description = $row.find('td:eq(2)').text().toLowerCase();
                const start = $row.find('td:eq(3)').text().toLowerCase();
                const end = $row.find('td:eq(4)').text().toLowerCase();
                const academies = $row.find('td:eq(5)').text().toLowerCase();
                const departments = $row.find('td:eq(6)').text().toLowerCase();
                const courses = $row.find('td:eq(7)').text().toLowerCase();
                return serial.includes(searchText) ||
                       title.includes(searchText) ||
                       description.includes(searchText) ||
                       start.includes(searchText) ||
                       end.includes(searchText) ||
                       academies.includes(searchText) ||
                       departments.includes(searchText) ||
                       courses.includes(searchText);
            });
        }
        currentPage = 1;
        updateTable();
    });

    // Handle entries per page change
    $('#entriesSelect').on('change', function() {
        entriesPerPage = parseInt($(this).val());
        currentPage = 1;
        updateTable();
    });

    // Handle pagination click
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            updateTable();
        }
    });

    // Function to update table with pagination and sorting
    function updateTable() {
        const start = (currentPage - 1) * entriesPerPage;
        const end = start + entriesPerPage;
        let sortedData = sortRequests(filteredData);
        const paginatedData = sortedData.slice(start, end);
        // Update table body
        const tbody = $('#requestsTable tbody');
        tbody.empty();
        if (paginatedData.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center">No requests found</td></tr>');
        } else {
            paginatedData.forEach((row, index) => {
                tbody.append(row);
            });
        }
        setupRequestsTableSorting();
        updatePagination();
        updateEntriesCount();
    }

    // Function to update pagination controls
    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / entriesPerPage) || 1;
        const paginationControls = $('#paginationControls');
        paginationControls.empty();

        // Only page numbers, with ellipsis if many pages
        let maxPagesToShow = 7;
        let startPage = 1;
        let endPage = totalPages;
        if (totalPages > maxPagesToShow) {
            if (currentPage <= 4) {
                startPage = 1;
                endPage = 5;
            } else if (currentPage >= totalPages - 3) {
                startPage = totalPages - 4;
                endPage = totalPages;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }

        // Always show first page
        paginationControls.append(`
            <li class="page-item${currentPage === 1 ? ' active' : ''}">
                <a class="page-link" href="#" data-page="1">1</a>
            </li>
        `);

        // Ellipsis after first page
        if (startPage > 2) {
            paginationControls.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        // Page numbers in range
        for (let i = Math.max(2, startPage); i <= Math.min(endPage, totalPages - 1); i++) {
            paginationControls.append(`
                <li class="page-item${currentPage === i ? ' active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Ellipsis before last page
        if (endPage < totalPages - 1) {
            paginationControls.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        // Always show last page if more than one
        if (totalPages > 1) {
            paginationControls.append(`
                <li class="page-item${currentPage === totalPages ? ' active' : ''}">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `);
        }
    }

    // Update the loadTemplates function to use pagination
    function loadTemplates() {
        $.ajax({
            url: '../php/get-request-templates.php',
            type: 'GET',
            success: function(response) {
                const tbody = $('#requestsTable tbody');
                if (response === '') {
                    tbody.html('<tr><td colspan="9" class="text-center">No requests found</td></tr>');
                    allRows = [];
                    filteredData = [];
                } else {
                    tbody.html(response);
                    allRows = $('#requestsTable tbody tr').toArray();
                    filteredData = allRows.slice();
                }
                currentPage = 1;
                updateTable();
            },
            error: function() {
                $('#requestsTable tbody').html('<tr><td colspan="9" class="text-center">Error loading requests</td></tr>');
                allRows = [];
                filteredData = [];
                updateTable();
            }
        });
    }

    // Function to show error messages
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    // Function to update entries count
    function updateEntriesCount() {
        const totalRows = $('#requestsTable tbody tr').length;
        const visibleRows = $('#requestsTable tbody tr:visible').length;
        const firstVisible = visibleRows > 0 ? 1 : 0;
        
        $('#entriesCount').text(
            `Showing ${firstVisible} to ${visibleRows} of ${totalRows} entries`
        );
    }
});

// Function to load academies
function loadAcademies(select) {
    const target = select || $('.academy-select');
    $.ajax({
        url: '../php/get-academies.php',
        type: 'GET',
        success: function(response) {
            target.html('<option value="">Select Academy</option>' + response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading academies:', error);
        }
    });
}

// Function to load departments for a specific row
function loadDepartmentsForRow(departmentRow) {
    if (!departmentRow.length) {
        console.error('Department row not found');
        return;
    }

    // Get all selected academy IDs
    const selectedAcademyIds = $('.academy-select').map(function() {
        return $(this).val();
    }).get().filter(Boolean);

    // Store current department selections
    const selectedDepartments = {};
    $('.department-select').each(function() {
        const val = $(this).val();
        if (val) {
            selectedDepartments[$(this).attr('id')] = val;
        }
    });

    if (selectedAcademyIds.length > 0) {
        $.ajax({
            url: '../php/get-departments.php',
            type: 'GET',
            data: { academy_ids: selectedAcademyIds },
            success: function(response) {
                // Update all department dropdowns with the new options
                $('.department-select').each(function() {
                    const currentSelect = $(this);
                    const currentId = currentSelect.attr('id');
                    const previousValue = selectedDepartments[currentId];
                    
                    currentSelect.html(response);
                    
                    // Restore previous selection if it exists in new options
                    if (previousValue && currentSelect.find('option[value="' + previousValue + '"]').length > 0) {
                        currentSelect.val(previousValue);
                    }
                });
                
                // Enable department selects
                $('.department-select').prop('disabled', false);
                
                // Update courses after restoring department selections
                updateCourses();
            },
            error: function(xhr, status, error) {
                console.error('Error loading departments:', error);
                $('.department-select').html('<option value="">Error loading departments</option>').prop('disabled', true);
            }
        });
    } else {
        $('.department-select').html('<option value="">Select Academy First</option>').prop('disabled', true);
        $('#coursesContainer').html('<p class="text-muted">Please select at least one department</p>');
    }
}

// Function to update courses based on selected departments
function updateCourses() {
    const selectedDepartments = $('.department-select').map(function() {
        return $(this).val();
    }).get().filter(Boolean);

    // Store currently checked courses
    const checkedCourses = $('#coursesContainer input[type="checkbox"]:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedDepartments.length) {
        $.ajax({
            url: '../php/get-courses.php',
            type: 'GET',
            data: { 
                department_id: selectedDepartments,
                checked_courses: checkedCourses
            },
            success: function(response) {
                $('#coursesContainer').html(response);
                
                // Restore checked state for previously selected courses
                checkedCourses.forEach(function(courseId) {
                    $('#coursesContainer input[value="' + courseId + '"]').prop('checked', true);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading courses:', error);
                $('#coursesContainer').html('<div class="text-danger">Error loading courses</div>');
            }
        });
    } else {
        $('#coursesContainer').html('<div class="text-muted">Please select at least one department</div>');
    }
}

// Function to save template
function saveTemplate(form) {
    const formData = new FormData();
    
    // Get template ID if it exists
    const templateId = $('#templateId').val();
    
    // Add template_id if editing
    if (templateId) {
        formData.append('template_id', templateId);
    }
    
    // Add basic form fields
    formData.append('templateTitle', form.find('#templateTitle').val());
    formData.append('templateDescription', form.find('#templateDescription').val());
    
    // Combine date and time for start and end
    const startDate = form.find('#startDate').val();
    const startTime = form.find('#startTime').val();
    const endDate = form.find('#endDate').val();
    const endTime = form.find('#endTime').val();
    
    formData.append('startDate', `${startDate} ${startTime}`);
    formData.append('endDate', `${endDate} ${endTime}`);

    // Add academies
    form.find('.academy-select').each(function() {
        if ($(this).val()) {
            formData.append('academies[]', $(this).val());
        }
    });

    // Add departments
    form.find('.department-select').each(function() {
        if ($(this).val()) {
            formData.append('departments[]', $(this).val());
        }
    });

    // Add selected courses
    $('#coursesContainer input[type="checkbox"]:checked').each(function() {
        formData.append('courses[]', $(this).val());
    });

    // Choose endpoint based on whether we're editing or creating
    const endpoint = templateId ? '../php/edit-request-template.php' : '../php/save-request-template.php';

    $.ajax({
        url: endpoint,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message || 'Template saved successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#addRequestModal').modal('hide');
                        form[0].reset();
                        $('#templateId').val(''); // Clear the template ID
                        loadTemplates();
                    });
                } else {
                    showError(result.message || 'Error saving template');
                }
            } catch (e) {
                showError('Invalid response from server');
            }
        }
    });
}

// Function to delete template
function deleteTemplate(templateId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../php/delete-request-template.php',
                type: 'POST',
                data: { template_id: templateId },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire(
                                'Deleted!',
                                'Template has been deleted.',
                                'success'
                            );
                            loadTemplates();
                        } else {
                            showError(result.message || 'Error deleting template');
                        }
                    } catch (e) {
                        showError('Invalid response from server');
                    }
                }
            });
        }
    });
}

// Function to load template for editing
function loadTemplateForEdit(templateId) {
    $.ajax({
        url: '../php/get-template-details.php',
        type: 'GET',
        data: { template_id: templateId },
        success: function(response) {
            try {
                const template = JSON.parse(response);
                if (template) {
                    // Reset form first
                    $('#addRequestForm')[0].reset();
                    
                    // Set basic fields
                    $('#templateId').val(template.id);
                    $('#templateTitle').val(template.title);
                    $('#templateDescription').val(template.description);
                    
                    // Split datetime into date and time
                    if (template.date_start) {
                        const [startDate, startTime] = template.date_start.split(' ');
                        $('#startDate').val(startDate);
                        $('#startTime').val(startTime);
                    }
                    
                    if (template.date_end) {
                        const [endDate, endTime] = template.date_end.split(' ');
                        $('#endDate').val(endDate);
                        $('#endTime').val(endTime);
                    }

                    // Clear existing rows except first ones
                    $('.academy-row:not(:first)').remove();
                    $('.department-row:not(:first)').remove();

                    // Load academies first
                    loadAcademies($('.academy-select:first')).then(() => {
                        // Set academy values and trigger changes
                        if (template.academy_ids && template.academy_ids.length > 0) {
                            template.academy_ids.forEach((academyId, index) => {
                                if (index === 0) {
                                    $('.academy-select:first').val(academyId).trigger('change');
                                } else {
                                    $('.add-academy:last').click();
                                    setTimeout(() => {
                                        $('.academy-select:last').val(academyId).trigger('change');
                                    }, 100);
                                }
                            });
                        }

                        // Set department values after a delay to ensure academies are loaded
                        setTimeout(() => {
                            if (template.department_ids && template.department_ids.length > 0) {
                                template.department_ids.forEach((departmentId, index) => {
                                    if (index === 0) {
                                        $('.department-select:first').val(departmentId).trigger('change');
                                    } else {
                                        $('.add-department:last').click();
                                        setTimeout(() => {
                                            $('.department-select:last').val(departmentId).trigger('change');
                                        }, 100);
                                    }
                                });
                            }

                            // Set course values after departments are loaded
                            setTimeout(() => {
                                if (template.course_ids && template.course_ids.length > 0) {
                                    template.course_ids.forEach(courseId => {
                                        $(`#coursesContainer input[value="${courseId}"]`).prop('checked', true);
                                    });
                                }
                            }, 500);
                        }, 500);
                    });

                    // Update modal title
                    $('#addRequestModalLabel').text('Επεξεργασία προτύπου αιτήματος');
                    $('#addRequestModal').modal('show');
                }
            } catch (e) {
                console.error('Error parsing template data:', e);
                showError('Invalid response from server');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading template:', error);
            showError('Error loading template details');
        }
    });
}

// Helper function to load academies with promise
function loadAcademies(select) {
    return new Promise((resolve, reject) => {
        const target = select || $('.academy-select');
        $.ajax({
            url: '../php/get-academies.php',
            type: 'GET',
            success: function(response) {
                target.html('<option value="">Select Academy</option>' + response);
                resolve();
            },
            error: function(xhr, status, error) {
                console.error('Error loading academies:', error);
                reject(error);
            }
        });
    });
}

// Function to update row numbers after filtering
function updateRowNumbers() {
    let counter = 1;
    $('#requestsTable tbody tr:visible').each(function() {
        $(this).find('td:first').text(counter++);
    });
}

// Function to update entries count
function updateEntriesCount() {
    const totalRows = $('#requestsTable tbody tr').length;
    const visibleRows = $('#requestsTable tbody tr:visible').length;
    const firstVisible = visibleRows > 0 ? 1 : 0;
    
    $('#entriesCount').text(
        `Showing ${firstVisible} to ${visibleRows} of ${totalRows} entries`
    );
} 