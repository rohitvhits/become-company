var _cvPhoneCount = 0;

$(function () {
    // Init status & reviewStatus as searchable multi-select2
    $('#thStatus').select2({ placeholder: 'All', allowClear: true, width: '100%' });
    $('#reviewStatus').select2({ placeholder: 'All', allowClear: true, width: '100%' });

    loadAgencies();

    var today    = moment().format('MM/DD/YYYY');
    var firstDay = moment().subtract(3, 'months').startOf('month').format('MM/DD/YYYY');
    $('#fromDate').val(firstDay);
    $('#toDate').val(today);

    $('.datepicker-single').datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });

    // Filter toggle
    $('#filter-btn').on('click', function () {
        $('#search-filter-btn').toggle();
    });

    // Open Create Visit modal
    $('#btn-create-visit').on('click', function () {
        $('#createVisitForm')[0].reset();
        $('#cv_agencyId').val('').trigger('change');
        $('#cv_socDateRow').hide();
        $('#cv_payerSourceSelect').val('');
        $('#cv_payerSourceOther').hide().val('');
        $('#cv_payerSourceValue').val('');
        $('#cv_serviceTypeSelect').val('');
        $('#cv_serviceTypeOther').hide().val('');
        $('#cv_serviceTypeValue').val('');
        $('#cv_speaksEnglishRow').hide();
        $('#cv_speaksEnglish').val('');
        $('#cv_extra_phones').empty();
        _cvPhoneCount = 0;
        cvSwitchTab('patient');
        cvClearAll();
        $('#createVisitAlert').hide().removeClass('alert-success alert-danger').html('');
        $('#btn-submit-visit-text').show();
        $('#btn-submit-visit-spinner').hide();
        $('#btn-submit-visit').prop('disabled', false);
        // Reset schedule required indicators
        $('#cv_assessmentDueDateReq, #cv_scheduleFreeTextReq').hide();
        // Init datepickers in modal
        $('.cv-datepicker').inputmask();
        $('#createVisitModal').modal('show');
    });

    // Schedule: toggle required indicators when Assessment Start is filled/cleared
    $(document).on('input change', '#cv_assessmentStartDate', function () {
        var filled = $(this).val().trim() !== '';
        $('#cv_assessmentDueDateReq, #cv_scheduleFreeTextReq').toggle(filled);
        if (!filled) {
            $('#err_assessmentDueDate, #err_scheduleFreeText').text('');
        }
    });

    // Language — show "Patient speaks English?" when non-English selected
    $(document).on('change', '#cv_language', function () {
        var val = $(this).val();
        if (val && val !== 'English') {
            $('#cv_speaksEnglishRow').show();
        } else {
            $('#cv_speaksEnglishRow').hide();
            $('#cv_speaksEnglish').val('');
        }
    });

    // Service Type — dropdown + free-text "Other"
    $(document).on('change', '#cv_serviceTypeSelect', function () {
        var val = $(this).val();
        if (val === '__other__') {
            $('#cv_serviceTypeOther').show().focus();
            $('#cv_serviceTypeValue').val('');
        } else {
            $('#cv_serviceTypeOther').hide().val('');
            $('#cv_serviceTypeValue').val(val);
        }
    });
    $(document).on('input', '#cv_serviceTypeOther', function () {
        $('#cv_serviceTypeValue').val($(this).val().trim());
    });

    // Payer Source — dropdown + free-text "Other"
    $(document).on('change', '#cv_payerSourceSelect', function () {
        var val = $(this).val();
        if (val === '__other__') {
            $('#cv_payerSourceOther').show().focus();
            $('#cv_payerSourceValue').val('');
        } else {
            $('#cv_payerSourceOther').hide().val('');
            $('#cv_payerSourceValue').val(val);
        }
    });
    $(document).on('input', '#cv_payerSourceOther', function () {
        $('#cv_payerSourceValue').val($(this).val().trim());
    });

    // Live-clear field errors on input/change
    $(document).on('input change', '#cv_agencyId, #cv_taskType, #cv_startOfCareDate, #cv_firstName, #cv_lastName, #cv_dob, #cv_gender, #cv_language, #cv_phone, #cv_address', function () {
        var key = $(this).attr('id').replace('cv_', '');
        $(this).removeClass('cv-invalid');
        $('#err_' + key).text('').removeClass('show');
        // Remove tab error dot if no more errors remain in that tab
        var tab = $(this).closest('.cv-tab-panel').attr('id');
        if (tab) {
            var tabId = tab.replace('cv-panel-', '');
            if (!$('#cv-panel-' + tabId + ' .cv-invalid').length) {
                $('[data-cv-tab="' + tabId + '"]').removeClass('has-error');
            }
        }
    });

    // Show/hide Start of Care Date when task type = REASSESSMENT
    $('#cv_taskType').on('change', function () {
        if ($(this).val() === 'REASSESSMENT') {
            $('#cv_socDateRow').show();
        } else {
            $('#cv_socDateRow').hide();
        }
    });

    // Submit Create Visit form
    $('#createVisitForm').on('submit', function (e) {
        e.preventDefault();

        if (!cvValidate()) return;

        $('#btn-submit-visit-text').hide();
        $('#btn-submit-visit-spinner').show();
        $('#btn-submit-visit').prop('disabled', true);
        $('#createVisitAlert').hide();

        $.ajax({
            url: _TH_VISIT_CREATE_URL,
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $('#btn-submit-visit-text').show();
                $('#btn-submit-visit-spinner').hide();
                $('#btn-submit-visit').prop('disabled', false);

                if (res.status) {
                    var taskId = (res.data && res.data.taskId) ? res.data.taskId : '';
                    var msg = res.message || 'Visit created successfully.';
                    if (taskId) { msg += ' Task ID: <strong>' + taskId + '</strong>'; }
                    $('#createVisitAlert')
                        .removeClass('alert-danger').addClass('alert-success')
                        .html(msg).show();
                    // Reload list after short delay
                    setTimeout(function () {
                        $('#createVisitModal').modal('hide');
                        loadVisitList(1);
                    }, 2000);
                } else {
                    $('#createVisitAlert')
                        .removeClass('alert-success').addClass('alert-danger')
                        .html(res.message || 'Failed to create visit.').show();
                }
            },
            error: function () {
                $('#btn-submit-visit-text').show();
                $('#btn-submit-visit-spinner').hide();
                $('#btn-submit-visit').prop('disabled', false);
                $('#createVisitAlert')
                    .removeClass('alert-success').addClass('alert-danger')
                    .html('An unexpected error occurred. Please try again.').show();
            }
        });
    });

    if (typeof _TH_VISIT_LIST_URL !== 'undefined' && _TH_VISIT_LIST_URL) {
        loadVisitList(1);
    }

    // Boost createVisitModal, editVisitModal, openForChangesModal above the
    // visit-detail drawer overlay (z-index:1055), then reset on close.
    $('#createVisitModal, #editVisitModal, #openForChangesModal').on('show.bs.modal', function () {
        $(this).css('z-index', 1080);
        setTimeout(function () {
            $('.modal-backdrop').last().css('z-index', 1070);
        }, 0);
    }).on('hidden.bs.modal', function () {
        $(this).css('z-index', '');
        $('.modal-backdrop').last().css('z-index', '');
    });
});

/* ═══════════════════════════════════════════════════════════════
   Create Visit — Client-side Validation
   ═══════════════════════════════════════════════════════════════ */

function cvClearAll() {
    var fields = ['agencyId','taskType','startOfCareDate','firstName','lastName','dob','gender','language','phone','address'];
    fields.forEach(function (f) {
        $('#cv_' + f).removeClass('cv-invalid');
        $('#err_' + f).text('').removeClass('show');
    });
    $('.cv-tab-btn').removeClass('has-error');
}

function cvSwitchTab(tabId, btnEl) {
    if (!btnEl) btnEl = document.querySelector('[data-cv-tab="' + tabId + '"]');
    $('.cv-tab-btn').removeClass('active');
    $('.cv-tab-panel').removeClass('active');
    if (btnEl) $(btnEl).addClass('active');
    $('#cv-panel-' + tabId).addClass('active');
}

function cvValidate() {
    cvClearAll();
    var valid = true;
    var firstErrEl = null;
    var firstErrTab = null;

    function fail(key, msg, tab) {
        $('#cv_' + key).addClass('cv-invalid');
        $('#err_' + key).text(msg).addClass('show');
        if (!firstErrEl) {
            firstErrEl = document.getElementById('err_' + key);
            firstErrTab = tab || null;
        }
        if (tab) $('[data-cv-tab="' + tab + '"]').addClass('has-error');
        valid = false;
    }

    // Agency bar (always visible — no tab)
    if (!$('#cv_agencyId').val())         fail('agencyId',       'Agency is required.');

    // Tab 1 — Patient
    if (!$('#cv_firstName').val().trim())  fail('firstName',      'First Name is required.',    'patient');
    if (!$('#cv_lastName').val().trim())   fail('lastName',       'Last Name is required.',     'patient');
    if (!$('#cv_dob').val().trim())        fail('dob',            'Date of Birth is required.', 'patient');
    if (!$('#cv_gender').val())            fail('gender',         'Gender is required.',        'patient');
    if (!$('#cv_language').val())          fail('language',       'Language is required.',      'patient');
    if (!$('#cv_phone').val().trim())      fail('phone',          'Phone is required.',         'patient');

    // Tab 2 — Address
    if (!$('#cv_address').val().trim())    fail('address',        'Address is required.',       'address');

    // Tab 3 — Visit
    if (!$('#cv_taskType').val())          fail('taskType',       'Visit Type is required.',    'visit');
    if ($('#cv_taskType').val() === 'REASSESSMENT' && !$('#cv_startOfCareDate').val())
                                           fail('startOfCareDate','Start of Care Date is required for Reassessment.', 'visit');

    // Schedule: if Assessment Start is filled, Due Date and Schedule Note are required
    if ($('#cv_assessmentStartDate').val().trim()) {
        if (!$('#cv_assessmentDueDate').val().trim())
            fail('assessmentDueDate',  'Assessment Due Date is required when Assessment Start is set.', 'visit');
        if (!$('#cv_scheduleFreeText').val().trim())
            fail('scheduleFreeText',   'Schedule Note is required when Assessment Start is set.',       'visit');
    }

    if (!valid) {
        if (firstErrTab) cvSwitchTab(firstErrTab);
        setTimeout(function () {
            if (firstErrEl) {
                var wrapper = document.getElementById('cv-tab-content-wrapper');
                if (wrapper) {
                    var offset = firstErrEl.getBoundingClientRect().top - wrapper.getBoundingClientRect().top + wrapper.scrollTop - 20;
                    wrapper.scrollTo({ top: offset, behavior: 'smooth' });
                }
            }
        }, 60);
    }

    return valid;
}

function cvAddPhone() {
    _cvPhoneCount++;
    var n = _cvPhoneCount;
    var html =
        '<div class="cv-phone-entry d-flex align-items-center mb-1" id="cv_extra_phone_' + n + '">' +
            '<span class="badge badge-secondary mr-1" style="font-size:10px;min-width:50px;text-align:center;padding:3px 5px;">Other</span>' +
            '<input type="text" class="form-control form-control-sm" name="patient_phone_extra[]" placeholder="+17185550400">' +
            '<button type="button" class="btn btn-link text-danger ml-1 p-0" onclick="cvRemovePhone(' + n + ')" title="Remove" style="line-height:1;">' +
                '<i class="mdi mdi-close-circle-outline" style="font-size:16px;"></i>' +
            '</button>' +
        '</div>';
    $('#cv_extra_phones').append(html);
}

function cvRemovePhone(n) {
    $('#cv_extra_phone_' + n).remove();
}

