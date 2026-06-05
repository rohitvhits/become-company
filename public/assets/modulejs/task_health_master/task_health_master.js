$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadTaskHealthMasterList(1);

    // Sync modal date pickers
    $('.datepickernn-sync').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        locale: { format: 'MM/DD/YYYY' }
    }, function(date) {
        $(this.element).val(date.format('MM/DD/YYYY'));
    });

    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function(chosen_date, end_date) {

        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

function loadTaskHealthMasterList(page) {
    $('.shimmer_id').show();
    $('#resp').html('');

    var created_date      = $('#created_date').val();
    var agency_id         = $('#agency_id').val();
    var type              = $('#type').val();
    var patient_name      = $('#filter_patient_name').val().trim();
    var task_id           = $('#filter_task_id').val().trim();
    var th_patient_id     = $('#filter_th_patient_id').val().trim();
    var mobile            = $('#filter_mobile').val().trim();
    var critical_alert    = $('#filter_critical_alert').val();
    var poc_check         = $('#filter_poc').is(':checked') ? 1 : '';
    var mdo_check         = $('#filter_mdo').is(':checked') ? 1 : '';
    var alert_check       = $('#filter_alert').is(':checked') ? 1 : '';
    var supervision_check = $('#filter_supervision').is(':checked') ? 1 : '';
    var assessment_check          = $('#filter_assessment').is(':checked') ? 1 : '';
    var kardex_check              = $('#filter_kardex').is(':checked') ? 1 : '';
    var patient_package_doc_check = $('#filter_patient_package_doc').is(':checked') ? 1 : '';

    // Highlight filter button when any filter is active
    var anyActive = agency_id || type || created_date || patient_name || task_id || th_patient_id || mobile || critical_alert || poc_check || mdo_check || alert_check || supervision_check || assessment_check || kardex_check || patient_package_doc_check;
    $('#filter-btn .active-filter').text(anyActive ? ' ●' : '');

    $.ajax({
        url:  _TASK_HEALTH_MASTER_LIST + '?page=' + page,
        type: 'GET',
        data: {
            agency_id:         agency_id,
            created_date:      created_date,
            type:              type,
            patient_name:      patient_name,
            task_id:           task_id,
            th_patient_id:     th_patient_id,
            mobile:            mobile,
            critical_alert:    critical_alert,
            poc_check:         poc_check,
            mdo_check:         mdo_check,
            alert_check:       alert_check,
            supervision_check: supervision_check,
            assessment_check:          assessment_check,
            kardex_check:              kardex_check,
            patient_package_doc_check: patient_package_doc_check,
        },
        success: function (response) {
            $('.shimmer_id').hide();
            $('#resp').html(response);
            $('#blank_div').css('margin-top', '30px');
        },
        error: function () {
            $('.shimmer_id').hide();
            $('#resp').html('<div class="alert alert-danger mt-2">Failed to load list. Please try again.</div>');
        }
    });
}

function resetMasterFilters() {
    $('#agency_id').val('');
    $('#type').val('');
    $('#created_date').val('');
    $('#filter_patient_name').val('');
    $('#filter_task_id').val('');
    $('#filter_th_patient_id').val('');
    $('#filter_mobile').val('');
    $('#filter_critical_alert').val('');
    $('#filter_poc, #filter_mdo, #filter_alert, #filter_supervision, #filter_assessment, #filter_kardex, #filter_patient_package_doc').prop('checked', false);
    $('#filter-btn .active-filter').text('');
    loadTaskHealthMasterList(1);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadTaskHealthMasterList(page);
});

function openRevertModal(taskHealthId, agencyId, name, dob, mobile, phone, agencyName, type) {
    // Reset modal state
    $('#revert_task_health_id').val(taskHealthId);
    $('#revert_agency_id').val(agencyId);
    $('#revert_selected_patient_id').val('');
    $('#revert_portal_id').val('');
    $('#revert_patient_name').val('');
    $('#revert_search_error').hide().text('');
    $('#revert_results_wrapper').hide();
    $('#revert_results_body').html('');
    $('#revert_no_results').hide();
    $('#revert_submit_btn').prop('disabled', true);
    $('#revert_submit_error').hide().text('');

    // Show current patient info in header
    $('#rcp_agency').text(agencyName || '-');
    $('#rcp_name').text(name || '-');
    $('#rcp_type').text(type || '-');
    $('#rcp_dob').text(dob || '-');
    $('#rcp_mobile').text(mobile || '-');
    $('#rcp_phone').text(phone || '-');
    $('#revert_current_patient_info').show();

    $('#revertPatientModal').modal('show');
}

