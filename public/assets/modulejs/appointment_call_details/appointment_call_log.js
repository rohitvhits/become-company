// ── State ──────────────────────────────────────────────────────────────────
var _callDetailsTabLoaded = false;

var _cdrRecords  = [], _cdrPage = 1, _cdrPageSize = 25;
var _msgRecords  = [], _msgPage = 1, _msgPageSize = 25;
var _msgLoaded   = false;

// ── Entry (sidebar tab onclick) ────────────────────────────────────────────
function loadCallDetailsTabSection() {
    if (_callDetailsTabLoaded) return;
    _callDetailsTabLoaded = true;
    _fetchCdr(_CALL_DETAILS_TAB_URL, {});
}

// ── Daterangepicker initializer ────────────────────────────────────────────
function _initDateRangePicker(pickerInputId, startHiddenId, endHiddenId, startVal, endVal) {
    if (typeof $.fn.daterangepicker === 'undefined' || typeof moment === 'undefined') return;

    var defaultStart = (startVal && moment(startVal).isValid()) ? moment(startVal) : moment().subtract(1, 'days').startOf('day');
    var defaultEnd   = (endVal   && moment(endVal).isValid())   ? moment(endVal)   : moment().endOf('day');

    var $input = $('#' + pickerInputId);

    $input.daterangepicker({
        startDate:       defaultStart,
        endDate:         defaultEnd,
        autoUpdateInput: false,
        autoApply:       true,
        startOfWeek:     'sunday',
        locale:          { format: 'MM/DD/YYYY' },
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

    _syncDatePicker($input, defaultStart, defaultEnd, startHiddenId, endHiddenId);

    $input.off('apply.daterangepicker').on('apply.daterangepicker', function (ev, picker) {
        _syncDatePicker($(this), picker.startDate, picker.endDate, startHiddenId, endHiddenId);
    });
}

function _syncDatePicker($input, start, end, startHiddenId, endHiddenId) {
    $input.val(start.format('MM/DD/YYYY') + ' – ' + end.format('MM/DD/YYYY'));
    $('#' + startHiddenId).val(start.format('YYYY-MM-DD'));
    $('#' + endHiddenId).val(end.format('YYYY-MM-DD'));
}

// ══════════════════════════════════════════════════════════════════════════
// CDR TAB
// ══════════════════════════════════════════════════════════════════════════

function _fetchCdr(url, params) {
    $('#cdCdrTableBody').html(
        '<tr><td colspan="12" class="text-center py-4">' +
            '<i class="fa fa-spinner fa-spin fa-2x" style="color:#2563eb;opacity:.7;"></i>' +
            '<p class="mt-2 mb-0" style="font-size:13px;color:#94a3b8;">Loading...</p>' +
        '</td></tr>'
    );
    $.ajax({
        url: url, type: 'GET', data: params,
        success: function (res) {
            if (res.status == 1) {
                _renderCdrContent(res.data);
            } else {
                $('#cdCdrTableBody').html(
                    '<tr><td colspan="12">' + _errorHtml(res.error_msg || 'Unable to load call details.') + '</td></tr>'
                );
            }
        },
        error: function (jqXHR) {
            showErrorAndLoginRedirection(jqXHR);
        }
    });
}

function _renderCdrContent(data) {
    _cdrRecords = data.call_details || [];
    _cdrPage    = 1;

    var total      = _cdrRecords.length;
    var badgeColor = total > 0 ? '#16a34a' : '#94a3b8';
    $('#cdCdrCountBadge').text(total).css('background', badgeColor);

    _renderCdrPage();
    _initDateRangePicker('cdCdrDateRangePicker', 'cdCdrStartDate', 'cdCdrEndDate',
        (data.filters || {}).start_date, (data.filters || {}).end_date);
}

function _renderCdrPage() {
    var total      = _cdrRecords.length;
    var totalPages = Math.max(1, Math.ceil(total / _cdrPageSize));
    _cdrPage       = Math.min(Math.max(_cdrPage, 1), totalPages);

    var start = (_cdrPage - 1) * _cdrPageSize;
    var slice = _cdrRecords.slice(start, start + _cdrPageSize);
    var rows  = '';

    if (slice.length === 0) {
        rows = '<tr><td colspan="13">' +
                   '<div class="cd-empty"><i class="fa fa-phone-slash"></i><p>No call records found.</p></div>' +
               '</td></tr>';
    } else {
        $.each(slice, function (i, call) {
            var badge;
            if      (call.type == 0) badge = '<span class="cd-badge cd-badge-outbound"><i class="fa fa-phone mr-1"></i>Outbound</span>';
            else if (call.type == 1) badge = '<span class="cd-badge cd-badge-inbound"><i class="fa fa-phone-volume mr-1"></i>Inbound</span>';
            else if (call.type == 2) badge = '<span class="cd-badge cd-badge-missed"><i class="fa fa-phone-slash mr-1"></i>Missed</span>';
            else                     badge = '<span class="cd-badge cd-badge-unknown">Unknown</span>';

            var recBtn = call.cdr_id
                ? '<button type="button" class="btn btn-sm btn-outline-secondary cd-btn-recording" ' +
                  'data-cdrid="' + _esc(call.cdr_id) + '" ' +
                  'data-timestart="' + _esc(call.time_start_unix) + '" ' +
                  'title="Download Recording">' +
                  '<i class="fa fa-download"></i></button>'
                : '-';

            rows += '<tr>' +
                '<td class="text-muted">' + (start + i + 1) + '</td>' +
                '<td style="white-space:nowrap;">' + _esc(call.time_start) + '</td>' +
                '<td>' + badge + '</td>' +
                '<td>' + _esc(call.caller_name) + '</td>' +
                '<td><code>' + _esc(call.caller_number) + '</code></td>' +
                '<td><code>' + _esc(call.dialed_number) + '</code></td>' +
                '<td class="text-center">' + _esc(call.extension) + '</td>' +
                '<td class="text-center">' + _esc(call.duration_fmt) + '</td>' +
                '<td class="text-center">' + _esc(call.talk_time_fmt) + '</td>' +
                '<td>' + _esc(call.by_action) + '</td>' +
                '<td>' + _esc(call.release_text) + '</td>' +
                '<td>' + _esc(call.codec) + '</td>' +
                '<td class="text-center">' + recBtn + '</td>' +
            '</tr>';
        });
    }

    $('#cdCdrTableBody').html(rows);
    _updatePagination('cdCdrPagInfo', 'cdCdrPageInfo', 'cdCdrPrevBtn', 'cdCdrNextBtn',
        _cdrPage, totalPages, start, _cdrPageSize, total);
}

// ══════════════════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════════════════

function _esc(v) {
    return (v === null || v === undefined || v === '') ? '-' : $('<div>').text(String(v)).html();
}

function _errorHtml(msg) {
    return '<div class="alert alert-danger mt-2" style="border-radius:8px;font-size:13px;">' +
               '<i class="fa fa-exclamation-circle mr-2"></i>' + _esc(msg) +
           '</div>';
}

function _updatePagination(pagInfoId, pageInfoId, prevId, nextId, page, totalPages, start, pageSize, total) {
    var from = total > 0 ? start + 1 : 0;
    var to   = Math.min(start + pageSize, total);
    $('#' + pagInfoId).text(total > 0 ? 'Showing ' + from + '–' + to + ' of ' + total : '');
    $('#' + pageInfoId).text('Page ' + page + ' of ' + totalPages);
    $('#' + prevId).prop('disabled', page <= 1);
    $('#' + nextId).prop('disabled', page >= totalPages);
}

// ── Events ─────────────────────────────────────────────────────────────────

$(document).on('click', '#cdCdrPrevBtn', function () { _cdrPage--; _renderCdrPage(); });
$(document).on('click', '#cdCdrNextBtn', function () { _cdrPage++; _renderCdrPage(); });

$(document).on('submit', '#cdCdrFilterForm', function (ev) {
    ev.preventDefault();
    _fetchCdr(_CALL_DETAILS_TAB_URL, {
        start_date: $('#cdCdrStartDate').val(),
        end_date:   $('#cdCdrEndDate').val()
    });
});

$(document).on('click', '.cd-btn-recording', function () {
    var btn       = $(this);
    var cdrId     = btn.data('cdrid');
    var timeStart = btn.data('timestart');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url:  _CALL_DETAILS_RECORDING_URL,
        type: 'GET',
        data: { cdr_id: cdrId, time_start: timeStart },
        success: function (res) {
            window.open(res.url, '_blank');
        },
        error: function (jqXHR) {
            showErrorAndLoginRedirection(jqXHR);
        },
        complete: function () {
            btn.prop('disabled', false).html('<i class="fa fa-play-circle"></i>');
        }
    });
});
