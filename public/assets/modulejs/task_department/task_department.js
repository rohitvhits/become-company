$(document).ready(function() {
    loadDepartmentList(1)
    // Handle user selection
    $('#user_select').on('change', function() {
        var userId = $(this).val();
        if (userId) {
            addUserToTable(userId);
            $(this).val('').trigger('change');
        }
    });

    // Form submit handler
    $('#departmentForm').on('submit', function(e) {
        e.preventDefault();
        saveDepartment();
    });

    $("#user_select").tokenInput(urlToken, {
        tokenLimit: 1,
        zindex: 9999,
        onAdd: function(item) {
            $('#user_select_id').val(item.id);
            $('#user_select_name').val(item.name);
            // Clear user error when adding a user
            $('#token-input-user_select').removeClass('is-invalid').css('border-color', '');
            $('#user-error').text('').removeClass('d-block').css('display', '');
        },
        onDelete: function(item) {
            $('#user_select_id').val('');
            $('#user_select_name').val('');
        }
    });

    // Clear name error on input
    $('#name').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#name-error').text('').removeClass('d-block');
    });
});

// Add user to assigned table
function addUserToTable(userId) {
    userId = parseInt(userId);

    // Check if already assigned
    if (assignedUsers.includes(userId)) {
        toastr.warning('User is already assigned');
        return;
    }

    // Find user data
    var user = allUsers.find(u => u.id === userId);
    if (!user) return;

    // Add to assignedUsers array
    assignedUsers.push(userId);

    // Remove "No users" row if exists
    $('#noUsersRow').remove();

    // Add row to table
    var rowNumber = $('#assignedUsersBody tr').length + 1;
    var row = `
        <tr data-user-id="${userId}">
            <td>${rowNumber}</td>
            <td>
                <strong>${user.name} - #<a target="_blank" href="${USER_ROUTE}/${userId}">${userId}</a> (${user.status})</strong><br>
                <small class="text-muted">${user.email}</small>
            </td>
            <td>
                <a class="btn btn-danger mr-2 badge badge-danger" style="background-color: #cb0b0b" onclick="removeUserFromTable(${userId})" title="Remove">
                    <i class="mdi mdi-delete"></i>
                </a>
            </td>
        </tr>
    `;
    $('#assignedUsersBody').append(row);
    // Remove from dropdown
    $('#user_select option[value="' + userId + '"]').remove();
    updateRowNumbers();
    setTimeout(function() {
        $("#user_select").tokenInput("clear");
    }, 500);  // 500ms delay
}

// Remove user from assigned table
function removeUserFromTable(userId) {
    userId = parseInt(userId);

    // Remove from array
    assignedUsers = assignedUsers.filter(id => id !== userId);

    // Remove row from table
    $('#assignedUsersBody tr[data-user-id="' + userId + '"]').remove();

    // Find user data and add back to dropdown
    var user = allUsers.find(u => u.id === userId);
    if (user) {
        var option = new Option(user.name + ' (' + user.email + ')', userId, false, false);
        $('#user_select').append(option);
    }

    // Show "No users" row if table is empty
    if (assignedUsers.length === 0) {
        $('#assignedUsersBody').html('<tr id="noUsersRow"><td colspan="3" class="text-center text-muted">No users assigned</td></tr>');
    }

    updateRowNumbers();
}

