let existingDuplicateRecord = [];
var globalFlag = 0;
$('#savePatientId').click(function(e){   
    var agency_name = $('#agency_name').val();
    var last_name_id = $('#last_name_id').val();
  
    var mobile = $('#mobile').val();
    var gender = $('input[name="gender"]').is(":checked");
    var dob_id = $('#dob_id').val();
    var cin = $('#cin').val();
    var email = $('#email').val();
    var other_name = $('input[name="other_name"]').val();
    var service_id = $('#create_service_id').val();
    var referral_type = $('#referral_type').val();
    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;

    $("#agency_name_error").html("");
    $("#phone_error").html("");
    $("#address2_error").html("");
    $("#last_name_error").html("");
    $("#mobile_error").html("");
    $("#dob_error").html("");
    $('#radio_type_error').html("");
    $('#patient_code_error').html("");
    $("#cin_error").html("");
    $("#state_error").html("");
    $("#city_error").html("");
    $("#zip_code_error").html("");
    $("#address1_error").html("");
    $('#email_error').html("");
    $("#other_name_error").html("");
    $("#create_service_id_error").html("");
    $(".location_branch_error").html("");
    var temp=0;
    if ($('input[name="type"]').is(':checked') == false) {
        $('#radio_type_error').html("Please select Type");
        temp++;
    }

  
    if(agency_name.trim() ==""){
        $('#agency_name_error').html("Please enter First Name");
        temp++;
    }

    if(last_name_id.trim() ==""){
        $('#last_name_error').html("Please enter Last Name");
        temp++;
    }

    if(mobile.trim() ==""){
        $('#mobile_error').html("Please enter Mobile");
        temp++;
    }else{
        
    }

    if(dob_id.trim() ==""){
        $('#dob_error').html("Please enter Date of Birth");
        temp++;
    }
    if(dob_id.trim() !=""){
        if (!regex.test(dob_id.trim())) {
            $('#dob_error').html("Please enter valid Date of Birth");
            temp++;
        }
    }
    if (gender == false) {
        $('#address2_error').html("Please select Gender");
        temp++;
    }else{
        if($('input[name="gender"]:checked').val().trim() =='other'){
            if(other_name.trim() ==''){
                $('#other_name_error').html("Please enter Other Name");
                temp++;
            }
        }
    }
  
    if (email != "") {
        var emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;
        
        if (emailRegex.test(email)) {
           $('#email_error').html("");
        }else{
            $('#email_error').html("Please enter a valid email address");
            temp++;
        }

    }
    
    if (referral_type == '') {
        if(_AUTH_APP_AGENCY_FK ==""){
            $("#referral_type_error").html("Please select Referral Source Type");
            temp++;
        }
    }

    if($('input[name="type"]:checked').val() =="Patient"){
        if (cin.trim() == '') {
            $("#cin_error").html("Please enter CIN/Medicaid Number");
            temp++;
        }   
    }

    if (service_id == "" || service_id ==null) {
        $('#create_service_id_error').html("Please select Service");
        temp++;
    }
    
    if (isBranchMandatory && !$('#branch_dropdown_wrapper').hasClass('hide')) {
        let branchVal = $('#patient_branch_id').val();
        if (!branchVal) {
            $('.location_branch_error').html('Branch selection is mandatory for this agency and service combination');
            temp++;
        }
    }
    if(temp !=0){
       
        return false;
    }else{
       let response =  loadExistingData();
  
        if(response ==1){
            if(globalFlag ==0){
                createNewAppointment();
            }
        }
        
    }
})

$('#agency_ids').change(function(e) {
    var urlResponse = _FIND_PATIENT_DETAILS + "?agency_id=" + $('#agency_ids').val();
    jQuery.noConflict()
    $('.search_patient').tokenInput('destroy');
    if($('#agency_ids').val() !=""){
        $(".search_patient").tokenInput(urlResponse, {

            onAdd: function(index, val, type) {
                $('#type_new').val(index.type);
                $('#patient_id').val(index.id);
                getResponse(index.type);
                $('.token-input-delete-token').attr('onclick','deleteTokenDetails()')
            },
            
            tokenLimit: 1,
        });
        $('.notes_existing_class').addClass("hide");
    }else{
        $(".search_patient").attr('style','')
        $('#type_new').val();
        $('#patient_id').val();
        $(".search_patient").val("");
        
        $('.notes_existing_class').removeClass("hide");
        $('#show_demographic-detail').removeClass("hide");
    }
    $('#show_demographic-detail').html("");
    $('#selected_agency_list').addClass('hide');
    $('.selected_agency').html($('#agency_ids option:selected').text())
    $('#selected_agency').val($('#agency_ids').val())
    addCss();
});

