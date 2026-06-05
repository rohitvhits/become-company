// Load deleted patients on page load
loadDeletedPatients(1);

// Function to load deleted patients list via AJAX
function loadDeletedPatients(page){
    $('#ajax_response_id').html("");
    $('.hideClass').removeClass('d-none');

    $.ajax({
        type: "GET",
        url: _DELETED_PATIENT_AJAX,
        data: {
            'patient_id': $('#patient_id').val(),
            'agency_fk': $('#agency_fk').val(),
            'page': page
        },
        success: function(res){
            $('.hideClass').addClass('d-none');
            $('#ajax_response_id').html(res);
        },
        error: function(xhr, status, error) {
            $('.hideClass').addClass('d-none');
            toastr.error('Error loading deleted patients');
        }
    });
}

// Filter button toggle
$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

// Refresh/Clear filters function
function refresh(){
    $('#patient_id').val('');
    $('#agency_fk').val('').change();
    loadDeletedPatients(1);
}

// Pagination handler
$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadDeletedPatients(page);
});

// Reactivate patient function
function reactivatePatient(patientId) {
    $.confirm({
        title: 'Reactivate Patient',
        content: 'Are you sure you want to reactivate this patient?',
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            confirm: {
                text: 'Reactivate',
                btnClass: 'btn-success',
                action: function() {
                    $.ajax({
                        type: "POST",
                        url: _DELETED_PATIENT_REACTIVATE,
                        data: {
                            'patient_id': patientId,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(res) {
                            if (res.status) {
                                toastr.success(res.error_msg);
                                loadDeletedPatients(1);
                            } else {
                                toastr.error(res.error_msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMsg = 'Error reactivating patient';
                            if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                                errorMsg = xhr.responseJSON.error_msg;
                            }
                            toastr.error(errorMsg);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function() {
                    // Close dialog
                }
            }
        }
    });
}

// Enter key handler for patient ID search
$('#patient_id').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        loadDeletedPatients(1);
    }
});
