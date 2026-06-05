hubRecordList(1);
function hubRecordList(page=1){
    $('#hub_record_report_res').html("")
    $('.hideClass').removeClass('d-none');
    let agency_fk = $('#agency_fk').val();
    let first_name = $('#first_name').val();
    let last_name = $('#last_name').val();    
    let created_date = $('#created_date').val();
    let created_by = $('#created_by_ny_id').val();
    let subject = $('#subject').val();
    let hub_record_id = $('#hub_record_id').val();
    $.ajax({
        url: _HUB_RECORD_LIST+"?page=" + page,
        type: "get",
        data:{
            'agency_fk':agency_fk,
            'first_name':first_name,
            'last_name':last_name,
            'created_date':created_date,
            'created_by':created_by,
            'subject':subject,
            'hub_record_id':hub_record_id
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            $('#hub_record_report_res').html("")
            $('#hub_record_report_res').html(response);
        }
    });
}

$(document).on('click', '.hub_record_report_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    hubRecordList(page);
});

function refresh(){
    $('#agency_fk').val('').trigger("change")
    $('#first_name').val('').trigger("change")
    $('#last_name').val('').trigger("change")
    $('#mobile').val('').trigger("change")
    $('#created_date').val('').trigger("change")
    $('#dob').val('').trigger("change")
    $("#created_by_ny").tokenInput("clear");
    $("#subject").val("").trigger("change")
    $("#hub_record_id").val("");
    hubRecordList(1);
}

function exportCsv()
{
    $('.hideClass').removeClass('d-none');
    let agency_fk = $('#agency_fk').val();
    let first_name = $('#first_name').val();
    let last_name = $('#last_name').val();   
    let created_date = $('#created_date').val();
    let created_by = $('#created_by_ny_id').val();
    let subject = $('#subject').val();
    let hub_record_id = $('#hub_record_id').val();
    $.ajax({
        url: _HUB_RECORD_CSV,
        type: "get",
       data:{
           'agency_fk':agency_fk,
           'first_name':first_name,
           'last_name':last_name,
           'created_date':created_date,
           'created_by':created_by,
           'subject':subject,
           'hub_record_id':hub_record_id
       },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            var blob = new Blob([response]);
            console.log(response);
            if(response == ""){
                toastr.error('Please check there is no data to export.');
            }else{
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                var form_name = "hub_record_report"+_DATE_TIME;
                link.download = form_name + ".csv";
                link.click();
            }
            
        }
    });
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

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

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