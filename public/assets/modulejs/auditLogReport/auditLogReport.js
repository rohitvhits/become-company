function auditList(page=1){
    
    $('#audit_report_res').html("")
    $('.hideClass').removeClass('d-none');
    let type = $('#type').val();
    let module = $('#module').val();
    let created_by = $('#created_by_ny_id').val();
    let created_date = $('#created_date').val();
    let patient_id = $('#patient_id').val(); 
    $.ajax({
        url: AUDIT_LIST+"?page=" + page,
        type: 'GET',
        async: false,
        global: false,
        data:{
            'module':module,
            'type':type,
            'created_date':created_date,
            'created_by':created_by,
            'patient_id':patient_id
        },
        success: function (response) {
            setTimeout(()=>{
                $('.hideClass').addClass('d-none');
                $('#audit_report_res').html(response);
            },2000);
            
            
        }
    });
}

$(document).on('click', '.audit_report_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    auditList(page);
});

function refresh(){
    $('#module').val('').trigger("change")
    $('#type').val('').trigger("change")
    $('#created_date').val('').trigger("change")
    $("#created_by_ny").tokenInput("clear");
    $('#patient_id').val('');
    auditList(1);
}



$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#created_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
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
        $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#created_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});

var start_days = moment().subtract(6, 'days');
var end_days = moment();
$('#created_date').val(start_days.format('MM/DD/YYYY') + ' - ' + end_days.format('MM/DD/YYYY'));

auditList(1);
$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$("#search-filter-btn").slideToggle(600);

$("#created_by_ny").tokenInput(urlToken, {
    tokenLimit: 1,
    zindex: 9999,
    onAdd: function (item) {
        $('#created_by_ny_id').val(item.id);
        $('#created_by_ny_name').val(item.name);
    },
    onDelete:function(item){
        $('#created_by_ny_id').val('');
        $('#created_by_ny_name').val('');
    }
});

function viewLog(id){
    $.ajax({
        url: GET_VIEW_LOG,
        data: {
            id: id
        },
        success: function(res){
            let old_response = res.data.old_response;
            let new_response = res.data.new_response;
            $('#log-model').modal('show');
            let content = '';
            content += `<div class=\"row\">`;
            content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-primary text-white\" style="padding:10px !important"><b>Old Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
            content += highlightJson(old_response);
            content += `</div></div></div>`;
            content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-success text-white\"  style="padding:10px !important"><b>New Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
            content += highlightJson(new_response);
            content += `</div></div></div>`;
            content += `</div>`;
            $('.dataContainer').html(content);
        }
    });
}

function highlightJson(jsonInput) {
    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    let pretty = JSON.stringify(obj, null, 4);
    // Basic syntax highlighting
    pretty = pretty.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    pretty = pretty.replace(/("[^"]+": )/g, '<span style="color:#007bff;">$1</span>'); // keys
    pretty = pretty.replace(/(:\s?)("[^"]*")/g, '$1<span style="color:#28a745;">$2</span>'); // string values
    pretty = pretty.replace(/(:\s?)(\d+\.?\d*)/g, '$1<span style="color:#d18f00;">$2</span>'); // numbers
    pretty = pretty.replace(/(:\s?)(true|false|null)/g, '$1<span style="color:#aa0d91;">$2</span>'); // booleans/null
    return '<pre style="word-break:break-all;white-space:pre-wrap;">' + pretty + '</pre>';
}