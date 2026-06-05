// HHA Medical Documents JavaScript
var currentPage = 1;

$(document).ready(function() {
    // Load initial data
    hhaMedicalList(1);

    // Pagination click handler
    $(document).on('click', '.hha_appointment_paginate .pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        hhaMedicalList(page);
    });
});

/**
 * Load HHA Medical List via AJAX
 */
function hhaMedicalList(page = 1) {
    currentPage = page;
    var formData = getFormData();
    formData.page = page;

    $.ajax({
        url: _LIST,
        method: 'POST',
        data: formData,
        beforeSend: function() {
            $('.shimmer_id').show();
            $('#resp').html('');
        },
        success: function(response) {
            $('.shimmer_id').hide();
            $('#resp').html(response);
        },
        error: function(xhr) {
            $('.shimmer_id').hide();
            toastr.error('Failed to load data');
        }
    });
}

/**
 * Get form data for filtering
 */
function getFormData() {
    return {
        _token: $('meta[name="csrf-token"]').attr('content'),
        agency_fk: $('#agency_fk').val() || '',
        office_fk: $('#office_fk').val() || '',
        medical_name: $('#medical_name').val() || '',
        status: $('#status').val() || ''
    };
}

/**
 * Refresh/Reset filters
 */
function refresh() {
    $('#search-form')[0].reset();
    $('#agency_fk').val('').trigger('change');
    $('#office_fk').val('').trigger('change');
    hhaMedicalList(1);
}

/**
 * Export HHA Medical List to CSV
 */
function hhaMedicalExport() {
    var formData = getFormData();

    // Build query string
    var queryString = $.param(formData);

    // Show loader
    $('#exportText').addClass('d-none');
    $('#exportLoader').removeClass('d-none');

    // Create temporary link and trigger download
    window.location.href = _EXPORT_CSV+'?' + queryString;

    // Hide loader after delay
    setTimeout(function() {
        $('#exportText').removeClass('d-none');
        $('#exportLoader').addClass('d-none');
        toastr.success('CSV exported successfully');
    }, 2000);
}


function updateMedicalStatus(checkbox, medicalId, newStatus, label) {
    
}

$(document).on('change', '.status-toggle', function(e) {
    e.preventDefault();

    var checkbox = $(this);
    var medicalId = checkbox.data('id');
    var currentStatus = checkbox.data('current-status');
    var newStatus = checkbox.is(':checked') ? 1 : 0;
    var label = checkbox.next('label');
    var statusText = newStatus == 1 ? 'activate' : 'deactivate';
    var medicalName = checkbox.data('medical-name');

    // Show confirmation dialog
    $.confirm({
        title: 'Confirm Status Change',
        content: 'Are you sure you want to ' + statusText + ' the medical record <strong>"' + medicalName + '"</strong>?',
        type: 'orange',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes, ' + statusText.charAt(0).toUpperCase() + statusText.slice(1),
                btnClass: 'btn-primary',
                action: function() {
                    // Proceed with status update
                    $.ajax({
                        url: _HHA_MEDICAL_TOOGLE,
                        method: 'POST',
                        data: {
                            _token:_CSRF_TOKEN,
                            id: medicalId,
                            status: newStatus
                        },
                        beforeSend: function() {
                            checkbox.prop('disabled', true);
                        },
                        success: function(response) {
                            if(response.success) {
                                // Update label text
                                label.text(newStatus == 1 ? 'Active' : 'Inactive');
                
                                // Update status badge in the same row
                                var statusCell = checkbox.closest('tr').find('td:eq(5)');
                                if(newStatus == 1) {
                                    statusCell.html('<span class="badge badge-success">Active</span>');
                                } else {
                                    statusCell.html('<span class="badge badge-secondary">Inactive</span>');
                                }
                
                                // Update current status data attribute
                                checkbox.data('current-status', newStatus);
                
                                // Show success message
                                toastr.success(response.message || 'Status updated successfully');
                            } else {
                                // Revert checkbox if failed
                                checkbox.prop('checked', !checkbox.is(':checked'));
                                toastr.error(response.message || 'Failed to update status');
                            }
                            checkbox.prop('disabled', false);
                        },
                        error: function(xhr) {
                            // Revert checkbox on error
                            checkbox.prop('checked', !checkbox.is(':checked'));
                            checkbox.prop('disabled', false);
                
                            var errorMessage = 'An error occurred while updating status';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                   
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-secondary',
                action: function() {
                    // Revert checkbox to previous state
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            }
        }
    });
});