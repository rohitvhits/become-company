function loadData(page){
    $('#loadertag1').removeClass('hide');
    $.ajax({
        type:"GET",
        url:_LOAD_DATA_URL,
        data:{
            'agency_id':_AGENCY_ID,
            'created_date':$('#appointment_date').val(),
            'page':page
        },
        success:function(res){
            $('#loadertag1').addClass('hide');
            $('.tableData').html('');
            $('.tableData').html(res);
      
        }
    })
}

loadData(1); 

function exportCSV(){
    $.ajax({
        type:"GET",
        url:_EXPORT_CSV,
        data:{
            'agency_id':_AGENCY_ID,
            'created_date':$('#appointment_date').val(),
        },
        success:function(res){
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "service_report" + _DATE_TIME + ".csv";
            link.click();
      
        }
    })
}

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepicker1').daterangepicker({
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

        $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    cb(start, end);
});

function refresh(){
    $('#agency_fk').val(null).trigger('change');
    $('#appointment_date').val('')
    loadData(1)
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadData(page);
});