function addCss(){
    $(".search_patient .token-input-dropdown").css({
        "max-height": "200px",
        "overflow-y": "auto",
        "overflow-x": "hidden"
    });
}
var SELECTED_ID;
var SELECTED_NAME;

function checkAgency(){
    
    var agency_ids = $('#agency_ids').val();
    $("#agency_error").html("");
    $('#agency_hha_enabled').addClass('hide');
    if(agency_ids ==""){
        $("#agency_error").html("Please select agency");
        return false
    }else{
        $('#cid').val('');
        $('#patient_agency_id').val(agency_ids)
        $("#agency_error").html("");
        var hha=$('#agency_ids option:selected').attr('data-app-name');
       
        if(hha =='1'){
            $('#agency_hha_enabled').removeClass('hide');
        }
        $('#click_event').attr('data-target','#patient_add_modal')
        $('#add_new_patient')[0].reset();
        $('.error').text('');
        $('#transition_aid').addClass('hide');
    }
    $('#total_search_appointment').html(0)
loadAgencyUsers(agency_ids);
}

function loadAgencyUsers(agencyId) {
    $('#agency_user_ids').tokenInput('destroy');
   
    if (!agencyId) {
        return;
    }
    var urlResponse = _SEARCH_USERS_BY_AGENCY + "?agency_id=" + agencyId;
    $('#agency_user_ids').tokenInput(urlResponse, {
        queryParam: "q",
        preventDuplicates: true,
        zindex: 1060,
        hintText: "Type to search Agency Rep",
        noResultsText: "No Agency Rep found",
        searchingText: "Searching...",
        tokenLimit: 1,
        onReady: function() {
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "180px",
                    "overflow-y": "auto",
                    "z-index": "999999",
                    "position": "fixed !important;",
                });
            }, 500);
        }
    });
  
}

$('#ssn').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});

