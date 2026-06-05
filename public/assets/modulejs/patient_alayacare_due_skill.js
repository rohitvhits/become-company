$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    dueSkillList(1)

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
    }, function (chosen_date, end_date) {

        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#created_date').daterangepicker({
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
    }, function (chosen_date, end_date) {

        $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});


function dueSkillList(page) {
    $('.hasClass').removeClass('d-none');
    $('#resp').html("");
    $.ajax({
        url: _DUESKILL_LIST,
        type: "GET",
        data: {
            'page': page,
            'agency_fk': $('#agency_fk').val(),
            'full_name': $('#full_name').val(),
            'code': $('#code').val(),
            'caregiver_phone': $('#caregiver_phone').val(),
            'skill_name': $('#medical_name').val(),
            'due_date': $('#due_date').val(),
            'status': $('#status').val(),
            'employee_status': $('#employee_status').val(),
            'created_date': $('#created_date').val(),
        },
        success: function (res) {
            $('.hasClass').addClass('d-none');
           
            $('#resp').html(res)
            // initialize();
        }
    })

    return false;
}

function singleDataAppointment(id) {
    $("#displine_error").html("");
    $("#radio_type_error").html("");
    $("#service_id_error").html("");
    $('#emp_id').val(id);
    $('#alaycare-emp-id').val('single');
    getResponse('Caregiver');
    $('#exampleModal-alayacare-emp-due-skill').modal('show');
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    dueSkillList(page);
});

$('body').on('click', '#cboxid', function (e) {
    var checked = $(this).is(":checked");
    if (checked == true) {
        $('.cbox').prop('checked', true);
    } else {
        $('.cbox').prop('checked', false);
    }
})

$('#saveId').click(function(){

    $('#create-alayacare-due-skill').removeClass('d-none');
    $('#btn-save-text').text('Saving...')
    $('#saveId').attr('disabled',false);
    var temp = 0;
    var diciplin_id = $('#diciplin_id').val();
    var service_id = $('#service_id').val();
    var selectedType = $('#alaycare-emp-id').val();
    $("#displine_error").html("");
    $("#service_id_error").html("");
    
    if (service_id == "") {
        $('#service_id_error').html("Please select Service");
        temp++;
    }
    
    if (diciplin_id == '') {
        $("#displine_error").html("Please select Discipline");
        temp++;
    }
    var final_array = [];
    if(selectedType =='single'){
        var empId = $('#emp_id').val();
        final_array.push(empId)
    }else{
        $('.cbox').each(function(i, v) {
            var schecked = $(this).is(":checked");
            if (schecked == true) {
                var values =   $(this).val();
                final_array.push(values);
            }

        });
      
    }

    if (temp !=0) {
        $('#create-alayacare-due-skill').addClass('d-none');
        $('#btn-save-text').text('Save')
        $('#saveId').attr('disabled',false);
        return false;
    }
    var forms = $('#submitId')[0];
    var newForms = new FormData(forms);
    newForms.append('ids', final_array);
    $('#saveId').attr('disabled',true);

    $.ajax({
        url: _ADDPATIENTAPPOINTMENT,
        type: "post",
        data: newForms,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.success(response.error_msg);
            $('#create-alayacare-due-skill').addClass('d-none');
            $('#btn-save-text').text('Save')
            $('#exampleModal-alayacare-emp-due-skill').modal('hide');
            clearAlayacareFormSkill();
            $('#saveId').attr('disabled',false);
            $("#service_id").trigger("reset");
            dueSkillList(1);
        },
        error: function (xhr) {
            $('#create-alayacare-due-skill').addClass('d-none');
            $('#btn-save-text').text('Save')
            $('#saveId').attr('disabled',false);
            showErrorAndLoginRedirection(xhr);
        }
    })
});
function addAppointment() {
    var checked = $('.cbox').is(":checked");
    if (checked == false) {
        toastr.error("Please select checkbox");
        return false;
    } else {
        $('#alaycare-emp-id').val('multiple')
        $('#exampleModal-alayacare-emp-due-skill').modal('show');
        clearAlayacareFormSkill();
        getResponse('Caregiver'); 
    }
}

$('body').on('click', '.record_id', function(e) {
    var fields = $(this).attr('data-field');
    var sort = $(this).attr('data-sort');

    $('#sortingColumn').val(fields);
    $('#sortingOrder').val(sort);
    dueSkillList(1);
})


$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function getResponse(id) {
console.log()
    if (id != '') {
        var jsonencode = (_EXISTING_SERVICES !="null")?_EXISTING_SERVICES:[];
  
        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: _GET_SERVICE,
            data: {
                "id": id,
                "jsonencode": jsonencode
            },
            success: function(res) {
                if (res != '') {
                    htmlsresp = res;
                } else {
                    htmlsresp += '<option value="">No record available</option>';
                }
                $('#service_id').html(htmlsresp);
            }
        })
    }
}

$(function() {
    $("#service_id").select2({
        placeholder: "Select Service"
    });

})

function refresh(){
    $('#search-form')[0].reset();
    $('#agency_fk').val(null).trigger("change");
    dueSkillList(1);
}

$('#close-modal-popup').click(function(e){
    clearAlayacareFormSkill();
   
})

function clearAlayacareFormSkill(){

    $('#submitId')[0].reset();
    $('#service_id_error').html("");
    $('#displine_error').html("");
}