function loadAgencies() {
    $.ajax({
        url: _TH_AGENCIES_URL,
        type: 'GET',
        success: function (res) {
            if (res.status && res.data && res.data.length) {
                $.each(res.data, function (i, agency) {
                    var opt = $('<option>', { value: agency.taskHealthAgencyId, text: agency.agencyName });
                    $('#agencyIds').append(opt.clone());
                    $('#cv_agencyId').append(opt);
                });
            }
            $('#agencyIds').select2({ placeholder: 'Select Agency', allowClear: true, width: '100%' });
        }
    });
}

// Join multi-select values as comma-separated string
function getMultiVal(selector) {
    var vals = $(selector).val();
    return (vals && vals.length) ? vals.join(',') : '';
}

function resetFilters() {
    $('#agencyIds').val(null).trigger('change');
    $('#thStatus').val(null).trigger('change');
    $('#reviewStatus').val(null).trigger('change');
    $('#fromDate').val(moment().subtract(3, 'months').startOf('month').format('MM/DD/YYYY'));
    $('#toDate').val(moment().format('MM/DD/YYYY'));
    $('#sortBy').val('createdAt');
    $('#thSearch').val('');
    $('#hasCriticalAlert').prop('checked', false);
    $('#vl_filter_poc, #vl_filter_mdo, #vl_filter_alert, #vl_filter_supervision, #vl_filter_assessment, #vl_filter_kardex, #vl_filter_patient_package_doc').prop('checked', false);
    loadVisitList(1);
}

/* ═══════════════════════════════════════════════════════════════
   Visit Detail Drawer — Right-side slide-in
   ═══════════════════════════════════════════════════════════════ */

var _currentDrawerTaskId   = null;
var _masterPanelLoaded     = false;   // tracks whether master panel has been fetched for current task
var _masterCheckResult     = null;    // caches the last check result
var _currentThTaskId       = null;    // TH task ID of currently open drawer
var _currentThPatientId    = null;    // TH patient ID of currently open drawer
var _currentVisitPatient   = null;    // raw patient object from visit API for current drawer
var _currentCaregiver      = null;    // raw caregiver object from visit API for current drawer

function openVisitModal(taskId) {
    if (!taskId) return;
    _currentDrawerTaskId  = taskId;
    _currentEditTaskId    = taskId;
    _masterPanelLoaded    = false;
    _masterCheckResult    = null;
    _currentThTaskId      = null;
    _currentThPatientId   = null;
    _currentVisitPatient  = null;
    _currentCaregiver     = null;
    _vdFlagAssessment     = 0;
    _vdFlagKardex         = 0;
    _vdFlagSupervision    = 0;
    _vdFlagPoc            = 0;
    _vdFlagMdo            = 0;
    _vdFlagPatientPackage = 0;
    var _thBadgeReset = document.getElementById('vd-th-patient-badge');
    if (_thBadgeReset) { _thBadgeReset.style.display = 'none'; _thBadgeReset.textContent = ''; }

    // Reset shimmer
    var shimmer = _vShimmer();
    document.getElementById('vt-general-content').innerHTML   = shimmer;
    document.getElementById('vt-documents-content').innerHTML = shimmer;

    // Reset header placeholders
    document.getElementById('vd-avatar-initials').textContent  = '…';
    document.getElementById('vModalPatientName').textContent    = 'Loading...';
    document.getElementById('vModalTaskId').textContent         = '#' + taskId;
    document.getElementById('vd-status-badge').style.display   = 'none';
    document.getElementById('vd-type-badge').style.display     = 'none';

    // Switch to General tab
    $('#vd-cancel-btn').hide();
    $('#vd-flag-btn').hide();
    switchVisitTab('general', document.querySelector('.vd-tab[data-tab="general"]'));
    // Show drawer
    document.getElementById('visitDetailModal').classList.add('show');
    document.body.style.overflow = 'hidden';

    // Click backdrop to close
    document.getElementById('visitDetailModal').onclick = function (e) {
        if (e.target === this) closeVisitModal();
    };

    // Fetch data
    $.ajax({
        url: _TH_VISIT_DETAIL_JSON_URL + '/' + taskId,
        type: 'GET',
        success: function (res) {
            if (res.status && res.data) {
                _vRender(res.data,res.ag_setting);
            } else {
                document.getElementById('vt-general-content').innerHTML =
                    '<div class="alert alert-danger m-3">' + (res.message || 'Failed to load visit detail.') + '</div>';
            }
        },
        error: function () {
            document.getElementById('vt-general-content').innerHTML =
                '<div class="alert alert-danger m-3">An error occurred while loading visit detail.</div>';
        }
    });
}

function closeVisitModal() {
    document.getElementById('visitDetailModal').classList.remove('show');
    document.body.style.overflow = '';
    _masterPanelLoaded  = false;
    _masterCheckResult  = null;
    _currentThTaskId     = null;
    _currentThPatientId  = null;
    _currentVisitPatient = null;
    var _thBadgeClose = document.getElementById('vd-th-patient-badge');
    if (_thBadgeClose) { _thBadgeClose.style.display = 'none'; _thBadgeClose.textContent = ''; }
    // Reset tab badge
    var badge = document.getElementById('vd-pr-tab-badge');
    if (badge) badge.style.display = 'none';
    // Reset panel content
    var panelContent = document.getElementById('vt-patientrecord-content');
    if (panelContent) panelContent.innerHTML = '<div style="text-align:center;color:#9ca3af;padding:60px 20px;"><i class="mdi mdi-link-variant" style="font-size:32px;"></i><p style="margin-top:8px;font-size:13px;">Loading patient record status\u2026</p></div>';
    // Ensure General tab is active on next open
    switchVisitTab('general', document.querySelector('.vd-tab[data-tab="general"]'));
}

function switchVisitTab(tab, btn) {
    document.querySelectorAll('.vd-panel').forEach(function (el) { el.classList.remove('active'); });
    document.querySelectorAll('.vd-tab').forEach(function (el) { el.classList.remove('active'); });
    var panel = document.getElementById('vt-' + tab);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
}

function deleteVisitFromDrawer() {
    if (_currentDrawerTaskId) deleteVisit(_currentDrawerTaskId);
}

/* ── Helpers ── */
function _vDash(v) { return (v !== null && v !== undefined && v !== '') ? v : '—'; }
function _vEsc(v) {
    if (!v) return '';
    return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function _vBool(v) { return v ? '<span style="color:#28a745;font-weight:600;">Yes</span>' : '<span style="color:#dc3545;font-weight:600;">No</span>'; }
function _vDate(v) {
    if (!v) return '—';

    let str = String(v).trim();

    // ✅ If already in MM/DD/YYYY → return as it is
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(str)) {
        return str;
    }

    // ✅ ISO format (YYYY-MM-DD or YYYY-MM-DDTHH:mm:ss)
    if (str.includes('T') || str.includes('-')) {
        let datePart = str.split('T')[0];
        let parts = datePart.split('-');

        if (parts.length === 3) {
            let [y, m, d] = parts;
            return `${m}/${d}/${y}`;
        }
    }

    return str; // fallback
}

function _vDT(v) {
    if (!v) return '—';

    var [datePart, timePart] = v.replace('Z', '').split('T');

    var d = datePart.split('-'); // YYYY-MM-DD
    var t = timePart.split(':'); // HH:mm:ss

    return d[1] + '/' + d[2] + '/' + d[0] + ' ' + t[0] + ':' + t[1];
}
function _vCell(label, value) {
    return '<div class="vd-cell"><div class="vd-cell-label">' + label + '</div><div class="vd-cell-value">' + (value || '—') + '</div></div>';
}
function _vShimmer() {
    return '<div class="shimmer-wrapper">' +
        '<div class="shimmer shimmer-header"></div>' +
        '<div class="row">' +
            '<div class="col-md-6"><div class="shimmer-card"><div class="shimmer shimmer-line title"></div><div class="shimmer shimmer-line long"></div><div class="shimmer shimmer-line medium"></div><div class="shimmer shimmer-line short"></div></div></div>' +
            '<div class="col-md-6"><div class="shimmer-card"><div class="shimmer shimmer-line title"></div><div class="shimmer shimmer-line medium"></div><div class="shimmer shimmer-line long"></div><div class="shimmer shimmer-line short"></div></div></div>' +
        '</div></div>';
}

