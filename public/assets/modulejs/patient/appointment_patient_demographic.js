$(function(){
    loadPatientList(1);
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
    $('.due_datenn').daterangepicker({
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

        $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
    $('.traning_date').daterangepicker({
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

        $('.traning_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('.dob').datepicker();
})

function loadPatientList(page){
    $('#aid_demo_details_id').attr('style','');
    $.ajax({
        type:"get",
        url:patienList+"?page="+page,
        data:$('#formsubmit').serialize(),
        success:function(res){
            $('#aid_demo_details_id').attr('style','display:none');
            $('#patient_list_id').html("")
            $('#patient_list_id').html(res)
 
        }
    })
}


$("#created_by_ny").tokenInput(urlToken, {
            
    tokenLimit: 1,
    zindex: 9999,
    prePopulate: empId !== "" && empName !== "" ? [{ id: empId, name: empName }] : [],
    onAdd: function (item) {
        $('#created_by_ny_id').val(item.id);
        $('#created_by_ny_name').val(item.name);
    },
    onDelete:function(item){
        $('#created_by_ny_id').val('');
        $('#created_by_ny_name').val('');
    }
});

$('.searchAppoinment').click(function(e){
    loadPatientList(1);
})

$('.clear_id').click(function(e){
  
    $('#formsubmit')[0].reset();
    $('#status_id').val(' ').change();
    $('#agency_fk').val(' ').change();
    $('#assign_user_id').val(' ').change();
    $('#locationId').val(' ').change();
    $('#created_by_ny').tokenInput('clear')
    loadPatientList(1);
})

$("#searchbtns").click(function() {
    $("#search-div").toggle();
});

function getArchiveById(id) {
    var consi = confirm('Are you sure archive this record?');

    var selected_data = [];
    selected_data.push(id);
    if (consi == true) {
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _PATIENT_ARCHIVE,
            data: {
                '_token':_CSRF_TOKEN,
                'patient_id': selected_data.join()
            },
            success: function(res) {
                if (res == 1) {
                    toastr.success('Appointment successfully archive.');
                    loadPatientList();
                } else {
                    toastr.error('Sorry, something went wrong. Please try again.');
                }
            }
        })
    }

}

function exportCsv(){
    $('#aid_demo_details_id').attr('style','');
    $.ajax({
        type:"get",
        url:_PATIENT_EXPORT_CSV,
        data:$('#formsubmit').serialize(),
       
        success:function(res){
            $('#aid_demo_details_id').attr('style','display:none');
            var blob = new Blob([res]);
            console.log(blob)
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);

            link.download = 'Patient_'+_DATE_TIME+'.csv'
            link.click();

        }
    })
}

$(document).on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadPatientList(page);
});

function bulkAppointmentDelete(){
    var selected_data = [];
    $('.cbox').each(function(){
        if($(this).is(":checked")){
            selected_data.push($(this).val());
        }
    });

    if(selected_data.length == 0){
        toastr.error('Please select at least one chart.');
    }else{
        $.confirm({
            title: 'Are you sure?',
            type: 'blue',
            columnClass: "col-md-6",
            content: 'You want to permanently delete all selected records?',
            buttons: {
                formSubmit: {
                    btnClass: 'btn-red',
                    text: 'Delete',
                    action: function(){
                        $.ajax({
                            async: false,
                            global: false,
                            type: "POST",
                            url: _BULK_APPOINTMENT_DELETE,
                            data: {
                                '_token': _CSRF_TOKEN,
                                'patient_id': selected_data.join(),
                                'type':'Chart'
                            },
                            success: function(res) {
                                toastr.success('Chart successfully deleted.');
                                loadPatientList(1)
                            },
                            error:function(jqr){
                                toastr.error(jqr.responseJson.error_msg);
                            }
                        });
                    }
                },
                cancel: function() {
                    //close
                },
            }
        })
    }
}
