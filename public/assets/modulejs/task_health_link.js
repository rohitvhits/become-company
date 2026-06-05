// ─── Patient Detail Page: Task Health tab ─────────────────────────────────────

// var _visitDataLoaded = false;

function loadTaskHealthSection() {
    // if (!_visitDataLoaded) {
        loadVisitData(1);
    // }
}

// Convert MM/DD/YYYY → YYYY-MM-DD for the API
function _thToApiDate(v) {
    if (!v) return '';
    var parts = v.split('/');
    if (parts.length === 3 && parts[2].length === 4) {
        return parts[2] + '-' + ('0' + parts[0]).slice(-2) + '-' + ('0' + parts[1]).slice(-2);
    }
    return v;
}

function loadVisitData(page) {
    $('#visit_list_loader_patient').show();
    $('#visit_list_patient_container').html('');

    var fromDateRaw  = $('#th_filter_from_date').val()     || '';
    var toDateRaw    = $('#th_filter_to_date').val()       || '';
    var fromDate     = _thToApiDate(fromDateRaw);
    var toDate       = _thToApiDate(toDateRaw);
    var sortBy       = $('#th_filter_sort_by').val()       || 'scheduledDateTime';
    var status       = $('#th_filter_status').val()        || '';
    var reviewStatus = $('#th_filter_review_status').val() || '';

    // Update active filter chips (show the human-readable MM/DD/YYYY)
    _thUpdateFilterChips(fromDateRaw, toDateRaw, sortBy, status, reviewStatus);

    $.ajax({
        url: _PATIENT_TASK_HEALTH_VISITS,
        type: 'GET',
        data: {
            patient_id:   _RECORD_ID,
            page:         page || 1,
            fromDate:     fromDate,
            toDate:       toDate,
            sortBy:       sortBy,
            status:       status,
            reviewStatus: reviewStatus,
        },
        success: function(response) {
            $('#visit_list_loader_patient').hide();
            $('#visit_list_patient_container').html(response);
            $('#visit_list_patient_container [data-toggle="tooltip"]').tooltip();
            $('#visit_list_patient_container [data-toggle="popover"]').popover({ html: true, sanitize: false });
        },
        error: function(jqr) {
            $('#visit_list_loader_patient').hide();
            $('#visit_list_patient_container').html('<div class="alert alert-danger small py-2">Failed to load visits. Please try again.</div>');
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function resetPatientVisitFilters() {
    var now      = new Date();
    var fromDate = new Date(now.getFullYear(), 0, 1);
    var toDate = new Date(now.getFullYear() + 2, 11, 31);
    var pad = function(n) { return n < 10 ? '0' + n : n; };
    var fmtMDY = function(d) {
        return pad(d.getMonth() + 1) + '/' + pad(d.getDate()) + '/' + d.getFullYear();
    };
    $('#th_filter_from_date').val(fmtMDY(fromDate));
    $('#th_filter_to_date').val(fmtMDY(toDate));
    $('#th_filter_sort_by').val('scheduledDateTime');
    $('#th_filter_status').val('');
    $('#th_filter_review_status').val('');
    loadVisitData(1);
}

function _thUpdateFilterChips(fromDate, toDate, sortBy, status, reviewStatus) {
    var hasFilter = false;

    function setChip(id, text, show) {
        if (show) { $('#' + id).text(text).show(); hasFilter = true; }
        else       { $('#' + id).hide(); }
    }

    setChip('th_chip_from',   'From: ' + fromDate,                 !!fromDate);
    setChip('th_chip_to',     'To: '   + toDate,                   !!toDate);
    setChip('th_chip_sort',   'Sort: ' + (sortBy === 'createdAt' ? 'Created Date' : 'Scheduled Date'),
                                          sortBy && sortBy !== 'scheduledDateTime');
    setChip('th_chip_status', 'Status: ' + status,                 !!status);
    setChip('th_chip_review', 'Review: ' + reviewStatus,           !!reviewStatus);

    if (hasFilter) {
        $('#th_filter_chips').show();
        $('#th_active_filter_dot').css('display', 'inline-block');
    } else {
        $('#th_filter_chips').hide();
        $('#th_active_filter_dot').hide();
    }
}

// ─── Visit Detail Modal ────────────────────────────────────────────────────────

function openTaskHealthVisitDetail(taskId) {
    if (!taskId) return;

    // Widen modal for sidebar layout
    $('#taskHealthVisitDetailModal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
    $('#taskHealthVisitDetailModal .modal-title').html(
        '<i class="mdi mdi-hospital-building mr-1"></i> Task Health Visit Detail'
    );

    _thInjectStyles();
    $('#taskHealthVisitDetailBody').html(_thShimmer());
    $('#taskHealthVisitDetailModal').modal('show');

    $.ajax({
        url: _TASK_HEALTH_VISIT_DETAIL_JSON + '/' + taskId,
        type: 'GET',
        success: function(res) {
            if (res.status && res.data) {
                $('#taskHealthVisitDetailBody').html(_thRenderDetail(res.data));
            } else {
                $('#taskHealthVisitDetailBody').html(
                    '<div class="alert alert-warning m-3 small">Could not load visit detail.</div>'
                );
            }
        },
        error: function() {
            $('#taskHealthVisitDetailBody').html(
                '<div class="alert alert-danger m-3 small">Failed to fetch visit detail.</div>'
            );
        }
    });
}

// Restore modal size on close
$('#taskHealthVisitDetailModal').on('hidden.bs.modal', function() {
    $(this).find('.modal-dialog').removeClass('modal-xl').addClass('modal-lg');
});

// Global tab switcher — called from onclick in dynamically rendered HTML
function _thTab(tabId) {
    document.querySelectorAll('#taskHealthVisitDetailBody .th-panel').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('#taskHealthVisitDetailBody .th-sbar-btn').forEach(function(el) {
        el.classList.remove('th-active');
    });
    var panel = document.getElementById('th-p-' + tabId);
    if (panel) panel.style.display = 'block';
    var btn = document.querySelector('#taskHealthVisitDetailBody .th-sbar-btn[data-tab="' + tabId + '"]');
    if (btn) btn.classList.add('th-active');
}

// ─── Inject CSS once ──────────────────────────────────────────────────────────

function _thInjectStyles() {
    var existing = document.getElementById('th-detail-styles');
    if (existing) existing.remove();
    var s = document.createElement('style');
    s.id = 'th-detail-styles';
    s.textContent = [
        /* modal body reset */
        '#taskHealthVisitDetailModal .modal-body{padding:0!important;overflow:hidden!important;}',

        /* ── Header ── */
        '.th-hdr{display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 20px;background:linear-gradient(135deg,#1e1e2f,#2d3a4a);flex-shrink:0;}',
        '.th-avatar{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-size:16px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;letter-spacing:1px;}',
        '.th-hdr-info{display:flex;flex-direction:column;gap:2px;flex:1;min-width:0;}',
        '.th-hdr-name{font-size:16px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}',
        '.th-hdr-sub{font-size:12.5px;color:rgba(255,255,255,.5);}',
        '.th-bs{padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;color:#fff;white-space:nowrap;}',
        '.th-bs.ok{background:#28a745;} .th-bs.no{background:#dc3545;} .th-bs.ip{background:#17a2b8;} .th-bs.wa{background:#e0a800;color:#333;}',
        '.th-bt{padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);white-space:nowrap;}',

        /* ── Quick strip ── */
        '.th-qs{display:flex;background:#fff;border-bottom:2px solid #e9ecef;flex-shrink:0;overflow-x:auto;}',
        '.th-qsi{display:flex;flex-direction:column;padding:9px 16px;border-right:1px solid #e9ecef;min-width:110px;flex-shrink:0;}',
        '.th-qsi:last-child{border-right:none;}',
        '.th-qsl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:2px;}',
        '.th-qsv{font-size:13.5px;font-weight:700;color:#1f2937;white-space:nowrap;}',

        /* ── Sidebar + panel layout ── */
        '.th-layout{display:flex;height:440px;overflow:hidden;}',
        '.th-sbar{width:168px;flex-shrink:0;background:#f8f9fa;border-right:1px solid #dee2e6;overflow-y:auto;padding:8px 0;display:flex;flex-direction:column;}',
        '.th-sbar-group{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#adb5bd;padding:10px 14px 3px;margin-top:2px;}',
        '.th-sbar-btn{display:flex;align-items:center;gap:8px;width:100%;padding:9px 14px;border:none;background:none;font-size:13px;font-weight:500;color:#6c757d;cursor:pointer;border-left:3px solid transparent;text-align:left;transition:all .15s;}',
        '.th-sbar-btn i{font-size:16px;min-width:18px;flex-shrink:0;}',
        '.th-sbar-btn span{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}',
        '.th-sbar-btn:hover{background:#e9ecef;color:#343a40;}',
        '.th-sbar-btn.th-active{color:#007bff;border-left-color:#007bff;background:#fff;font-weight:600;}',
        '.th-sbar-bdg{background:#dee2e6;color:#6c757d;border-radius:9px;font-size:11px;padding:1px 6px;font-weight:700;flex-shrink:0;}',
        '.th-sbar-btn.th-active .th-sbar-bdg{background:#cfe2ff;color:#0d6efd;}',

        /* ── Panel container + panels ── */
        '.th-panels{flex:1;position:relative;overflow:hidden;}',
        '.th-panel{position:absolute;inset:0;overflow-y:auto;background:#f0f2f5;padding:12px;display:none;}',

        /* ── Section card ── */
        '.th-sec{background:#fff;border-radius:7px;border:1px solid #e9ecef;padding:13px 15px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.04);}',
        '.th-sec:last-child{margin-bottom:0;}',
        '.th-sec-title{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;margin-bottom:11px;padding-bottom:7px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;gap:6px;}',
        '.th-sec-title::before{content:"";width:3px;height:12px;background:#007bff;border-radius:2px;flex-shrink:0;}',

        /* ── Stat cards row ── */
        '.th-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:8px;}',
        '.th-stat-card{background:#f8f9fa;border-radius:6px;padding:10px 12px;border:1px solid #e9ecef;text-align:center;}',
        '.th-stat-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:5px;}',
        '.th-stat-val{font-size:14px;font-weight:700;color:#1f2937;line-height:1.3;word-break:break-word;}',

        /* ── Detail grid ── */
        '.th-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}',
        '.th-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;}',
        '.th-cl{font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.3px;margin-bottom:2px;}',
        '.th-cv{font-size:13.5px;font-weight:600;color:#1f2937;word-break:break-word;line-height:1.4;}',

        /* ── Table ── */
        '.th-tbl{width:100%;border-collapse:collapse;font-size:13px;}',
        '.th-tbl th{background:#f8f9fa;color:#6c757d;font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:.3px;padding:8px 10px;border-bottom:2px solid #dee2e6;text-align:left;}',
        '.th-tbl td{padding:8px 10px;border-bottom:1px solid #f0f2f5;color:#1f2937;vertical-align:middle;}',
        '.th-tbl tr:last-child td{border-bottom:none;}',
        '.th-tbl tr:hover td{background:#fafbff;}',

        /* ── Empty state ── */
        '.th-empty{text-align:center;padding:32px 16px;color:#adb5bd;}',
        '.th-empty i{font-size:42px;display:block;margin-bottom:8px;}',
        '.th-empty p{font-size:14px;margin:0;font-weight:500;}',

        /* ── Critical alert inline banner ── */
        '.th-warn{background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px 13px;font-size:16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:8px;}',
        '.th-warn i{color:#e0a800;font-size:17px;flex-shrink:0;margin-top:1px;}',

        /* ── Critical Alert panel ── */
        '.th-ca-hero{border-radius:10px;padding:22px 20px;display:flex;align-items:center;gap:16px;margin-bottom:12px;}',
        '.th-ca-hero.th-ca-critical{background:linear-gradient(135deg,#ff416c,#c0392b);border:1px solid #c0392b;}',
        '.th-ca-hero.th-ca-clear{background:linear-gradient(135deg,#11998e,#38ef7d);border:1px solid #27ae60;}',
        '.th-ca-hero.th-ca-na{background:linear-gradient(135deg,#6c757d,#495057);border:1px solid #495057;}',
        '.th-ca-hero-icon{font-size:40px;color:#fff;flex-shrink:0;line-height:1;}',
        '.th-ca-hero-text{display:flex;flex-direction:column;gap:3px;}',
        '.th-ca-hero-status{font-size:19px;font-weight:800;color:#fff;letter-spacing:.3px;}',
        '.th-ca-hero-sub{font-size:13px;color:rgba(255,255,255,.75);}',
        '.th-ca-summary{background:#fff;border:1px solid #e9ecef;border-radius:7px;padding:14px 16px;margin-bottom:10px;}',
        '.th-ca-summary-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:6px;}',
        '.th-ca-summary-text{font-size:14px;color:#1f2937;line-height:1.7;font-weight:500;white-space:pre-wrap;}',

        /* ── Critical alert sidebar alert button ── */
        '.th-sbar-btn.th-alert-active{color:#dc3545!important;border-left-color:#dc3545!important;}',
        '.th-sbar-btn.th-alert-active .th-sbar-bdg{background:#dc3545!important;color:#fff!important;}',
        '.th-sbar-btn.th-alert-active:hover{background:#fff5f5!important;}',

        /* ── Shimmer ── */
        '.th-shim{background:linear-gradient(90deg,#f0f0f0 25%,#e4e4e4 50%,#f0f0f0 75%);background-size:200% 100%;animation:thShimA 1.4s infinite;border-radius:5px;}',
        '@keyframes thShimA{0%{background-position:200% 0}100%{background-position:-200% 0}}',
    ].join('');
    document.head.appendChild(s);
}

// ─── Shimmer loader ───────────────────────────────────────────────────────────

function _thShimmer() {
    var shimRow = '<div style="display:flex;flex-direction:column;gap:10px;">' +
        '<div class="th-shim" style="height:88px;"></div>' +
        '<div class="th-shim" style="height:88px;"></div>' +
        '<div class="th-shim" style="height:130px;"></div>' +
        '<div class="th-shim" style="height:80px;"></div>' +
        '</div>';
    return  '<div class="th-shim" style="height:72px;border-radius:0;"></div>' +
            '<div class="th-shim" style="height:44px;border-radius:0;margin-top:1px;"></div>' +
            '<div style="display:flex;height:440px;">' +
                '<div class="th-shim" style="width:158px;border-radius:0;flex-shrink:0;opacity:.55;"></div>' +
                '<div style="flex:1;padding:12px;background:#f0f2f5;">' + shimRow + '</div>' +
            '</div>';
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function _thD(v)    { return (v !== null && v !== undefined && v !== '') ? v : '—'; }
function _thEsc(v)  { if (!v) return ''; return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function _thDate(v) {
    if (!v) return '—';
    var d = new Date(v); if (isNaN(d)) return v;
    return d.toLocaleDateString('en-US', {month:'2-digit', day:'2-digit', year:'numeric'});
}
function _thDT(v) {
    if (!v) return '—';
    var d = new Date(v); if (isNaN(d)) return v;
    return d.toLocaleDateString('en-US', {month:'2-digit', day:'2-digit', year:'numeric'}) +
           ' ' + d.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
}
function _thCell(label, value) {
    return '<div><div class="th-cl">' + label + '</div><div class="th-cv">' + (value || '—') + '</div></div>';
}
function _thStatCard(label, value) {
    return '<div class="th-stat-card"><div class="th-stat-lbl">' + label + '</div><div class="th-stat-val">' + value + '</div></div>';
}
function _thStatusBadge(status) {
    if (!status) return '<span style="color:#9ca3af;">—</span>';
    var sl = status.toLowerCase();
    var cls = sl.includes('complet') ? 'ok' : sl.includes('cancel') ? 'no' : sl.includes('progress') ? 'ip' : 'wa';
    return '<span class="th-bs ' + cls + '">' + _thEsc(status) + '</span>';
}
function _thSfRating(rating) {
    if (!rating) return '—';
    var r = rating.toLowerCase();
    var bg = r === 'meets' ? '#28a745' : r === 'needsimprovement' ? '#e0a800' : '#6c757d';
    return '<span style="background:' + bg + ';color:#fff;padding:2px 8px;border-radius:9px;font-size:10.5px;">' + _thEsc(rating) + '</span>';
}
function _thSbarBtn(tab, icon, label, badge) {
    var bdg = badge ? '<span class="th-sbar-bdg">' + badge + '</span>' : '';
    var act = (tab === 'overview') ? ' th-active' : '';
    return '<button class="th-sbar-btn' + act + '" data-tab="' + tab + '" onclick="_thTab(\'' + tab + '\')">' +
        '<i class="mdi ' + icon + '"></i><span>' + label + '</span>' + bdg + '</button>';
}
function _thPanel(id, content, active) {
    return '<div id="th-p-' + id + '" class="th-panel" style="display:' + (active ? 'block' : 'none') + ';">' + content + '</div>';
}

// ─── Main render ──────────────────────────────────────────────────────────────

function _thRenderDetail(data) {
    var task            = (data.task && typeof data.task === 'object') ? data.task : data;
    var patient         = (data.patient && typeof data.patient === 'object') ? data.patient : {};
    var caregiver       = (data.caregiver && typeof data.caregiver === 'object') ? data.caregiver : {};
    var taskDocs        = Array.isArray(task.documents)            ? task.documents            : [];
    var upDocs          = Array.isArray(patient.uploadedDocuments) ? patient.uploadedDocuments : [];
    var planOfCareItems = Array.isArray(data.planOfCareItems)      ? data.planOfCareItems      : [];
    var supervisoryForm = (data.supervisoryForm && typeof data.supervisoryForm === 'object') ? data.supervisoryForm : null;

    var criticalAlert = (data.criticalAlert && typeof data.criticalAlert === 'object') ? data.criticalAlert : null;
    var hasCritical   = !!(criticalAlert && criticalAlert.alert);

    var firstName  = patient.firstName  || task.patientFirstName || '';
    var lastName   = patient.lastName   || task.patientLastName  || '';
    var patName    = (firstName + ' ' + lastName).trim() || '—';
    var initials   = ((firstName[0] || '') + (lastName[0] || '')).toUpperCase() || '?';
    var status     = task.status || '';
    var taskType   = (task.type || task.taskType || '').replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    var taskId     = task.id || task.taskId || '—';
    var cert       = (task.certificationPeriod && typeof task.certificationPeriod === 'object') ? task.certificationPeriod : {};
    var certPeriod = (_thDate(cert.startDate) !== '—') ? _thDate(cert.startDate) + ' – ' + _thDate(cert.endDate) : '—';
    var phones     = Array.isArray(patient.phoneNumbers)   ? patient.phoneNumbers   : [];
    var langs      = Array.isArray(patient.languages)      ? patient.languages.filter(Boolean) : [];
    var diags      = Array.isArray(patient.diagnosisCodes) ? patient.diagnosisCodes : [];
    var primPhone  = phones.find(function(p) { return p.isPrimary; }) || phones[0] || null;
    var docCount   = taskDocs.length + upDocs.length;
    var sl         = status.toLowerCase();
    var sCls       = sl.includes('complet') ? 'ok' : sl.includes('cancel') ? 'no' : sl.includes('progress') ? 'ip' : 'wa';

    // ── Header ────────────────────────────────────────────────────────────────
    var hdr = '<div class="th-hdr">' +
        '<div class="th-avatar">' + initials + '</div>' +
        '<div class="th-hdr-info">' +
            '<div class="th-hdr-name">' + _thEsc(patName) + '</div>' +
            '<div class="th-hdr-sub">Task #' + taskId +
                (task.reviewStatus ? ' &nbsp;·&nbsp; Review: ' + _thEsc(task.reviewStatus) : '') +
            '</div>' +
        '</div>' +
        (status   ? '<span class="th-bs ' + sCls + '">' + _thEsc(status)   + '</span>' : '') +
        (taskType ? '<span class="th-bt">'              + _thEsc(taskType) + '</span>' : '') +
    '</div>';

    // ── Quick strip ───────────────────────────────────────────────────────────
    var qs = '<div class="th-qs">' +
        '<div class="th-qsi"><div class="th-qsl">Scheduled</div><div class="th-qsv">' + _thDT(task.scheduledDateTime)  + '</div></div>' +
        '<div class="th-qsi"><div class="th-qsl">DOB</div><div class="th-qsv">'       + _thDate(patient.dateOfBirth)   + '</div></div>' +
        '<div class="th-qsi"><div class="th-qsl">Gender</div><div class="th-qsv">'    + _thD(patient.gender)           + '</div></div>' +
        '<div class="th-qsi"><div class="th-qsl">Phone</div><div class="th-qsv">'     + (primPhone ? primPhone.number : '—') + '</div></div>' +
        '<div class="th-qsi"><div class="th-qsl">Service</div><div class="th-qsv">'   + _thD(task.serviceType)         + '</div></div>' +
        '<div class="th-qsi"><div class="th-qsl">Payer</div><div class="th-qsv">'     + _thD(task.payerSource)         + '</div></div>' +
    '</div>';

    // ── Sidebar ───────────────────────────────────────────────────────────────
    var caFindings     = (criticalAlert && Array.isArray(criticalAlert.findings)) ? criticalAlert.findings.filter(Boolean) : [];
    var critAlertBadge = hasCritical ? (caFindings.length ? caFindings.length : '!') : (criticalAlert ? '✓' : '');
    var critAlertExtra = hasCritical ? ' th-alert-active' : '';
    var critAlertBdg   = critAlertBadge ? '<span class="th-sbar-bdg">' + critAlertBadge + '</span>' : '';
    var critAlertBtn   = '<button class="th-sbar-btn' + critAlertExtra + '" data-tab="critical" onclick="_thTab(\'critical\')">' +
        '<i class="mdi mdi-alert-circle"></i><span>Critical Alert</span>' + critAlertBdg + '</button>';

    var sbar = '<div class="th-sbar">' +
        '<div class="th-sbar-group">Navigation</div>' +
        _thSbarBtn('overview',    'mdi-view-dashboard',    'Overview',     '') +
        _thSbarBtn('patient',     'mdi-account-circle',    'Patient Info', '') +
        _thSbarBtn('visit',       'mdi-calendar-check',    'Visit Info',   '') +
        _thSbarBtn('poc',         'mdi-clipboard-text',    'Plan of Care', planOfCareItems.length || '') +
        _thSbarBtn('supervisory', 'mdi-clipboard-account', 'Supervisory',  '') +
        _thSbarBtn('documents',   'mdi-file-multiple',     'Documents',    docCount || '') +
        critAlertBtn +
    '</div>';

    // ═══════════════════════════════════════════════════════════
    // PANEL 1 — Overview
    // ═══════════════════════════════════════════════════════════
    var pOverview = '';

    if (hasCritical) {
        pOverview += '<div class="th-warn" style="cursor:pointer;" onclick="_thTab(\'critical\')" title="Click to view Critical Alert details">' +
            '<i class="mdi mdi-alert-circle"></i>' +
            '<div style="flex:1;"><strong>Critical Alert</strong>&nbsp; <small>' + _thEsc(criticalAlert.summary || '') + '</small></div>' +
            '<small style="color:#856404;white-space:nowrap;font-weight:600;">View &rsaquo;</small>' +
        '</div>';
    }

    pOverview += '<div class="th-sec"><div class="th-sec-title">Task Summary</div>';
    pOverview += '<div class="th-stat-row">';
    pOverview += _thStatCard('Status',      status ? '<span class="th-bs ' + sCls + '" style="font-size:12px;">' + _thEsc(status) + '</span>' : '—');
    pOverview += _thStatCard('Task Type',   taskType || '—');
    pOverview += _thStatCard('Review',      _thD(task.reviewStatus));
    pOverview += '</div><div class="th-stat-row">';
    pOverview += _thStatCard('Scheduled',   _thDT(task.scheduledDateTime));
    pOverview += _thStatCard('Cert Period', certPeriod);
    pOverview += _thStatCard('Frequency',   _thD(task.frequency));
    pOverview += '</div></div>';

    pOverview += '<div class="th-sec"><div class="th-sec-title">Patient Quick View</div><div class="th-grid">';
    pOverview += _thCell('Full Name',    _thEsc(patName));
    pOverview += _thCell('Date of Birth', _thDate(patient.dateOfBirth));
    pOverview += _thCell('Gender',       _thD(patient.gender));
    pOverview += _thCell('Phone',        primPhone ? primPhone.number : '—');
    pOverview += _thCell('Language',     langs.length ? Array.from(new Set(langs)).join(', ') : '—');
    pOverview += _thCell('Start of Care', _thDate(patient.startOfCareDate));
    pOverview += '</div>';
    if (caregiver && caregiver.name) {
        pOverview += '<div style="border-top:1px solid #f0f2f5;margin-top:10px;padding-top:10px;"><div class="th-grid-2">';
        pOverview += _thCell('Caregiver',       _thD(caregiver.name));
        pOverview += _thCell('Caregiver Phone', _thD(caregiver.phoneNumber));
        pOverview += '</div></div>';
    }
    pOverview += '</div>';

    // ═══════════════════════════════════════════════════════════
    // PANEL 2 — Patient Info
    // ═══════════════════════════════════════════════════════════
    var pPatient = '';

    pPatient += '<div class="th-sec"><div class="th-sec-title">Personal Information</div><div class="th-grid">';
    pPatient += _thCell('First Name',    _thD(patient.firstName));
    pPatient += _thCell('Middle Name',   _thD(patient.middleName));
    pPatient += _thCell('Last Name',     _thD(patient.lastName));
    pPatient += _thCell('Date of Birth', _thDate(patient.dateOfBirth));
    pPatient += _thCell('Gender',        _thD(patient.gender));
    pPatient += _thCell('Start of Care', _thDate(patient.startOfCareDate));
    if (langs.length) pPatient += _thCell('Language(s)', Array.from(new Set(langs)).join(', '));
    pPatient += '</div></div>';

    pPatient += '<div class="th-sec"><div class="th-sec-title">Address</div><div class="th-grid">';
    pPatient += _thCell('Address',      _thD(patient.address));
    pPatient += _thCell('Address 2',    _thD(patient.address2));
    pPatient += _thCell('Instructions', _thD(patient.addressInstructions));
    pPatient += '</div></div>';

    pPatient += '<div class="th-sec"><div class="th-sec-title">Contact</div><div class="th-grid">';
    if (phones.length) {
        phones.forEach(function(ph) {
            pPatient += _thCell(ph.isPrimary ? '★ Primary Phone' : 'Phone', _thD(ph.number));
        });
    } else {
        pPatient += _thCell('Phone', '—');
    }
    pPatient += '</div>';
    if (caregiver && caregiver.name) {
        pPatient += '<div style="border-top:1px solid #f0f2f5;margin-top:10px;padding-top:10px;"><div class="th-grid-2">';
        pPatient += _thCell('Caregiver Name',  _thD(caregiver.name));
        pPatient += _thCell('Caregiver Phone', _thD(caregiver.phoneNumber));
        pPatient += '</div></div>';
    }
    pPatient += '</div>';

    if (diags.length) {
        pPatient += '<div class="th-sec"><div class="th-sec-title">Diagnosis Codes (' + diags.length + ')</div>';
        pPatient += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>Code</th><th>Description</th><th>Type</th><th>From</th><th>To</th></tr></thead><tbody>';
        diags.forEach(function(d) {
            var icd = (d.icd && typeof d.icd === 'object') ? d.icd : {};
            pPatient += '<tr><td><strong>' + _thD(icd.formattedDxCode) + '</strong></td>' +
                '<td><small>' + _thD(icd.description) + '</small></td>' +
                '<td>' + _thD(d.type) + '</td>' +
                '<td>' + _thDate(d.startDate) + '</td>' +
                '<td>' + _thDate(d.endDate)   + '</td></tr>';
        });
        pPatient += '</tbody></table></div></div>';
    }

    // ═══════════════════════════════════════════════════════════
    // PANEL 3 — Visit Info
    // ═══════════════════════════════════════════════════════════
    var pVisit = '';

    pVisit += '<div class="th-sec"><div class="th-sec-title">Schedule & Service</div><div class="th-grid">';
    pVisit += _thCell('Scheduled Date',       _thDT(task.scheduledDateTime));
    pVisit += _thCell('Created Date',         _thDT(task.createdAt));
    pVisit += _thCell('Service Type',         _thD(task.serviceType));
    pVisit += _thCell('Certification Period', certPeriod);
    pVisit += _thCell('Payer Source',         _thD(task.payerSource));
    pVisit += _thCell('Frequency',            _thD(task.frequency));
    pVisit += '</div></div>';

    if (task.agencyNote || task.interpretation) {
        pVisit += '<div class="th-sec"><div class="th-sec-title">Notes & Instructions</div>' +
            '<div style="display:flex;flex-direction:column;gap:12px;">';
        if (task.agencyNote) {
            pVisit += '<div><div class="th-cl">Agency Note</div>' +
                '<div class="th-cv" style="white-space:pre-wrap;">' + _thEsc(task.agencyNote) + '</div></div>';
        }
        if (task.interpretation) {
            pVisit += '<div><div class="th-cl">Interpretation</div>' +
                '<div class="th-cv" style="white-space:pre-wrap;">' + _thEsc(task.interpretation) + '</div></div>';
        }
        pVisit += '</div></div>';
    }

    if (hasCritical) {
        pVisit += '<div class="th-warn" style="cursor:pointer;" onclick="_thTab(\'critical\')" title="Click to view Critical Alert details">' +
            '<i class="mdi mdi-alert-circle"></i>' +
            '<div style="flex:1;"><strong>Critical Alert</strong><br><small>' + _thEsc(criticalAlert.summary || '') + '</small></div>' +
            '<small style="color:#856404;white-space:nowrap;font-weight:600;">View &rsaquo;</small>' +
        '</div>';
    }

    // ═══════════════════════════════════════════════════════════
    // PANEL 4 — Plan of Care
    // ═══════════════════════════════════════════════════════════
    var pPoc = '';

    if (planOfCareItems.length) {
        pPoc += '<div class="th-sec"><div class="th-sec-title">Plan of Care Items (' + planOfCareItems.length + ')</div>';
        pPoc += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>Code</th><th>Task</th><th>Frequency</th><th>Notes</th></tr></thead><tbody>';
        planOfCareItems.forEach(function(item) {
            pPoc += '<tr><td>' + _thD(item.code) + '</td>' +
                '<td><strong>' + _thD(item.name) + '</strong></td>' +
                '<td>' + _thD(item.frequency) + '</td>' +
                '<td>' + _thD(item.notes) + '</td></tr>';
        });
        pPoc += '</tbody></table></div></div>';
    } else {
        pPoc = '<div class="th-sec"><div class="th-empty">' +
            '<i class="mdi mdi-clipboard-text-outline"></i><p>No plan of care items found.</p></div></div>';
    }

    // ═══════════════════════════════════════════════════════════
    // PANEL 5 — Supervisory Form
    // ═══════════════════════════════════════════════════════════
    var pSuper = '';

    if (supervisoryForm) {
        var sf           = supervisoryForm;
        var pocTasks     = Array.isArray(sf.pocTasks)     ? sf.pocTasks     : [];
        var competencies = Array.isArray(sf.competencies) ? sf.competencies : [];

        pSuper += '<div class="th-sec"><div class="th-sec-title">Supervisory Overview</div><div class="th-grid">';
        pSuper += _thCell('Aide Present',
            sf.isAidePresent === true  ? '<span style="color:#28a745;font-weight:700;">Yes</span>' :
            sf.isAidePresent === false ? '<span style="color:#dc3545;font-weight:700;">No</span>'  : '—');
        pSuper += _thCell('Visit Date',     _thDate(sf.visitDate));
        pSuper += _thCell('Observed Tasks', _thD(sf.observedTasks));
        pSuper += '</div></div>';

        if (pocTasks.length) {
            pSuper += '<div class="th-sec"><div class="th-sec-title">POC Tasks (' + pocTasks.length + ')</div>';
            pSuper += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>Task</th><th>Rating</th></tr></thead><tbody>';
            pocTasks.forEach(function(t) {
                pSuper += '<tr><td>' + _thD(t.name) + '</td><td>' + _thSfRating(t.rating) + '</td></tr>';
            });
            pSuper += '</tbody></table></div></div>';
        }

        if (competencies.length) {
            pSuper += '<div class="th-sec"><div class="th-sec-title">Competencies (' + competencies.length + ')</div>';
            pSuper += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>Competency</th><th>Rating</th></tr></thead><tbody>';
            competencies.forEach(function(c) {
                pSuper += '<tr><td>' + _thD(c.name) + '</td><td>' + _thSfRating(c.rating) + '</td></tr>';
            });
            pSuper += '</tbody></table></div></div>';
        }

        if (sf.supervisorComments) {
            pSuper += '<div class="th-sec"><div class="th-sec-title">Supervisor Comments</div>' +
                '<div style="font-size:12.5px;white-space:pre-wrap;color:#1f2937;line-height:1.6;">' +
                _thEsc(sf.supervisorComments) + '</div></div>';
        }
    } else {
        pSuper = '<div class="th-sec"><div class="th-empty">' +
            '<i class="mdi mdi-clipboard-account-outline"></i><p>No supervisory form data available.</p></div></div>';
    }

    // ═══════════════════════════════════════════════════════════
    // PANEL 6 — Documents
    // ═══════════════════════════════════════════════════════════
    var pDocs = '';

    if (!taskDocs.length && !upDocs.length) {
        pDocs = '<div class="th-sec"><div class="th-empty">' +
            '<i class="mdi mdi-file-document-outline"></i><p>No documents available.</p></div></div>';
    }

    if (taskDocs.length) {
        pDocs += '<div class="th-sec"><div class="th-sec-title">Task Documents (' + taskDocs.length + ')</div>';
        pDocs += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>#</th><th>Title</th><th>Status</th><th>Submitted</th><th>View</th></tr></thead><tbody>';
        taskDocs.forEach(function(doc, i) {
            var dtype = (doc.type && typeof doc.type === 'object') ? doc.type : {};
            var sc    = (doc.status || '').toLowerCase();
            var bgc   = sc === 'completed' ? '#28a745' : sc === 'rejected' ? '#dc3545' : '#e0a800';
            var vBtn  = doc.url
                ? '<a href="' + doc.url + '" target="_blank" style="background:#17a2b8;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;text-decoration:none;display:inline-block;">' +
                  '<i class="mdi mdi-file-pdf"></i> View</a>'
                : '—';
            pDocs += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td><strong>' + _thD(dtype.title) + '</strong></td>' +
                '<td><span style="background:' + bgc + ';color:#fff;padding:2px 8px;border-radius:9px;font-size:10.5px;">' + _thD(doc.status) + '</span></td>' +
                '<td>' + _thDT(doc.submittedAt) + '</td>' +
                '<td>' + vBtn + '</td></tr>';
        });
        pDocs += '</tbody></table></div></div>';
    }

    if (upDocs.length) {
        pDocs += '<div class="th-sec"><div class="th-sec-title">Uploaded Documents (' + upDocs.length + ')</div>';
        pDocs += '<div style="overflow-x:auto;"><table class="th-tbl"><thead><tr><th>#</th><th>Name</th><th>View</th></tr></thead><tbody>';
        upDocs.forEach(function(doc, i) {
            var nm  = typeof doc === 'object' ? (doc.name || '—') : doc;
            var url = typeof doc === 'object' ? (doc.url  || '')  : '';
            pDocs += '<tr><td>' + (i + 1) + '</td><td>' + _thEsc(nm) + '</td><td>' +
                (url ? '<a href="' + url + '" target="_blank" style="background:#17a2b8;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;text-decoration:none;display:inline-block;">' +
                       '<i class="mdi mdi-file-pdf"></i> View</a>' : '—') +
                '</td></tr>';
        });
        pDocs += '</tbody></table></div></div>';
    }

    // ═══════════════════════════════════════════════════════════
    // PANEL 7 — Critical Alert
    // ═══════════════════════════════════════════════════════════
    var pCritical = '';

    if (!criticalAlert) {
        // null → not yet analyzed by AI
        pCritical = '<div class="th-sec">' +
            '<div class="th-ca-hero th-ca-na">' +
                '<div class="th-ca-hero-icon"><i class="mdi mdi-clock-outline"></i></div>' +
                '<div class="th-ca-hero-text">' +
                    '<div class="th-ca-hero-status">NOT YET ANALYZED</div>' +
                    '<div class="th-ca-hero-sub">AI alert assessment has not been completed for this visit</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    } else if (hasCritical) {
        // alert === true → active critical alert
        pCritical += '<div class="th-ca-hero th-ca-critical">' +
            '<div class="th-ca-hero-icon"><i class="mdi mdi-alert-circle"></i></div>' +
            '<div class="th-ca-hero-text">' +
                '<div class="th-ca-hero-status">CRITICAL ALERT ACTIVE</div>' +
                '<div class="th-ca-hero-sub">This visit has been flagged with a critical clinical alert</div>' +
            '</div>' +
        '</div>';

        if (criticalAlert.summary) {
            pCritical += '<div class="th-ca-summary">' +
                '<div class="th-ca-summary-lbl"><i class="mdi mdi-text-box-outline mr-1"></i>Summary</div>' +
                '<div class="th-ca-summary-text">' + _thEsc(criticalAlert.summary) + '</div>' +
            '</div>';
        }

        if (caFindings.length) {
            pCritical += '<div class="th-sec" style="border-color:#f5c6cb;">' +
                '<div class="th-sec-title" style="color:#721c24;">' +
                    '<i class="mdi mdi-flag-variant" style="margin-right:5px;"></i>Findings' +
                    '<span style="margin-left:6px;background:#dc3545;color:#fff;border-radius:9px;font-size:10px;padding:1px 7px;font-weight:700;">' + caFindings.length + '</span>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:7px;">';
            caFindings.forEach(function(f, i) {
                pCritical += '<div style="display:flex;align-items:flex-start;gap:10px;background:#fff5f5;border:1px solid #f5c6cb;border-radius:6px;padding:9px 12px;">' +
                    '<div style="min-width:22px;height:22px;border-radius:50%;background:#dc3545;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">' + (i + 1) + '</div>' +
                    '<div style="font-size:12.5px;color:#1f2937;line-height:1.6;">' + _thEsc(f) + '</div>' +
                '</div>';
            });
            pCritical += '</div></div>';
        }
    } else {
        // alert === false → analyzed, all clear
        pCritical += '<div class="th-ca-hero th-ca-clear">' +
            '<div class="th-ca-hero-icon"><i class="mdi mdi-check-circle"></i></div>' +
            '<div class="th-ca-hero-text">' +
                '<div class="th-ca-hero-status">ALL CLEAR</div>'+
            '</div>' +
        '</div>';

        if (criticalAlert.summary) {
            pCritical += '<div class="th-ca-summary">' +
                '<div class="th-ca-summary-lbl">Summary</div>' +
                '<div class="th-ca-summary-text">' + _thEsc(criticalAlert.summary) + '</div>' +
            '</div>';
        }

        if (caFindings.length) {
            pCritical += '<div class="th-sec">' +
                '<div class="th-sec-title">' +
                    '<i class="mdi mdi-flag-check" style="margin-right:5px;"></i>Findings' +
                    '<span style="margin-left:6px;background:#28a745;color:#fff;border-radius:9px;font-size:10px;padding:1px 7px;font-weight:700;">' + caFindings.length + '</span>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:7px;">';
            caFindings.forEach(function(f, i) {
                pCritical += '<div style="display:flex;align-items:flex-start;gap:10px;background:#f0fff4;border:1px solid #c3e6cb;border-radius:6px;padding:9px 12px;">' +
                    '<div style="min-width:22px;height:22px;border-radius:50%;background:#28a745;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">' + (i + 1) + '</div>' +
                    '<div style="font-size:12.5px;color:#1f2937;line-height:1.6;">' + _thEsc(f) + '</div>' +
                '</div>';
            });
            pCritical += '</div></div>';
        }
    }

    // ── Assemble layout ───────────────────────────────────────────────────────
    return hdr + qs +
        '<div class="th-layout">' +
            sbar +
            '<div class="th-panels">' +
                _thPanel('overview',    pOverview,  true)  +
                _thPanel('patient',     pPatient,   false) +
                _thPanel('visit',       pVisit,     false) +
                _thPanel('poc',         pPoc,       false) +
                _thPanel('supervisory', pSuper,     false) +
                _thPanel('documents',   pDocs,      false) +
                _thPanel('critical',    pCritical,  false) +
            '</div>' +
        '</div>';
}

// ─── End Task Health visit detail functions ───────────────────────────────────

function searchTaskHealthPatient() {
    var firstName = $('#th_search_first_name').val().trim();
    var lastName = $('#th_search_last_name').val().trim();
    var patientCode = $('#th_search_patient_code').val().trim();
    var phone = $('#th_search_phone').val().trim();

    if (firstName === '' && lastName === '' && patientCode === '' && phone === '') {
        toastr.warning('Please enter at least one search criteria');
        return false;
    }

    $('#th_search_results_section').show();
    $('#th_search_results_loader').show();
    $('#th_search_results').html('');
    $('#th_search_error').html('');

    $('#btn_th_search').addClass('disabled').css('pointer-events', 'none');
    $('#btn_th_search_icon').addClass('d-none');
    $('#btn_th_search_spinner').removeClass('d-none');
    $('#btn_th_search_text').text(' Searching...');

    $.ajax({
        type: 'GET',
        url: _SEARCH_TASK_HEALTH_PATIENT,
        data: {
            'first_name': firstName,
            'last_name': lastName,
            'patient_code': patientCode,
            'phone': phone,
            'agency_id': _AGENCYID
        },
        success: function(res) {
            $('#btn_th_search').removeClass('disabled').css('pointer-events', '');
            $('#btn_th_search_icon').removeClass('d-none');
            $('#btn_th_search_spinner').addClass('d-none');
            $('#btn_th_search_text').text(' Search');
            $('#th_search_results_loader').hide();

            var tableResponse = '';
            if (res.data && res.data.length > 0) {
                var cnt = 1;
                $.each(res.data, function(i, v) {
                    var fullName = (v.first_name || '') + ' ' + (v.last_name || '');
                    tableResponse += '<tr>' +
                        '<td class="small">' + cnt++ + '</td>' +
                        '<td class="small">' + v.id + '</td>' +
                        '<td class="small">' + fullName.trim() + '</td>' +
                        '<td class="small">' + (v.patient_code || '') + '</td>' +
                        '<td class="small"><input type="radio" name="th_patient_radio" value="' + v.id + '"' +
                        ' data-name="' + fullName.trim() + '" data-code="' + (v.id || '') + '"></td>' +
                        '</tr>';
                });
                $('#th_search_results').html(tableResponse);
            } else {
                $('#th_search_results').html('<tr><td colspan="5" class="text-center small">No record available</td></tr>');
            }
        },
        error: function(jqr) {
            $('#btn_th_search').removeClass('disabled').css('pointer-events', '');
            $('#btn_th_search_icon').removeClass('d-none');
            $('#btn_th_search_spinner').addClass('d-none');
            $('#btn_th_search_text').text(' Search');
            $('#th_search_results_loader').hide();
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function saveTaskHealthLink() {
    var checked = $('input[name="th_patient_radio"]').is(':checked');
    $('#th_search_error').html('');
    if (!checked) {
        $('#th_search_error').html('Please select a Task Health patient');
        return false;
    }

    var selected = $('input[name="th_patient_radio"]:checked');
    $.confirm({
        title: 'Link Task Health Patient',
        columnClass: 'col-md-6',
        content: 'Are you sure you want to link this Task Health patient?',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-success',
                action: function() {
                    $('#btn_th_save_spinner').removeClass('d-none');
                    $('#btn_th_save_text').text('Saving...');
                    $.ajax({
                        type: 'POST',
                        url: _LINK_TASK_HEALTH_PATIENT,
                        data: {
                            'patient_id': _RECORD_ID,
                            'task_health_id': selected.val(),
                            '_token': _CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            var displayName = selected.attr('data-name') + ' ( ' + selected.attr('data-code') + ' )';
                            $('#task_health_patient_display').html(displayName);
                            $('#task_health_patient_id').val(selected.val());
                            $('#task_health_patient_name').val(displayName);
                            $('#task_health_patient_link_id').removeClass('hide');
                            $('#btn_th_save_spinner').addClass('d-none');
                            $('#btn_th_save_text').text('Save');
                            $('#closeTaskHealthModal').click();
                            location.reload();
                        },
                        error: function(jqr) {
                            $('#btn_th_save_spinner').addClass('d-none');
                            $('#btn_th_save_text').text('Save');
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function() {}
        }
    });
}

function unlinkTaskHealthPatient() {
    var taskHealthId = $('#task_health_patient_id').val();
    if (!taskHealthId) {
        return false;
    }
    $.confirm({
        title: 'Unlink Task Health Patient',
        content: 'Are you sure you want to unlink this Task Health patient?',
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function() {
                    $.ajax({
                        type: 'POST',
                        url: _UNLINK_TASK_HEALTH_PATIENT,
                        data: {
                            'patient_id': _RECORD_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            $('#task_health_patient_id').val('');
                            $('#task_health_patient_name').val('');
                            $('#task_health_patient_display').html('N/A');
                            $('#task_health_patient_link_id').addClass('hide');
                            location.reload();
                        },
                        error: function(jqr) {
                            toastr.error(jqr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {}
        }
    });
}

$('#exampleModal-link-task-health-patient').on('hidden.bs.modal', function() {
    $('#form_task_health_search')[0].reset();
    $('#th_search_results_section').hide();
    $('#th_search_results').html('');
    $('#th_search_error').html('');
});

// ── Patient Critical Alerts ──────────────────────────────────────────────────

var _patientCaLoaded = false;

function loadPatientCriticalAlerts() {
    $('#patient-ca-loader').show();
    $('#patient-ca-container').html('');

    $.ajax({
        url: _PATIENT_TASK_HEALTH_CA,
        type: 'GET',
        data: { patient_id: _RECORD_ID },
        success: function(html) {
            $('#patient-ca-loader').hide();
            $('#patient-ca-container').html(html);
            _patientCaLoaded = true;
        },
        error: function() {
            $('#patient-ca-loader').hide();
            $('#patient-ca-container').html('<div class="alert alert-danger mt-2">Failed to load critical alerts. Please try again.</div>');
        }
    });
}

function loadPatientCriticalAlertsInline() {
    var $section = $('#patient-ca-inline-section');
    if (!$section.length) { return; }

    $section.html('<div class="text-center py-2"><img src="/ajax-loader.gif" alt="Loading..."></div>');

    $.ajax({
        url: _PATIENT_TASK_HEALTH_CA,
        type: 'GET',
        data: { patient_id: _RECORD_ID, inline: 1 },
        success: function(html) {
            $section.html(html);
        },
        error: function() {
            $section.html('');
        }
    });
}

$(function() {
    if ($('#patient-ca-inline-section').length) {
        loadPatientCriticalAlertsInline();
    }
});

function openPatientCaResolve(id) {
    $('#ca-resolve-id').val(id);
    $('#ca-resolve-notes').val('');
    $('#caResolveModal').modal('show');
}

$(document).on('click', '#ca-resolve-save-btn', function() {
    var id    = $('#ca-resolve-id').val();
    var notes = $('#ca-resolve-notes').val();
    var $btn  = $(this);

    $btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url:  _TASK_HEALTH_CA_RESOLVE_BASE + '/' + id + '/resolve',
        type: 'POST',
        data: { _token: _CSRF_TOKEN, notes: notes },
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Save');
            $('#caResolveModal').modal('hide');
            toastr.success('Alert marked as resolved.');
            loadPatientCriticalAlertsInline();
            if (_patientCaLoaded) { loadPatientCriticalAlerts(); }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Save');
            toastr.error('Failed to resolve alert. Please try again.');
        }
    });
});
