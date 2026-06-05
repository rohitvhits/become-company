$(document).ready(function () {
    loadBranchLinkList(1);

    // Initialize select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Form submit handler
    $('#branchLinkForm').on('submit', function (e) {
        e.preventDefault();
        saveBranchLink();
    });
});

// Open Create Modal
function openCreateLinkModal() {
    $('#branchLinkModalLabel').text('Add Branch Link');
    $('#branch_link_id').val('');
    $('#link_branch_id').val('').trigger('change');
    $('#link_agency_ids').val([]).trigger('change');
    $('#link_service_ids').val([]).trigger('change');
    $('#submitLinkBtn .submit-text').html('Save');
    clearLinkValidationErrors();

    // Initialize select2 inside modal
    $('#link_branch_id').select2({
        dropdownParent: $('#branchLinkModal'),
        placeholder: '-- Select Branch --',
        allowClear: true
    });
    $('#link_agency_ids').select2({
        dropdownParent: $('#branchLinkModal'),
        placeholder: '-- Select Agencies --',
        allowClear: true
    });
    $('#link_service_ids').select2({
        dropdownParent: $('#branchLinkModal'),
        placeholder: '-- Select Services --',
        allowClear: true
    });

    $('#branchLinkModal').modal('show');
}

// Clear validation errors
function clearLinkValidationErrors() {
    $('#link_branch_id').removeClass('is-invalid');
    $('#branch_id-error').text('').removeClass('d-block');
    $('#link_agency_ids').removeClass('is-invalid');
    $('#agency_ids-error').text('').removeClass('d-block');
    $('#link_service_ids').removeClass('is-invalid');
    $('#service_ids-error').text('').removeClass('d-block');
}

// Open Edit Modal
function openEditLinkModal(id) {
    $('#branchLinkModalLabel').text('Edit Branch Link');
    $('#branch_link_id').val(id);
    $('#submitLinkBtn .submit-text').html('Update');
    clearLinkValidationErrors();

    $.ajax({
        url: BRANCH_LINK_MASTER + '/' + id,
        type: 'GET',
        success: function (response) {
            if (response.status) {
                // Initialize select2 inside modal
                $('#link_branch_id').select2({
                    dropdownParent: $('#branchLinkModal'),
                    placeholder: '-- Select Branch --',
                    allowClear: true
                });
                $('#link_agency_ids').select2({
                    dropdownParent: $('#branchLinkModal'),
                    placeholder: '-- Select Agencies --',
                    allowClear: true
                });
                $('#link_service_ids').select2({
                    dropdownParent: $('#branchLinkModal'),
                    placeholder: '-- Select Services --',
                    allowClear: true
                });

                $('#link_branch_id').val(response.data.branch_id).trigger('change');
                $('#link_agency_ids').val([response.data.agency_id]).trigger('change');
                $('#link_service_ids').val([response.data.service_id]).trigger('change');

                $('#branchLinkModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load branch link data');
            }
        },
        error: function (xhr) {
            toastr.error('Failed to load branch link data');
        }
    });
}

// Save Branch Link (Create/Update)
function saveBranchLink() {
    let id = $('#branch_link_id').val();
    let url = id ? `${BRANCH_LINK_MASTER}/${id}` : `${BRANCH_LINK_MASTER}`;
    let method = id ? 'PUT' : 'POST';

    let formData = {
        _token: _CSRF_TOKEN,
        branch_id: $('#link_branch_id').val(),
        agency_ids: $('#link_agency_ids').val(),
        service_ids: $('#link_service_ids').val()
    };

    let cnt = 0;
    clearLinkValidationErrors();

    if (!$('#link_branch_id').val()) {
        $('#branch_id-error').text('Branch is required.').addClass('d-block');
        cnt++;
    }
    if (!$('#link_agency_ids').val() || $('#link_agency_ids').val().length === 0) {
        $('#agency_ids-error').text('At least one agency is required.').addClass('d-block');
        cnt++;
    }
    if (!$('#link_service_ids').val() || $('#link_service_ids').val().length === 0) {
        $('#service_ids-error').text('At least one service is required.').addClass('d-block');
        cnt++;
    }

    if (cnt == 0) {
        $('#submitLinkBtn').prop('disabled', true);
        $('#submitLinkBtn .spinner-border').removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#branchLinkModal').modal('hide');
                    loadBranchLinkList(1);
                } else {
                    toastr.error(response.message || 'Operation failed');
                }
            },
            error: function (xhr) {
                let response = xhr.responseJSON;
                if (response && response.message) {
                    toastr.error(response.message);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function () {
                $('#submitLinkBtn').prop('disabled', false);
                $('#submitLinkBtn .spinner-border').addClass('d-none');
            }
        });
    }
}

// Delete Branch Link
function deleteBranchLink(id) {
    $.confirm({
        title: "Are you sure?",
        content: 'You want to delete this branch link? This action cannot be undone.',
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: BRANCH_LINK_MASTER + '/' + id,
                        type: 'DELETE',
                        data: {
                            _token: _CSRF_TOKEN
                        },
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                loadBranchLinkList(1);
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

// Load Branch Link List via AJAX
function loadBranchLinkList(page) {
    $('#branch-link-list').html('');
    $('#branch-link-data-loader').show();

    var branch_id = $('#filter_branch_id').val();
    var agency_id = $('#filter_agency_id').val();
    var created_date = $('#link_created_date').val();

    $.ajax({
        url: BRANCH_LINK_AJAX,
        type: 'GET',
        data: {
            'page': page,
            'branch_id': branch_id,
            'agency_id': agency_id,
            'created_date': created_date
        },
        success: function (response) {
            $('#branch-link-list').html(response);
            $('#branch-link-data-loader').hide();
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
    loadBranchLinkList(page);
});

// Refresh/Clear filters
function refreshLinkData() {
    $('#search-form')[0].reset();
    $('#filter_branch_id').val('').trigger('change');
    $('#filter_agency_id').val('').trigger('change');
    loadBranchLinkList(1);
}

// Date range picker for filter
$(function () {
    let start = moment().startOf('day');
    let end = moment().endOf('day');

    $('#link_created_date').daterangepicker({
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
    $('#link_created_date').on('apply.daterangepicker', function (ev, picker) {
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});

function changeMandatoryVal(id){
    $.ajax({
        url: CHANGE_MANDATORY_OPTION,
        type: 'post',
        data: {
            'id' : id,
            '_token': _CSRF_TOKEN,
            'is_val_mandatory' : $('#is_val_mandatory_'+id).is(':checked') == true ? 1 : 0
        },
        success: function (response) {
            if (response.status) {
                toastr.success(response.message);
                loadBranchLinkList(1);
            } else {
                toastr.error(response.message || 'Operation failed');
            }
        },
        error: function (xhr) {
            let response = xhr.responseJSON;
            if (response && response.message) {
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred. Please try again.');
            }
        },
    });
}