$('.search_patient').change(function(){
    showDemoGraphicDetails();
});
var allDataArray = [];
function showDemoGraphicDetails(){
    id = $('#patient_id').val();
    
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _DETAILS_PATIENTDATA,
        data: {
            "id": id
        },
        success: function(res) {
           allDataArray = [];
            htmlsresp = '';
            htmlsresp += `<label for="">Related Data</label> 
            <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th nowrap>Portal ID</th>
                                    <th nowrap>Agency Name</th>
                                    <th nowrap>Name</th>
                                    <th nowrap>Mobile</th>
                                    <th nowrap>Birth Date</th>
                                    <th nowrap>Archive</th>
                                    <th nowrap>Status</th>
                                    <th nowrap>Action</th>
                                </tr>
                            </thead>
                            <tbody>`;
            if (JSON.parse(res).length > 0) {
                
                 $.each(JSON.parse(res),function(key,value){
                    allDataArray.push(value);
                    var archive =  value.archived_at == null ? 'No' : 'Yes';
                  
                    if(value.status !="" && value.status !=null){
                        if (value.status.toLowerCase() == 'pending') {
                            var status_label = "<label class='badge badge-warning'>Pending</label>";
                        }
    
                        if (value.status.toLowerCase() == 'booked') {
                            var status_label = "<label class='badge badge-info'>Booked</label>";
                        }
    
                        if (value.status.toLowerCase() == 'completed') {
                            var status_label = "<label class='badge badge-success'>Completed</label>";
                        }
    
                        if (value.status.toLowerCase() == 'cancelled') {
                            var status_label = "<label class='badge badge-danger'>Cancelled</label>";
                        }
    
                        if (value.status.toLowerCase() == 'noshow') {
                            var status_label = "<label class='badge badge-light'>No Show</label>";
                        }
    
                        if (value.status.toLowerCase() == 'refused') {
                            var status_label = "<label class='badge badge-danger'>Refused</label>";
                        }
    
                        if (value.status.toLowerCase() == 'processing') {
                            var status_label = "<label class='badge badge-info'>processing</label>";
                        }
    
                        if (value.status.toLowerCase() == 'arrived') {
                            var status_label = "<label class='badge badge-primary'>Arrived</label>";
                        }
    
                        if (value.status.toLowerCase() == 'checkin') {
                            var status_label = "<label class='badge badge-primary'>Mark as ClockIn</label>";
                        }
    
                        if (value.status.toLowerCase() == 'not interested') {
                            var status_label = "<label class='badge badge-primary'>Not Interested</label>";
                        }
                        if (value.status.toLowerCase() == 'hospitalized/rehab') {
                            var status_label = "<label class='badge badge-secondary'>Hospitalized/Rehab</label>";
                        }
                        if (value.status.toLowerCase() == 'unabletocontact') {
                            var status_label = "<label class='badge badge-primary'>Unable To Contact</label>";
                        }
                        if(value.status == '1st Attempt - Unable to Contact' || value.status == '2nd Attempt - Unable to Contact' || value.status == '3rd Attempt - Unable to Contact' || value.status == 'Patient Asked to Reschedule' || value.status == 'New Order Received'){
                            var status_label = "<label class='badge badge-info'>"+ value.status+"</label>";
                        }

                        if (value.status == 'Telehealth Completed' || value.status == 'Telehealth Completed , Pending Forms' || value.status == 'Form Completed' || value.status == 'Service Provided'){
                            var status_label = "<label class='badge badge-success'>"+ value.status+"</label>";
                        }
                        if(value.status == 'Patient Deceased' || value.status == 'Appointment was missed' || value.status == 'Appointment Missed' || value.status == 'Closed Temporarily'){
                            var status_label = "<label class='badge badge-danger'>"+ value.status+"</label>";
                        }

                        if (value.status == 'Signed' || value.status == 'Signed & Sent Back to the Agency' || value.status == 'New Form Requested'){
                            var status_label = "<label class='badge badge-primary'>"+ value.status+"</label>";
                        }
                    }else{
                        var status_label = "<label class='badge badge-warning'>Pending</label>";
                    }
                    
                    var highlightClass= '';
                    if(id == value.id)
                    {
                        var highlightClass = 'selected-highlight';
                    }
                    dateString = moment(value.dob).format('MM/DD/YYYY');
                    
                    // Rearrange to DD-MM-YYYY format and return
                    var date = dateString;
                    htmlsresp += `<tr class="${highlightClass}" id="related_${key}">`;
                    htmlsresp += `<td nowrap><a class="_blank" href="${_PATIENT_VIEW}/${value.id}">${value.id}</a></td>`;
                    htmlsresp += `<td nowrap>${value.agency_detail.agency_name}</td>`;
                    htmlsresp += `<td nowrap>${value.first_name} ${value.last_name} (${value.type})</td>`;
                    htmlsresp += `<td nowrap>${value.mobile}</td>`;
                    htmlsresp += `<td nowrap>${date}</td>`;
                    htmlsresp += `<td nowrap>${archive}</td>`;
                    htmlsresp += `<td nowrap>${status_label}</td>`;
                    htmlsresp += `<td nowrap><input type="radio" name="select_redio" onclick="selectedOption(${key})"></td>`;
                    htmlsresp += `<tr>`;
                 })
                 
            } else {
                htmlsresp += '<tr><td colspan="6">No record available</td></tr>';
            }
            htmlsresp += `</tbody></table>`;
            $('#show_demographic-detail').html(htmlsresp);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    })
}
function getResponse(id,type="") {
    if (id != '') {
        var jsonencode =[];
        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: _TYPE_WISE_SERVICE_LIST,
            data: {
                "id": id,
                "jsonencode": jsonencode,
                'agency_id': $('#agency_ids').val()
            },
            success: function(res) {
                if (res != '') {
                    htmlsresp = res;
                } else {
                    htmlsresp += '<option value="">No record available</option>';
                }

                if(type !=""){
                    $('#create_service_id').html(htmlsresp);
                    // Reset branch dropdown when services are reloaded
                    resetBranchDropdown();
                }else{
                    $('#service_id').html(htmlsresp);
                }

            },
            error:function(jqr){
                showErrorAndLoginRedirection(jqr);
            }
        })

    }

    $('#hideShowRedCIN').addClass('hide');
    if(id.trim() =='Patient'){
        $('#hideShowRedCIN').removeClass('hide');
    }

    $('#transition_aid').addClass('hide');
    if(id =='Caregiver'){
        $('#transition_aid').removeClass('hide');
    }

    if($('#agency_name').val().trim() !="" || $('#last_name_id').val().trim() !="" || $('#dob_id').val().trim() !="" || $('input[name="gender"]:checked').val() !=undefined){
        
        getExistingUserData();
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
        url:_GET_COUNTRY_CODE,
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

function clearModal(){
$('#add_new_patient')[0].reset();
if (agencyUserTokenInitialized) {
    $('#agency_user_ids').tokenInput('destroy');
    agencyUserTokenInitialized = false;
}
resetBranchDropdown();
}

$('#main_form_submit_id').submit(function(e){
    var temp = 0;
    var type = $('#type_new').val();
    var search_patient = $('#search_patient').val();
    var agency_ids = $('#agency_ids').val();
    var service_id = $('#service_id').val();

    $("#agency_error").html("");
    $("#service_id_error").html("");
    $(".search_patient_error").html("");

    if(search_patient ==""){
        $('.search_patient_error').html("Please select Existing Record");
        temp++;
    }
    if(agency_ids ==""){
        $('#agency_error').html("Please select Agency");
        temp++;
    }

    // if(type =='Patient'){
    //     if (cin.trim() == "") {
    //         $('#cin_error').html("Please enter CIN/Medicaid Number");
    //         temp++;
    //     }
    // }

    if (service_id == "" || service_id ==null) {
        $('#service_id_error').html("Please select Service");
        temp++;
    }

    if (temp == 0) {
        $("#insertButton").prop('disabled', true);
        return true;
    } else {
        return false;
    }
})

$('#insurance_name').change(function(e){
    var insurance_name = $('#insurance_name').val();
    $('#other_insurance').addClass('hide');
    $('#other_insurance_name_error').html("");
    if(insurance_name =='other'){
        $('#other_insurance').removeClass('hide');
    }
})

function getHHADetails() {
    $('#load-caregiver-demographics').removeClass('hide');
    var type = $('input[name="type"]').is(":checked");
    var patient_code = $('input[name="patient_code"]').val();
    var agency_ids = $('select[name="agency_id"]').val();
    var cnt = 0;
    $('#radio_type_error').html("");
    $('#patient_code_error').html("");
    $('#agency_error').html("");
    if (type == false) {
        $('#radio_type_error').html("Please select Type");
        cnt++;
    }
    if (type == true) {
        var type = $('input[name="type"]:checked').val();
    }
    if (patient_code.trim() == '') {
        $('#patient_code_error').html("Please enter Code");
        cnt++;
    }

    if (agency_ids == '') {
        $('#agency_error').html("Please select Agency");
        cnt++;
    }

    if (cnt != 0) {
        $('#load-caregiver-demographics').addClass('hide');
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: _HHA_PATIENT_DETAILS,
            data: {
                "agency_id": agency_ids,
                "type": type,
                'patient_code': patient_code
            },
            success: function(res) {
                setTimeout(()=>{
                    $('#load-caregiver-demographics').addClass('hide');
                },5000)
               
                var json = res.data;

                if (json.length != 0) {
                    if (type == 'Caregiver') {
                        $('#cid').val(json[0].caregiver_id);
                    } else {
                        $('#cid').val(json[0].PatientID);
                    }
                    if (type == 'Caregiver') {
                        var fName = json[0].first_name;
                        var middle_name = json[0].middle_name;
                        var last_name = json[0].last_name;
                        var dob = json[0].dob;
                        var mobile_or_sms = json[0].mobile_or_sms;
                        var HomePhone = json[0].HomePhone;
                        var State = json[0].State;
                        var City = json[0].City;
                        var Zip5 = json[0].Zip5;
                        var emergencyName = json[0].emergencyName;
                        var emergencyPhone1 = json[0].emergencyPhone1;
                    }else{
                        var fName = json[0].firstName;
                        var middle_name = json[0].middleName;
                        var last_name = json[0].lastName;
                        var dob = json[0].dob;
                        var mobile_or_sms = json[0].home_phone;
                        var HomePhone = json[0].phone2;
                        var State = json[0].state;
                        var City = json[0].city;
                        var Zip5 = json[0].zip5;
                    }
                    $('input[name="first_name"]').val(fName)
                    $('input[name="middle_name"]').val(middle_name)
                    $('input[name="last_name"]').val(last_name)
                    $('input[name="dob"]').val(dob)
                    $('input[name="mobile"]').val(mobile_or_sms)
                    $('input[name="phone"]').val(HomePhone)
                    $('input[value="' + json[0].gender.toLowerCase() + '"]').prop("checked", true)
                    $('input[name="address1"]').val(json[0].address1)
                    $('input[name="address2"]').val(json[0].address2)
                    $('input[name="state"]').val(State)
                    $('input[name="city"]').val(City)
                    $('input[name="zip_code"]').val(Zip5);

                    $('#language_id option').filter(function() {
                        return $(this).text() === json[0].language;
                    }).prop('selected', true);
                   
                    $('input[name="cin"]').val(json[0].medicaid_number);
                   
                    if (json[0].discipline) {
                        $('select[name="diciplin"]').val(json[0].discipline);
                    }
                    if (json[0].medicaid_number) {
                        $('input[name="insurance_id"]').val(json[0].medicaid_number);
                    }

                    
                    $('input[name="emergency_contact_name"]').val(json[0].emergencyName);
                    $('input[name="emergency_phone"]').val(json[0].emergencyPhone1);
                    if (Zip5 != '') {
                        getCountyByZipCode(Zip5)
                    }
                }
            }
        })
    }
}

