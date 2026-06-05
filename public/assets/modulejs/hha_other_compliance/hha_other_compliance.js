function hhaAppoitnemtList(page) {
    var fname = $('#full_name').val();
    var code = $('#code').val();
    var caregiver_phone = $('#caregiver_phone').val();
    var hha_code = $('#hha_code').val();
    var medical_name = $('#medical_name').val();
    var due_date = $('#due_date').val();
    var agency_fk = $('#agency_fk').val();
    var status = $('#status').val();
    $('.shimmer_id').removeClass('hide');
    $('#response_other_compliance').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _HHA_OTHER_COMPLIANCE_LIST+"?page=" + page,
        type: "GET",
        data: {
            'agency_fk': agency_fk,
            'fname': fname,
            'code': code,
            'caregiver_phone': caregiver_phone,
            'hha_code': hha_code,
            'medical_name': medical_name,
            'due_date': due_date,
            'status': status,
            'office_id': $('#office_id_other').val(),

        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');
         
            $('#response_other_compliance').html(res)
        },
        error:function(xhr){
            showErrorAndLoginRedirection(xhr);
        }
    })
    return false;
}

function addAppointment() {
    var checked = $('.cbox').is(":checked");
    if (checked == false) {
        toastr.error("Please select checkbox");
        return false;
    } else {
        var final_array = [];
        $('.cbox').each(function(i, v) {
            var schecked = $(this).is(":checked");
            if (schecked == true) {
                var values = $('.cbox:checked').val();

                final_array.push(values);
            }
        });
        $.confirm({
            title: "Are You Sure?",
            columnClass: 'col-md-6',
            type: 'blue',
            content: 'You want to create a new appointment',
            buttons: {
                formSubmit: {
                    text: 'Submit',
                    btnClass:"btn btn-primary",
                    action: function () {
                        $.ajax({
                            url: _ADD_APPOINTMENT_OTHER_COMPLIANCE,
                            type: "post",
                            data: {
                                'final_array': final_array,
                                '_token': _CSRF_TOKEN,
                
                            },
                            success: function(res) {
                                final_array.pop();
                                toastr.success(res.error_msg);
                               
                                hhaAppoitnemtList(1);
                
                            },
                            error: function(xhr, status, error) {
                                showErrorAndLoginRedirection(xhr);
                            }
                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {
                      
                    }
                }
            }
        })
    }
}

function singleDataAppointment(id) {
    var final_array = [];
    final_array.push(id);
    $.confirm({
        title: "Are You Sure?",
        columnClass: 'col-md-6',
        type: 'blue',
        content: 'You want to create a new appointment',
        
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass:"btn btn-primary",
                action: function () {
                    $.ajax({
                        url: _ADD_APPOINTMENT_OTHER_COMPLIANCE,
                        type: "post",
                        data: {
                            'final_array': final_array,
                            '_token': _CSRF_TOKEN,
                        },
                        success: function(res) {
                            final_array.pop();
                            toastr.success(res.error_msg);
                            hhaAppoitnemtList(1);
                
                        },
                        error: function(xhr, status, error) {
                            showErrorAndLoginRedirection(xhr);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {
                  
                }
            }
        }
    })
    
}

function exportCSV(){
    var fname = $('#full_name').val();
    var code = $('#code').val();
    var caregiver_phone = $('#caregiver_phone').val();
    var hha_code = $('#hha_code').val();
    var medical_name = $('#medical_name').val();
    var due_date = $('#due_date').val();
    var agency_fk = $('#agency_fk').val();
    var status = $('#status').val();
    window.location.href= _HHA_OTHER_COMPLIANCE_EXPORT_CSV+"?agency_fk="+agency_fk+"&fname="+fname+"&fname="+fname+"&code="+code+"&caregiver_phone="+caregiver_phone+"&hha_code="+hha_code+"&medical_name="+medical_name+"&due_date="+due_date+"&status="+status+"&office_id="+$('#office_id').val()
     
 }

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
});