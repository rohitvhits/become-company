$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    if(typeof _REMOTE_AJAX_LIST  != 'undefined'){
        robortList(1);
    }
});


$(document).ready(function() {
    $("#service_id").select2({
        placeholder: "Select Service"
    });

    // Filter toggle
    $("#filter-btn").click(function() {
        $("#search-filter-btn").slideToggle();
    });
});

function refresh() {
    $('#agency_id').val('');
    $('#full_name').val('');
    $('#dob').val('');
    $('#gender').val('');
    $('#patient_status').val('');
    $('#status').val('');
    $('#due_date').val('');
    robortList(1);
}

function robortList(page) {
    $('#resp').html("");
    $('.location-wise-data-loader').attr('style','display:block');
    if(typeof _REMOTE_AJAX_LIST !="undefined"){
        $.ajax({
            url: _REMOTE_AJAX_LIST+"?page=" + page,
            type: "GET",
            data: {
                'full_name': $('#full_name').val(),
                'agency_id': $('#agency_id').val(),
                'dob': $('#dob').val(),
                'gender': $('#gender').val(),
                'patient_status': $('#patient_status').val(),
                'status': $('#status').val(),
                'created_date': $('#due_date').val(),
            },
            success: function (res) {
                $('.location-wise-data-loader').attr('style','display:none');
                
                $('#resp').html(res)
               
            }
        })
        return false;
    }
    
}
initialize();
function initialize() {
    var start = moment().subtract(0, 'days');
    var end = moment();

    $('.dob').datepicker();
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
}
$('body').on('click', '#cboxid', function (e) {
    var checked = $(this).is(":checked");
    if (checked == true) {
        $('.cbox').prop('checked', true);
    } else {
        $('.cbox').prop('checked', false);
    }
})
$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    robortList(page);
});

function addAppointment(id, type) {
    $('#appointment_type').val(type)
    $('#appointment_ids').val(id)
    if (type == 'single') {
        getResponse('Patient');
        loadHHADicipline(id);
        $('#exampleModal-add-remote-appointment').modal('show');
    } else {
        var checked = $('.cbox').is(":checked");
        if (checked == false) {
            toastr.error("Please select checkbox");
            return false;
        } else {
            $('#exampleModal-add-remote-appointment').modal('show');
        }
    }

    $('#service_id_error').html("");
}
function getResponse(id) {

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _REMOTE_SERVICES,
        data: {
            "id": id,
        },
        success: function (res) {
          
            $('#service_id').html("");
            let htmlOption="";
            if(res.data.length !=0){
                $.each(res.data,function(i,v){
                    htmlOption +="<option value='"+v.id+"'>"+v.name+"</option>";
                })
            }
            $('#service_id').html(htmlOption);  
        }
    })

}