function selectedOption(key){

    var urlResponse = _FIND_PATIENT_DETAILS + "?agency_id=" + $('#agency_ids').val();
    jQuery.noConflict()
    $('.search_patient').tokenInput('destroy');

    var dob ="";
    if(allDataArray[key].dob !="" && allDataArray[key].dob !=null){
        dob = moment(allDataArray[key].dob).format('MM/DD/YYYY');
    }

    $('#type_new').val(allDataArray[key].type);
    $('#patient_id').val(allDataArray[key].id);

    $(".search_patient").tokenInput(urlResponse, {
        onAdd: function(index, val, type) {
           
        },
        prePopulate: [
            {
                id: allDataArray[key].id, 
                
                name: allDataArray[key].id + ' - '+allDataArray[key].last_name+' - '+allDataArray[key].mobile+ ' - ' +allDataArray[key].type+ ' - ' +dob+' - ' + allDataArray[key].agency_detail.agency_name+" - "+allDataArray[key].status
            }
        ],
        tokenLimit: 1,
    });
    
    $('.selected_agency').html(allDataArray[key].agency_detail.agency_name)
    $('#selected_agency').val(allDataArray[key].agency_detail.id)
    getResponse(allDataArray[key].type);

    $('#selected_agency_list').removeClass('hide');
    $('.notes_existing_class').addClass('hide');
    $('#agency_ids').val(allDataArray[key].agency_id);

    $('.selected-highlight').removeClass('selected-highlight');

    $('#related_'+key).addClass('selected-highlight');

}