// Update row numbers
function updateRowNumbers() {
    $('#assignedUsersBody tr[data-user-id]').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

// Open Create Modal
function openCreateModal() {
    $('#departmentModalLabel').text('Add Department');
    $('#department_id').val('');
    $('#name').val('');
    $('#submitBtn .submit-text').html('Save');
    // Clear all validation errors
    clearValidationErrors();

    // Reset assigned users
    assignedUsers = [];
    $('#assignedUsersBody').html('<tr id="noUsersRow"><td colspan="3" class="text-center text-muted">No users assigned</td></tr>');

    // Reset dropdown to show all users
    $('#user_select').empty().append('<option value="">-- Select User --</option>');
    allUsers.forEach(function(user) {
        $('#user_select').append(new Option(user.name + ' (' + user.email + ')', user.id, false, false));
    });
    $('#user_select').val('').trigger('change');

    $('#departmentModal').modal('show');
}

// Clear validation errors
function clearValidationErrors() {
    $('#name').removeClass('is-invalid');
    $('#name-error').text('').removeClass('d-block');
    $('#token-input-user_select').removeClass('is-invalid').css('border-color', '');
    $('#user-error').text('').removeClass('d-block').css('display', '');
}

// Open Edit Modal
function openEditModal(id) {
    $('#departmentModalLabel').text('Edit Department');
    $('#department_id').val(id);
    $('#submitBtn .submit-text').html('Update');
    // Clear all validation errors
    clearValidationErrors();

    // Reset
    assignedUsers = [];
    $('#assignedUsersBody').html('<tr id="noUsersRow"><td colspan="3" class="text-center text-muted">No users assigned</td></tr>');
    $('#user_select').empty().append('<option value="">-- Select User --</option>');

    // Fetch department data
    $.ajax({
        url: TASK_DEPARTMENT + '/' + id,
        type: 'GET',
        success: function(response) {
            if (response.status) {
                $('#name').val(response.data.name);

                // Add assigned users to table
                if (response.data.user_ids && response.data.user_ids.length > 0) {
                    response.data.user_ids.forEach(function(userId) {
                        addUserToTable(userId);
                    });
                }

                // Add remaining users to dropdown
                allUsers.forEach(function(user) {
                    if (!assignedUsers.includes(user.id)) {
                        $('#user_select').append(new Option(user.name + ' (' + user.email + ')', user.id, false, false));
                    }
                });

                $('#departmentModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load department data');
            }
        },
        error: function(xhr) {
            toastr.error('Failed to load department data');
        }
    });
}

// Save Department (Create/Update)
function saveDepartment() {
    var id = $('#department_id').val();
    var url = id 
        ? `${TASK_DEPARTMENT}/${id}` 
        : `${TASK_DEPARTMENT}`;
    var method = id ? 'PUT' : 'POST';

    var formData = {
        _token: _CSRF_TOKEN,
        name: $('#name').val(),
        user_ids: assignedUsers
    };
    cnt = 0;
    $('#name').removeClass('is-invalid');
    $('#name-error').text('').removeClass('d-block');
    $('#token-input-user_select').removeClass('is-invalid');
    $('#user-error').text('').removeClass('d-block');
    $('.token-input-list').attr('style','');
    if($('#name').val().trim() == ""){
        $('#name').addClass('is-invalid');
        $('#name-error').text('Department name is required.').addClass('d-block');
        cnt++;
    }
    if(assignedUsers.length === 0){
        $('#token-input-user_select').addClass('is-invalid').css('border-color', '#dc3545');
        $('#user-error').text('The user ids field is required.').addClass('d-block').css({
            'display': 'block',
            'color': '#dc3545',
            'font-size': '80%',
            'margin-top': '0.25rem'
        });
        $('.token-input-list').attr('style','border-color: #dc3545;');
        cnt++;
    }
    if(cnt == 0){
         // Show loading
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn .spinner-border').removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#departmentModal').modal('hide');
                    loadDepartmentList(1);
                } else {
                    toastr.error(response.message || 'Operation failed');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.message) {
                    // Clear all previous error states
                    $('#name').removeClass('is-invalid');
                    $('#name-error').text('').removeClass('d-block');
                    $('#token-input-user_select').removeClass('is-invalid');
                    $('#user-error').text('').removeClass('d-block');
                    $('.token-input-list').attr('style','');

                    // Handle name field errors
                    if (response.message.toLowerCase().includes('name')) {
                        $('#name').addClass('is-invalid');
                        $('#name-error').text(response.message).addClass('d-block');
                    }

                    // Handle user field errors
                    if (response.message.toLowerCase().includes('user')) {
                        $('#token-input-user_select').addClass('is-invalid').css('border-color', '#dc3545');
                        $('#user-error').text(response.message).addClass('d-block').css({
                            'display': 'block',
                            'color': '#dc3545',
                            'font-size': '80%',
                            'margin-top': '0.25rem'
                        });
                        $('.token-input-list').attr('style','border-color: #dc3545;');
                        toastr.error(response.message);
                    }
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false);
                $('#submitBtn .spinner-border').addClass('d-none');
            }
        });
    }
}

// Confirm Delete
function deleteDepartment(id) {
    $.confirm({
        title: "Are you sure?",
        content: 'you want to delete this department? This action cannot be undone.',
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function () {
                   $.ajax({
                        url: TASK_DEPARTMENT +'/'+ id,
                        type: 'DELETE',
                        data: {
                            _token: _CSRF_TOKEN
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('#deleteModal').modal('hide');
                                loadDepartmentList(1);
                            } else {
                                toastr.error(response.message || 'Delete failed');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('An error occurred. Please try again.');
                        },
                    });
                }
            },
            cancel: function() {
                //close
            },
        }
    });
}

function loadDepartmentList(page){
    $('#dep-wise-data-loader').attr('style','display:flex');
    $('#department-list').html('');
    var name = $('#dp_name').val();
    var created_date = $('#created_date').val();
    var created_by = $('#created_by').val();
    $.ajax({
        url: TASK_DEPARTMENT_AJAX,
        type: 'GET',
        data: {
            'page': page,
            'name' : name,
            'created_date' : created_date,
            'created_by' : created_by,
        },
        success: function(response) {
            $('#department-list').html(response);
            $('#dep-wise-data-loader').attr('style','display:none');
        }
    });
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadDepartmentList(page);
});

function refreshDepData(){
    $('#search-form')[0].reset();
    clearTokenInputIfPresent("#created_by");
    loadDepartmentList(1);
}

// Confirm Delete
function statusUpdate(id,status) {
    var msg = status == 0 ? 'you want to enable department' : 'you want to disable department';
    $.confirm({
        title: "Are you sure?",
        content: msg,
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-info',
                action: function () {
                   $.ajax({
                    url: TASK_DEPARTMENT_STATUS,
                    type: 'POST',
                    data: {
                        _token: _CSRF_TOKEN,
                        'id' : id,
                        'status': status
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            loadDepartmentList(1);
                        } else {
                            toastr.error(response.message || 'Status update failed');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                    },
                    complete: function() {
                        $('#statusUpdateBtn').prop('disabled', false);
                        $('#statusUpdateBtn .spinner-border').addClass('d-none');
                    }
                });
                }
            },
            cancel: function() {
                //close
                if(status == 1){
                    $('#row_last_status'+id).prop("checked",true);
                }else{
                    $('#row_last_status'+id).prop("checked",false);
                }
            },
        }
    });
}

$("#created_by").tokenInput(urlToken, {
    tokenLimit: 1,
    zindex: 9999,
   
    onAdd: function (item) {
       
    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto"
            });
        }, 500);
    }
});

$(function() {
    let start = moment().startOf('day');
    let end = moment().endOf('day');

    $('#created_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        showCustomRangeLabel: true,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
        }
    });
    $('#created_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});

function clearTokenInputIfPresent(selector) {
    const $input = $(selector);
    const tokenInput = $input.data("tokenInputObject");
    if (tokenInput && tokenInput.getTokens().length > 0) {
        tokenInput.clear();
    }
}