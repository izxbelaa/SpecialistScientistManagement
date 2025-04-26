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
        $('#addRequestModalLabel').text('Add New Request Template');
        
        // Clear existing rows except first ones
        $('.academy-row:not(:first)').remove();
        $('.department-row:not(:first)').remove();
        
        // Reset selects
        $('.academy-select').val('').trigger('change');
        $('.department-select').val('').trigger('change');
        $('#coursesContainer').empty();
    });

    // Handle search input
    $('#searchInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        
        $('#requestsTable tbody tr').each(function() {
            const row = $(this);
            if (!row.find('td').length) return; // Skip "no results" row
            
            const title = row.find('td:eq(1)').text().toLowerCase();
            const description = row.find('td:eq(2)').text().toLowerCase();
            const academies = row.find('td:eq(5)').text().toLowerCase();
            const departments = row.find('td:eq(6)').text().toLowerCase();
            const courses = row.find('td:eq(7)').text().toLowerCase();
            
            const matches = title.includes(searchText) ||
                          description.includes(searchText) ||
                          academies.includes(searchText) ||
                          departments.includes(searchText) ||
                          courses.includes(searchText);
            
            row.toggle(matches);
        });
        
        updateRowNumbers();
        updateEntriesCount();
    });
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

// Function to load existing templates
function loadTemplates() {
    $.ajax({
        url: '../php/get-request-templates.php',
        type: 'GET',
        success: function(response) {
            if (response === '') {
                $('#requestsTable tbody').html('<tr><td colspan="9" class="text-center">No requests found</td></tr>');
            } else {
                $('#requestsTable tbody').html(response);
            }
            updateEntriesCount();
        },
        error: function() {
            $('#requestsTable tbody').html('<tr><td colspan="9" class="text-center">Error loading requests</td></tr>');
        }
    });
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
                    $('#addRequestModalLabel').text('Edit Request Template');
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

// Function to show error messages
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
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