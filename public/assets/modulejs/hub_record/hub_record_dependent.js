loadDependentData(1);

function setBasicDetails() {
    $("#dob_error").html("");
    $("#last_name_error").html("");
    $("#first_name_error").html("");
    $("#ssn_error").html("");
    $('#other_insurance_name_error').html("");
    $('.basic-detail-div').find('.show, .hide').toggleClass('show hide');
    getBasicDeatils();
}

function getBasicDeatils(response) {
    if(response == undefined){
        $.ajax({
            async: false,
            global: false,
            url: GET_BASIC_DETAILS,
            type: "get",
            data: {
                id : _RECORD_ID,
            },
            success: function(response) {
                response = response.data;
                $('#first_name_id').val(response.first_name)
                $('#middle_name').val(response.middle_name)
                $('#last_name_id').val(response.last_name)
                $('#dob_id').val(moment(response.dob).format("MM/DD/YYYY"))
                var val = response.ssn.replace(/\D/g, '');
                val = val.replace(/^(\d{3})/, '$1-');
                val = val.replace(/-(\d{2})/, '-$1-');
                val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
                $('#ssn_id').val(val)
            }
        });
    }else{
        data = response.data;
        $('#basic_first_name').html(data.first_name)
        $('#basic_middle_name').html(data.middle_name)
        $('#basic_last_name').html(data.last_name)
        $('#patient_dob').html(moment(data.dob).format("MM/DD/YYYY"))
        var val = data.ssn.replace(/\D/g, '');
        val = val.replace(/^(\d{3})/, '$1-');
        val = val.replace(/-(\d{2})/, '-$1-');
        val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
        $('#patient_ssn').html(val)
        $('#basic_location_branch').html(data.location_branch)
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

$("#dob_id").datepicker({
    maxDate: 0,
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});

$('#ssn').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});


$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    loadDocumentAjaxList(page);
});

$(document).on('click', '.log-pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    loadAllHubLogs(page);
});

$('#ssn_id').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});

$('#dep_ssn, #edit_dep_ssn').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});

$('#hub_agency_id').change(function(){
    let id = $('#hub_agency_id option:selected').val();
    // get Agency wise data
    getAgencyWiseOtherData(id);

})

function loadDependentData(page){
    $('#child_table').html('');
    $.ajax({
        url: GET_DEPENDENT_DATA,
        type: "get",
        data: {
            'page': page,
        },
        success: function(response) {
            $('#child_table').html(response);
        }
    });
    return false;
}

