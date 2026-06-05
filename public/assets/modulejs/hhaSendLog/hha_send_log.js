function loadHHASendLogAjax(page) {
    page = page || 1;
    $('.shimmer_id').removeClass('hide');
    $('#response_hha_send_log').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _HHA_SEND_LOG_AJAX + "?page=" + page,
        type: "get",
        data: {
            'created_date': $('#created_date').val()
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('#response_hha_send_log').html(res);
            $('.location-wise-data-loader').attr('style', 'display:none');
        },
        error: function(xhr) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    });
}

loadHHASendLogAjax(1);

// Pagination click handler
$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadHHASendLogAjax(page);
});

// Filter toggle
$('#filter-btn').click(function() {
    $('#search-filter-btn').slideToggle();
});

function refreshHHASendLog() {
    $('#search-form')[0].reset();
    $('#created_date').val('');
    loadHHASendLogAjax(1);
}

function viewSendRequest(id) {
    $('#hha-send-log-modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    $('#hha-send-log-modal').modal('show');
    $.ajax({
        url: _HHA_SEND_LOG_VIEW,
        type: "get",
        data: { 'id': id },
        success: function(res) {
            var content = '';
            
            content += '<div class="row">';
            content += '<div class="col-md-6"><div class="card mb-3"><div class="card-header bg-success text-white" style="padding:10px !important"><b>Send Response</b></div>';
            content += '<div class="card-body" style="max-height:300px;overflow-y:auto;overflow-x:hidden;">';
            content += highlightJson(res.data.send_response);
            content += '</div></div></div>';
            content += '<div class="col-md-6"><div class="card mb-3"><div class="card-header bg-info text-white" style="padding:10px !important"><b>Return Response</b></div>';
            content += '<div class="card-body" style="max-height:300px;overflow-y:auto;overflow-x:hidden;">';
            content += highlightJson(res.data.return_response);
            content += '</div></div></div>';
            content += '</div>';
            $('#hha-send-log-modal-body').html(content);
        },
        error: function(xhr) {
            $('#hha-send-log-modal-body').html('<div class="text-center text-danger">Failed to load data</div>');
        }
    });
}

function highlightJson(jsonInput) {
    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    var obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
        } catch (e) {
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    var pretty = JSON.stringify(obj, null, 4);
    pretty = pretty.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    pretty = pretty.replace(/("[^"]+": )/g, '<span style="color:#007bff;">$1</span>');
    pretty = pretty.replace(/(:\s?)("[^"]*")/g, '$1<span style="color:#28a745;">$2</span>');
    pretty = pretty.replace(/(:\s?)(\d+\.?\d*)/g, '$1<span style="color:#d18f00;">$2</span>');
    pretty = pretty.replace(/(:\s?)(true|false|null)/g, '$1<span style="color:#aa0d91;">$2</span>');
    return '<pre style="word-break:break-all;white-space:pre-wrap;">' + pretty + '</pre>';
}

// Date range picker
var start = moment().subtract(0, 'days');
var end = moment();
$('#created_date').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    ranges: {
        'Select Date': [start, end],
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
}, function(chosen_date, end_date) {
    $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
});

$('#created_date').on('apply.daterangepicker', function(ev, picker) {
    if (picker.chosenLabel === 'Select Date') {
        $(this).val('');
    } else {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    }
});
