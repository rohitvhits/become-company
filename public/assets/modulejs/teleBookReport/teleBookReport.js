teleBookList(1);
function teleBookList(page=1){
    $('#tele_book_report_res').html("")
    $('.hideClass').removeClass('d-none');
    var agency_fk = $('#agency_fk').val();
    var type = $('#type').val();
    var appointment_date = $('#appointment_date').val();
    var nurse_id = $('#nurse_id').val();
    var language_id = $('#language_id').val();
    var created_date = $('#created_date').val();
    $.ajax({
        url: _TELE_BOOK_LIST+"?page=" + page,
        type: "get",
        data:{
            'agency_fk':agency_fk,
            'type':type,
            'appointment_date':appointment_date,
            'nurse_id':nurse_id,
            'language_id':language_id,
            'created_date':created_date,
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            $('#tele_book_report_res').html("")
            $('#tele_book_report_res').html(response);
        }
    });
}

$(document).on('click', '.tele_book_report_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    teleBookList(page);
});

function refresh(){
    $('#agency_fk').val('').trigger("change")
    $('#type').val('').trigger("change")
    $('#appointment_date').val('')
    $('#nurse_id').val('').trigger("change")
    $('#language_id').val('').trigger("change")
    $('#created_date').val('')
    teleBookList(1);
}

function exportCsv()
{
    $('.hideClass').removeClass('d-none');
    var agency_fk = $('#agency_fk').val();
    var type = $('#type').val();
    var appointment_date = $('#appointment_date').val();
    var nurse_id = $('#nurse_id').val();
    var language_id = $('#language_id').val();
    var created_date = $('#created_date').val();
    $.ajax({
        url: _TELE_BOOK_CSV,
        type: "get",
       data:{
        'agency_fk':agency_fk,
        'type':type,
        'appointment_date':appointment_date,
        'nurse_id':nurse_id,
        'language_id':language_id,
        'created_date':created_date,
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
                var form_name = "telehealth_book_report_"+_DATE_TIME;
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

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#appointment_date').daterangepicker({
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

        $('#appointment_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#appointment_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});