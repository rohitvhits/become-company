function dashboardList(page=1){

    $('#report_res').html("")
    $('.hideClass').removeClass('d-none');

    let agency_fk = $('#agency_fk').val();
    let created_date = $('#created_date').val();
     let last_updated_date = $('#last_updated_date').val();
    let top= $('#top').val();
    let type = $('#type').val();

    $.ajax({
        url: _LIST+"?page=" + page,
        type: 'GET',
        async: false,
        global: false,
        data:{
            'agency_id':agency_fk,
            'created_date':created_date,
            'last_updated_date':last_updated_date,
            'top':top,
            'type':type
        },
        success: function (response) {
            setTimeout(()=>{
                $('.hideClass').addClass('d-none');
                $('#report_res').html(response);
            },2000);

        }
    });
}

$(document).on('click', '.report_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    dashboardList(page);
});

function refresh(){

    $('#agency_fk').val('').trigger("change")
    $('#created_date').val('').trigger("change")
    $('#last_updated_date').val('').trigger("change")

    dashboardList(1);
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

    $('#last_updated_date').daterangepicker({
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
        $('#last_updated_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#last_updated_date').on('apply.daterangepicker', function(ev, picker) {
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
$('#created_date').val('');
$('#last_updated_date').val('');

dashboardList(1);
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

$('#exportBtn').on('click', function() {

    // Create a new workbook
    let wb = XLSX.utils.book_new();

    // Convert each HTML table to a worksheet
    let referralsAnalyticsDashboardTable = document.getElementById("referralsAnalyticsDashboardTable");

    let ws1 = XLSX.utils.table_to_sheet(referralsAnalyticsDashboardTable);
  
    // Append sheets
    XLSX.utils.book_append_sheet(wb, ws1, "Referrals Analytics Dashboard");

    // Export Excel file
    XLSX.writeFile(wb, "referrals_analytics_dashboard.xlsx");
});