function resetRevertSearch() {
    $('#revert_selected_patient_id').val('');
    $('#revert_results_wrapper').hide();
    $('#revert_no_results').hide();
    $('#revert_search_error').hide().text('');
    $('#revert_results_body').html('');
    $('#revert_submit_btn').prop('disabled', true);
}

function searchRevertPatient() {
    var agencyId  = $('#revert_agency_id').val();
    var portalId  = $('#revert_portal_id').val().trim();
    var name      = $('#revert_patient_name').val().trim();

    $('#revert_search_error').hide().text('');
    $('#revert_results_wrapper').hide();
    $('#revert_no_results').hide();

    if (!agencyId) {
        $('#revert_search_error').text('Please select an agency first.').show();
        return;
    }

    if (!portalId && !name) {
        $('#revert_search_error').text('Please enter a Portal ID or Patient Name to search.').show();
        return;
    }

    $('#revert_loader').show();
    $('#revert_selected_patient_id').val('');
    $('#revert_submit_btn').prop('disabled', true);

    $.ajax({
        url: _TASK_HEALTH_REVERT_SEARCH,
        type: 'GET',
        data: {
            agency_id: agencyId,
            portal_id: portalId,
            name: name
        },
        success: function(response) {
            $('#revert_loader').hide();
            $('#revert_results_body').html('');

            if (response && response.length > 0) {
                $.each(response, function(i, patient) {
                    var dob = patient.dob ? patient.dob : '';
                    var row = '<tr class="search-result-row" onclick="selectRevertPatient(' + patient.id + ')">' +
                        '<td>' + patient.id + '</td>' +
                        '<td>' + patient.first_name + ' ' + patient.last_name + '</td>' +
                        '<td>' + (patient.mobile || '') + '</td>' +
                        '<td>' + dob + '</td>' +
                        '<td>' + (patient.status || '') + '</td>' +
                        '<td><button type="button" class="btn btn-sm btn-outline-primary">Select</button></td>' +
                        '</tr>';
                    $('#revert_results_body').append(row);
                });
                $('#revert_results_wrapper').show();
            } else {
                $('#revert_no_results').show();
            }
        },
        error: function() {
            $('#revert_loader').hide();
            $('#revert_search_error').text('Error searching patients. Please try again.').show();
        }
    });
}

function selectRevertPatient(patientId) {
    $('#revert_selected_patient_id').val(patientId);
    $('#revert_submit_error').hide().text('');
    $('#revert_submit_btn').prop('disabled', false);

    // Highlight selected row
    $('#revert_results_body tr').removeClass('selected-patient');
    $('#revert_results_body tr').filter(function() {
        return $(this).find('td:first').text() == patientId;
    }).addClass('selected-patient');
}

function submitRevertPatient() {
    var taskHealthId = $('#revert_task_health_id').val();
    var patientId    = $('#revert_selected_patient_id').val();
    var agencyId     = $('#revert_agency_id').val();

    if (!agencyId) {
        $('#revert_submit_error').text('Please select an agency.').show();
        return;
    }
    if (!patientId) {
        $('#revert_submit_error').text('Please select a patient from the search results first.').show();
        return;
    }
    $('#revert_submit_error').hide().text('');

    $('#revert_submit_btn').prop('disabled', true).text('Saving...');

    $.ajax({
        url: _TASK_HEALTH_REVERT_PATIENT,
        type: 'POST',
        data: {
            _token: _CSRF_TOKEN,
            task_health_id: taskHealthId,
            patient_id: patientId,
            agency_id: agencyId
        },
        success: function(response) {
            if (response.status) {
                $('#revertPatientModal').modal('hide');
                toastr.success(response.message)
                loadTaskHealthMasterList(1);
            } else {
                toastr.error(response.message || 'Something went wrong.');
                $('#revert_submit_btn').prop('disabled', false).html('<span class="text-white">&#9873;</span> Mark as Red Flag &amp; Link');
            }
        },
        error: function(xhr) {
            var msg = 'Error saving. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            toastr.error(response.message || 'Something went wrong.');
            $('#revert_submit_btn').prop('disabled', false).html('<span class="text-white">&#9873;</span> Mark as Red Flag &amp; Link');
        }
    });
}