function getExistingUserData(){
    var first_name =$('#agency_name').val()
    var last_name =$('#last_name_id').val();
    var dob_id =$('#dob_id').val();
    var gender = $('input[name="gender"]:checked').val();
    var type = $('input[name="type"]:checked').val();
    var mobile = $('input[name="mobile"]').val();
    $('#total_search_appointment').html("");
    
    if((first_name.trim() != '' || last_name.trim() != '' || dob_id.trim() != '' || typeof gender != 'undefined') && type !=""){
        $.ajax({
            type:"GET",
            url:_GET_APPOINTMENT_EXISTING_DATA,
            data:{
                'first_name':first_name,
                'last_name':last_name,
                'dob_id':dob_id,
                'gender':gender,
                'agency_id':$('#agency_ids').val(),
                'type':$('input[name="type"]:checked').val(),
                'ssn':$('#ssn').val(),
                'mobile_s': mobile
            },
            success:function(response){
           
                $('#total_search_appointment').html(response.data.length);
    
            },
            error: function(xhr) {
                showErrorAndLoginRedirection(xhr);
            }
        })
    }else{
   
        $('#total_search_appointment').html(0);
    }
    
}

$('input[name="gender"]').click(function(e){
    var name = $('input[name="gender"]:checked').val();
    $('#other_div_hide').addClass('hide');
    if(name.trim() =='other'){
        $('#other_div_hide').removeClass('hide');
    }
})

function clearLinkModal(){
    
}

function deleteTokenDetails(){
    $('#show_demographic-detail').html("");
}

/****************Portal Listing Page */
function bulkAppointmentDelete(){
    var selected_data = [];
    $('.cbox').each(function(){
        if($(this).is(":checked")){
            selected_data.push($(this).val());
        }
    });

    if(selected_data.length == 0){
        toastr.error('Please select at least one appointment.');
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
                                'patient_id': selected_data.join()
                            },
                            success: function(res) {
                                toastr.success('Appointment successfully deleted.');
                                location.reload();
                            },
                            error:function(jqr){
                                showErrorAndLoginRedirection(jqr);
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

function submitBulkAssignUser(){
    var bulk_user_id = $('#bulk_user_id').val();
    var cnt = 0;
    if(bulk_user_id == ''){
        $('#bulk_user_id_error').html('Please select assign user');
        cnt = 1;
    }

    if(cnt == 1){
        return false;
    }else{
        return true
    }
}

function bulkAssignUserForAppointment(){
    var selected_data = [];
    $('.cbox').each(function(){
        if($(this).is(":checked")){
            selected_data.push($(this).val());
        }
    });
    $('#bulk_appointments_id').val('');
    if(selected_data.length > 0){
      loadNyBestUser();
      $('#bulk_appointments_id').val(selected_data.join(','));
        $('#modals_bulk_assign_user').attr('data-target','#exampleModal-bulk-assign-user');
    }else{
        toastr.error('Please select at least one appointment.');
    }
}

function closeBulkAssignUserModal(){
    $('#modals_bulk_assign_user').removeAttr('data-target');
}

$('#exampleModal-bulk-assign-user').on('hide.bs.modal', function (e) {
    $('.cbox').prop("checked",false)
});

function editReferralSourceType(){
    $('#edit_referral_source_type').val($('#edit_referral_type_source_id').val());
    $('.referral_source_type_error').html("");
}

function updateReferralSources(){
    var edit_referral_source_type = $('#edit_referral_source_type').val();
    var cnt =0;
    $('.referral_source_type_error').html("");
    if(edit_referral_source_type ==''){
        $('.referral_source_type_error').html("Please select Referral Source Type");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            type:"POST",
            url:_UPDATE_APPOINTMENT_REFERRAL_SOURCE,
            data:{
                'referral_source_type':$('#edit_referral_source_type').val(),
                'id':_RECORD_ID,
                '_token':_CSRF_TOKEN
            },
            success:function(response){
                toastr.success(response.error_msg);
                $('#edit_referral_type_source_id').val(response.data.referral_type);
                $('#html_referral_type_source_id').html(response.data.referral_type);
                $('#exampleModal-edit-referral-source-modal').modal('hide');
            },
            error:function(jqr){
                showErrorAndLoginRedirection(jqr);
            }
        })
    }
}

