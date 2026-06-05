<style>
    /* ── Overlay ──────────────────────────────────────────────────── */
    .call-details-view-modal {
        position: fixed;
        z-index: 1050;
        inset: 0;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 20, 40, 0);
        pointer-events: none;
        transition: background 0.28s ease;
    }
    .call-details-view-modal.show {
        background: rgba(15, 20, 40, 0.62);
        pointer-events: auto;
        overflow-y: auto;
    }

    /* ── Modal box ────────────────────────────────────────────────── */
    .call-details-modal-content {
        background: #fff;
        border-radius: 12px;
        width: 100%;
        max-width: 1200px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 64px rgba(0,0,0,0.22);
        transform: translateY(32px) scale(0.97);
        opacity: 0;
        transition: transform 0.30s cubic-bezier(0.34,1.30,0.64,1), opacity 0.25s ease;
        margin: auto;
        flex-shrink: 0;
    }
    .call-details-view-modal.show .call-details-modal-content {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* ── Header ───────────────────────────────────────────────────── */
    .call-details-modal-header {
        background: linear-gradient(135deg, #1e1e2f 0%, #2d2d50 100%);
        color: #fff;
        padding: 18px 24px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .call-details-modal-header h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .call-details-modal-close {
        font-size: 26px;
        font-weight: 700;
        color: rgba(255,255,255,0.7);
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 32px;
        height: 32px;
        line-height: 1;
        border-radius: 6px;
        transition: color 0.15s, background 0.15s;
        display: flex; align-items: center; justify-content: center;
    }
    .call-details-modal-close:hover { color: #fff; background: rgba(255,255,255,0.15); }

    /* ── Body layout ──────────────────────────────────────────────── */
    .call-details-modal-body {
        padding: 0;
        overflow: hidden;
        flex: 1;
        display: flex;
        min-height: 0;
    }

    /* ── Sidebar tabs ─────────────────────────────────────────────── */
    .call-details-tabs {
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e8ecf3;
        background: #f7f9fc;
        width: 210px;
        flex-shrink: 0;
        overflow-y: auto;
        padding: 12px 0;
    }
    .call-details-tab-button {
        padding: 13px 18px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        border-left: 3px solid transparent;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.15s, background 0.15s, border-color 0.15s;
        border-radius: 0 8px 8px 0;
        margin: 1px 8px 1px 0;
    }
    .call-details-tab-button:hover { background: #eef2f9; color: #2563eb; }
    .call-details-tab-button.active { color: #2563eb; border-left-color: #2563eb; background: #eff6ff; }
    .call-details-tab-button i { font-size: 16px; min-width: 18px; }

    /* ── Content area ─────────────────────────────────────────────── */
    .call-details-tab-content-wrapper {
        flex: 1;
        overflow-y: auto;
        background: #f9fafc;
        min-width: 0;
    }
    .call-details-tab-content {
        display: none;
        padding: 24px;
        min-height: 100%;
    }
    .call-details-tab-content.active { display: block; }

    /* ── Shimmer ──────────────────────────────────────────────────── */
    .cd-shimmer-wrapper { padding: 4px 0; }
    .cd-shimmer-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .cd-shimmer {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: cdShimmer 1.5s infinite;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .cd-shimmer.title     { height: 20px; width: 40%; margin-bottom: 16px; }
    .cd-shimmer.long      { height: 13px; width: 100%; }
    .cd-shimmer.medium    { height: 13px; width: 70%; }
    .cd-shimmer.short     { height: 13px; width: 45%; }
    .cd-shimmer.header    { height: 52px; border-radius: 10px; margin-bottom: 16px; }
    .cd-shimmer.table-row { height: 34px; margin-bottom: 5px; }
    @keyframes cdShimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ── Hero banner ──────────────────────────────────────────────── */
    .call-detail-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 14px 20px;
        border-radius: 10px;
        color: #fff;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .call-detail-hero-icon {
        width: 42px; height: 42px;
        background: rgba(255,255,255,0.18);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .call-detail-hero h4 { margin: 0; color: #fff; font-size: 16px; font-weight: 700; }
    .call-detail-hero small { color: rgba(255,255,255,0.85); font-size: 12px; }

    /* ── Info panel ───────────────────────────────────────────────── */
    .call-detail-panel {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        margin-bottom: 14px;
        overflow: hidden;
    }
    .call-detail-panel-header {
        padding: 11px 16px;
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        background: #f8faff;
    }
    .call-detail-panel-header.green { background: #f0fdf4; border-bottom-color: #d1fae5; }
    .call-detail-panel-body { padding: 14px 16px; }

    .call-detail-info-grid {
        display: grid;
        grid-template-columns: 110px 1fr;
        row-gap: 8px;
        column-gap: 12px;
        font-size: 13px;
    }
    .call-detail-info-label { color: #64748b; font-weight: 600; }

    /* ── Filter form ──────────────────────────────────────────────── */
    .call-detail-filter label { color: #64748b; font-size: 11px; font-weight: 700; letter-spacing: 0.4px; text-transform: uppercase; margin-bottom: 4px; display: block; }
    .call-detail-filter .form-control-sm { border-radius: 6px; border-color: #d1d5db; font-size: 13px; }
    .call-detail-filter .form-control-sm:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }

    /* ── Search button loading state ──────────────────────────────── */
    .cd-search-btn { position: relative; min-width: 90px; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; }
    .cd-search-btn .cd-btn-text { transition: opacity 0.15s; }
    .cd-search-btn .cd-btn-spinner { display: none; position: absolute; inset: 0; align-items: center; justify-content: center; }
    .cd-search-btn.loading .cd-btn-text { opacity: 0; }
    .cd-search-btn.loading .cd-btn-spinner { display: flex; }
    .cd-spinner-ring {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: cdSpin 0.7s linear infinite;
    }
    @keyframes cdSpin { to { transform: rotate(360deg); } }

    /* ── CDR Table ────────────────────────────────────────────────── */
    .call-detail-table { margin-bottom: 0; font-size: 12.5px; }
    .call-detail-table thead th {
        background: #f8faff;
        color: #374151;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        white-space: nowrap;
        border-bottom: 2px solid #e5e7eb;
        padding: 10px 12px;
    }
    .call-detail-table tbody td { padding: 9px 12px; vertical-align: middle; border-color: #f1f5f9; }
    .call-detail-table tbody tr { transition: background 0.12s; }
    .call-detail-table tbody tr:hover { background: #f8faff; }

    /* Row fade-in animation */
    .cdr-row-animate {
        animation: cdrRowIn 0.22s ease both;
    }
    @keyframes cdrRowIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Badges ───────────────────────────────────────────────────── */
    .cdr-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
    }
    .cdr-badge.inbound  { background: #dcfce7; color: #15803d; }
    .cdr-badge.outbound { background: #dbeafe; color: #1d4ed8; }
    .cdr-badge.missed   { background: #fef9c3; color: #92400e; }
    .cdr-badge.unknown  { background: #f3f4f6; color: #6b7280; }

    /* ── Empty state ──────────────────────────────────────────────── */
    .cdr-empty-state {
        padding: 48px 20px;
        text-align: center;
        color: #94a3b8;
    }
    .cdr-empty-state .cdr-empty-icon {
        width: 64px; height: 64px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px; color: #cbd5e1;
    }
    .cdr-empty-state h6 { color: #475569; font-weight: 600; margin-bottom: 6px; }
    .cdr-empty-state p  { font-size: 13px; margin: 0; }

    /* ── Pagination ───────────────────────────────────────────────── */
    .cdr-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .cdr-pagination .btn { border-radius: 6px !important; font-size: 12px !important; font-weight: 600 !important; }

    /* ── Responsive ───────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .call-details-modal-body { flex-direction: column; }
        .call-details-tabs {
            width: 100%; flex-direction: row; overflow-x: auto;
            border-right: 0; border-bottom: 1px solid #e8ecf3; padding: 0;
        }
        .call-details-tab-button {
            border-left: 0; border-bottom: 3px solid transparent; border-radius: 0;
            padding: 12px 16px; white-space: nowrap; flex: 1; justify-content: center;
        }
        .call-details-tab-button.active { border-bottom-color: #2563eb; background: #fff; }
        .call-details-tab-content { padding: 16px; }
        .call-detail-info-grid { grid-template-columns: 1fr; row-gap: 4px; }
    }
</style>

<div id="callDetailsModal" class="call-details-view-modal" aria-labelledby="callDetailsModalLabel" aria-hidden="true">
    <div class="call-details-modal-content">
        <div class="call-details-modal-header">
            <h4 id="callDetailsModalLabel">
                <i class="fa fa-phone-square mr-2" style="opacity:0.8;"></i>
                Call Details — <span id="callDetailsPatientName">Loading…</span>
            </h4>
            <button type="button" class="call-details-modal-close" onclick="closeCallDetailsModal()" aria-label="Close">&times;</button>
        </div>

        <div class="call-details-modal-body">
            <div class="call-details-tabs" role="tablist">
                <button type="button" class="call-details-tab-button active" role="tab" data-tab="call-details-panel">
                    <i class="fa fa-phone"></i>
                    <span>Call Details</span>
                </button>
                {{-- <button type="button" class="call-details-tab-button" role="tab" data-tab="messages-panel">
                    <i class="fa fa-comments"></i><span>Messages</span>
                </button> --}}
            </div>

            <div class="call-details-tab-content-wrapper">
                <div class="call-details-tab-content active" id="call-details-panel" role="tabpanel"></div>
                <div class="call-details-tab-content" id="messages-panel" role="tabpanel"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ── State ──────────────────────────────────────────────────────────────
    var cdrAllRecords  = [], cdrCurrentPage = 1, cdrPageSize = 25;
    var msgAllRecords  = [], msgCurrentPage = 1, msgPageSize = 25;
    var cdrModalUrl = '', messagesModalUrl = '', messagesLoaded = false;

    // ── Shimmer ────────────────────────────────────────────────────────────
    function cdShimmerHtml() {
        var rows = '';
        for (var i = 0; i < 8; i++) rows += '<div class="cd-shimmer table-row"></div>';
        return `<div class="cd-shimmer-wrapper">
            <div class="cd-shimmer-card">
                <div class="cd-shimmer header"></div>
                <div class="row"><div class="col-md-4">
                    <div class="cd-shimmer title"></div>
                    <div class="cd-shimmer long"></div><div class="cd-shimmer medium"></div><div class="cd-shimmer short"></div>
                </div><div class="col-md-8">
                    <div class="cd-shimmer title"></div>
                    <div class="cd-shimmer long"></div><div class="cd-shimmer medium"></div>
                </div></div>
            </div>
            <div class="cd-shimmer-card">${rows}</div>
        </div>`;
    }

    // ── Modal open / close ─────────────────────────────────────────────────
    window.openCallDetailsModal = function () {
        var $modal = $('#callDetailsModal');
        $modal.css('display', 'flex').attr('aria-hidden', 'false');
        $('body').css('overflow', 'hidden');
        // tiny delay so CSS transition fires after display:flex
        requestAnimationFrame(function () { $modal.addClass('show'); });
    };

    window.closeCallDetailsModal = function () {
        var $modal = $('#callDetailsModal');
        $modal.removeClass('show').attr('aria-hidden', 'true');
        $('body').css('overflow', '');
        setTimeout(function () { $modal.css('display', ''); }, 300);
    };

    // ── HTML escape ────────────────────────────────────────────────────────
    window.escapeCallDetailHtml = function (v) {
        if (v === null || v === undefined || v === '') return '-';
        return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    };
    var e = window.escapeCallDetailHtml;

    // ── Search button helpers ──────────────────────────────────────────────
    function setSearchLoading($form, loading) {
        $form.find('.cd-search-btn').toggleClass('loading', loading).prop('disabled', loading);
    }

    // ── Load CDR data ──────────────────────────────────────────────────────
    function loadCallDetailsModal(url, params, $form) {
        $('#call-details-panel').html(cdShimmerHtml());
        if ($form) setSearchLoading($form, true);

        $.ajax({
            url: url, type: 'GET', data: params || {},
            success: function (res) {
                if ($form) setSearchLoading($form, false);
                if (res.status == 1) {
                    var d = res.data; var f = (d.filters || {});
                    $('#call-details-panel').html(renderCallDetailsContent(d, url));
                    window.renderCdrPage();
                    initModalDateRangePicker('callDetailsCdrRangePicker', 'callDetailsCdrStartDate', 'callDetailsCdrEndDate', f.start_date, f.end_date);
                } else {
                    $('#call-details-panel').html(errorBox(res.error_msg || 'Unable to load call details.'));
                }
            },
            error: function (xhr) {
                if ($form) setSearchLoading($form, false);
                var msg = (xhr.responseJSON && xhr.responseJSON.error_msg) ? xhr.responseJSON.error_msg : 'Unable to load call details. Please try again.';
                $('#call-details-panel').html(errorBox(msg));
            }
        });
    }

    function errorBox(msg) {
        return '<div class="alert alert-danger m-3"><i class="fa fa-exclamation-triangle mr-2"></i>' + e(msg) + '</div>';
    }

    // ── Daterangepicker initializer ────────────────────────────────────────
    function initModalDateRangePicker(pickerInputId, startHiddenId, endHiddenId, startVal, endVal) {
        if (typeof $.fn.daterangepicker === 'undefined' || typeof moment === 'undefined') return;
        var defaultStart = (startVal && moment(startVal).isValid()) ? moment(startVal) : moment().subtract(1, 'days').startOf('day');
        var defaultEnd   = (endVal   && moment(endVal).isValid())   ? moment(endVal)   : moment().endOf('day');
        var $input = $('#' + pickerInputId);
        $input.daterangepicker({
            startDate: defaultStart, endDate: defaultEnd,
            autoUpdateInput: false, autoApply: true, startOfWeek: 'sunday',
            locale: { format: 'MM/DD/YYYY' },
            ranges: {
                'Today':        [moment(), moment()],
                'Yesterday':    [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month':   [moment().startOf('month'), moment().endOf('month')],
                'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Next Month':   [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                'Next Week':    [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
                'Last Week':    [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')]
            }
        });
        syncModalPicker($input, defaultStart, defaultEnd, startHiddenId, endHiddenId);
        $input.off('apply.daterangepicker').on('apply.daterangepicker', function (ev, picker) {
            syncModalPicker($(this), picker.startDate, picker.endDate, startHiddenId, endHiddenId);
        });
    }

    function syncModalPicker($input, start, end, startHiddenId, endHiddenId) {
        $input.val(start.format('MM/DD/YYYY') + ' – ' + end.format('MM/DD/YYYY'));
        $('#' + startHiddenId).val(start.format('YYYY-MM-DD'));
        $('#' + endHiddenId).val(end.format('YYYY-MM-DD'));
    }

    // ── CDR page render ────────────────────────────────────────────────────
    window.renderCdrPage = function () {
        var total = cdrAllRecords.length;
        var totalPages = Math.max(1, Math.ceil(total / cdrPageSize));
        cdrCurrentPage = Math.min(Math.max(cdrCurrentPage, 1), totalPages);

        var start     = (cdrCurrentPage - 1) * cdrPageSize;
        var pageSlice = cdrAllRecords.slice(start, start + cdrPageSize);

        var $tbody = $('#cdrTableBody');

        if (pageSlice.length === 0) {
            $tbody.html(`<tr><td colspan="12">
                <div class="cdr-empty-state">
                    <div class="cdr-empty-icon"><i class="fa fa-phone-slash"></i></div>
                    <h6>No call records found</h6>
                    <p>Try expanding the date range or check the patient's phone number.</p>
                </div>
            </td></tr>`);
        } else {
            var rows = '';
            $.each(pageSlice, function (i, call) {
                var badge = call.type == 0
                    ? '<span class="cdr-badge outbound"><i class="fa fa-arrow-up"></i> Outbound</span>'
                    : (call.type == 1
                        ? '<span class="cdr-badge inbound"><i class="fa fa-arrow-down"></i> Inbound</span>'
                        : (call.type == 2
                            ? '<span class="cdr-badge missed"><i class="fa fa-times"></i> Missed</span>'
                            : '<span class="cdr-badge unknown">Unknown</span>'));
                rows += `<tr class="cdr-row-animate" style="animation-delay:${i * 28}ms">
                    <td><span class="text-muted">${start + i + 1}</span></td>
                    <td><span style="font-size:12px;white-space:nowrap;">${e(call.time_start)}</span></td>
                    <td>${badge}</td>
                    <td>${e(call.caller_name)}</td>
                    <td><code style="font-size:11px;">${e(call.caller_number)}</code></td>
                    <td><code style="font-size:11px;">${e(call.dialed_number)}</code></td>
                    <td>${e(call.extension)}</td>
                    <td>${e(call.duration_fmt)}</td>
                    <td>${e(call.talk_time_fmt)}</td>
                    <td>${e(call.by_action)}</td>
                    <td>${e(call.release_text)}</td>
                    <td>${e(call.codec)}</td>
                </tr>`;
            });
            $tbody.html(rows);
        }

        var from = total > 0 ? start + 1 : 0;
        var to   = Math.min(start + cdrPageSize, total);
        $('#cdrPaginationInfo').text(total > 0 ? 'Showing ' + from + '–' + to + ' of ' + total + ' records' : '');
        $('#cdrPageInfo').text('Page ' + cdrCurrentPage + ' of ' + totalPages);
        $('#cdrPrevBtn').prop('disabled', cdrCurrentPage <= 1);
        $('#cdrNextBtn').prop('disabled', cdrCurrentPage >= totalPages);
    };

    // ── CDR content builder ────────────────────────────────────────────────
    function renderCallDetailsContent(data, url) {
        var patient  = data.patient  || {};
        var filters  = data.filters  || {};
        var errorHtml = data.error_message ? errorBox(data.error_message) : '';

        cdrAllRecords  = data.call_details || [];
        cdrCurrentPage = 1;

        var totalBadgeColor = cdrAllRecords.length > 0 ? '#22c55e' : '#94a3b8';

        return `
        <div class="call-detail-hero">
            <div class="call-detail-hero-icon"><i class="fa fa-phone"></i></div>
            <div>
                <h4>Call Details</h4>
                <small>RingLogix CDR history — filtered by patient phone numbers</small>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <div style="font-size:28px;font-weight:800;color:#fff;line-height:1;">${cdrAllRecords.length}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.75);">Total Records</div>
            </div>
        </div>
        ${errorHtml}
        <div class="row">
            <div class="col-md-4">
                <div class="call-detail-panel">
                    <div class="call-detail-panel-header">
                        <i class="fa fa-user text-primary mr-1"></i> Patient Information
                    </div>
                    <div class="call-detail-panel-body">
                        <div class="call-detail-info-grid">
                            <div class="call-detail-info-label">Patient ID</div>
                            <div>${patient.id ? '<strong>#' + e(patient.id) + '</strong>' : '-'}</div>
                            <div class="call-detail-info-label">Full Name</div>
                            <div><strong>${e(patient.full_name)}</strong></div>
                            <div class="call-detail-info-label">Mobile</div>
                            <div><code style="font-size:12px;background:#f1f5f9;padding:2px 6px;border-radius:4px;">${e(patient.mobile)}</code></div>
                            <div class="call-detail-info-label">Phone</div>
                            <div><code style="font-size:12px;background:#f1f5f9;padding:2px 6px;border-radius:4px;">${e(patient.phone)}</code></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="call-detail-panel">
                    <div class="call-detail-panel-header green">
                        <i class="fa fa-filter text-success mr-1"></i> Filter
                    </div>
                    <div class="call-detail-panel-body">
                        <form id="callDetailsFilterForm" class="call-detail-filter" data-ajax-url="${e(url)}">
                            <div class="d-flex align-items-end" style="gap:10px;">
                                <div style="flex:0 0 280px;">
                                    <label>Date Range</label>
                                    <input type="text" id="callDetailsCdrRangePicker" class="form-control form-control-sm" placeholder="Select date range" readonly>
                                    <input type="hidden" id="callDetailsCdrStartDate" name="start_date">
                                    <input type="hidden" id="callDetailsCdrEndDate" name="end_date">
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-sm cd-search-btn">
                                        <span class="cd-btn-text"><i class="fa fa-search mr-1"></i>Search</span>
                                        <span class="cd-btn-spinner"><span class="cd-spinner-ring"></span></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="call-detail-panel">
            <div class="call-detail-panel-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fa fa-list text-primary mr-1"></i> CDR List
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;background:${totalBadgeColor};color:#fff;border-radius:11px;font-size:11px;font-weight:700;padding:0 6px;margin-left:6px;">${cdrAllRecords.length}</span>
                </span>
                <small id="cdrPaginationInfo" class="text-muted"></small>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table call-detail-table mb-0">
                        <thead><tr>
                            <th>#</th>
                            <th>Date &amp; Time</th>
                            <th>Type</th>
                            <th>Caller Name</th>
                            <th>Caller #</th>
                            <th>Dialed #</th>
                            <th>Ext.</th>
                            <th>Duration</th>
                            <th>Talk Time</th>
                            <th>Action</th>
                            <th>Release</th>
                            <th>Codec</th>
                        </tr></thead>
                        <tbody id="cdrTableBody"></tbody>
                    </table>
                </div>
                <div class="cdr-pagination">
                    <button id="cdrPrevBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled>
                        <i class="fa fa-chevron-left mr-1"></i> Prev
                    </button>
                    <span id="cdrPageInfo" class="text-muted" style="font-size:12px;"></span>
                    <button id="cdrNextBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled>
                        Next <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>`;
    }

    $('body').on('click', '#cdrPrevBtn', function () {
        cdrCurrentPage--;
        window.renderCdrPage();
    });

    $('body').on('click', '#cdrNextBtn', function () {
        cdrCurrentPage++;
        window.renderCdrPage();
    });

    // ── Tab switching ──────────────────────────────────────────────────────
    $('body').on('click', '.call-details-tab-button', function () {
        var targetId = $(this).data('tab');
        $('.call-details-tab-button').removeClass('active');
        $(this).addClass('active');
        $('.call-details-tab-content').removeClass('active');
        $('#' + targetId).addClass('active');

        if (targetId === 'messages-panel' && messagesModalUrl && !messagesLoaded) {
            messagesLoaded = true;
            $('#messages-panel').html(cdShimmerHtml());
            loadMessagesTab(messagesModalUrl);
        }
    });

    // ── Open modal trigger ─────────────────────────────────────────────────
    $('body').on('click', '.view-call-details', function (e) {
        e.preventDefault();
        cdrModalUrl      = $(this).data('url');
        messagesModalUrl = cdrModalUrl.replace('/ajax', '/messages');
        messagesLoaded   = false;

        $('.call-details-tab-button').removeClass('active').first().addClass('active');
        $('.call-details-tab-content').removeClass('active').first().addClass('active');
        $('#messages-panel').html('');

        $('#callDetailsPatientName').text($(this).data('patient-name') || '');
        openCallDetailsModal();
        loadCallDetailsModal(cdrModalUrl);
    });

    // ── CDR filter form submit ─────────────────────────────────────────────
    $('body').on('submit', '#callDetailsFilterForm', function (e) {
        e.preventDefault();
        var $form = $(this);
        loadCallDetailsModal($form.data('ajax-url'), {
            start_date: $('#callDetailsCdrStartDate').val(),
            end_date:   $('#callDetailsCdrEndDate').val()
        }, $form);
    });

    // ── Close on backdrop click ────────────────────────────────────────────
    $('body').on('click', '#callDetailsModal', function (ev) {
        if (ev.target.id === 'callDetailsModal') closeCallDetailsModal();
    });

    // ── ESC key close ──────────────────────────────────────────────────────
    $(document).on('keydown.callDetailsModal', function (ev) {
        if (ev.key === 'Escape' && $('#callDetailsModal').hasClass('show')) closeCallDetailsModal();
    });

    // ── Messages tab ───────────────────────────────────────────────────────
    function loadMessagesTab(url, params, $form) {
        if (params) $('#messages-panel').html(cdShimmerHtml());
        if ($form)  setSearchLoading($form, true);
        $.ajax({
            url: url, type: 'GET', data: params || {},
            success: function (res) {
                if ($form) setSearchLoading($form, false);
                if (res.status == 1) {
                    var md = res.data; var mf = (md.filters || {});
                    $('#messages-panel').html(renderMessagesContent(md, url));
                    window.renderMsgPage();
                    initModalDateRangePicker('callDetailsMsgRangePicker', 'callDetailsMsgStartDate', 'callDetailsMsgEndDate', mf.start_date, mf.end_date);
                } else {
                    $('#messages-panel').html('<div class="alert alert-danger m-3">' + (res.error_msg || 'Unable to load messages.') + '</div>');
                }
            },
            error: function (xhr) {
                if ($form) setSearchLoading($form, false);
                var msg = (xhr.responseJSON && xhr.responseJSON.error_msg) ? xhr.responseJSON.error_msg : 'Unable to load messages.';
                $('#messages-panel').html('<div class="alert alert-danger m-3">' + msg + '</div>');
            }
        });
    }

    $('body').on('submit', '#messagesFilterForm', function (ev) {
        ev.preventDefault();
        var $form = $(this);
        loadMessagesTab($form.data('ajax-url'), {
            start_date: $('#callDetailsMsgStartDate').val(),
            end_date:   $('#callDetailsMsgEndDate').val()
        }, $form);
    });

    window.renderMsgPage = function () {
        var total = msgAllRecords.length;
        var totalPages = Math.max(1, Math.ceil(total / msgPageSize));
        msgCurrentPage = Math.min(Math.max(msgCurrentPage, 1), totalPages);
        var start = (msgCurrentPage - 1) * msgPageSize, pageSlice = msgAllRecords.slice(start, start + msgPageSize), rows = '';

        if (pageSlice.length > 0) {
            $.each(pageSlice, function (i, msg) {
                var dir = (msg.msg_dir == 'out' || msg.direction == 'out')
                    ? '<span class="cdr-badge outbound">Outbound</span>' : '<span class="cdr-badge inbound">Inbound</span>';
                rows += `<tr class="cdr-row-animate" style="animation-delay:${i*28}ms">
                    <td>${start + i + 1}</td>
                    <td>${e(msg.time_last ?? msg.time ?? '-')}</td>
                    <td>${e(msg.peer ?? msg.from ?? msg.to ?? '-')}</td>
                    <td>${dir}</td>
                    <td style="max-width:280px;white-space:normal;">${e(msg.last_msg ?? msg.msg_body ?? '-')}</td>
                    <td>${e(msg.msg_count ?? '-')}</td>
                    <td>${e(msg.msg_status ?? msg.status ?? '-')}</td>
                </tr>`;
            });
        } else {
            rows = `<tr><td colspan="7"><div class="cdr-empty-state">
                <div class="cdr-empty-icon"><i class="fa fa-comments"></i></div>
                <h6>No messages found</h6><p>Try a wider date range.</p>
            </div></td></tr>`;
        }

        $('#msgTableBody').html(rows);
        var from = total > 0 ? start + 1 : 0, to = Math.min(start + msgPageSize, total);
        $('#msgPaginationInfo').text(total > 0 ? 'Showing ' + from + '–' + to + ' of ' + total : '');
        $('#msgPageInfo').text('Page ' + msgCurrentPage + ' of ' + totalPages);
        $('#msgPrevBtn').prop('disabled', msgCurrentPage <= 1);
        $('#msgNextBtn').prop('disabled', msgCurrentPage >= totalPages);
    };

    function renderMessagesContent(data, url) {
        var filters = data.filters || {};
        msgAllRecords = data.messages || [];
        msgCurrentPage = 1;
        return `
        <div class="call-detail-hero">
            <div class="call-detail-hero-icon"><i class="fa fa-comments"></i></div>
            <div><h4>Messages</h4><small>RingLogix message sessions for this patient</small></div>
            <div style="margin-left:auto;text-align:right;">
                <div style="font-size:28px;font-weight:800;color:#fff;line-height:1;">${msgAllRecords.length}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.75);">Total Sessions</div>
            </div>
        </div>
        <div class="call-detail-panel">
            <div class="call-detail-panel-header green"><i class="fa fa-filter text-success mr-1"></i> Filter</div>
            <div class="call-detail-panel-body">
                <form id="messagesFilterForm" class="call-detail-filter" data-ajax-url="${e(url)}">
                    <div class="d-flex align-items-end" style="gap:10px;">
                        <div style="flex:0 0 280px;">
                            <label>Date Range</label>
                            <input type="text" id="callDetailsMsgRangePicker" class="form-control form-control-sm" placeholder="Select date range" readonly>
                            <input type="hidden" id="callDetailsMsgStartDate" name="start_date">
                            <input type="hidden" id="callDetailsMsgEndDate" name="end_date">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm cd-search-btn">
                                <span class="cd-btn-text"><i class="fa fa-search mr-1"></i>Search</span>
                                <span class="cd-btn-spinner"><span class="cd-spinner-ring"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="call-detail-panel">
            <div class="call-detail-panel-header d-flex justify-content-between align-items-center">
                <span><i class="fa fa-envelope text-primary mr-1"></i> Message Sessions
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;background:#6366f1;color:#fff;border-radius:11px;font-size:11px;font-weight:700;padding:0 6px;margin-left:6px;">${msgAllRecords.length}</span>
                </span>
                <small id="msgPaginationInfo" class="text-muted"></small>
            </div>
            <div class="table-responsive">
                <table class="table call-detail-table mb-0">
                    <thead><tr><th>#</th><th>Date &amp; Time</th><th>Peer</th><th>Direction</th><th>Last Message</th><th>Count</th><th>Status</th></tr></thead>
                    <tbody id="msgTableBody"></tbody>
                </table>
            </div>
            <div class="cdr-pagination">
                <button id="msgPrevBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled><i class="fa fa-chevron-left mr-1"></i> Prev</button>
                <span id="msgPageInfo" class="text-muted" style="font-size:12px;"></span>
                <button id="msgNextBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled>Next <i class="fa fa-chevron-right ml-1"></i></button>
            </div>
        </div>`;
    }
    $('body').on('click', '#msgPrevBtn', function () {
        msgCurrentPage--;
        window.renderMsgPage();
    });

    $('body').on('click', '#msgNextBtn', function () {
        msgCurrentPage++;
        window.renderMsgPage();
    });
})();
</script>
