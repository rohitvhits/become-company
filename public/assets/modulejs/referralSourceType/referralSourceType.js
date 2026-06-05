loadReferralSourceType(1);

function loadReferralSourceType(page){
    $('#ajax_response_id').html("")
    $('.hideClass').removeClass('d-none');
    $.ajax({
        type:"GET",
        url:_REFERRAL_SOURCE_TYPE_AJAX,
        data:{
            'referral_type':$('#referral_source_type').val(),
            'created_date':$('#created_date').val(),
            'page':page
        },
        success:function(res){
            $('.hideClass').addClass('d-none');
            $('#ajax_response_id').html(res);
        }
    })
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

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

function exportCsv(page){
   
    $.ajax({
        type:"GET",
        url:_REFERRAL_SOURCE_TYPE_EXPORT_CSV,
        data:{
            'referral_type':$('#referral_source_type').val(),
            'created_date':$('#created_date').val(),
           
        },
        success:function(response){
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var form_name = "referralType_"+_DATE_TIME;
            link.download = form_name + ".csv";
            link.click();
        }
    })
}

function refresh(){
    $('#referral_source_type').val('');
    $('#created_date').val('');
    loadReferralSourceType(1);
}