function openAddChildForm(){
    $('#agency-div').attr('style','display:none');
    // $('#hub_add_dependent_modal').modal('show');
    $('.error_html').html("");
    $('#hub_add_modal').modal('show');
    // $('#hub_add_modal .modal-title').html('Create New Hub Dependent');
}
$('#saveHub').click(function(e){   
   
    let first_name = $('#dep_first_name').val();
    let last_name_id = $('#dep_last_name_id').val();
    let mobile = $('#dep_mobile_no').val();
    let email = $('#dep_email').val();
    let dep_ssn = $('#dep_ssn').val();
    let dep_dob_id = $('#dep_dob_id').val();
    var ssnPattern = /^\d{3}-\d{2}-\d{4}$/;

    $('#dep_first_name_error').html("");
    $("#dep_agency_name_error").html("");
    $("#dep_phone_error").html("");
    $("#dep_last_name_error").html("");
    $("#dep_mobile_error").html("");
    $('#dep_email_error').html("");
    $('#dep_ssn_error').html("");
    $('#dep_dob_error').html("");

    $('#dep_employee_code_error').html("");
    var temp=0;
    
    if(first_name.trim() ==""){
        $('#dep_first_name_error').html("Please enter First Name");
        temp++;
    }

    if(last_name_id.trim() ==""){
        $('#dep_last_name_error').html("Please enter Last Name");
        temp++;
    }

     
    if(dep_dob_id.trim() ==""){
        $('#dep_dob_error').html("Please enter Date of Birth");
        temp++;
    }

    if(mobile.trim() ==""){
        $('#dep_mobile_error').html("Please enter Mobile");
        temp++;
    }
  
    if (email != "") {
        var emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;
        
        if (emailRegex.test(email)) {
           $('#dep_email_error').html("");
        }else{
            $('#dep_email_error').html("Please enter a valid email address");
            temp++;
        }
    }

    if(dep_ssn.trim() ==""){
        $('#dep_ssn_error').html("Please enter SSN");
        temp++;
    }else{
        if(ssnPattern.test(dep_ssn)){

        }else{
            $('#dep_ssn_error').html("Invalid SSN format");
            temp++;
        }
    }

    if(temp !=0){
        return false;
    }else{
        $.confirm({
        title: "Are you sure?",
        content:"The provided data is accurate and relevant, and do you wish to proceed with submission?",
        type: 'blue',
        columnClass: 'col-md-9',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    var formData = new FormData($('#add_new_hub')[0]);
                    formData.append('_token',_CSRF_TOKEN);
                    formData.append('agency_id',$('#hub_agency_id').val());
                    formData.append('hub_record_id',_RECORD_ID);
                    $.ajax({
                        async:false,
                        global:false,
                        type:"POST",
                        url:_SAVE_HUB_DEPENDENT_DETAILS,
                        data:formData,
                        processData: false,
                        contentType: false,
                        success:function(res){
                            if(res.status){
                                toastr.success(res.error_msg);
                                loadDependentData(1); 
                                $('#add_new_hub')[0].reset();
                                $('.close').click();
                            }else{
                                toastr.error(res.error_msg);
                            }
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
            }
        }
    });
    }
})

$('#updateHub').click(function(e){   
   
    let first_name = $('#edit_dep_first_name').val();
    let last_name_id = $('#edit_dep_last_name_id').val();
    let mobile = $('#edit_dep_mobile_no').val();
    let email = $('#edit_dep_email').val();
    let dep_ssn = $('#edit_dep_ssn').val();
    let dep_dob_id = $('#edit_dep_dob_id').val();
    var ssnPattern = /^\d{3}-\d{2}-\d{4}$/;

    $('#edit_dep_first_name_error').html("");
    $("#edit_dep_agency_name_error").html("");
    $("#edit_dep_phone_error").html("");
    $("#edit_dep_last_name_error").html("");
    $("#edit_dep_mobile_error").html("");
    $('#edit_dep_email_error').html("");
    $('#edit_dep_ssn_error').html("");
    $('#edit_dep_dob_error').html("");

    var temp=0;
    
    if(first_name.trim() ==""){
        $('#edit_dep_first_name_error').html("Please enter First Name");
        temp++;
    }

    if(last_name_id.trim() ==""){
        $('#edit_dep_last_name_error').html("Please enter Last Name");
        temp++;
    }

     
    if(dep_dob_id.trim() ==""){
        $('#edit_dep_dob_error').html("Please enter Date of Birth");
        temp++;
    }

    if(mobile.trim() ==""){
        $('#edit_dep_mobile_error').html("Please enter Mobile");
        temp++;
    }
  
    if (email != "") {
        var emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;
        
        if (emailRegex.test(email)) {
           $('edit_#dep_email_error').html("");
        }else{
            $('#edit_dep_email_error').html("Please enter a valid email address");
            temp++;
        }
    }

    if(dep_ssn.trim() ==""){
        $('#edit_dep_ssn_error').html("Please enter SSN");
        temp++;
    }else{
        if(ssnPattern.test(dep_ssn)){

        }else{
            $('#edit_dep_ssn_error').html("Invalid SSN format");
            temp++;
        }
    }

    if(temp !=0){
        return false;
    }else{
        $.confirm({
        title: "Are you sure?",
        content:"The provided data is accurate and relevant, and do you wish to proceed with submission?",
        type: 'blue',
        columnClass: 'col-md-9',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    var formData = new FormData($('#edit_new_hub')[0]);
                    formData.append('_token',_CSRF_TOKEN);
                    formData.append('agency_id',$('#hub_agency_id').val());
                    formData.append('dependent_id',$('#dependent_id').val());
                    formData.append('hub_record_id',_RECORD_ID);
                    $.ajax({
                        async:false,
                        global:false,
                        type:"POST",
                        url:_UPDATE_HUB_DEPENDENT_DETAILS,
                        data:formData,
                        processData: false,
                        contentType: false,
                        success:function(res){
                            if(res.status){
                                toastr.success(res.error_msg);
                                loadDependentData(1); 
                                $('#edit_new_hub')[0].reset();
                                $('.close').click();
                            }else{
                                toastr.error(res.error_msg);
                            }
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
            }
        }
    });
    }
})
function openEditChildForm(id){

    $(`#edit-dependent-${id}`)
    let el = $(`#edit-dependent-${id}`);

    let firstName = el.attr('data-first-name');
    let lastName  = el.attr('data-last-name');
    let email     = el.attr('data-email');
    let dob       = el.attr('data-dob');
    let phone     = el.attr('data-phone');
    let mobile    = el.attr('data-mobile');
    let ssn       = el.attr('data-ssn');
    console.log(firstName, lastName, email, dob, phone, mobile, ssn); 

    $('#dependent_id').val(id);
    $('#edit_dep_first_name').val(firstName);
    $('#edit_dep_last_name_id').val(lastName);
    $('#edit_dep_email').val(email);
    $('#edit_dep_dob_id').val(formatDateToMMDDYYYY(dob));
    $('#edit_dep_phone').val(phone);
    $('#edit_dep_mobile_no').val(mobile);
    var ssnval = ssn.replace(/\D/g, '');
    ssnval = ssnval.replace(/^(\d{3})/, '$1-');
    ssnval = ssnval.replace(/-(\d{2})/, '-$1-');
    ssnval = ssnval.replace(/(\d)-(\d{4}).*/, '$1-$2');
    $('#edit_dep_ssn').val(ssnval);
    
    $('.error_html').html("");
    $('#hub_edit_modal').modal('show');

}
function clearModal(){
    $('#add_new_hub')[0].reset();
    $('#edit_new_hub')[0].reset();
}

function formatDateToMMDDYYYY(dateStr) {
    let date = new Date(dateStr);
    if (isNaN(date.getTime())) return ''; // if invalid date
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let day = String(date.getDate()).padStart(2, '0');
    let year = date.getFullYear();
    return `${month}/${day}/${year}`;
}