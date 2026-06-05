function setBasicDetails() {
    $("#dob_error").html("");
    $("#payment_type_error").html("");
    $("#last_name_error").html("");
    $("#first_name_error").html("");
    $('#other_insurance_name_error').html("");
    $('.basic-detail-div').find('.show, .hide').toggleClass('show hide');
}

function saveBasicDetails(){
    var temp = 0;

    var last_name_id = $('#last_name_id').val();
    var first_name_id = $('#first_name_id').val();
    var dob_id = $('#dob_id').val();

    $("#dob_error").html("");
    $("#payment_type_error").html("");
    $("#last_name_error").html("");
    $("#first_name_error").html("");
    $('#other_insurance_name_error').html("");

    if (last_name_id.trim() == "") {
        $('#last_name_error').html("Please enter Last Name");
        temp++;
    }
    
    if (first_name_id.trim() == "") {
        $('#first_name_error').html("Please enter First Name");
        temp++;
    }

    if (dob_id == '') {
        $('#dob_error').html("Please select Date of Birth");
        temp++;
    }

    if (temp == 0) {
        $.ajax({
            async: false,
            global: false,
            url: SAVE_BASIC_DETAILS,
            type: "post",
            data: {
                id : _RECORD_ID,
                _token: _CSRF_TOKEN,
                patient_code : $('#patient_code').val(),
                last_name : last_name_id,
                first_name : first_name_id,
                middle_name : $('#middle_name').val(),
                dob : dob_id,
                insurance_id : $('#insurance_id').val(),
                insurance_name : $('#insurance_name').val(),
                ssn: $('#ssn').val(),
                payment_type: $('#payment_type').val(),
                diciplin: $('#diciplin_id').val(),
                location_branch: $('#location_branch').val(),
                hamaspik_payment: $('input[name="hamaspik_payment"]:checked').val(),
                other_insurance_name: $('#other_insurance_name').val()
            },
            success: function(response) {
               toastr.success(response.error_msg);
               getBasicDeatils(response);
               setBasicDetails();
            }
        });
    } else {
        return false;
    }
}

function getBasicDeatils(response) {
    if(response == ''){
        // Ajax call
    }else{
        data = response.data;
        $('#basic_patient_code').html(data.patient_code)
        $('#basic_first_name').html(data.first_name)
        $('#basic_middle_name').html(data.middle_name)
        $('#basic_last_name').html(data.last_name)
        $('#patient_dob').html(moment(data.dob).format("MM/DD/YYYY"))
        $('#dob').val(moment(data.dob).format("MM/DD/YYYY"))
        $('#basic_insuurance_id').html(data.insurance_name == null ? '' : data.insurance_name)
        $('#basic_ssn').html(data.ssn)
        $('#basic_payment_type_ham').html()
        $('#basic_location_branch').html(data.location_branch)
        $('#basic_insurance_name').html(data.insuranceName)
        $('#basic_payment_type').html(data.payment_type_new)
        $('#diciplin').html(data.diciplin)
        $('#basic_payment_type_ham').html(data.hamaspik_payment)
        $('#other_insurance').attr('style','display:none');
        if(data.insuranceName == 'Other'){
            $('#basic_other_insurance_name').html(data.other_insurance_name)
            $('#other_insurance').attr('style','display:block');
        }
    }
}

function setAddressDetails() {
    $('.address-detail-div').find('.show, .hide').toggleClass('show hide');
}

function saveAddressDetails(){
    var temp = 0;
    if (temp == 0) {
        $.ajax({
            async: false,
            global: false,
            url: SAVE_ADDRESS_DETAILS,
            type: "post",
            data: {
                id : _RECORD_ID,
                _token: _CSRF_TOKEN,
                county : $('#county').val(),
                state : $('#state').val(),
                city : $('#city').val(),
                address1 : $('#address1').val(),
                address2 : $('#address2').val(),
                zip_code : $('#zip_code').val(),
                email : $('#email').val(),
                emergency_contact_name : $('#emergency_contact_name').val(),
                emergency_phone : $('#emergency_phone').val(),
                
            },
            success: function(response) {
               toastr.success(response.error_msg);
               getAddressDeatils(response);
               setAddressDetails();
            }
        });
    } else {
        return false;
    }
}

function getAddressDeatils(response) {
    if(response == ''){
        // Ajax call
    }else{
        data = response.data;
        $('#basic_country').html(data.country)
        $('#basic_state').html(data.state)
        $('#basic_city').html(data.city)
        $('#basic_address1').html(data.address1)
        $('#basic_address2').html(data.address2)
        $('#basic_zipcode').html(data.zip_code)
        $('#basic_email').html(data.email)
        $('#basic_emergency_contact_name').html(data.emergency_contact_name)
        $('#basic_emergency_phone').html(data.emergency_phone)
    }
}

function isNumber(evt) {

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

        return false;
    }
    return true;
}

function getCountyByZipCode(val) {

    $.ajax({
        async: false,
        global: false,
        url: GET_COUNTY,
        type: "post",
        data: {
            zip_code: val,
            _token: _CSRF_TOKEN
        },
        success: function(response) {

            if (response != "County not found") {
                $('#county').val(response);
            } else {
                $('#county').val('');
            }
        }
    });
}

$("#id_due_date").datepicker({
    minDate: new Date(),
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});

$("#id_completed_date").datepicker({
    minDate: new Date(),
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});
function setOtherDetails() {
    $('.other-detail-div').find('.show, .hide').toggleClass('show hide');
    $('#message').val($('#html_patient_nots').val());
}

function saveOtherDetails(){
    
        $.ajax({
            async: false,
            global: false,
            url: SAVE_OTHER_DETAILS,
            type: "post",
            data: {
                id : _RECORD_ID,
                _token: _CSRF_TOKEN,
                language : $('#language_form_id').val(),
                note : $('#message').val(),
                medicare_no : $('#medicare_no').val(),
                cin: $('#cin').val(),
                completed_date: $('#id_completed_date').val()
            },
            success: function(response) {
               toastr.success(response.error_msg);
               getOtherDeatils(response);
               setOtherDetails();
            }
    });
}

function getOtherDeatils(response) {
    if(response == ''){
        // Ajax call
    }else{
        data = response.data;
        $('#basic_cin').html(data.cin)
        $('#basic_medicareno').html(data.medicare_no)
        $('#basic_language').html(data.languageName)
        $('#html_patient_notes_id').html(data.remarks)
        $('#html_patient_nots').val(data.remarks)
        $('#completed_date_id').html(moment(data.completed_date).format("MM/DD/YYYY"))
        $('#id_completed_date').html(moment(data.completed_date).format("MM/DD/YYYY"))
        $('#comp_id').html(moment(data.completed_date).format("MM/DD/YYYY"))
    }
}

$('#insurance_name').change(function(e){
    var insurance_name = $('#insurance_name').val();
    $('#other_insurance').attr('style','display:none');
    $('#other_insurance_name_error').html("");
    if(insurance_name =='other'){
        $('#other_insurance').attr('style','display:block');
    }
})

$('#ssn').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});