$('#saveId').click(function (e) {
    $('#saveId').prop('disabled', true);
    $('#create-add-remote').removeClass('d-none');
    $('#btn-save-text-remote').text('Saving...')
    var selectedType = $('input[name="type"]').is(":checked");
    var diciplin_id = $('#diciplin_id').val();
    var service_id = $('#service_id').val();
    var agency_id = $('#agency_id').val();

    $('.error').html("");
    var type = $('#appointment_type').val();
    var finalArray = [];
    if (type == 'single') {
        var id = $('#appointment_ids').val();
        finalArray.push(id);
    } else {
        $('.cbox').each(function (i, v) {
            var schecked = $(this).is(":checked");
            if (schecked == true) {
                var values = $(this).val();
                finalArray.push(values);
            }
        });
    }

    var cnt = 0;

    if (service_id == '') {
        $('#service_id_error').html("Please select Services");
        cnt =1;
    }

    if (cnt == 1) {
        $('#create-add-remote').addClass('d-none')
        $('#btn-save-text-remote').text('Save')
        $('#saveId').prop('disabled', false);
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _REMOTE_ADD_APPOINTMENT,
            type: "post",
            data: {
                'appointment_ids': finalArray,
                'diciplin_id': diciplin_id,
                'service_id': service_id,
                'type':"Patient",
                '_token': _CSRF_TOKEN,

            },
            success: function (res) {
                $('#saveId').prop('disabled', false);
                $('#create-add-remote').addClass('d-none');
                $('#btn-save-text-remote').text('Save')
                finalArray.pop();
                toastr.success(res.error_msg);

                clearData();
                $('#exampleModal-add-remote-appointment').modal('hide');
                robortList(1);
            },
            error: function (xhr, status, error) {
                $('#saveId').prop('disabled', false);
                $('#create-add-remote').addClass('d-none');
                $('#btn-save-text-remote').text('Save')
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
})

function clearData() {
    $('#submitId')[0].reset();
    $('.error').html("")
    $('#service_id').html("")
    $('#agency_id').html("")
}

function loadHHADicipline(id) {

    $.ajax({
        async: false,
        global: false,
        url: _REMOTE_LOAD_DICIPLINE,
        type: "get",
        data: {
            'id': id,
        },
        success: function (res) {
            $('#hha_dicipline_id').html("");
      
            $('#hha_dicipline_id').html(res.data.dicipline);
        }
    })
}

function uploadRemoteDocument(id){
    $('#uploadDocumentFormID')[0].reset();
    $('#document_upload_error').html("");
    $('#remote_id').val(id);
    $('#exampleModal-upload-remote-document').modal('show');
}

$('#uploadDocumentButton').click(function(i){
    $('#uploadDocumentButton').prop('disabled',true);
    var upload_document = $('#upload_document').prop('files');
    var cnt =0;
    $('#document_upload_error').html("");
    if(upload_document.length ==0){
        $('#document_upload_error').html("Please select Document");
        cnt =1;
    }else{
        var allowedExtensions = ["pdf", "doc", "docx", "jpg", "png"]; // Add allowed extensions
        var fileName = upload_document[0].name;
        var fileExtension = fileName.split('.').pop().toLowerCase();

        if ($.inArray(fileExtension, allowedExtensions) === -1) {
            $('#document_upload_error').html("Invalid file type. Allowed types: " + allowedExtensions.join(", "));
            cnt = 1;
        }
    }

    if(cnt ==1){
        $('#uploadDocumentButton').prop('disabled',false);
        return false;
    }else{
        $("#loaderAddEbook").attr('style',"display:block");
        $("#ebookSave").prop("disabled", true);
        var formData = new FormData($("#uploadDocumentFormID")[0]);
        formData.append('_token', _CSRF_TOKEN)
        
        $.ajax({
            type: "POST",
            url: _REMOTE_UPLOAD_DOCUMENT,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#uploadDocumentButton').prop('disabled',false);
                toastr.success(res.error_msg);
                $('#uploadDocumentFormID')[0].reset();
                $('#exampleModal-upload-remote-document').modal('hide');
            },
            error: function (jqXHR) {
                $('#uploadDocumentButton').prop('disabled',false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    }
})

function sentRemoteDetails(){
    $.ajax({
        
        type: "GET",
        url: _GET_REMOTE_DETAIL,
        data: {
            "id": _RECORD_ID,
            "agency_id":_AGENCYID
        },
        success: function(res) {
            $('#add_remote_demographic')[0].reset();
            $('.error_html').html("");
            $('#remote_bestday_to_call').val(' ').trigger("change");
            $('#remote_best_time').val(' ').trigger("change");
            $('#remote_referred_to_far').val(' ').trigger("change");
            $('#remote_prognosis').val(' ').trigger("change");
            $('#other_best_call_day').addClass('hide')
            $('#other_best_time_to_call').addClass('hide')
            $('#saveRemotePatientId').prop('disabled',false)
            $('#remote_patient_code').val(res.data.basic_details.patient_code)
            $('#remote_first_name').val(res.data.basic_details.firstName)
            $('#remote_middle_name').val(res.data.basic_details.middleName)
            $('#remote_last_name_id').val(res.data.basic_details.lastName)
            $('#remote_dob_id').val(res.data.basic_details.dob)
            $('#remote_mobile').val(res.data.basic_details.phones)
            $('input[name="remote_gender"][value="'+res.data.basic_details.gender+'"]').prop("checked", true);
            $('#remote_language_id').val(res.data.basic_details.primaryLanguage)
            $('#remote_insurance_id').val(res.data.basic_details.insurance_id)
            $('#remote_ext_id').val(res.data.basic_details.externalId)
            $('#remote_id').val(res.data.basic_details.id)
            $('#remote_agency_id').val(res.data.basic_details.agency_id)

            var insuranceOption="<option value=''>Select Insurance Type</option>";
            if(res.data.insurance.length !=0){
                $.each(res.data.insurance,function(i,v){
                    insuranceOption +='<option value="'+v.value+'">'+v.value+'</option>';
                })
            }
            $('#remote_insurance_name').html('')
            $('#remote_insurance_name').html(insuranceOption)

            var addressTypeOption="<option value=''>Select Type</option>";
            if(res.data.addressType.length !=0){
                $.each(res.data.addressType,function(i,v){
                    addressTypeOption +='<option value="'+v.value+'">'+v.value+'</option>';
                })
            }
            $('#remote_type').html('')
            $('#remote_type').html(addressTypeOption)


            $('#remote_patient_add_modal').modal('show')
           
        },
        error:function(jqr){
            toastr.error(jqr.responseJSON.error_msg);
        }
    })
    return false;
}


function clearDemoModal(){}

$('#remote_bestday_to_call').change(function(e){
    var remote_bestday_to_call = $('#remote_bestday_to_call').val();
    $('#other_best_call_day').addClass('hide');
    if(remote_bestday_to_call.includes('Other')){
        $('#other_best_call_day').removeClass('hide');
    }
})

$('#remote_best_time').change(function(e){
    var remote_best_time = $('#remote_best_time').val();
    $('#other_best_time_to_call').addClass('hide');
    if(remote_best_time.includes('Other')){
        $('#other_best_time_to_call').removeClass('hide');
    }
})

$('#saveRemotePatientId').click(function(i){
    var remote_first_name = $('#remote_first_name').val();
    var remote_last_name_id = $('#remote_last_name_id').val();
    var remote_dob_id = $('#remote_dob_id').val();
    var remote_mobile = $('#remote_mobile').val();
    var remote_gender = $('input[name="remote_gender"]:checked').val();
    var remote_referral_source = $('#remote_referral_source').val();
    var remote_bestday_to_call = $('#remote_bestday_to_call').val();
    var remote_language_id = $('#remote_language_id').val();
    var upload_document = $('#upload_document').prop('files');

    var remote_best_time = $('#remote_best_time').val();
    var remote_icd10 = $('#remote_icd10').val();
    var remote_prognosis = $('#remote_prognosis').val();
    var remote_start_date = $('#remote_start_date').val();
    var remote_end_date = $('#remote_end_date').val();
    var remote_insurance_name = $('#remote_insurance_name').val();
    var remote_insurance_id = $('#remote_insurance_id').val();
    var remote_address = $('#remote_address').val();
    var remote_city = $('#remote_city').val();
    var remote_state = $('#remote_state').val();
    var remote_zip = $('#remote_zip').val();
    var remote_type = $('#remote_type').val();

    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;

    $('#remote_name_error').html("");
    $('#remote_last_name_error').html("");
    $('#remote_dob_error').html("");
    $('#remote_mobile_error').html("");
    $('#remote_address2_error').html("");
    $('#remote_referral_source_error').html("");
    $('#remote_bestday_to_call_error').html("");
    $('#remote_best_time_error').html("");
    $('#remote_icd10_error').html("");
    $('#remote_prognosis_error').html("");
    $('#remote_start_date_error').html("");
    $('#remote_end_date_error').html("");
    $('#remote_language_id_error').html("");
    $('#remote_insurance_name_error').html("");
    $('#remote_insurance_id_error').html("");
   
    $('#remote_address_error').html("");
    $('#remote_city_error').html("");
    $('#remote_state_error').html("");
    $('#remote_zip_error').html("");
    $('#remote_type_error').html("");
    $('#remote_document_upload_error').html("");
    var cnt = 0;
    
    if (remote_first_name.trim() == '') {
        $('#remote_name_error').html("Please enter First Name");
        cnt = 1;
    }
    if (remote_last_name_id.trim() == '') {
        $('#remote_last_name_error').html("Please enter Last Name");
        cnt = 1;
    }
    if (remote_dob_id.trim() == '') {
        $('#remote_dob_error').html("Please enter Date of Birth");
        cnt = 1;
    }
    if (remote_mobile.trim() == '') {
        $('#remote_mobile_error').html("Please enter Mobile");
        cnt = 1;
    }

    if (remote_gender == '') {
        $('#remote_address2_error').html("Please enter Gender");
        cnt = 1;
    }

    if (remote_language_id == '') {
        $('#remote_language_id_error').html("Please select Language");
        cnt = 1;
    }

    if (remote_referral_source == '') {
        $('#remote_referral_source_error').html("Please select Referral Source");
        cnt = 1;
    }

    if (remote_icd10.trim() =='') {
        $('#remote_icd10_error').html("Please enter Diagnoses");
        cnt = 1;
    }

    if (remote_prognosis =='' || remote_prognosis ==null) {
        $('#remote_prognosis_error').html("Please select Prognosis");
        cnt = 1;
    }

    if (remote_start_date =='') {
        $('#remote_start_date_error').html("Please select Start Date");
        cnt = 1;
    }
    if(remote_start_date.trim() !=""){
        if (!regex.test(remote_start_date.trim())) {
            $('#remote_start_date_error').html("Please enter valid Start Date");
            cnt = 1;
        }
    }
    if (remote_end_date =='') {
        $('#remote_end_date_error').html("Please select End Date");
        cnt = 1;
    }
    if(remote_end_date.trim() !=""){
        if (!regex.test(remote_end_date.trim())) {
            $('#remote_end_date_error').html("Please enter valid End Date");
            cnt = 1;
        }
    }
    if(remote_insurance_name !=""){
        if(remote_insurance_id.trim() ==''){
            $('#remote_insurance_id_error').html("Please enter Insurance ID");
            cnt = 1;
        }
    }

    if(remote_insurance_id.trim() !=""){
        if(remote_insurance_name ==''){
            $('#remote_insurance_name_error').html("Please enter Insurance Type");
            cnt = 1;
        }
    }

    if(remote_address.trim() ==''){
        $('#remote_address_error').html("Please enter Address");
        cnt = 1;
    }
    if(remote_city.trim() ==''){
        $('#remote_city_error').html("Please enter City");
        cnt = 1;
    }
    if(remote_state.trim() ==''){
        $('#remote_state_error').html("Please enter State");
        cnt = 1;
    }
    if(remote_state.trim() !=''){
        if(remote_state.length >2){
            $('#remote_state_error').html("A maximum of 2 characters are allowed");
            cnt = 1;
        }
    }

    if(remote_zip.trim() ==''){
        $('#remote_zip_error').html("Please enter Zip");
        cnt = 1;
    }
    if(remote_zip.trim() !=''){
        if(remote_zip.length >5){
            $('#remote_zip_error').html("A maximum of 5 digits are allowed");
            cnt = 1;
        }
    }
    if(remote_type ==''){
        $('#remote_type_error').html("Please select Type");
        cnt = 1;
    }

    if(upload_document.length ==0){
        $('#remote_document_upload_error').html("Please select Document");
        cnt =1;
    }else{
        var allowedExtensions = ["pdf"];
        var fileName = upload_document[0].name;
        var fileExtension = fileName.split('.').pop().toLowerCase();

        if ($.inArray(fileExtension, allowedExtensions) === -1) {
            $('#remote_document_upload_error').html("Invalid file type. Allowed types: " + allowedExtensions.join(", "));
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#saveRemotePatientId").prop('disabled',true);
        var formData = new FormData($('#add_remote_demographic')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('remote_id',_ROBORTID);
        formData.append('record_id',_RECORD_ID);
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url:_SEND_REMOTE_DEMOGRAPHIC_DETAILS,
            data: formData,
            contentType: false,
            processData: false,
            success:function(res){
                toastr.success(res.error_msg);
                $('#add_remote_demographic')[0].reset();
                $('#remote_patient_add_modal').modal('hide')
            },
            error:function(jqXHR){
                $("#saveRemotePatientId").prop('disabled',false);
                toastr.error(jqXHR.responseJSON.error_msg)
            }
        })
    } else {
        return false;
    }
})