function exportTaskHealthCsv() {
    var params = new URLSearchParams();
    params.append('agency_id',       $('#agency_id').val()            || '');
    params.append('type',            $('#type').val()                 || '');
    params.append('status',          $('#status').val()               || '');
    params.append('created_date',    $('#created_date').val()         || '');
    params.append('patient_name',    $('#filter_patient_name').val().trim() || '');
    params.append('task_id',         $('#filter_task_id').val().trim()      || '');
    params.append('th_patient_id',   $('#filter_th_patient_id').val().trim() || '');
    params.append('mobile',          $('#filter_mobile').val().trim()       || '');
    params.append('critical_alert',  $('#filter_critical_alert').val()      || '');
    if ($('#filter_poc').is(':checked'))         params.append('poc_check', 1);
    if ($('#filter_mdo').is(':checked'))         params.append('mdo_check', 1);
    if ($('#filter_alert').is(':checked'))       params.append('alert_check', 1);
    if ($('#filter_supervision').is(':checked')) params.append('supervision_check', 1);
    if ($('#filter_assessment').is(':checked'))  params.append('assessment_check', 1);
    if ($('#filter_kardex').is(':checked'))      params.append('kardex_check', 1);
    window.location.href = _TASK_HEALTH_EXPORT_CSV + '?' + params.toString();
}

function openSyncModal() {
    $('#sync-result').hide().html('');
    $('#sync-ca-btn').prop('disabled', false);
    $('#sync-ca-btn-text').text('Run Sync');
    $('#sync_sortBy').val('scheduledDateTime');
    $('#sync_fromDate').val('');
    $('#sync_toDate').val('');
    $('#syncCriticalAlertsModal').modal('show');
}

function runSyncCriticalAlerts() {
    var fromDate = $('#sync_fromDate').val();
    var toDate   = $('#sync_toDate').val();
    var sortBy   = $('#sync_sortBy').val();

    $('#sync-ca-btn').prop('disabled', true);
    $('#sync-ca-btn-text').html('<i class="mdi mdi-loading mdi-spin"></i> Syncing...');
    $('#sync-result').hide().html('');

    $.ajax({
        url:  _TASK_HEALTH_SYNC_CA_URL,
        type: 'POST',
        data: {
            _token:   _CSRF_TOKEN,
            fromDate: fromDate,
            toDate:   toDate,
            sortBy:   sortBy,
        },
        success: function(res) {
            $('#sync-ca-btn').prop('disabled', false);
            $('#sync-ca-btn-text').text('Run Sync');
            var cls = res.status ? 'alert-success' : 'alert-danger';
            $('#sync-result')
                .html('<div class="alert ' + cls + ' mb-0" style="font-size:12px;">' + res.message + '</div>')
                .show();
            if (res.status) {
                loadTaskHealthMasterList(1);
            }
        },
        error: function(xhr) {
            $('#sync-ca-btn').prop('disabled', false);
            $('#sync-ca-btn-text').text('Run Sync');
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Sync failed. Please try again.';
            $('#sync-result')
                .html('<div class="alert alert-danger mb-0" style="font-size:12px;">' + msg + '</div>')
                .show();
        }
    });
}

function openConvertTaskHealthModal(masterId) {
    $('#convert_master_id').val(masterId);
    $('#convert_master_agency_id').val('').trigger('change');
    $('#convert_master_agency_error').text('');
    $('#convertTaskHealthModal').modal('show');
}

function submitConvertTaskHealth() {
    var masterId = $('#convert_master_id').val();
    var agencyId = $('#convert_master_agency_id').val();

    if (!agencyId) {
        $('#convert_master_agency_error').text('Please select an agency.');
        return;
    }
    $('#convert_master_agency_error').text('');

    $('#convertMasterSubmitBtn').prop('disabled', true).text('Processing...');

    $.ajax({
        url: _TASK_HEALTH_CONVERT,
        type: 'POST',
        data: {
            master_id: masterId,
            agency_id: agencyId,
            _token: _CSRF_TOKEN
        },
        success: function (response) {
            $('#convertMasterSubmitBtn').prop('disabled', false).text('Convert');
            if (response.status == 1) {
                $('#convertTaskHealthModal').modal('hide');
                $('button[onclick="openConvertTaskHealthModal(' + masterId + ')"]').remove();
                toastr.success(response.error_msg || "Converted successfully.");
                loadTaskHealthMasterList(1);
            } else {
                toastr.error(response.error_msg || "Something went wrong.");
            }
        },
        error: function (xhr) {
            $('#convertMasterSubmitBtn').prop('disabled', false).text('Convert');
            var msg = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                msg = xhr.responseJSON.error_msg;
            }
            showErrorAndLoginRedirection(xhr);
        }
    });
}

