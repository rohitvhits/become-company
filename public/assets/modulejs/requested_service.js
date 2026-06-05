function ajaxList(page =1){
    var formData = $('#formsubmit')[0];
    $.ajax({
        url: _AJAX_LIST,
        type: "GET",
        data:{
            'status':$('#status_id').val(),
            'agency_fk':$('#agency_fk').val(),
            'patient_code':$('#patient_code').val(),
            'first_name':$('input[name="first_name"]').val(),
            'mobile':$('#mobile').val(),
            'appointment_date':$('#appointment_date').val(),
            'locationId':$('#locationId').val(),
            'created_date':$('#created_date').val(),
            'sms_status':$('#sms_status').val(),
            'diciplin_id':$('#diciplin_id').val(),
            'type':$('#type').val(),
            'completed_date':$('#completed_date').val(),
            'follow_up_date':$('#follow_up_date').val(),
            'page':page
          
        },

        success:function(res){
            $('#response_requested_id').html("")
            $('#response_requested_id').html(res)
        }
    })
    
}

ajaxList(1);

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
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

        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('.completed_date').daterangepicker({
           startDate: start,
           endDate: end,
           autoUpdateInput: false,
           startOfWeek: 'sunday',
           ranges: {
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

           $('.completed_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
               'MM/DD/YYYY'));
    })

    $('.follow_up_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
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

        $('.follow_up_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

$('.searchAppoinment').click(function(){
    ajaxList(1)
})

$(document).on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    ajaxList(page);
});