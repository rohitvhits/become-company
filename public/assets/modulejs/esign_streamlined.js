/**
 * Streamlined E-sign module for Patient type records.
 * Provides one-click form send with auto-notification.
 */

function loadStreamlinedEsign() {
    loadStreamlinedTemplates();
    loadStreamlinedDoctorList();
    esignResponseNew1(); // reuse existing function to load sent docs
}

function loadStreamlinedDoctorList() {
    $.ajax({
        url: _LOAD_DOCTOR_LIST,
        type: "GET",
        data: '',
        success: function (res) {
            var json = res.data;
            $('#streamlined_doctor_id').html('');
            var option = '<option value="">Select Doctor</option>';
            if (json && json.length != 0) {
                $.each(json, function (i, v) {
                    option += '<option value="' + v.id + '">' + v.full_name + '</option>';
                });
            }
            $('#streamlined_doctor_id').html(option);

            // Also populate the modal doctor list for backward compatibility
            $('#doctor_idNew').html(option);
        }
    });
}

function loadStreamlinedTemplates() {
    $('#streamlinedEsignLoader').show();
    $('#streamlined_template_list').html('');

    $.ajax({
        url: _STREAMLINED_FORM_LIST,
        type: "GET",
        data: {
            'patient_id': _RECORD_ID,
            'agency_id': _AGENCYID
        },
        success: function (res) {
            $('#streamlinedEsignLoader').hide();
            var html = '';

            if (res.data && res.data.length > 0) {
                $.each(res.data, function (i, v) {
                    html += '<div class="col-md-4 col-lg-3 mb-3">' +
                        '<div class="card streamlined-card">' +
                        '<div class="card-body">' +
                        '<h6 class="card-title">' + escapeHtml(v.template_name) + '</h6>' +
                        '<div class="streamlined-btn-group d-flex gap-1">' +
                        '<button class="btn btn-success btn-sm flex-fill" onclick="streamlinedSend(' + v.id + ', \'form_complete\')" title="Send form and mark as complete">' +
                        '<i class="fa fa-check"></i> Form Complete</button>' +
                        '<button class="btn btn-primary btn-sm flex-fill" onclick="streamlinedSend(' + v.id + ', \'require_signature\')" title="Send form and request signature">' +
                        '<i class="fa fa-pencil"></i> Require Signature</button>' +
                        '</div></div></div></div>';
                });
            } else {
                html = '<div class="col-12"><p class="text-muted">No templates available for this agency.</p></div>';
            }

            $('#streamlined_template_list').html(html);
        },
        error: function () {
            $('#streamlinedEsignLoader').hide();
            $('#streamlined_template_list').html('<div class="col-12"><p class="text-danger">Failed to load templates.</p></div>');
        }
    });
}

function streamlinedSend(templateId, action) {
    var doctorId = $('#streamlined_doctor_id').val();

    $('#streamlined_doctor_error').html('');

    if (!doctorId) {
        $('#streamlined_doctor_error').html('Please select a doctor first.');
        return;
    }

    var actionLabel = action === 'form_complete' ? 'Form Complete' : 'Require Signature';

    if (!confirm('Are you sure you want to send this form as "' + actionLabel + '"? The signer will be automatically notified.')) {
        return;
    }

    // Disable all streamlined buttons during submission
    $('.streamlined-btn-group .btn').prop('disabled', true);

    $.ajax({
        url: _STREAMLINED_FORM_SEND,
        type: "POST",
        data: {
            '_token': _CSRF_TOKEN,
            'template_id': templateId,
            'patient_id': _RECORD_ID,
            'doctor_id': doctorId,
            'action': action
        },
        success: function (res) {
            $('.streamlined-btn-group .btn').prop('disabled', false);

            if (res.status == 1) {
                toastr.success(res.error_msg || 'Document sent and signer notified successfully.');
                esignResponseNew1(); // refresh sent docs list
            } else {
                toastr.error(res.error_msg || 'Something went wrong.');
            }
        },
        error: function (xhr) {
            $('.streamlined-btn-group .btn').prop('disabled', false);

            var errorMsg = 'Something went wrong. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                errorMsg = xhr.responseJSON.error_msg;
            }
            toastr.error(errorMsg);
        }
    });
}

/**
 * Trigger Form Complete or Require Signature on an existing sent document.
 * Called from the Action dropdown in esign_ajax_list.blade.php.
 */
function streamlinedActionOnDoc(documentId, action) {
    var actionLabel = action === 'form_complete' ? 'Form Complete' : 'Require Signature';
    var url = action === 'form_complete' ? _STREAMLINED_FORM_COMPLETE : _STREAMLINED_REQUIRE_SIGNATURE;

    if (!confirm('Are you sure you want to mark this document as "' + actionLabel + '"? The next signer will be automatically notified.')) {
        return;
    }

    $.ajax({
        url: url,
        type: "POST",
        data: {
            '_token': _CSRF_TOKEN,
            'document_id': documentId
        },
        success: function (res) {
            if (res.success) {
                toastr.success(res.message || 'Action completed successfully.');
                esignResponseNew1(); // refresh sent docs list
            } else {
                toastr.error(res.error_msg || 'Something went wrong.');
            }
        },
        error: function (xhr) {
            var errorMsg = 'Something went wrong. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                errorMsg = xhr.responseJSON.error_msg;
            }
            toastr.error(errorMsg);
        }
    });
}

function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

// Auto-load on page ready if the streamlined section exists
$(document).ready(function () {
    if ($('#streamlined_template_list').length > 0) {
        loadStreamlinedEsign();
    }
});
