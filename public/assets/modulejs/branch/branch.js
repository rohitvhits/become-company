$(document).ready(function () {
    loadBranchList(1);

    // Form submit handler
    $('#branchForm').on('submit', function (e) {
        e.preventDefault();
        saveBranch();
    });

    // Clear name error on input
    $('#branch_name_input').on('input', function () {
        $(this).removeClass('is-invalid');
        $('#branch_name-error').text('').removeClass('d-block');
    });
});

// Open Create Modal
function openCreateModal() {
    $('#branchModalLabel').text('Add Branch');
    $('#branch_id').val('');
    $('#branch_name_input').val('');
    $('#submitBtn .submit-text').html('Save');
    clearBranchValidationErrors();
    $('#branchModal').modal('show');
}

// Clear validation errors
function clearBranchValidationErrors() {
    $('#branch_name_input').removeClass('is-invalid');
    $('#branch_name-error').text('').removeClass('d-block');
}

// Open Edit Modal
function openEditModal(id) {
    $('#branchModalLabel').text('Edit Branch');
    $('#branch_id').val(id);
    $('#submitBtn .submit-text').html('Update');
    clearBranchValidationErrors();

    $.ajax({
        url: BRANCH_MASTER + '/' + id,
        type: 'GET',
        success: function (response) {
            if (response.status) {
                $('#branch_name_input').val(response.data.branch_name);
                $('#branchModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load branch data');
            }
        },
        error: function (xhr) {
            toastr.error('Failed to load branch data');
        }
    });
}

// Save Branch (Create/Update)
function saveBranch() {
    var id = $('#branch_id').val();
    var url = id ? `${BRANCH_MASTER}/${id}` : `${BRANCH_MASTER}`;
    var method = id ? 'PUT' : 'POST';

    var formData = {
        _token: _CSRF_TOKEN,
        branch_name: $('#branch_name_input').val()
    };

    var cnt = 0;
    $('#branch_name_input').removeClass('is-invalid');
    $('#branch_name-error').text('').removeClass('d-block');

    if ($('#branch_name_input').val().trim() == "") {
        $('#branch_name_input').addClass('is-invalid');
        $('#branch_name-error').text('Branch name is required.').addClass('d-block');
        cnt++;
    }

    if (cnt == 0) {
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn .spinner-border').removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#branchModal').modal('hide');
                    loadBranchList(1);
                } else {
                    toastr.error(response.message || 'Operation failed');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                if (response && response.message) {
                    if (response.message.toLowerCase().includes('branch')) {
                        $('#branch_name_input').addClass('is-invalid');
                        $('#branch_name-error').text(response.message).addClass('d-block');
                    }
                    toastr.error(response.message);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false);
                $('#submitBtn .spinner-border').addClass('d-none');
            }
        });
    }
}

// Delete Branch
function deleteBranch(id) {
    $.confirm({
        title: "Are you sure?",
        content: 'You want to delete this branch? This action cannot be undone.',
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: BRANCH_MASTER + '/' + id,
                        type: 'DELETE',
                        data: {
                            _token: _CSRF_TOKEN
                        },
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                loadBranchList(1);
                            } else {
                                toastr.error(response.message || 'Delete failed');
                            }
                        },
                        error: function (xhr) {
                            toastr.error('An error occurred. Please try again.');
                        }
                    });
                }
            },
            cancel: function () {
                //close
            }
        }
    });
}

// Load Branch List via AJAX
function loadBranchList(page) {
    $('#branch-list').html('');
    $('#branch-data-loader').show();
    var branch_name = $('#branch_name').val();
    var created_date = $('#created_date').val();
    var created_by = $('#created_by').val();

    $.ajax({
        url: BRANCH_AJAX,
        type: 'GET',
        data: {
            'page': page,
            'branch_name': branch_name,
            'created_date': created_date,
            'created_by': created_by
        },
        success: function (response) {
            $('#branch-list').html(response);
            $('#branch-data-loader').hide();
        }
    });
}

// Filter toggle
$("#filter-btn").click(function () {
    $("#search-filter-btn").slideToggle(600);
});

// Pagination
$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadBranchList(page);
});

// Refresh/Clear filters
function refreshBranchData() {
    $('#search-form')[0].reset();
    clearTokenInputIfPresent("#created_by");
    loadBranchList(1);
}

// Status Update
function statusUpdate(id, status) {
    var msg = status == 0 ? 'You want to enable this branch?' : 'You want to disable this branch?';
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
                        url: BRANCH_STATUS,
                        type: 'POST',
                        data: {
                            _token: _CSRF_TOKEN,
                            'id': id,
                            'status': status
                        },
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                loadBranchList(1);
                            } else {
                                toastr.error(response.message || 'Status update failed');
                            }
                        },
                        error: function (xhr) {
                            toastr.error('An error occurred. Please try again.');
                        }
                    });
                }
            },
            cancel: function () {
                if (status == 1) {
                    $('#row_last_status' + id).prop("checked", true);
                } else {
                    $('#row_last_status' + id).prop("checked", false);
                }
            }
        }
    });
}

// Token input for created_by filter
if (typeof urlToken !== 'undefined') {
    $("#created_by").tokenInput(urlToken, {
        tokenLimit: 1,
        zindex: 9999,
        onAdd: function (item) {},
        onReady: function () {
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "180px",
                    "overflow-y": "auto"
                });
            }, 500);
        }
    });
}

// Date range picker
$(function () {
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
        }
    });
    $('#created_date').on('apply.daterangepicker', function (ev, picker) {
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