/* ── Populate header badges ── */
function _vRender(data,ag_setting) {
    var task             = (data.task      && typeof data.task      === 'object') ? data.task      : data;
    var patient          = (data.patient   && typeof data.patient   === 'object') ? data.patient   : {};
    var caregiver        = (data.caregiver && typeof data.caregiver === 'object') ? data.caregiver : {};
    var sched            = (data.lastScheduleDetails && typeof data.lastScheduleDetails === 'object') ? data.lastScheduleDetails : {};
    var taskDocs         = Array.isArray(task.documents)            ? task.documents            : [];
    var upDocs           = Array.isArray(patient.uploadedDocuments) ? patient.uploadedDocuments : [];
    var planOfCareItems  = Array.isArray(data.planOfCareItems)      ? data.planOfCareItems      : [];
    var supervisoryForm  = (data.supervisoryForm && typeof data.supervisoryForm === 'object') ? data.supervisoryForm : null;

    var firstName = patient.firstName || '';
    var lastName  = patient.lastName  || '';
    var patName   = (firstName + ' ' + lastName).trim() || '—';
    var initials  = ((firstName[0] || '') + (lastName[0] || '')).toUpperCase() || '?';
    var taskId    = task.id || task.taskId || '—';
    var status    = task.status || '';
    var taskType  = task.type   || task.taskType || '';

    // Store TH IDs and raw patient for confirm modal / ID strip
    _currentThTaskId    = task.id || task.taskId || null;
    _currentThPatientId = patient.id || null;
    _currentVisitPatient = patient;
    _currentCaregiver    = caregiver;

    // Header
    document.getElementById('vd-avatar-initials').textContent = initials;
    document.getElementById('vModalPatientName').textContent  = patName;
    document.getElementById('vModalTaskId').textContent       = '#' + taskId;

    // Show TH patient ID badge in header
    var thBadge = document.getElementById('vd-th-patient-badge');
    if (thBadge) {
        if (_currentThPatientId) {
            thBadge.textContent   = 'TH Patient #' + _currentThPatientId;
            thBadge.style.display = '';
        } else {
            thBadge.style.display = 'none';
        }
    }

    // Status badge
    var statusEl = document.getElementById('vd-status-badge');
    if (status) {
        var sl = status.toLowerCase();
        statusEl.className = 'vd-badge-status ' +
            (sl.includes('complet') ? 'success' : sl.includes('cancel') ? 'danger' : sl.includes('progress') ? 'info' : 'warning');
        statusEl.textContent    = status;
        statusEl.style.display  = '';

        if(!sl.includes('cancel') && !sl.includes('complet')){
            $('#vd-cancel-btn').show();
        }
    }

    // Type badge
    var typeEl = document.getElementById('vd-type-badge');
    if (taskType) {
        typeEl.textContent   = taskType.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        typeEl.style.display = '';
    }

    var tid = task.id || task.taskId || null;
    var criticalAlert = (data.criticalAlert !== undefined) ? data.criticalAlert : undefined;
    document.getElementById('vt-general-content').innerHTML   = _vRenderGeneral(task, patient, caregiver, sched, taskDocs, tid, planOfCareItems, supervisoryForm, criticalAlert,ag_setting);
    document.getElementById('vt-documents-content').innerHTML = _vRenderDocs(taskDocs, upDocs, tid,task.agencyId,ag_setting);

    // Auto-check patient master record (populates banner in General tab)
    if (tid) { _vCheckMasterRecord(tid); }

    // ── Flag Action button ───────────────────────────────────────────────────
    var $flagBtn = $('#vd-flag-btn');
    var thPatId  = patient.id || '';
    var emptyInfo = { name: patName, poc: 0, poc_by: '—', poc_date: '—', mdo: 0, mdo_by: '—', mdo_date: '—', alert: 0, alert_by: '—', alert_date: '—', supervision: 0, supervision_by: '—', supervision_date: '—',assessment: 0,assessment_by:'—',assessment_date:'—',kardex: 0,kardex_by: '—',kardex_date:'—',patient_package_doc:0,patient_package_doc_by:'—',patient_package_doc_date:'—',upd_by: '—', upd_at: '—' };
    $flagBtn
        .data('task-id',       tid      || '')
        .data('th-patient-id', thPatId  || '')
        .data('name',          patName  || '')
        .data('poc', 0).data('mdo', 0).data('alert', 0).data('supervision', 0).data('kardex',0).data('assessment',0).data('patient_package_doc',0)
        .data('info',  emptyInfo)
        .hide(); // shown only after master record check confirms HHA link

    // Async-load current flag state from DB
    if (tid && typeof _TH_FLAGS_BY_TASK_URL !== 'undefined') {
        $.get(_TH_FLAGS_BY_TASK_URL, { task_id: tid }, function (res) {
            if (!res.status || !res.data) return;
            var d = res.data;
            $flagBtn
                .data('poc',                 d.poc)
                .data('mdo',                 d.mdo)
                .data('alert',               d.alert)
                .data('supervision',         d.supervision)
                .data('assessment',          d.assessment)
                .data('kardex',              d.kardex)
                .data('patient_package_doc', d.patient_package_doc)
                .data('info', { name: patName, poc: d.poc, poc_by: d.poc_by, poc_date: d.poc_date, mdo: d.mdo, mdo_by: d.mdo_by, mdo_date: d.mdo_date, alert: d.alert, alert_by: d.alert_by, alert_date: d.alert_date, supervision: d.supervision, supervision_by: d.supervision_by, supervision_date: d.supervision_date, assessment: d.assessment,assessment_by:d.assessment_by,assessment_date:d.assessment_date,kardex: d.kardex,kardex_by: d.kardex_by,kardex_date:d.kardex_date,patient_package_doc:d.patient_package_doc,patient_package_doc_by:d.patient_package_doc_by,patient_package_doc_date:d.patient_package_doc_date,upd_by: d.upd_by, upd_at: d.upd_at });
            // Hide supervision button if supervision is already completed
            if (d.supervision == 1) {
                $('#btn-supervision-' + tid).hide();
            }
            // Cache flag state so Documents tab can use it even if rendered after this callback
            _vdFlagAssessment     = d.assessment          ? 1 : 0;
            _vdFlagKardex         = d.kardex              ? 1 : 0;
            _vdFlagSupervision    = d.supervision         ? 1 : 0;
            _vdFlagPoc            = d.poc                 ? 1 : 0;
            _vdFlagMdo            = d.mdo                 ? 1 : 0;
            _vdFlagPatientPackage = d.patient_package_doc ? 1 : 0;
            // If the Documents tab or General tab is already rendered, apply immediately
            if (_vdFlagAssessment)     { _vdMarkUploaded('vd-upload-hha-80752'); _vdMarkUploaded('vd-gen-upload-hha-80752'); }
            if (_vdFlagKardex)         { _vdMarkUploaded('vd-upload-hha-81049'); _vdMarkUploaded('vd-gen-upload-hha-81049'); }
            if (_vdFlagSupervision)    { _vdMarkUploaded('vd-upload-hha-80950'); _vdMarkUploaded('vd-gen-upload-hha-80950'); }
            if (_vdFlagPoc)            { _vdMarkUploaded('vd-upload-hha-80983'); _vdMarkUploaded('vd-gen-upload-hha-80983'); }
            if (_vdFlagMdo)            { _vdMarkUploaded('vd-upload-hha-81082'); _vdMarkUploaded('vd-gen-upload-hha-81082'); }
            if (_vdFlagPatientPackage) { _vdMarkUploaded('vd-upload-hha-81016'); _vdMarkUploaded('vd-gen-upload-hha-81016'); }
        });
    }
}

function _vdMarkUploaded(btnId) {
    var $el = $('#' + btnId);
    if ($el.length) {
        $el.html('<span style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1b6b3a;border:1px solid #4caf7d;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:600;"><i class="mdi mdi-check-circle"></i> Uploaded</span>');
    }
}

function vdOpenHhaUploadModal(taskId, docTypeId, docTitle) {
    if (typeof _TH_HHA_PREVIEW_BY_TASK === 'undefined') {
        alert('HHA upload URL not configured on this page.');
        return;
    }
    _vdHhaPendingTaskId    = taskId;
    _vdHhaPendingDocTypeId = docTypeId;
    _vdHhaPendingPatientId = null;
    _vdHhaPendingDocTitle  = docTitle || '';

    $('#vd-hha-confirm-doc-title').text(docTitle);
    $('#vd-hha-patient-loader').show();
    $('#vd-hha-patient-error').hide().text('');
    $('#vd-hha-patient-info').hide();
    $('#vd-hha-confirm-upload-btn').prop('disabled', true);

    $('#vdHhaUploadModal').modal('show').on('shown.bs.modal', function () {
        // Ensure this modal and its backdrop sit above the vd-overlay (z-index 1055)
        $(this).css('z-index', 1100);
        $('.modal-backdrop').not('.modal-backdrop ~ .modal-backdrop').last().css('z-index', 1090);
    });

    $.ajax({
        url: _TH_HHA_PREVIEW_BY_TASK + '/' + taskId + '/hha-patient-preview',
        type: 'GET',
        success: function (res) {
            $('#vd-hha-patient-loader').hide();
            if (!res.status) {
                $('#vd-hha-patient-error').show().text(res.message || 'HHA patient not found');
                return;
            }
            var p = res.hha_patient;
            _vdHhaPendingPatientId = p.patient_id;
            var addr = [p.address1, p.city, p.state, p.zip].filter(Boolean).join(', ');
            $('#vd-hha-confirm-pid').text('#' + (p.patient_id || '—'));
            $('#vd-hha-confirm-name').text(((p.first_name || '') + ' ' + (p.last_name || '')).trim() || '—');
            $('#vd-hha-confirm-dob').text(p.dob || '—');
            $('#vd-hha-confirm-address').text(addr || '—');
            $('#vd-hha-patient-info').show();
            $('#vd-hha-confirm-upload-btn').prop('disabled', false);
        },
        error: function () {
            $('#vd-hha-patient-loader').hide();
            $('#vd-hha-patient-error').show().text('Error searching HHA patient. Please try again.');
        }
    });
}

function vdConfirmHhaUpload() {
    if (!_vdHhaPendingTaskId || !_vdHhaPendingDocTypeId || !_vdHhaPendingPatientId) return;
    if (typeof _TH_UPLOAD_DOC_BY_TASK === 'undefined') return;

    var $btn = $('#vd-hha-confirm-upload-btn');
    $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Uploading…');

    $.ajax({
        url: _TH_UPLOAD_DOC_BY_TASK + '/' + _vdHhaPendingTaskId + '/upload-doc-to-hha',
        type: 'POST',
        data: {
            _token:         _CSRF_TOKEN,
            doc_type_id:    _vdHhaPendingDocTypeId,
            hha_patient_id: _vdHhaPendingPatientId,
            doc_title:      _vdHhaPendingDocTitle || '',
        },
        success: function (res) {
            $('#vdHhaUploadModal').modal('hide');
            if (res.status) {
                if (typeof toastr !== 'undefined') toastr.success(res.message || 'Document uploaded successfully');
                // Mark the button as uploaded in both Documents tab and General tab
                _vdMarkUploaded('vd-upload-hha-'     + _vdHhaPendingDocTypeId);
                _vdMarkUploaded('vd-gen-upload-hha-' + _vdHhaPendingDocTypeId);
            } else {
                if (typeof toastr !== 'undefined') toastr.error(res.message || 'Upload failed');
            }
        },
        error: function () {
            if (typeof toastr !== 'undefined') toastr.error('Server error during upload. Please try again.');
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="mdi mdi-upload"></i> Send to HHA');
        }
    });
}