function combineRecord(){
    var appointment_id =  $('#appointment_id').val();
    $('#appointment_id_error').html("");
    var cnt =0;
    if(appointment_id.trim() ==''){
        $('#appointment_id_error').html("Chart Id is required");
        cnt =1;
    }
    if(cnt ==1){
        return false;
    }
    else{
        $.confirm({
            title: 'Confirmation',
            columnClass: "col-md-6",
            content: 'Are you sure you want to merge the record <b>' +  appointment_id  +'</b> to <b>'+_RECORD_ID+'</b>?',
            type:'blue',
            buttons: {
                formSubmit: {
                    text: 'Yes',
                    btnClass: 'btn-primary',
                    action: function() {
                        $('#appointment_id_error').html("");
                        var cnt =0;
                        if(appointment_id.trim() ==''){
                            $('#appointment_id_error').html("Chart Id is required");
                            cnt =1;
                        }
                        $.ajax({
                            type:"post",
                            url:_PATIENT_COMBINE_APPOINTMENT,
                            data:{
                                'record_id':_RECORD_ID,
                                'appointment_id':appointment_id,
                                '_token':_CSRF_TOKEN
                            },
                            success:function(res){
                                toastr.success(res.error_msg);
                                $('#exampleModal-merge-record').modal('hide');
                                $('#appointment_id').html("");
                                mergeAppointmentData();
                            },
                            error:function(xhr){
                                showErrorAndLoginRedirection(xhr);
                            }
                        })
                    }
                },
                cancel: {
                    'text' : 'No'
                },
            },
        });
    }                   
}

function hideCombineAppointment() {
    $('#exampleModal-merge-record').modal('hide');
    $('.error').html("");
}

function unMergeAppointment(recordId,mergeAppointmentId){
    $.confirm({
        title: 'Confirmation',
        columnClass: "col-md-6",
        content: 'Are you sure you want to unmerge the record <b>' +  mergeAppointmentId  +'</b> to <b>'+recordId+'</b>?',
        buttons: {
            formSubmit: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type:"post",
                        url:_PATIENT_UNMERGE_APPOINTMENT,
                        data:{
                            'record_id':recordId,
                            'appointment_id':mergeAppointmentId,
                            '_token':_CSRF_TOKEN
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                       
                        location.reload();

                        },
                        error:function(xhr){
                            showErrorAndLoginRedirection(xhr);
                        }
                    })
                }
            },
            cancel: {
                'text' : 'No'
            },
        },
    });
}

function loadExistingData(){
    var res = 0;
    if($('#agency_name').val().trim() !="" || $('#last_name_id').val().trim() !="" || $('#dob_id').val().trim() !="" || $('#ssn').val().trim() !=""){
        var first_name =$('#agency_name').val()
        var last_name =$('#last_name_id').val();
        var dob_id =$('#dob_id').val();
        var ssn =$('#ssn').val();
        var gender = $('input[name="gender"]:checked').val();
        var mobile = $('input[name="mobile"]').val();
        $('#searchLoader').attr('style','display:flex');
     
            $.ajax({
                async:false,
                global:false,
                type:"GET",
                url:_GET_APPOINTMENT_EXISTING_DATA,
                data:{
                    
                    'first_name':first_name,
                    'last_name':last_name,
                    'dob_id':dob_id,
                    'gender':gender,
                    'agency_id':$('#agency_ids').val(),
                    'type':$('input[name="type"]:checked').val(),
                    'ssn':ssn,
                    'mobile_s': mobile
                },
                success:function(response){
                    res =response.data.length;
                    globalFlag = response.data.length;
                    if(response.data.length > 0){
                     
                        $("#patient_add_modal").css('opacity','0')
                        existingDuplicateRecord = response.data || [];
                        getSearchModalData(existingDuplicateRecord,globalFlag,ssn,mobile);
                        res = 0;
                      
                    }else{
                        globalFlag=0;
                        res =  1;                    
                    }
                    $('#searchLoader').attr('style','display:none');
                },
                error:function(jqr){
                    showErrorAndLoginRedirection(jqr);
                }
            })

            return res;
    }
    
}

