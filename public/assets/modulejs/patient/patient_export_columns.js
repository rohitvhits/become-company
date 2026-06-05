/**
 * Patient Export Column Selection
 * Handles the modal for selecting which columns to export
 */

// Global variable to store export data
var exportData = {};

/**
 * Build export URL with selected columns
 */
function buildExportURL(selectedColumns) {
    var authAgencyFk = exportData.authAgencyFk;
    var authId = exportData.authId;
    var user_type_fk = exportData.user_type_fk;

    var links = exportData.baseUrl + "/patients/patient-export?sms_status=" + exportData.sms_status + "&status=" + exportData.status +
        "&agency_fk=" + exportData.agency_fk + "&first_name=" + exportData.first_name + "&mobile=" + exportData.mobile + "&service_id=" +
        exportData.service_id + "&assign_user_id=" + exportData.assign_user_id + "&due_date=" + exportData.due_date +
        "&appointment_date=" + exportData.appointment_date + "&locationId=" + exportData.locationId + "&created_date=" +
        exportData.created_date + "&is_archive=" + exportData.isArchived + "&dicipline=" + exportData.isDiscipline + "&type=" + exportData.type +
        '&patient_code=' + exportData.patient_code + '&inservice_date=' + exportData.inservice_date + "&is_past_show=" + exportData.isPastShow +
        '&completed_date=' + exportData.completed_date + '&follow_up_date=' + exportData.follow_up_date + '&transition_aid=' + exportData.transition_aid +
        '&language_id=' + exportData.language_id + '&dob=' + exportData.dob;

    if (authAgencyFk == 106 || authId == 482) {
        links = links + "&traning_date=" + exportData.traning_date;
    }
    if (authAgencyFk != "") {
        var id = $('#created_by').val();
        if (id != "") {
            links = links + "&created_by=" + id;
        }
    } else {
        var id = $('#created_by_ny_id').val();
        links = links + "&created_by=" + id;
    }
    if (user_type_fk == 184) {
        links = links + "&traning_status=" + exportData.traning_status;
    }
    if (user_type_fk == 6 || user_type_fk == 184) {
        links = links + '&last_status_update=' + exportData.last_status_update +
            '&last_status_updated_by_id=' + exportData.last_status_updated_by_id;
    }
    links = links + "&agency_filter_type=" + exportData.agency_filter_type;
    links = links + "&service_filter_type=" + exportData.service_filter_type;
    links = links + "&insurance_elg=" + exportData.insurance_elg;
    links = links + "&medication_list=" + exportData.medication_list;
    links = links + "&mdo_tag=" + exportData.mdo_tag;
    links = links + "&filter_branch_id=" + exportData.filter_branch_id;
    links = links + "&branch_filter_type=" + exportData.branch_filter_type;
    links = links + "&state=" + exportData.state;
    links = links + "&agency_status=" + exportData.agency_status;
    links = links + "&referral_type=" + exportData.referral_type;
    links = links + "&record_read=" + exportData.record_read;

    if (exportData.debug != "") {
        links = links + "&debug=" + exportData.debug;
    }

    // Add selected columns parameter
    if (selectedColumns && selectedColumns.length > 0) {
        links = links + "&columns=" + encodeURIComponent(JSON.stringify(selectedColumns));
    }

    return links;
}

/**
 * Populate export columns modal with checkboxes
 */
function populateExportColumns() {
    var authAgencyFk = exportData.authAgencyFk;
    var user_type_fk = exportData.user_type_fk;

    // Define columns based on agency (matching the controller logic)
    var columns = [];
    if (authAgencyFk == 106) {
        columns = ['No', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob',
            'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', 'Booked Via',
            'Assign NyBest User', 'Created Date', 'Created By', 'Due Date', 'FU Date', 'Is Archive',
            'Training Status', 'Completed date', 'Follow Up Date', 'Traning Due Date', 'Location / Branch', 'Reason',
            'Language', 'Clinician Code'];
    } else {
        columns = ['No', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob',
            'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', 'Booked Via',
            'Assign NyBest User', 'Created Date', 'Created By', 'Due Date', 'FU Date', 'Is Archive',
            'Completed date', 'Follow Up Date', 'Location / Branch', 'Reason', 'state',
            'Language', 'Clinician Code'];

        if (user_type_fk == 184) {
            columns.push('Training Date', 'Training Status', 'Last Status Updated', 'Last Status Updated By', 'Referral Type');
        }
    }

    // Clear and populate the columns list
    var columnsList = $('#exportColumnsList');
    columnsList.empty();

    columns.forEach(function (column, index) {
        var colHtml = '<div class="col-md-3 column-toggle-item">' +
            '<input type="checkbox" class="column-toggle-input" id="col_' + index + '" value="' + column + '" checked>' +
            '<label class="column-toggle-label" for="col_' + index + '">' +
            '<span>' + column + '</span>' +
            '<div class="toggle-switch"></div>' +
            '</label>' +
            '</div>';
        columnsList.append(colHtml);
    });

    updateSelectedCount();
}

/**
 * Update selected column count display
 */
function updateSelectedCount() {
    var count = $('.column-toggle-input:checked').length;
    $('#selectedCount').text(count);
}

/**
 * Initialize export column modal handlers
 */
$(document).ready(function () {
    // Select All button
    $(document).on('click', '#selectAllColumns', function () {
        $('.column-toggle-input').prop('checked', true);
        updateSelectedCount();
    });

    // Deselect All button
    $(document).on('click', '#deselectAllColumns', function () {
        $('.column-toggle-input').prop('checked', false);
        updateSelectedCount();
    });

    // Update count when checkboxes change
    $(document).on('change', '.column-toggle-input', function () {
        updateSelectedCount();
    });

    // Confirm Export button
    $(document).on('click', '#confirmExport', function () {
        var selectedColumns = [];
        $('.column-toggle-input:checked').each(function () {
            selectedColumns.push($(this).val());
        });

        if (selectedColumns.length === 0) {
            alert('Please select at least one column to export');
            return false;
        }

        // Build URL with selected columns and trigger download
        var exportURL = buildExportURL(selectedColumns);
        window.location.href = exportURL;

        // Close the modal
        if (typeof $.fn.modal === 'function') {
            $('#exportColumnModal').modal('hide');
        } else {
            $('#exportColumnModal').removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }
    });

    // Close modal on backdrop click or close button
    $(document).on('click', '#exportColumnModal .close, #exportColumnModal', function (e) {
        if (e.target === this) {
            if (typeof $.fn.modal === 'function') {
                $('#exportColumnModal').modal('hide');
            } else {
                $('#exportColumnModal').removeClass('show').css('display', 'none');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }
        }
    });

    // Handle Cancel button and any button with data-dismiss="modal"
    $(document).on('click', '#exportColumnModal [data-dismiss="modal"]', function (e) {
        e.preventDefault();
        if (typeof $.fn.modal === 'function') {
            $('#exportColumnModal').modal('hide');
        } else {
            $('#exportColumnModal').removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }
    });

    // Prevent modal body clicks from closing the modal
    $(document).on('click', '#exportColumnModal .modal-dialog', function (e) {
        e.stopPropagation();
    });
});