/* ── General tab: all sections ── */
function _vRenderGeneral(task, patient, caregiver, sched, taskDocs, taskId, planOfCareItems, supervisoryForm, criticalAlert,ag_setting) {
    var phones = Array.isArray(patient.phoneNumbers)   ? patient.phoneNumbers   : [];
    var langs  = Array.isArray(patient.languages)      ? patient.languages.filter(Boolean) : [];
    var diags  = Array.isArray(patient.diagnosisCodes) ? patient.diagnosisCodes  : [];
    var cert   = (task.certificationPeriod && typeof task.certificationPeriod === 'object') ? task.certificationPeriod : {};
    var html   = '';
    var agency_id = task.agencyId;

    // ── Patient Record link status banner (async, permission-gated) ──
    if (typeof _TH_CAN_LINK_MASTER === 'undefined' || _TH_CAN_LINK_MASTER) {
    // ── Section 0: ID Summary Strip ──
    var _thPid = (patient && patient.id) ? patient.id : null;
    html += '<div class="vd-id-strip">';
    html += '<div class="vd-id-pill">';
    html += '<div class="vd-id-pill-label"><i class="mdi mdi-identifier"></i> TH Task ID</div>';
    html += '<div class="vd-id-pill-value">' + (taskId ? '#' + taskId : '—') + '</div>';
    html += '</div>';
    html += '<div class="vd-id-pill">';
    html += '<div class="vd-id-pill-label"><i class="mdi mdi-account-outline"></i> TH Patient ID</div>';
    html += '<div class="vd-id-pill-value">' + (_thPid ? '#' + _thPid : '—') + '</div>';
    html += '</div>';
    html += '<div class="vd-id-pill vd-id-loading" id="vd-erp-patient-id-pill">';
    html += '<div class="vd-id-pill-label"><i class="mdi mdi-hospital-building"></i> Portal</div>';
    html += '<div class="vd-id-pill-value" id="vd-erp-patient-id-value"><i class="mdi mdi-loading mdi-spin" style="font-size:12px;"></i></div>';
    html += '</div>';
    html += '</div>';

        html += '<div class="vd-pr-banner vd-pr-banner-loading" id="vd-master-record-banner">';
        html += '<div class="vd-pr-banner-header"><i class="mdi mdi-link-variant"></i>&nbsp; Patient Record Link Status</div>';
        html += '<div class="vd-pr-banner-body">';
        html += '<div class="vd-pr-info"><span class="vd-pr-icon" style="color:#dee2e6;"><i class="mdi mdi-loading mdi-spin"></i></span>';
        html += '<div class="vd-pr-text-block"><div class="vd-pr-status-label" style="color:#9ca3af;">Checking record…</div></div></div>';
        html += '</div></div>';
    }

    // ── Section 1: Patient Information ──
    html += '<div class="vd-section">';
    html += '<div class="vd-section-title">Patient Information</div>';
    html += '<div class="vd-grid-3">';
    html += _vCell('First Name',    _vDash(patient.firstName));
    html += _vCell('Middle Name',   _vDash(patient.middleName));
    html += _vCell('Last Name',     _vDash(patient.lastName));
    html += _vCell('Date of Birth', _vDate(patient.dateOfBirth));
    html += _vCell('Gender',        _vDash(patient.gender));
    html += _vCell('Start of Care', _vDate(patient.startOfCareDate));
    html += _vCell('Main Language', langs.length ? [...new Set(langs)].join(', ') : '—');
    html += '</div></div>';

    // ── Section 2: Patient Address ──
    html += '<div class="vd-section">';
    html += '<div class="vd-section-title">Patient Address</div>';
    html += '<div class="vd-grid-3">';
    html += _vCell('Address',               _vDash(patient.address));
    html += _vCell('Address 2',             _vDash(patient.address2));
    html += _vCell('Address Instructions',  _vDash(patient.addressInstructions));
    html += '</div></div>';

    // ── Section 3: Contact Information ──
    var patFullName  = ((patient.firstName || '') + ' ' + (patient.lastName || '')).trim() || '—';
    var primaryPhone = phones.find(function (p) { return p.isPrimary; });
    var phoneDisplay = primaryPhone ? primaryPhone.number : (phones.length ? phones[0].number : '—');
    var certPeriod   = (_vDate(cert.startDate) !== '—')
        ? _vDate(cert.startDate) + ' – ' + _vDate(cert.endDate)
        : '—';
    html += '<div class="vd-section">';
    html += '<div class="vd-section-title">Contact Information</div>';
    html += '<div class="vd-patient-contact">';
    html += '<span class="vd-contact-name"><i class="mdi mdi-account-circle-outline"></i> ' + patFullName + ' <small style="color:#9ca3af;">(The Patient)</small></span>';
    html += '<span class="vd-contact-phone"><i class="mdi mdi-phone-outline"></i> ' + phoneDisplay + '</span>';
    html += '</div>';
    html += '<div class="vd-grid-3 mt-1">';
    if (phones.length) {
        phones.forEach(function (ph) {
            html += _vCell(ph.isPrimary ? '★ Primary Phone' : 'Phone', _vDash(ph.number));
        });
    } else {
        html += _vCell('Phone', '—');
    }
    if (caregiver && caregiver.name) {
        html += _vCell('Caregiver Name',  _vDash(caregiver.name));
        html += _vCell('Caregiver Phone', _vDash(caregiver.phoneNumber));
    }
    html += '</div></div>';

    // ── Section 4: Patient Diagnosis Codes ──
    if (diags.length) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Patient Diagnosis Codes</div>';
        html += '<div style="overflow-x:auto;"><table class="vd-table">';
        html += '<thead><tr><th>Code</th><th>Type</th><th>From</th><th>To</th></tr></thead><tbody>';
        diags.forEach(function (d) {
            var icd = (d.icd && typeof d.icd === 'object') ? d.icd : {};
            html += '<tr>';
            html += '<td><strong>' + _vDash(icd.formattedDxCode) + '</strong><br><small style="color:#9ca3af;">' + _vDash(icd.description) + '</small></td>';
            html += '<td>' + _vDash(d.type) + '</td>';
            html += '<td>' + _vDate(d.startDate) + '</td>';
            html += '<td>' + _vDate(d.endDate) + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div></div>';
    }

    // ── Section 5: Patient Documents ──
    if (taskDocs.length) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Patient Documents</div>';
        html += '<div style="overflow-x:auto;"><table class="vd-table">';
        html += '<thead><tr><th>#</th><th>Document</th><th>Status</th><th>Submitted</th><th>View</th><th>Action</th></tr></thead><tbody>';
        taskDocs.forEach(function (doc, i) {
            var dtype  = (doc.type && typeof doc.type === 'object') ? doc.type : {};
            var docId  = doc.id || doc.scheduledDocId || null;
            var title  = dtype.title || ('Document ' + (i + 1));
            var sc     = (doc.status || '').toLowerCase();
            var bgc    = sc === 'completed' ? '#28a745' : sc === 'rejected' ? '#dc3545' : '#e0a800';
            var viewBtn = doc.url
                ? '<a href="' + doc.url + '" target="_blank" class="vd-btn-sm vd-btn-info"><i class="mdi mdi-file-pdf"></i> View</a>'
                : '—';
            var acts = '';
            if (taskId && docId) {
                var st = title.replace(/'/g, "\\'");
                if (typeof canDocApprove !== 'undefined' && canDocApprove) {
                    acts += '<button class="vd-btn-sm vd-btn-success" onclick="approveDocument(' + taskId + ',' + docId + ',\'' + st + '\')"><i class="mdi mdi-check"></i> Approve</button>';
                }
                if (typeof canDocChange !== 'undefined' && canDocChange) {
                    acts += '<button class="vd-btn-sm vd-btn-warning" onclick="openOpenForChangesModal(' + taskId + ',' + docId + ',\'' + st + '\')"><i class="mdi mdi-undo-variant"></i> Changes</button>';
                }
            }
            var typeId = (dtype && dtype.id) ? parseInt(dtype.id, 10) : null;
            if (doc.url && taskId && _vdShouldShowHhaUpload(typeId, title, ag_setting)) {
                var safetitleGen = title.replace(/'/g, "\\'");
                var genBtnId     = 'vd-gen-upload-hha-' + typeId;
                var alreadyDoneGen = (typeId === 80752 && _vdFlagAssessment)
                                  || (typeId === 81049 && _vdFlagKardex)
                                  || (typeId === 80950 && _vdFlagSupervision)
                                  || (typeId === 80983 && _vdFlagPoc)
                                  || (typeId === 81082 && _vdFlagMdo)
                                  || (typeId === 81016 && _vdFlagPatientPackage);
                if (alreadyDoneGen) {
                    acts += '<span id="' + genBtnId + '" style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1b6b3a;border:1px solid #4caf7d;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:600;"><i class="mdi mdi-check-circle"></i> Uploaded to HHA</span>';
                } else {
                    acts += '<span id="' + genBtnId + '">'
                         + '<button class="vd-btn-sm" style="background:#1a73e8;color:#fff;" '
                         + 'onclick="vdOpenHhaUploadModal(' + taskId + ',' + typeId + ',\'' + safetitleGen + '\')">'
                         + '<i class="mdi mdi-upload"></i> Upload to HHA</button>'
                         + '</span>';
                }
            }
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td><strong>' + _vDash(dtype.title) + '</strong></td>';
            html += '<td><span style="background:' + bgc + ';color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">' + _vDash(doc.status) + '</span></td>';
            html += '<td>' + _vDT(doc.submittedAt) + '</td>';
            html += '<td>' + viewBtn + '</td>';
            html += '<td style="white-space:nowrap;">' + (acts || '—') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div></div>';
    }

    // ── Section 6: Visit Information ──
    html += '<div class="vd-section">';
    html += '<div class="vd-section-title">Visit Information</div>';
    html += '<div class="vd-grid-3">';
    html += _vCell('Service Type',            _vDash(task.serviceType));
    html += _vCell('Certification Period',    certPeriod);
    html += _vCell('Payer Source',            _vDash(task.payerSource));
    html += _vCell('Frequency',               _vDash(task.frequency));
    html += _vCell('Additional Instructions', _vDash(task.agencyNote));
    html += '</div>';
    html += '</div>';

    // ── Section 7: Plan of Care Items ──
    if (planOfCareItems && planOfCareItems.length) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Plan of Care Items</div>';
        html += '<div style="overflow-x:auto;"><table class="vd-table">';
        html += '<thead><tr><th>Code</th><th>Task</th><th>Frequency</th><th>Notes</th></tr></thead><tbody>';
        planOfCareItems.forEach(function (item) {
            html += '<tr>';
            html += '<td>' + _vDash(item.code) + '</td>';
            html += '<td><strong>' + _vDash(item.name) + '</strong></td>';
            html += '<td>' + _vDash(item.frequency) + '</td>';
            html += '<td>' + _vDash(item.notes) + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        html += '</div>';
    }

    // ── Section 8: Supervisory Form ──
    if (supervisoryForm) {
        var sf = supervisoryForm;
        var pocTasks     = Array.isArray(sf.pocTasks)     ? sf.pocTasks     : [];
        var competencies = Array.isArray(sf.competencies) ? sf.competencies : [];

        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Supervisory Form</div>';

        html += '<div class="vd-grid-3">';
        html += _vCell('Aide Present', sf.isAidePresent === true ? 'Yes' : sf.isAidePresent === false ? 'No' : '—');
        html += _vCell('Visit Date',   _vDate(sf.visitDate));
        html += _vCell('Observed Tasks', _vDash(sf.observedTasks));
        html += '</div>';

        if (pocTasks.length) {
            html += '<div class="vd-section-subtitle mt-2 mb-1" style="font-weight:600;color:#374151;">POC Tasks</div>';
            html += '<div style="overflow-x:auto;"><table class="vd-table">';
            html += '<thead><tr><th>Task</th><th>Rating</th></tr></thead><tbody>';
            pocTasks.forEach(function (t) {
                var ratingLabel = _vSfRating(t.rating);
                html += '<tr>';
                html += '<td>' + _vDash(t.name) + '</td>';
                html += '<td>' + ratingLabel + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
        }

        if (competencies.length) {
            html += '<div class="vd-section-subtitle mt-2 mb-1" style="font-weight:600;color:#374151;">Competencies</div>';
            html += '<div style="overflow-x:auto;"><table class="vd-table">';
            html += '<thead><tr><th>Competency</th><th>Rating</th></tr></thead><tbody>';
            competencies.forEach(function (c) {
                var ratingLabel = _vSfRating(c.rating);
                html += '<tr>';
                html += '<td>' + _vDash(c.name) + '</td>';
                html += '<td>' + ratingLabel + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
        }

        if (sf.supervisorComments) {
            html += '<div class="vd-cell mt-2" style="grid-column:1/-1;">';
            html += '<div class="vd-label">Supervisor Comments</div>';
            html += '<div class="vd-value" style="white-space:pre-wrap;">' + _vEsc(sf.supervisorComments) + '</div>';
            html += '</div>';
        }

        html += '</div>';
    }

    // ── Section 9: Critical Alert ──
    if (criticalAlert !== undefined) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Critical Alert</div>';
        if (criticalAlert === null) {
            html += '<div style="display:flex;align-items:center;gap:8px;padding:10px 0;">'
                 +  '<span style="background:#777;color:#fff;padding:3px 10px;border-radius:.25em;font-size:12px;font-weight:700;">Not Analyzed</span>'
                 +  '<span style="font-size:12px;color:#6c757d;">Assessment not yet analyzed.</span>'
                 +  '</div>';
        } else if (!criticalAlert.alert) {
            html += '<div style="display:flex;align-items:center;gap:8px;padding:10px 0;">'
                 +  '<span style="background:#5cb85c;color:#fff;padding:3px 10px;border-radius:.25em;font-size:12px;font-weight:700;">Clear</span>'
                 +  '<span style="font-size:12px;color:#6c757d;">No critical findings detected.</span>'
                 +  '</div>';
        } else {
            var caFindings = Array.isArray(criticalAlert.findings) ? criticalAlert.findings : [];
            var caSummary  = criticalAlert.summary || '';
            html += '<div style="padding:10px 0;">';
            html += '<span style="background:#d9534f;color:#fff;padding:3px 10px;border-radius:.25em;font-size:12px;font-weight:700;">&#9888; Critical</span>';
            if (caSummary) {
                html += '<p style="margin:10px 0 6px;font-size:13px;color:#1f2937;">' + _vEsc(caSummary) + '</p>';
            }
            if (caFindings.length) {
                html += '<ul style="margin:6px 0 0;padding-left:18px;">';
                caFindings.forEach(function (f) {
                    html += '<li style="font-size:12.5px;color:#495057;margin-bottom:3px;">' + _vEsc(f) + '</li>';
                });
                html += '</ul>';
            }
            html += '</div>';
        }
        html += '</div>';
    }

    return html;
}

function _vSfRating(rating) {
    if (!rating) return '—';
    var r = rating.toLowerCase();
    if (r === 'meets')           return '<span style="background:#28a745;color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">Meets</span>';
    if (r === 'needsimprovement') return '<span style="background:#e0a800;color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">Needs Improvement</span>';
    if (r === 'na')              return '<span style="background:#6c757d;color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">N/A</span>';
    return '<span style="background:#6c757d;color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">' + _vEsc(rating) + '</span>';
}

/* ── HHA Upload target doc type IDs ── */
var _VD_TYPE_TO_SETTING_MAP = {
    80752: 'upload_hha_assessment',
    81049: 'upload_hha_kardex',
    81082: 'upload_hha_cms_mdo_485',
    80950: 'upload_hha_supervision',
    80983: 'upload_hha_supervision', // adjust if different
    81016: 'upload_hha_patient_package_doc'
};

// Cached flag state for current open visit (populated after flags API returns)
var _vdFlagAssessment     = 0;
var _vdFlagKardex         = 0;
var _vdFlagSupervision    = 0;
var _vdFlagPoc            = 0;
var _vdFlagMdo            = 0;
var _vdFlagPatientPackage = 0;

// State for pending upload
var _vdHhaPendingTaskId     = null;
var _vdHhaPendingDocTypeId  = null;
var _vdHhaPendingPatientId  = null;
var _vdHhaPendingDocTitle   = null;

function _vdShouldShowHhaUpload(typeId, title, ag_setting) {
    if (!ag_setting || ag_setting.upload_document_cron != 1) {
        return false;
    }
    var t = (title || '').toLowerCase();
    var key = null;
    if (t.includes('poc')) {
        key = 'upload_hha_poc';
    } else if (t.includes('supervis')) {
        key = 'upload_hha_supervision';
    } else if (t.includes('assessment')) {
        key = 'upload_hha_assessment';
    } else if (t.includes('kardex')) {
        key = 'upload_hha_kardex';
    } else if (t.includes('package')) {
        key = 'upload_hha_patient_package_doc';
    }
    if (key && ag_setting.hasOwnProperty(key)) {
        return ag_setting[key] == 1;
    }
    return false;
}

/* ── Documents tab ── */
function _vRenderDocs(taskDocs, upDocs, taskId,agency_id,ag_setting) {
    var html = '';

    if (!taskDocs.length && !upDocs.length) {
        return '<div class="vd-section"><div class="text-center text-muted py-5"><i class="mdi mdi-file-document-outline" style="font-size:40px;display:block;margin-bottom:8px;"></i>No documents available.</div></div>';
    }

    if (taskDocs.length) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Task Documents (' + taskDocs.length + ')</div>';
        html += '<div style="overflow-x:auto;"><table class="vd-table">';
        html += '<thead><tr><th>#</th><th>Title</th><th>Status</th><th>Submitted</th><th>View</th><th>Action</th></tr></thead><tbody>';
        taskDocs.forEach(function (doc, i) {
            var dtype  = (doc.type && typeof doc.type === 'object') ? doc.type : {};
            var docId  = doc.id || doc.scheduledDocId || null;
            var typeId = dtype.id || null;
            var title  = dtype.title || ('Document ' + (i + 1));
            var sc     = (doc.status || '').toLowerCase();
            var bgc    = sc === 'completed' ? '#28a745' : sc === 'rejected' ? '#dc3545' : '#e0a800';
            var viewBtn = doc.url
                ? '<a href="' + doc.url + '" target="_blank" class="vd-btn-sm vd-btn-info"><i class="mdi mdi-file-pdf"></i> View</a>'
                : '—';
            var acts = '';
            if (taskId && docId) {
                var st = title.replace(/'/g, "\\'");
                if (typeof canDocApprove !== 'undefined' && canDocApprove) {
                    acts += '<button class="vd-btn-sm vd-btn-success" onclick="approveDocument(' + taskId + ',' + docId + ',\'' + st + '\')"><i class="mdi mdi-check"></i> Approve</button>';
                }
                if (typeof canDocChange !== 'undefined' && canDocChange) {
                    acts += '<button class="vd-btn-sm vd-btn-warning" onclick="openOpenForChangesModal(' + taskId + ',' + docId + ',\'' + st + '\')"><i class="mdi mdi-undo-variant"></i> Changes</button>';
                }
            }
            // Upload to HHA button for target doc types
            if (doc.url && taskId && _vdShouldShowHhaUpload(typeId, title,ag_setting)) {
                var safetitle = title.replace(/'/g, "\\'");
                var btnId = 'vd-upload-hha-' + typeId;
                var alreadyDone = (typeId === 80752 && _vdFlagAssessment)
                               || (typeId === 81049 && _vdFlagKardex)
                               || (typeId === 80950 && _vdFlagSupervision)
                               || (typeId === 80983 && _vdFlagPoc)
                               || (typeId === 81082 && _vdFlagMdo)
                               || (typeId === 81016 && _vdFlagPatientPackage);
                if (alreadyDone) {
                    acts += '<span id="' + btnId + '" style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1b6b3a;border:1px solid #4caf7d;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:600;"><i class="mdi mdi-check-circle"></i> Uploaded to hha</span>';
                } else {
                    acts += '<span id="' + btnId + '">'
                         + '<button class="vd-btn-sm" style="background:#1a73e8;color:#fff;" '
                         + 'onclick="vdOpenHhaUploadModal(' + taskId + ',' + typeId + ',\'' + safetitle + '\')">'
                         + '<i class="mdi mdi-upload"></i> Upload to HHA</button>'
                         + '</span>';
                }
            }
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td><strong>' + _vDash(dtype.title) + '</strong></td>';
            html += '<td><span style="background:' + bgc + ';color:#fff;padding:2px 9px;border-radius:10px;font-size:11px;">' + _vDash(doc.status) + '</span></td>';
            html += '<td>' + _vDT(doc.submittedAt) + '</td>';
            html += '<td>' + viewBtn + '</td>';
            html += '<td style="white-space:nowrap;">' + (acts || '—') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div></div>';
    }

    if (upDocs.length) {
        html += '<div class="vd-section">';
        html += '<div class="vd-section-title">Uploaded Documents (' + upDocs.length + ')</div>';
        html += '<div style="overflow-x:auto;"><table class="vd-table">';
        html += '<thead><tr><th>#</th><th>Name</th><th>View</th></tr></thead><tbody>';
        upDocs.forEach(function (doc, i) {
            var nm  = typeof doc === 'object' ? (doc.name || '—') : doc;
            var url = typeof doc === 'object' ? (doc.url  || '')  : '';
            html += '<tr><td>' + (i + 1) + '</td><td>' + nm + '</td><td>' +
                (url ? '<a href="' + url + '" target="_blank" class="vd-btn-sm vd-btn-info"><i class="mdi mdi-file-pdf"></i> View</a>' : '—') +
                '</td></tr>';
        });
        html += '</tbody></table></div></div>';
    }

    return html;
}

/* ═══════════════════════════════════════════════════════════════
   Edit Visit Modal
   ═══════════════════════════════════════════════════════════════ */

var _currentEditTaskId = null;

function openEditModal(taskId) {
    _currentEditTaskId = taskId;
    document.getElementById('editVisitTaskId').value = taskId;
    document.getElementById('editModalTaskIdLabel').textContent = '#' + taskId;
    document.getElementById('editInstruction').value = '';
    document.getElementById('editCharCount').textContent = '0 / 2000';
    document.getElementById('editVisitResponse').style.display = 'none';
    document.getElementById('editVisitResponse').innerHTML = '';
    document.getElementById('btn-edit-text').style.display   = '';
    document.getElementById('btn-edit-spinner').style.display = 'none';
    document.getElementById('btn-submit-edit').disabled = false;
    $('#editVisitModal').modal('show');
}

function openEditModalFromOverlay() {
    if (!_currentEditTaskId) return;
    $('#editVisitModal').modal('show');
    document.getElementById('editVisitTaskId').value = _currentEditTaskId;
    document.getElementById('editModalTaskIdLabel').textContent = '#' + _currentEditTaskId;
    document.getElementById('editInstruction').value = '';
    document.getElementById('editCharCount').textContent = '0 / 2000';
    document.getElementById('editVisitResponse').style.display = 'none';
    document.getElementById('editVisitResponse').innerHTML = '';
    document.getElementById('btn-edit-text').style.display   = '';
    document.getElementById('btn-edit-spinner').style.display = 'none';
    document.getElementById('btn-submit-edit').disabled = false;
}

$(document).on('input', '#editInstruction', function () {
    var len = $(this).val().length;
    $('#editCharCount').text(len + ' / 2000');
    $('#editCharCount').css('color', len > 1800 ? '#dc3545' : '#6c757d');
});

$(document).on('submit', '#editVisitForm', function (e) {
    e.preventDefault();
    var taskId      = document.getElementById('editVisitTaskId').value;
    var instruction = document.getElementById('editInstruction').value.trim();
    if (!instruction) return;

    document.getElementById('btn-edit-text').style.display    = 'none';
    document.getElementById('btn-edit-spinner').style.display = '';
    document.getElementById('btn-submit-edit').disabled = true;
    document.getElementById('editVisitResponse').style.display = 'none';

    $.ajax({
        url:  _TH_VISIT_EDIT_URL + '/' + taskId,
        type: 'POST',
        data: { _token: _CSRF_TOKEN, instruction: instruction },
        success: function (res) {
            document.getElementById('btn-edit-text').style.display    = '';
            document.getElementById('btn-edit-spinner').style.display = 'none';
            document.getElementById('btn-submit-edit').disabled = false;

            var respEl = document.getElementById('editVisitResponse');
            respEl.style.display = 'block';

            if (res.status) {
                respEl.innerHTML =
                    '<div class="alert alert-success mb-0" style="font-size:13px;">' +
                        '<strong><i class="mdi mdi-check-circle"></i> Applied!</strong>' +
                        (res.interpretation ? '<div class="mt-1" style="color:#155724;">' + res.interpretation + '</div>' : '') +
                    '</div>';
                // Close edit modal then refresh overlay detail if open, else reload list
                setTimeout(function () {
                    $('#editVisitModal').modal('hide');
                    if (_currentDrawerTaskId && document.getElementById('visitDetailModal').classList.contains('show')) {
                        openVisitModal(taskId);
                    } else {
                        loadVisitList(1);
                    }
                }, 1500);
            } else {
                respEl.innerHTML =
                    '<div class="alert alert-danger mb-0" style="font-size:13px;">' +
                        '<i class="mdi mdi-alert-circle"></i> ' + (res.message || 'Failed to apply edit.') +
                    '</div>';
            }
        },
        error: function () {
            document.getElementById('btn-edit-text').style.display    = '';
            document.getElementById('btn-edit-spinner').style.display = 'none';
            document.getElementById('btn-submit-edit').disabled = false;
            var respEl = document.getElementById('editVisitResponse');
            respEl.style.display = 'block';
            respEl.innerHTML = '<div class="alert alert-danger mb-0" style="font-size:13px;"><i class="mdi mdi-alert-circle"></i> An unexpected error occurred.</div>';
        }
    });
});

/* ──────────────────────────────────────────────────────────────── */

function loadVisitList(page) {
    $('.shimmer_id').show();
    $('#response_requested_id').html('');

    let _toApiDate = function (v) {
        let m = moment(v, 'MM/DD/YYYY', true);
        return m.isValid() ? m.format('YYYY-MM-DD') : v;
    };
    let hasCriticalAlert = '';
    if($('input[id="hasCriticalAlert"]:checked').val() != undefined){
        hasCriticalAlert = true
    }
    let params = {
        agencyIds:         getMultiVal('#agencyIds'),
        fromDate:          _toApiDate($('#fromDate').val()),
        toDate:            _toApiDate($('#toDate').val()),
        sortBy:            $('#sortBy').val(),
        status:            getMultiVal('#thStatus'),
        reviewStatus:      getMultiVal('#reviewStatus'),
        hasCriticalAlert:  hasCriticalAlert,
        search:            $('#thSearch').val(),
        poc_check:         $('#vl_filter_poc').is(':checked') ? 1 : '',
        mdo_check:         $('#vl_filter_mdo').is(':checked') ? 1 : '',
        alert_check:       $('#vl_filter_alert').is(':checked') ? 1 : '',
        supervision_check: $('#vl_filter_supervision').is(':checked') ? 1 : '',
        assessment_check:          $('#vl_filter_assessment').is(':checked') ? 1 : '',
        kardex_check:              $('#vl_filter_kardex').is(':checked') ? 1 : '',
        patient_package_doc_check: $('#vl_filter_patient_package_doc').is(':checked') ? 1 : '',
        page:                      page
    };

    $.ajax({
        url: _TH_VISIT_LIST_URL,
        type: 'GET',
        data: params,
        success: function (response) {
            $('#response_requested_id').html(response);
            // Init tooltips & popovers for newly rendered rows
            $('#response_requested_id [data-toggle="tooltip"]').tooltip();
            $('#response_requested_id [data-toggle="popover"]').popover({ html: true, sanitize: false });
        },
        error: function () {
            $('.shimmer_id').hide();
            $('#blank_div').attr('style', 'margin-top:10%');
            $('#response_requested_id').html('<div class="alert alert-danger">Failed to load visit list. Please try again.</div>');
        }
    });
}

/* ═══════════════════════════════════════════════════════════════
   Document Actions — Approve & Open for Changes
   ═══════════════════════════════════════════════════════════════ */

var _currentDocAction = null;

function approveDocument(taskId, docId, docTitle) {
    $.confirm({
        title: 'Approve Document',
        columnClass: 'col-md-5',
        content: 'Are you sure you want to approve <strong>' + (docTitle || 'this document') + '</strong>?',
        buttons: {
            confirm: {
                text: 'Yes, Approve',
                btnClass: 'btn-success',
                action: function () {
                    $.ajax({
                        url: _TH_DOC_APPROVE_URL + '/' + taskId + '/' + docId,
                        type: 'POST',
                        data: { _token: _CSRF_TOKEN },
                        success: function (res) {
                            if (res.status) {
                                $.alert({
                                    title: 'Approved!',
                                    content: res.message || 'Document approved successfully.',
                                    type: 'green',
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            action: function () { openVisitModal(taskId); }
                                        }
                                    }
                                });
                            } else {
                                $.alert({ title: 'Error', content: res.message || 'Failed to approve document.', type: 'red' });
                            }
                        },
                        error: function () {
                            $.alert({ title: 'Error', content: 'An unexpected error occurred. Please try again.', type: 'red' });
                        }
                    });
                }
            },
            cancel: { text: 'Cancel', btnClass: 'btn-secondary' }
        }
    });
}

function openOpenForChangesModal(taskId, docId, docTitle) {
    _currentDocAction = { taskId: taskId, docId: docId };
    document.getElementById('ofc-doc-title').textContent = docTitle || 'Document';
    document.getElementById('ofc-rejections-container').innerHTML =
        '<div class="ofc-rejection-row mb-2">' +
        '<textarea class="form-control ofc-rejection-input" rows="2" ' +
        'placeholder="e.g. Patient signature is missing on page 2" style="font-size:13px;resize:vertical;"></textarea>' +
        '</div>';
    document.getElementById('ofc-response').style.display = 'none';
    document.getElementById('ofc-response').innerHTML = '';
    document.getElementById('btn-ofc-submit-text').style.display = '';
    document.getElementById('btn-ofc-spinner').style.display = 'none';
    document.getElementById('btn-ofc-submit').disabled = false;
    $('#openForChangesModal').modal('show');
}

function addRejectionRow() {
    $('#ofc-rejections-container').append(
        '<div class="ofc-rejection-row mb-2 d-flex" style="gap:6px;">' +
        '<textarea class="form-control ofc-rejection-input" rows="2" ' +
        'placeholder="e.g. Date on form is incorrect" style="font-size:13px;resize:vertical;flex:1;"></textarea>' +
        '<button type="button" class="btn btn-sm btn-outline-danger align-self-start mt-1" ' +
        'onclick="$(this).closest(\'.ofc-rejection-row\').remove()"><i class="mdi mdi-close"></i></button>' +
        '</div>'
    );
}

function submitOpenForChanges() {
    if (!_currentDocAction) return;

    var rejections = [];
    $('.ofc-rejection-input').each(function () {
        var val = $(this).val().trim();
        if (val) rejections.push(val);
    });

    if (!rejections.length) {
        document.getElementById('ofc-response').innerHTML =
            '<div class="alert alert-danger mb-0" style="font-size:13px;">At least one rejection reason is required.</div>';
        document.getElementById('ofc-response').style.display = 'block';
        return;
    }

    document.getElementById('btn-ofc-submit-text').style.display = 'none';
    document.getElementById('btn-ofc-spinner').style.display = '';
    document.getElementById('btn-ofc-submit').disabled = true;
    document.getElementById('ofc-response').style.display = 'none';

    $.ajax({
        url: _TH_DOC_OPEN_CHANGES_URL + '/' + _currentDocAction.taskId + '/' + _currentDocAction.docId,
        type: 'POST',
        data: { _token: _CSRF_TOKEN, rejections: rejections },
        success: function (res) {
            document.getElementById('btn-ofc-submit-text').style.display = '';
            document.getElementById('btn-ofc-spinner').style.display = 'none';
            document.getElementById('btn-ofc-submit').disabled = false;

            var respEl = document.getElementById('ofc-response');
            respEl.style.display = 'block';
            if (res.status) {
                respEl.innerHTML = '<div class="alert alert-success mb-0" style="font-size:13px;"><i class="mdi mdi-check-circle"></i> ' + (res.message || 'Document opened for changes.') + '</div>';
                setTimeout(function () {
                    $('#openForChangesModal').modal('hide');
                    openVisitModal(_currentDocAction.taskId);
                }, 1200);
            } else {
                respEl.innerHTML = '<div class="alert alert-danger mb-0" style="font-size:13px;"><i class="mdi mdi-alert-circle"></i> ' + (res.message || 'Failed to open document for changes.') + '</div>';
            }
        },
        error: function () {
            document.getElementById('btn-ofc-submit-text').style.display = '';
            document.getElementById('btn-ofc-spinner').style.display = 'none';
            document.getElementById('btn-ofc-submit').disabled = false;
            var respEl = document.getElementById('ofc-response');
            respEl.style.display = 'block';
            respEl.innerHTML = '<div class="alert alert-danger mb-0" style="font-size:13px;"><i class="mdi mdi-alert-circle"></i> An unexpected error occurred. Please try again.</div>';
        }
    });
}

function deleteVisit(taskId){
    $.confirm({
        title: 'Cancel Visit',
        columnClass: 'col-md-5',
        content: 'Are you sure you want to cancel visit <strong>#' + taskId + '</strong>? This action cannot be undone.',
        buttons: {
            confirm: {
                text: 'Yes, Cancel Visit',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: _TH_DELETE_URL + '/' + taskId,
                        type: 'DELETE',
                        data: { _token: _CSRF_TOKEN },
                        success: function (res) {
                            if (res.status) {
                                $.alert({
                                    title: 'Success',
                                    content: res.message || 'Visit cancelled successfully.',
                                    type: 'green',
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            action: function () {
                                                window.location.href = '/task-health/visit';
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.alert({ title: 'Error', content: res.message || 'Failed to cancel visit.', type: 'red' });
                            }
                        },
                        error: function () {
                            $.alert({ title: 'Error', content: 'An unexpected error occurred. Please try again.', type: 'red' });
                        }
                    });
                }
            },
            cancel: {
                text: 'No, Keep It',
                btnClass: 'btn-secondary'
            }
        }
    });
}
/* ═══════════════════════════════════════════════════════════════
   Patient Master Record — Check / Create
   ═══════════════════════════════════════════════════════════════ */

function _vCheckMasterRecord(taskId) {
    if (typeof _TH_CAN_LINK_MASTER !== 'undefined' && !_TH_CAN_LINK_MASTER) return;
    if (!taskId) return;
    $.get(_TH_CHECK_MASTER_URL + '/' + taskId, function(res) {
        _masterCheckResult = res;
        _vSetMasterBanner(res);
        // If panel tab is currently active, populate it too
        var panel = document.getElementById('vt-patientrecord');
        if (panel && panel.classList.contains('active')) {
            _masterPanelLoaded = true;
            _vSetMasterPanel(res, taskId);
        }
        // Update tab badge
        _vUpdateMasterTabBadge(res);
    }).fail(function() {
        var banner = document.getElementById('vd-master-record-banner');
        if (banner) banner.style.display = 'none';
    });
}

function _vEnsureMasterPanelLoaded() {
    if (typeof _TH_CAN_LINK_MASTER !== 'undefined' && !_TH_CAN_LINK_MASTER) return;
    var taskId = _currentDrawerTaskId;
    if (!taskId) return;
    if (_masterPanelLoaded) return;
    _masterPanelLoaded = true;
    if (_masterCheckResult !== null) {
        _vSetMasterPanel(_masterCheckResult, taskId);
    } else {
        document.getElementById('vt-patientrecord-content').innerHTML =
            '<div style="text-align:center;padding:40px;"><i class="mdi mdi-loading mdi-spin" style="font-size:24px;color:#9ca3af;"></i></div>';
        $.get(_TH_CHECK_MASTER_URL + '/' + taskId, function(res) {
            _masterCheckResult = res;
            _vSetMasterPanel(res, taskId);
            _vSetMasterBanner(res);
            _vUpdateMasterTabBadge(res);
        }).fail(function() {
            document.getElementById('vt-patientrecord-content').innerHTML =
                '<div class="vd-pr-card"><div class="alert alert-danger">Failed to check patient record.</div></div>';
        });
    }
}

function _vUpdateMasterTabBadge(res) {
    if (typeof _TH_CAN_LINK_MASTER !== 'undefined' && !_TH_CAN_LINK_MASTER) return;
    var badge = document.getElementById('vd-pr-tab-badge');
    if (!badge) return;
    if (res && res.status && res.found && res.patient_linked) {
        badge.innerHTML = '<span style="background:#28a745;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;">Linked</span>';
        badge.style.display = '';
    } else if (res && res.status && (res.found || !res.found)) {
        badge.innerHTML = '<span style="background:#f0ad4e;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;">Unlinked</span>';
        badge.style.display = '';
    } else {
        badge.style.display = 'none';
    }
}

function _vAgencyOpts(selectId) {
    var opts = '<option value="">-- Select Agency --</option>';
    if (typeof _TH_LOCAL_AGENCIES !== 'undefined' && Array.isArray(_TH_LOCAL_AGENCIES)) {
        _TH_LOCAL_AGENCIES.forEach(function(a) {
            opts += '<option value="' + a.id + '">' + _vEsc(a.name) + '</option>';
        });
    }
    return opts;
}

function _vSetMasterBanner(res) {
    if (typeof _TH_CAN_LINK_MASTER !== 'undefined' && !_TH_CAN_LINK_MASTER) return;
    var wrap = document.getElementById('vd-master-record-banner');
    if (!wrap) return;

    if (!res || !res.status) {
        wrap.style.display = 'none';
        return;
    }

    var tid  = _currentDrawerTaskId;
    var html = '<div class="vd-pr-banner-header"><i class="mdi mdi-link-variant"></i>&nbsp; Patient Master Record</div>';
    html += '<div class="vd-pr-banner-body">';

    if (res.found && res.record) {
        var r = res.record;
        if (res.patient_linked && res.patient) {
            // ✅ Fully linked — master + local patient
            wrap.className = 'vd-pr-banner vd-pr-banner-found';
            html += '<div class="vd-pr-info">';
            html += '<span class="vd-pr-icon" style="color:#28a745;"><i class="mdi mdi-check-decagram"></i></span>';
            html += '<div class="vd-pr-text-block">';
            html += '<div class="vd-pr-status-label" style="color:#28a745;">Records Linked</div>';
            html += '<div class="vd-pr-meta">';
            html += '<strong>' + _vEsc(res.patient.name) + '</strong> (ID #' + res.patient.id + ')';
            if (r.status) html += ' &nbsp;·&nbsp; <em>' + _vEsc(r.status) + '</em>';
            html += '</div></div></div>';
            html += '<div class="vd-pr-actions">';
            html += '<a href="' + _vEsc(res.patient.patient_url) + '" target="_blank" class="vd-pr-btn vd-pr-btn-green"><i class="mdi mdi-open-in-new"></i> View Patient</a>';
            html += '</div>';
        } else {
            // ⚠️ Master found but patient not linked yet
            wrap.className = 'vd-pr-banner vd-pr-banner-missing';
            html += '<div style="width:100%;">';
            html += '<div class="vd-pr-info" style="margin-bottom:8px;">';
            html += '<span class="vd-pr-icon" style="color:#f0ad4e;"><i class="mdi mdi-link-off"></i></span>';
            html += '<div class="vd-pr-text-block">';
            html += '<div class="vd-pr-status-label" style="color:#856404;">Master Found — Patient Not Linked</div>';
            html += '<div class="vd-pr-meta">Master: <strong>' + _vEsc(r.name) + '</strong>';
            if (r.dob) html += ' &nbsp;·&nbsp; DOB: ' + _vEsc(r.dob);
            html += '</div></div></div>';
            html += _vAgencyActionRow(res, tid, '<i class="mdi mdi-account-plus-outline"></i> Link Patient', 'createMasterRecord');
            if (r.patient_url) {
                html += '<div style="margin-top:6px;"><a href="' + _vEsc(r.patient_url) + '" target="_blank" class="vd-pr-btn vd-pr-btn-outline"><i class="mdi mdi-open-in-new"></i> View Patient</a></div>';
            }
            html += '</div>';
        }
    } else {
        // ❌ Nothing found — create both
        wrap.className = 'vd-pr-banner vd-pr-banner-missing';
        html += '<div style="width:100%;">';
        html += '<div class="vd-pr-info" style="margin-bottom:8px;">';
        html += '<span class="vd-pr-icon" style="color:#f0ad4e;"><i class="mdi mdi-alert-circle-outline"></i></span>';
        html += '<div class="vd-pr-text-block">';
        html += '<div class="vd-pr-status-label" style="color:#856404;">No Record Found</div>';
        html += '<div class="vd-pr-meta">No local patient record linked.</div>';
        html += '</div></div>';
        html += _vAgencyActionRow(res, tid, '<i class="mdi mdi-plus-circle-outline"></i> Create &amp; Link', 'createMasterRecord');
        html += '</div>';
    }

    html += '</div>';
    wrap.innerHTML = html;

    // ── Update ERP Patient ID pill in ID strip ──────────────────────────────
    var erpPill  = document.getElementById('vd-erp-patient-id-pill');
    var erpValue = document.getElementById('vd-erp-patient-id-value');
    if (erpValue) {
        if (res.found && res.patient_linked && res.patient) {
            var _erpId  = res.patient.id;
            var _erpUrl = res.patient.patient_url;
            $('#thf-patient-id').val(_erpId || '');
            erpValue.innerHTML = _erpUrl
                ? '<a href="' + _vEsc(_erpUrl) + '" target="_blank">#' + _erpId + ' <i class="mdi mdi-open-in-new" style="font-size:10px;vertical-align:middle;"></i></a>'
                : '#' + _erpId;
            if (erpPill) { erpPill.classList.remove('vd-id-loading'); erpPill.classList.add('vd-id-linked'); }
        } else {
            erpValue.innerHTML = '<span style="color:#f0ad4e;font-size:12px;font-family:sans-serif;font-weight:600;letter-spacing:0;">Not Linked</span>';
            if (erpPill) { erpPill.classList.remove('vd-id-loading'); }
        }
    }

    // ── Show/hide Manage Flags + Send POC buttons based on HHA link ──────────
    var hasHhaLink = res.has_hha_link === true;
    $('#vd-flag-btn').toggle(true);
    $('#hide_show_send_poc').toggle(hasHhaLink);
}

function _vSetMasterPanel(res, taskId) {
    var wrap = document.getElementById('vt-patientrecord-content');
    if (!wrap) return;

    if (!res || !res.status) {
        wrap.innerHTML = '<div class="vd-pr-card"><div class="alert alert-warning m-0">Could not check patient record status.</div></div>';
        return;
    }

    var html = '';

    // ── ID Summary Strip at top of panel ────────────────────────────────────
    var _panelThTaskId    = _currentThTaskId    ? '#' + _currentThTaskId    : '—';
    var _panelThPatientId = _currentThPatientId ? '#' + _currentThPatientId : '—';
    var _panelErpId       = (res.patient_linked && res.patient) ? res.patient.id  : null;
    var _panelErpUrl      = (res.patient_linked && res.patient) ? res.patient.patient_url : null;
    var _panelMasterId    = (res.found && res.record) ? res.record.id : null;
    var _panelMasterUrl   = (res.found && res.record) ? (res.record.master_url || null) : null;

    html += '<div class="vd-id-strip" style="margin-top:14px;">';
    // TH Task ID
    html += '<div class="vd-id-pill">';
    html += '<div class="vd-id-pill-label"><i class="mdi mdi-identifier"></i> TH Task ID</div>';
    html += '<div class="vd-id-pill-value">' + _panelThTaskId + '</div>';
    html += '</div>';
    // TH Patient ID
    html += '<div class="vd-id-pill">';
    html += '<div class="vd-id-pill-label"><i class="mdi mdi-account-outline"></i> TH Patient ID</div>';
    html += '<div class="vd-id-pill-value">' + _panelThPatientId + '</div>';
    html += '</div>';
    // Master Record ID (with link to detail page)
    if (_panelMasterId) {
        html += '<div class="vd-id-pill vd-id-linked">';
        html += '<div class="vd-id-pill-label"><i class="mdi mdi-database"></i> Master Record ID</div>';
        html += '<div class="vd-id-pill-value">';
        html += _panelMasterUrl
            ? '<a href="' + _vEsc(_panelMasterUrl) + '" target="_blank">#' + _panelMasterId + ' <i class="mdi mdi-open-in-new" style="font-size:10px;vertical-align:middle;"></i></a>'
            : '#' + _panelMasterId;
        html += '</div></div>';
    }
    // Portal (local patient)
    if (typeof _TH_CAN_LINK_MASTER === 'undefined' || _TH_CAN_LINK_MASTER) {
        html += '<div class="vd-id-pill' + (_panelErpId ? ' vd-id-linked' : '') + '">';
        html += '<div class="vd-id-pill-label"><i class="mdi mdi-hospital-building"></i> Portal</div>';
        html += '<div class="vd-id-pill-value">';
    }
    if (_panelErpId) {
        html += _panelErpUrl
            ? '<a href="' + _vEsc(_panelErpUrl) + '" target="_blank">#' + _panelErpId + ' <i class="mdi mdi-open-in-new" style="font-size:10px;vertical-align:middle;"></i></a>'
            : '#' + _panelErpId;
    } else {
        html += '<span style="color:#9ca3af;font-size:12px;font-family:sans-serif;font-weight:600;letter-spacing:0;">Not Linked</span>';
    }
    html += '</div></div>';
    html += '</div>';

    if (res.found && res.record) {
        var r = res.record;

        // ── Master Record card ───────────────────────────────────────────────
        html += '<div class="vd-pr-card">';
        html += '<div class="vd-pr-card-title"><span style="color:#007bff;font-size:18px;"><i class="mdi mdi-database-check"></i></span>&nbsp; Task Health Master Record</div>';
        html += '<div class="vd-pr-detail-grid">';
        html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Name</div><div class="vd-pr-detail-value">' + _vEsc(r.name || '—') + '</div></div>';
        html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Date of Birth</div><div class="vd-pr-detail-value">' + _vEsc(r.dob || '—') + '</div></div>';
        html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Phone</div><div class="vd-pr-detail-value">' + _vEsc(r.phone || '—') + '</div></div>';
        html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Status</div><div class="vd-pr-detail-value">' + _vEsc(r.status || '—') + '</div></div>';
        html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Type</div><div class="vd-pr-detail-value">' + _vEsc(r.type || '—') + '</div></div>';
        html += '</div>';
        html += '<div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">';
        if (_panelMasterUrl) {
            html += '<a href="' + _vEsc(_panelMasterUrl) + '" target="_blank" class="vd-pr-btn" style="background:#e8f0fe;color:#1a73e8;border:1px solid #c5d8fb;"><i class="mdi mdi-database-eye-outline"></i> View Master Record</a>';
        }
        var _hasCaregiver = _currentCaregiver && _currentCaregiver.name;
        if (_panelErpId && _hasCaregiver) {
            html += '<button type="button" id="btn-supervision-' + parseInt(taskId) + '" class="vd-pr-btn" style="background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;" onclick="vdSupervisionConfirm(' + parseInt(taskId) + ',' + parseInt(_panelErpId) + ')"><i class="mdi mdi-eye-check-outline"></i> Supervision</button>';
        }
        html += '</div>';
        html += '</div>';

        // ── Local Patient card ───────────────────────────────────────────────
        if (res.patient_linked && res.patient) {
            var p = res.patient;
            html += '<div class="vd-pr-card" style="border-left:3px solid #28a745;">';
            html += '<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0faf3;border-bottom:1px solid #d4edda;">';
            html += '<i class="mdi mdi-check-circle" style="font-size:20px;color:#28a745;"></i>';
            html += '<div>';
            html += '<div style="font-size:13px;font-weight:700;color:#155724;">Records Linked</div>';
            html += '<div style="font-size:11px;color:#28a745;margin-top:1px;">This visit patient is successfully linked to a local record.</div>';
            html += '</div></div>';
            html += '<div class="vd-pr-detail-grid" style="padding:14px;">';
            html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Patient Name</div><div class="vd-pr-detail-value"><strong>' + _vEsc(p.name || '—') + '</strong></div></div>';
            html += '<div class="vd-pr-detail-item"><div class="vd-pr-detail-label">Patient ID</div><div class="vd-pr-detail-value"><strong>#' + p.id + '</strong></div></div>';
            html += '</div>';
            if (p.patient_url) {
                html += '<div style="padding:0 14px 14px;"><a href="' + _vEsc(p.patient_url) + '" target="_blank" class="vd-pr-btn vd-pr-btn-green"><i class="mdi mdi-open-in-new"></i> View Patient</a></div>';
            }
            html += '</div>';
        } else {
            // Master exists but patient not linked — show link form
            html += '<div class="vd-pr-card">';
            html += '<div class="vd-pr-card-title"><span style="color:#f0ad4e;font-size:18px;"><i class="mdi mdi-account-alert"></i></span>&nbsp; Patient &nbsp;<span style="background:#fff3cd;color:#856404;border-radius:10px;padding:2px 10px;font-size:11px;font-weight:700;">Not Linked</span></div>';
            html += '<p style="font-size:13px;color:#6c757d;margin-bottom:14px;">No local patient is linked to this master record. Select the agency and click <strong>Link Patient</strong> — the system will automatically find a matching patient or create a new one.</p>';
            html += _vMasterPanelForm(taskId, 'Link Patient', 'mdi-account-plus-outline');
            html += '</div>';
        }
    } else {
        // ── Nothing found ────────────────────────────────────────────────────
        html += '<div class="vd-pr-card">';
        html += '<div class="vd-pr-card-title"><span style="color:#f0ad4e;font-size:18px;"><i class="mdi mdi-alert-circle-outline"></i></span>&nbsp; No Record Found</div>';
        html += '<p style="font-size:13px;color:#6c757d;margin-bottom:14px;">No local patient master record exists for this visit\'s patient. Select the agency and click <strong>Create &amp; Link</strong> — the visit data will be used to create both a master record and a local patient automatically.</p>';
        html += '<div style="background:#fff8e1;border-radius:6px;padding:10px 13px;border:1px solid #ffe082;margin-bottom:14px;font-size:12px;color:#5d4037;">';
        html += '<i class="mdi mdi-information-outline"></i>&nbsp; The system will first search for an existing matching patient. If found, it will be linked; otherwise a new patient record will be created.';
        html += '</div>';
        html += _vMasterPanelForm(taskId, 'Create &amp; Link', 'mdi-plus-circle-outline');
        html += '</div>';
    }

    wrap.innerHTML = html;
}

function _vMasterPanelForm(taskId, btnLabel, btnIcon) {
    return _vAgencyActionRow(_masterCheckResult, taskId, '<i class="mdi ' + btnIcon + '"></i> ' + btnLabel, 'createMasterRecordFromPanel');
}

/* ── Action row: just the button, no agency selection ── */
function _vAgencyActionRow(res, taskId, btnHtml, callbackName) {
    return '<div style="margin-top:8px;padding-top:8px;border-top:1px dashed #dee2e6;">'
         + '<button class="vd-pr-btn vd-pr-btn-orange" onclick="' + callbackName + '(' + taskId + ')" id="vd-pr-create-btn">'
         + btnHtml + '</button>'
         + '</div>';
}

/* ═══════════════════════════════════════════════════════════════
   Link Patient Modal — new detailed workflow
   ═══════════════════════════════════════════════════════════════ */

var _thLinkTaskId    = null;
var _thLinkHasMatch  = false;   // true when local_patient_match exists

function _thShowLinkModal(taskId) {
    _thLinkTaskId   = taskId;
    _thLinkHasMatch = false;

    var p      = _currentVisitPatient || {};
    var phones = Array.isArray(p.phoneNumbers) ? p.phoneNumbers : [];
    var prim   = phones.find(function(x){ return x.isPrimary; }) || phones[0] || null;
    var mobile = prim ? (prim.number || '—') : '—';
    var others = phones.filter(function(x){ return !x.isPrimary; });
    var phone  = others.length ? others[0].number : mobile;

    // ── Build patient info grid ──
    var fields = [
        ['First Name',      p.firstName   || '—'],
        ['Middle Name',     p.middleName  || '—'],
        ['Last Name',       p.lastName    || '—'],
        ['Date of Birth',   p.dateOfBirth ? _vDate(p.dateOfBirth) : '—'],
        ['Mobile',          mobile],
        ['Phone',           phone],
        ['Gender',          p.gender      || '—'],
        ['Address 1',       p.address     || '—'],
        ['Apt/Suite/Floor', p.address2    || '—'],
        ['State',           p.state       || '—'],
        ['City',            p.city        || '—'],
        ['Zip Code',        p.zipCode     || '—'],
        ['County',          p.county      || '—'],
    ];
    var gridHtml = '';
    fields.forEach(function(f) {
        gridHtml += '<div class="th-lp-field">'
                  + '<div class="th-lp-label">' + f[0] + '</div>'
                  + '<div class="th-lp-value">' + _vEsc(f[1]) + '</div>'
                  + '</div>';
    });
    document.getElementById('th-lp-info-grid').innerHTML = gridHtml;

    var localMatch = _masterCheckResult ? (_masterCheckResult.local_patient_match || null) : null;

    if (localMatch) {
        // Existing local patient found — just confirm the link
        _thLinkHasMatch = true;
        document.getElementById('th-lp-modal-title').innerHTML = 'Link Patient Record';
        document.getElementById('th-lp-found-alert').style.display = '';
        document.getElementById('th-lp-found-alert').innerHTML =
            '<i class="mdi mdi-account-check" style="font-size:16px;vertical-align:middle;margin-right:6px;"></i>'
            + '<strong>Existing patient found:</strong> '
            + _vEsc(localMatch.name) + ' (ID&nbsp;<strong>#' + localMatch.id + '</strong>) will be linked to this visit.'
            + (localMatch.patient_url ? ' <a href="' + _vEsc(localMatch.patient_url) + '" target="_blank" style="font-size:12px;margin-left:6px;">'
               + '<i class="mdi mdi-open-in-new"></i> View</a>' : '');
        document.getElementById('th-lp-inputs').style.display = 'none';
        document.getElementById('th-lp-proceed-label').innerHTML = 'Confirm &amp; Link';
    } else {
        // No match — show create form
        _thLinkHasMatch = false;
        document.getElementById('th-lp-modal-title').innerHTML = 'Create &amp; Link Patient Record';
        document.getElementById('th-lp-found-alert').style.display = 'none';
        document.getElementById('th-lp-inputs').style.display = '';
        document.getElementById('th-lp-proceed-label').innerHTML = 'Create &amp; Link';
        // Reset inputs
        $('#th-lp-discipline').val('');
        $('#th-lp-followup-date').val('');
        $('#th-lp-due-date').val('');
        if ($('#th-lp-services').data('select2')) {
            $('#th-lp-services').val(null).trigger('change');
        }
    }

    $('#thLinkPatientModal').modal('show');
}

$('#thLinkPatientModal').on('shown.bs.modal', function() {
    if (!_thLinkHasMatch) {
        // Services select2
        if (!$('#th-lp-services').data('select2')) {
            $('#th-lp-services').select2({ dropdownParent: $('#thLinkPatientModal'), placeholder: 'Select services…', allowClear: true });
        }
        // Clear service error on change
        $('#th-lp-services').off('change.lpvalidate').on('change.lpvalidate', function () {
            if ($(this).val() && $(this).val().length > 0) {
                $('#th-lp-service-error').hide();
                $(this).next('.select2-container').find('.select2-selection').css('border-color', '');
            }
        });
        // Date inputmask
        $('#th-lp-followup-date, #th-lp-due-date').inputmask();
    }
}).on('hide.bs.modal', function () {
    $('#th-lp-service-error').hide();
    $('.modal-backdrop').last().css('z-index', '');
});

$('#th-lp-proceed-btn').on('click', function () {
    var taskId = _thLinkTaskId;
    if (!taskId) return;

    // Service validation — required when creating a new patient
    if (!_thLinkHasMatch) {
        var selectedServices = $('#th-lp-services').val();
        if (!selectedServices || selectedServices.length === 0) {
            $('#th-lp-service-error').show();
            $('#th-lp-services').next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
            return;
        }
        $('#th-lp-service-error').hide();
        $('#th-lp-services').next('.select2-container').find('.select2-selection').css('border-color', '');
    }

    $('#thLinkPatientModal').modal('hide');
    _doCreateMaster(taskId, 'vd-pr-create-btn');
});

function _doCreateMaster(taskId, btnSelector) {
    var btn = document.getElementById(btnSelector);
    var origLabel = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing…'; }

    var postData = {
        _token:     _CSRF_TOKEN,
        agency_id:  (_masterCheckResult && _masterCheckResult.suggested_agency) ? _masterCheckResult.suggested_agency.id : '',
        discipline: '',
        service_ids: [],
        followup_date: '',
        due_date: '',
    };

    if (!_thLinkHasMatch) {
        postData.discipline    = $('#th-lp-discipline').val() || '';
        postData.service_ids   = ($('#th-lp-services').val() || []).join(',');
        postData.followup_date = $('#th-lp-followup-date').val() || '';
        postData.due_date      = $('#th-lp-due-date').val() || '';
    }

    $.ajax({
        url:         _TH_CREATE_MASTER_URL + '/' + taskId,
        type:        'POST',
        data:        postData,
        traditional: true,
        success: function (res) {
            if (btn) { btn.disabled = false; btn.innerHTML = origLabel; }
            if (res.status && res.record) {
                var newRes = {
                    status: 1, found: true, record: res.record,
                    patient_linked: res.patient_linked || false,
                    patient: res.patient || null,
                    suggested_agency:    _masterCheckResult ? _masterCheckResult.suggested_agency    : null,
                    local_patient_match: _masterCheckResult ? _masterCheckResult.local_patient_match : null,
                };
                _masterCheckResult = newRes;
                _vSetMasterBanner(newRes);
                if (_masterPanelLoaded) { _vSetMasterPanel(newRes, taskId); }
                _vUpdateMasterTabBadge(newRes);
                if (typeof toastr !== 'undefined') toastr.success(res.message || 'Linked successfully.');
            } else {
                if (typeof toastr !== 'undefined') toastr.error(res.message || 'Failed to process. Please try again.');
            }
        },
        error: function () {
            if (btn) { btn.disabled = false; btn.innerHTML = origLabel; }
            if (typeof toastr !== 'undefined') toastr.error('An error occurred. Please try again.');
        }
    });
}

function createMasterRecord(taskId) {
    _thShowLinkModal(taskId);
}

function createMasterRecordFromPanel(taskId) {
    _thShowLinkModal(taskId);
}

function _createMasterFromPanelLegacy(taskId) { createMasterRecordFromPanel(taskId); }

function exportVisitCsv() {
    var params = new URLSearchParams();

    // Agency IDs (multi-select) — join with comma
    var agencyVals = $('#agencyIds').val();
    if (agencyVals && agencyVals.length) params.append('agencyIds', agencyVals.join(','));

    // Status (multi-select)
    var statusVals = $('#thStatus').val();
    if (statusVals && statusVals.length) params.append('status', statusVals.join(','));

    // Review Status (multi-select)
    var reviewVals = $('#reviewStatus').val();
    if (reviewVals && reviewVals.length) params.append('reviewStatus', reviewVals.join(','));

    // Date range
    var fromDate = $('#fromDate').val();
    var toDate   = $('#toDate').val();
    if (fromDate) params.append('fromDate', fromDate);
    if (toDate)   params.append('toDate',   toDate);

    // Text search
    var search = $('#thSearch').val();
    if (search) params.append('search', search);

    // Critical alert checkbox
    if ($('#hasCriticalAlert').is(':checked')) params.append('hasCriticalAlert', 'true');

    // Flag filters
    if ($('#vl_filter_poc').is(':checked'))         params.append('poc_check', 1);
    if ($('#vl_filter_mdo').is(':checked'))         params.append('mdo_check', 1);
    if ($('#vl_filter_alert').is(':checked'))       params.append('alert_check', 1);
    if ($('#vl_filter_supervision').is(':checked')) params.append('supervision_check', 1);
    if ($('#vl_filter_assessment').is(':checked'))          params.append('assessment_check', 1);
    if ($('#vl_filter_kardex').is(':checked'))              params.append('kardex_check', 1);
    if ($('#vl_filter_patient_package_doc').is(':checked')) params.append('patient_package_doc_check', 1);

    window.location.href = _TH_VISIT_EXPORT_URL + '?' + params.toString();
}

$('#thLinkPatientModal').on('show.bs.modal', function () {
    $(this).css('z-index', 1080);
    setTimeout(function () {
        $('.modal-backdrop').last().css('z-index', 1075);
    }, 0);
});

/* ═══════════════════════════════════════════════════════════════
   Supervision — $.confirm popup + AJAX submit
   ═══════════════════════════════════════════════════════════════ */
function vdSupervisionConfirm(taskId, patientId) {
    $.confirm({
        title: 'Confirm Supervision',
        columnClass: 'col-md-5',
        content: 'You want to submit supervision for:<br><br>' +
                 '<strong>Task ID:</strong> ' + taskId + '<br>' +
                 '<strong>Patient ID:</strong> ' + patientId,
        buttons: {
            formSubmit: {
                text: 'Yes, Submit',
                btnClass: 'btn-success',
                action: function () {
                    var self = this;
                    var $svpBtn = $('#btn-supervision-' + taskId);
                    self.close();
                    $svpBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Submitting...');
                    $.ajax({
                        url: _SEND_HHA_SUPERVISION,
                        type: 'GET',
                        data: { task_id: taskId, patient_id: patientId },
                        success: function (res) {
                            $svpBtn.prop('disabled', false).html('<i class="mdi mdi-eye-check-outline"></i> Supervision');
                            toastr.success(res.error_msg || 'Supervision saved successfully.');
                        },
                        error: function (xhr) {
                            $svpBtn.prop('disabled', false).html('<i class="mdi mdi-eye-check-outline"></i> Supervision');
                            showErrorAndLoginRedirection(xhr);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel'
            }
        }
    });
}