function getSearchModalData(existingDuplicateRecord,count,ssn){
    var confirm_message =" with the same first name, last name,date of birth,gender,mobile and type.";
    if(ssn.trim() !=""){
        confirm_message =" with the same first name, last name,date of birth,gender,mobile,ssn and type.";
    }
    var tableHtml = '<div class="duplicate-warning">' +
        '<strong>Warning!</strong> Found ' + count + ' existing appointment(s)'+confirm_message +
        '</div>' +
        '<div style="max-height: 300px; overflow-y: auto;">' +
        '<table class="duplicate-table">' +
        '<thead><tr>' +
        '<th ></th><th style="white-space:nowrap">Portal ID</th>' +
        '<th style="white-space:nowrap">Agency Name</th>' +
        '<th style="white-space:nowrap">Full Name(Type)</th>' +
        '<th style="white-space:nowrap">Mobile</th>' +
        '<th style="white-space:nowrap">Birth Date</th><th style="white-space:nowrap">Archive</th><th style="white-space:nowrap">Status</th><th style="white-space:nowrap">Created Date</th><th style="white-space:nowrap">Created By</th>' +
        '</tr></thead><tbody>';
var cnt=1;
    $.each(existingDuplicateRecord, function(index, data) {
        if(data.archived_at == null){
            var archived = 'No';
        }else{
            var archived = 'Yes';
        }
        var status_label  = data.status;
        var ufName="";
        var uLName="";
        if(data?.users?.first_name !=""){
            ufName = data?.users?.first_name;
        }

        if(data?.users?.last_name !=""){
            uLName = data?.users?.last_name;
        }

        var ufcFullName = ufName+" "+uLName;
        tableHtml += '<tr>' +
        '<td>' + cnt++ + '</td>' +
            '<td style="white-space:nowrap">' + data.id + '</td>' +
            '<td style="white-space:nowrap">' + (data?.agency_detail?.agency_name || '-') + '</td>' +
            '<td style="white-space:nowrap">' + data.first_name+' '+data.last_name+ ' ('+data.type+')'+ '</td>' +
            '<td style="white-space:nowrap">' + (data.mobile || '-') + '</td>' +
            '<td style="white-space:nowrap">' + (moment(data.dob).format('MM/DD/YYYY') || '-') + '</td>' +
            '<td style="white-space:nowrap">' + (archived || '-') + '</td>' +
            '<td style="white-space:nowrap">' + (status_label || '-') + '</td>' +
            '<td style="white-space:nowrap">' + (moment(data.created_date).format('MM/DD/YYYY hh:mm A') || '-') + '</td>' +
            '<td style="white-space:nowrap">' + (ufcFullName || '-') + '</td>' +
            '</tr>';
    });

    tableHtml += '</tbody></table></div>';

    $.confirm({
        title: 'Duplicate Records Found',
        content: tableHtml,
        type: 'blue',
        columnClass: 'col-md-12',
        buttons: {
            confirm: {
                text: 'Create',
                btnClass: 'btn-primary',
                action: function() {
                    skipDuplicateCheck = true;
                    createNewAppointment();
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-secondary',
                action: function() {
                    $("#patient_add_modal").css('opacity','1')
                }
            }
        }
    });
}

function createNewAppointment(){
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
                    var btn =  this.buttons.submit;
                    btn.disable();

                    var formData = new FormData($('#add_new_patient')[0]);
                    formData.append('_token',_CSRF_TOKEN);
                    formData.append('redirection',"normal");
                    $.ajax({
                        async:false,
                        global:false,
                        type:"POST",
                        url:_SAVE_PATIENT_DEMOGRAPHIC_DETAILS,
                        data:formData,
                        processData: false,
                        contentType: false,
                        success:function(res){
                            
                            toastr.success(res.error_msg);

                            window.location.href=_PATIENT_LISTING_PAGE
                           
                        },
                        error:function(jqr){
                            btn.enable();
                            showErrorAndLoginRedirection(jqr);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {
                    var btn =  this.buttons.submit;
                    btn.enable();
                    $("#patient_add_modal").css('opacity','1')
                }
            }
        }
    });
}

$('input[name="gender"]').click(function(){
    getExistingUserData();
})

$('input[name="ssn"]').change(function(){
    getExistingUserData();
})

function mergeAppointmentData(){
    $('.mergeAppointmentLoader').attr('style', '');
    $("#merge_response_id").html("");
    
    $.ajax({
       
        type:"GET",
        url:_MERGE_RECORD_LIST,
        data:{
            'id':_RECORD_ID,
        },
        success:function(response){
            $('.mergeAppointmentLoader').attr('style', 'display:none');
            
            $('#merge_response_id').html(response);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function newUnMergeAppointment(id,status){
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: 'you want to unmerge the record?',
        type:'blue',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type:"post",
                        url:_PATIENT_NEW_UNMERGE_APPOINTMENT,
                        data:{
                        
                            'status':status,
                            'id': id,
                            '_token':_CSRF_TOKEN
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            mergeAppointmentData();
                        },
                        error:function(xhr){
                            showErrorAndLoginRedirection(xhr);
                        }
                    })
                }
            },
            cancel: {
                'text' : 'Cancel'
            },
        },
    });
}

