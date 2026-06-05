$(document).ready(function () {
    populateUserDropdowns();
    populateFilterNameDropdown();
    initFilterSelect2();
    loadAjaxList();

    $('#saveDocApproval').click(function () {
        saveRecord();
    });

    $('#updateDocApproval').click(function () {
        updateRecord();
    });

    $('#filter-btn').on('click', function () {
        $('#search-filter-btn').slideToggle();
    });

    // Initialize Select2 when modals are shown
    $('#createDocApprovalModal, #editDocApprovalModal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
});

function populateUserDropdowns() {
    var options = '<option value="">-- Select User --</option>';
    $.each(_USER_LIST, function (_, u) {
        options += '<option value="' + u.id + '">' + u.name + '</option>';
    });
    $('#create_user_id, #edit_user_id').html(options);
}

function populateFilterNameDropdown() {
    var options = '<option value="">-- All --</option>';
    $.each(_USER_LIST, function (_, u) {
        options += '<option value="' + u.id + '">' + u.name + '</option>';
    });
    $('#search_name').html(options);
}

function initFilterSelect2() {
    $('#search_name, #search_key').select2({
        placeholder: '-- All --',
        allowClear: true
    });
}

function loadAjaxList(page = 1) {
    var userId = $('#search_name').val();
    var key    = $('#search_key').val();
    $('.shimmer_id').removeClass('hide');
    $('#response_requested_id').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');

    $.ajax({
        type: 'GET',
        url: _AJAX_LIST,
        data: {
            'page': page,
            'user_id': userId,
            'key': key
        },
        success: function (data) {
            $('.shimmer_id').addClass('hide');
            $('#response_requested_id').html(data);
            $('.location-wise-data-loader').attr('style', 'display:none');
        },
        error: function (jqXHR) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');
            showErrorAndLoginRedirection(jqXHR);
        }
    });
}

function refresh() {
    $('#search_name').val('').trigger('change');
    $('#search_key').val('').trigger('change');
    loadAjaxList(1);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

function openCreateModal() {
    $('#docApprovalCreateForm')[0].reset();
    $('#create_user_id_error, #create_key_error').text('');
    populateUserDropdowns();
    $('#create_user_id').val("").trigger('change');
    $('input[name="key"]').prop('checked', false);
    $('#create_key_mdo_label, #create_key_all_label').removeClass('active');
    $('#createDocApprovalModal').modal('show');
}

function saveRecord() {
    $('#create_user_id_error, #create_key_error').text('');

    var userId = $('#create_user_id').val();
    var key = $('input[name="key"]:checked').val();

    if (!userId) {
        $('#create_user_id_error').text('Please select a user.');
        return;
    }
    if (!key) {
        $('#create_key_error').text('Please select a key type.');
        return;
    }

    $('#btn-save-text').text('Saving...');
    $('#saveDocApproval').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: _STORE_URL,
        data: {
            _token: _CSRF_TOKEN,
            user_id: userId,
            key: key
        },
        success: function (response) {
            $('#btn-save-text').text('Save');
            $('#saveDocApproval').prop('disabled', false);
            if (response.status) {
                toastr.success(response.error_msg);
                $('#createDocApprovalModal').modal('hide');
                loadAjaxList();
            } else {
                toastr.error(response.error_msg);
            }
        },
        error: function (jqXHR) {
            $('#btn-save-text').text('Save');
            $('#saveDocApproval').prop('disabled', false);
            showErrorAndLoginRedirection(jqXHR);
        }
    });
}

function editRecord(id) {
    $('#edit_user_id_error, #edit_key_error').text('');

    $.ajax({
        type: 'GET',
        url: _SHOW_URL + '/' + id,
        success: function (response) {
            if (response.status) {
                var data = response.data;
                $('#edit_record_id').val(data.id);
                populateUserDropdowns();
                $('#edit_user_id').val(data.user_id).trigger('change');
                var $editKeyInput = $('input[name="edit_key"][value="' + data.key + '"]');
                $editKeyInput.prop('checked', true).closest('label').addClass('active').siblings('label').removeClass('active');
                $('#btn-update-text').text('Update');
                $('#updateDocApproval').prop('disabled', false);
                $('#editDocApprovalModal').modal('show');
            } else {
                toastr.error(response.error_msg);
            }
        },
        error: function (jqXHR) {
            showErrorAndLoginRedirection(jqXHR);
        }
    });
}

function updateRecord() {
    $('#edit_user_id_error, #edit_key_error').text('');

    var id = $('#edit_record_id').val();
    var userId = $('#edit_user_id').val();
    var key = $('input[name="edit_key"]:checked').val();

    if (!userId) {
        $('#edit_user_id_error').text('Please select a user.');
        return;
    }
    if (!key) {
        $('#edit_key_error').text('Please select a key type.');
        return;
    }

    $('#btn-update-text').text('Updating...');
    $('#updateDocApproval').prop('disabled', true);

    $.ajax({
        type: 'PUT',
        url: _UPDATE_URL + '/' + id,
        data: {
            _token: _CSRF_TOKEN,
            user_id: userId,
            key: key
        },
        success: function (response) {
            $('#btn-update-text').text('Update');
            $('#updateDocApproval').prop('disabled', false);
            if (response.status) {
                toastr.success(response.error_msg);
                $('#editDocApprovalModal').modal('hide');
                loadAjaxList();
            } else {
                toastr.error(response.error_msg);
            }
        },
        error: function (jqXHR) {
            $('#btn-update-text').text('Update');
            $('#updateDocApproval').prop('disabled', false);
            showErrorAndLoginRedirection(jqXHR);
        }
    });
}

function deleteRecord(id) {
    $.confirm({
        title: "Are you sure?",
        content: "you want to delete this record.",
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: _DELETE_URL + '/' + id,
                        type: 'POST',
                        data: {
                            _token: _CSRF_TOKEN,
                            _method: 'DELETE'
                        },
                        success: function (res) {
                            if (res.status) {
                                toastr.success(res.error_msg);
                                loadAjaxList();
                            } else {
                                toastr.error(res.error_msg);
                            }
                        },
                        error: function (jqXHR) {
                            showErrorAndLoginRedirection(jqXHR);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
            }
        }
    });
}