$('#edit_merge_appointment_id').click(function(){
    $('#appointment_id').val('');
    $('#appointment_id_error').html("");
})

function loadAppointmentsSection(page){
    $('#loaderAppointments').attr('style','');
    $("#appointments_response_list").html("");

    $.ajax({

        type: "GET",
        url: _APPOINTMENT_LIST,
        data: {
            "id":_RECORD_ID,
            "page": page 
        },
        success: function(res) {
            $('#loaderAppointments').attr('style','display:none');
            $('#appointments_response_list').html("")
            $('#appointments_response_list').html(res)
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    })
    return false;
}
// Branch dropdown logic - show only when agency + services exist in branch_list_link
$('body').on('change', '#create_service_id', function(){
    loadBranchDropdown();
});

function loadBranchDropdown(){
    var agencyId = $('#patient_agency_id').val() || $('#agency_ids').val();
    var serviceIds = $('#create_service_id').val();

    if(!agencyId || !serviceIds || serviceIds.length === 0){
        resetBranchDropdown();
        return;
    }

    if(typeof _GET_BRANCHES_BY_AGENCY_SERVICES === 'undefined'){
        return;
    }

    $.ajax({
        type: "GET",
        url: _GET_BRANCHES_BY_AGENCY_SERVICES,
        data: {
            'agency_id': agencyId,
            'service_ids': serviceIds
        },
        success: function(res){
            if(res.status && res.data && res.data.length > 0){
                var html = '<option value="">Select Branch</option>';
                $.each(res.data, function(i, branch){
                    html += '<option value="'+ branch.branch_id +'">'+ branch.branch_name +'</option>';
                });
                $('#patient_branch_id').html(html);
                $('#branch_dropdown_wrapper').removeClass('hide');
                $('#location_branch_wrapper').addClass('hide');
                $('#location_branch').val('');
                checkBranchMandatory(agencyId, serviceIds);
            } else {
                resetBranchDropdown();
            }
        },
        error: function(){
            resetBranchDropdown();
        }
    });
}

function resetBranchDropdown(){
    $('#patient_branch_id').html('<option value="">Select Branch</option>');
    $('#branch_dropdown_wrapper').addClass('hide');
    $('#location_branch_wrapper').removeClass('hide');
}

function resetFilterBranchDropdown(){
    $('#filter_branch_id').html('<option value="">Select Branch</option>');
}

loadFilterBranchDropdown();

function loadFilterBranchDropdown(){
    if(typeof _GET_BRANCHES === 'undefined'){
        return;
    }

    $.ajax({
        type: "GET",
        url: _GET_BRANCHES,
        success: function(res){
            if(res.status && res.data && res.data.length > 0){
                var html = '<option value="">Select Branch</option>';
                $.each(res.data, function(i, branch){
                    html += '<option value="'+ branch.id +'">'+ branch.branch_name +'</option>';
                });
                $('#filter_branch_id').html(html);
                $('#filter_branch_id').val(SELECTED_BRANCH_ID);
            } else {
                resetFilterBranchDropdown();
            }
        },
        error: function(){
            resetFilterBranchDropdown();
        }
    });
}

function checkBranchMandatory(agencyId, serviceIds){
    isBranchMandatory = false;
    $.ajax({
        type: "GET",
        url: _CHECK_MANDATORY,
        async: false,
        global: false,
        data: {
            'agency_id': agencyId,
            'service_ids': serviceIds
        },
        success: function(res){
            if(res.status && res.data == 1){
                isBranchMandatory = true;
            }
        }
    });
}

function allowSpecialCharacters(e) {
    const key = e.key;

    // ✅ Allow control keys
    const allowedKeys = [
        "Backspace", "Delete", "Tab", "Escape", "Enter",
        "ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown",
        "Home", "End"
    ];

    if (allowedKeys.includes(key)) {
        return; // allow editing/navigation
    }

    // ✅ Allow Ctrl/Cmd shortcuts (copy, paste, select all)
    if (e.ctrlKey || e.metaKey) {
        return;
    }

    // ✅ Allow letters, space, hyphen
    if (!/^[a-zA-Z -]$/.test(key)) {
        e.preventDefault();
    }
}

$('#agency_name, #last_name_id, #middle_name_id').on('keydown', allowSpecialCharacters);
$('#agency_name, #last_name_id, #middle_name_id').on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z -]/g, '');
});
