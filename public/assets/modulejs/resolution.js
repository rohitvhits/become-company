$("#res_patient_telehealth_date_id").datepicker({
    minDate: new Date(),
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    beforeShowDay: unavailable
});
document.addEventListener('DOMContentLoaded', function () {

    $("#form_res_service_id").select2({
        placeholder: "Select Service",
        allowClear: true
    });

    $("#res_service_id").select2({
        placeholder: "Select Service",
        allowClear: true
    });

    $("#res_tele_patient_service_id").select2({
        placeholder: "Select Service",
        allowClear: true
    });

    getServiceRequested();
    let currentStep = 1;
    const totalSteps = 4;
    var countErr = 0;
    let selectedTeam = null;
    let selectedTeamHtml = null;
    let selectedClinicianOption = null;
    document.getElementById('stepSummary').style.display = 'none';

    function showStep(step) {
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById('step-' + i).style.display = (i === step) ? 'block' : 'none';
        }
        document.getElementById('progressIndicator').style.width = (step / totalSteps * 100) + '%';
        updateStepSummary();
    }

    function updateStepSummary() {
        let summary = '';
        if (selectedTeamHtml) {
            summary += '<span class="summary-label">Team:</span> <b>' + selectedTeamHtml + '</b>';
            document.getElementById('stepSummary').innerHTML = summary;
            document.getElementById('stepSummary').style.display = '';
        }
    }

    // Highlight selected radio for team
    document.querySelectorAll('input[name="team"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="team"]').forEach(function (r) {
                r.parentElement.classList.remove('selected');
            });
            this.parentElement.classList.add('selected');
            selectedTeam = this.value;
            selectedTeamHtml = this.parentElement.textContent.trim();
            document.getElementById('toStep2').disabled = false;
            updateStepSummary();
        });
    });

    // Step 2: Show options based on team
    document.getElementById('toStep2').addEventListener('click', function () {
        if (!selectedTeam) return;
        showStep(2);
        getServiceRequested();
        const divs = document.querySelectorAll('#step2div div');
        divs.forEach(div => {
            div.style.display = 'none';
        });
        if (selectedTeam) {
            document.getElementById(selectedTeam).style.display = 'block';
            document.getElementById('otherTeamOptions').style.display = 'none';
        } else {
            document.getElementById('otherTeamOptions').style.display = 'block';
            document.getElementById('toStep3').disabled = false;
        }
        // Reset selection for step 2
        document.querySelectorAll('input[name="step2-option"]').forEach(function (radio) {
            radio.checked = false;
            radio.parentElement.classList.remove('selected');
        });
        document.getElementById('toStep3').disabled = true;
        selectedClinicianOption = null;
        updateStepSummary();
        currentStep = 2;
    });

    document.querySelectorAll('input[name="step2-option"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="step2-option"]').forEach(function (r) {
                r.parentElement.classList.remove('selected');
            });
            this.parentElement.classList.add('selected');
            selectedClinicianOption = this.value;
            document.getElementById('toStepSubmitId').style.display = 'none';
            document.getElementById('toStep3').style.display = '';
            if (selectedClinicianOption == 'Telehealth Completed , Pending Forms' || selectedClinicianOption == 'Signed & Sent Back to the Agency') {
              $('#notesshowingDiv').attr('style','display:none');  
            } 
            document.getElementById('toStep3').disabled = false;
            updateStepSummary();
        });
    });

    // Step 2: Next to Step 3
    document.getElementById('toStep3').addEventListener('click', function () {
        showStep(3);
        document.getElementById('cancelledReasonDiv').style.display = 'none';
        document.getElementById('refusedReasonDiv').style.display = 'none';
        document.getElementById('notesDiv').style.display = 'none';
        document.getElementById('otherResolutionDiv').style.display = 'none';
        document.getElementById('bookDiv').style.display = 'none';
        document.getElementById('telehaelthbookDiv').style.display = 'none';
        document.getElementById('notesServiceDiv').style.display = 'none';
        document.getElementById('notesServiceRequestDiv').style.display = 'none';
        document.getElementById('notesshowingDiv').style.display = 'block';
        if (selectedClinicianOption === 'Cancelled') {
            document.getElementById('cancelledReasonDiv').style.display = 'block';
            document.getElementById('cancelNotesServiceRequestDiv').style.display = 'block';
        } else if (selectedClinicianOption === 'Refused') {
            document.getElementById('refusedReasonDiv').style.display = 'block';
            document.getElementById('refuseNotesServiceRequestDiv').style.display = 'block';
        } else if (selectedClinicianOption != 'Cancelled' && selectedClinicianOption != 'Refused' && selectedClinicianOption != 'Booked' && selectedClinicianOption != "New Order Received" && selectedClinicianOption != "New Form Requested") {
            document.getElementById('notesDiv').style.display = 'block';
            document.getElementById('notesServiceRequestDiv').style.display = 'block';
        } else if (selectedClinicianOption === 'Booked') {
            if(_RECORD_TYPE == 'Caregiver'){
                document.getElementById('bookDiv').style.display = 'block';
                if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                    setTimeout(function() {
                        $('.js-example-basic-multiple').select2({width: '100%'});
                        if ($('#res_service_id').hasClass('select2-hidden-accessible')) {
                                $('#res_service_id').select2('destroy');
                            }
                            $('#res_service_id').select2({width: '100%'});
                    }, 100);
                }
            }else{
              document.getElementById('telehaelthbookDiv').style.display = 'block';  
            }
        } else if (selectedClinicianOption == 'New Order Received') {
            document.getElementById('notesDiv').style.display = 'block';
            var services = $("#init_services").val(); // Get current selection
            var servicesArray = services ? services.split(',') : []; // ["178", "179"]
            if (!servicesArray.includes("181")) { // Avoid duplicate
                servicesArray.push("181");
            }
            
            $('#form_res_service_id').val(servicesArray).trigger('change'); // Update select
            $('#notesServiceDiv').attr('style','display:');
        } else if (selectedClinicianOption === 'New Form Requested') {
            document.getElementById('notesDiv').style.display = 'block';
            var services = $('#form_res_service_id').val('');
            var initServices = $("#init_services").val();
            var servicesArray = initServices ? initServices.split(',') : []; // ["178", "179"]
            $('#form_res_service_id').val(servicesArray).trigger('change');
           $('#notesServiceDiv').attr('style','display:');
        } else {
            document.getElementById('otherResolutionDiv').style.display = 'block';
        }
        currentStep = 3;
        if (selectedClinicianOption == 'Booked') {
            document.getElementById('toStep3SubmitId').style.display = 'none';
            document.getElementById('toStep4').style.display = '';
            document.getElementById('progressIndicator').style.width = (3 / totalSteps * 100) + '%';
        } else {
            document.getElementById('toStep3SubmitId').style.display = '';
            document.getElementById('toStep4').style.display = 'none';
            document.getElementById('progressIndicator').style.width = (4 / totalSteps * 100) + '%';
        }
        updateStepSummary();
    });

    // Step 2: Next to Step 3
    document.getElementById('toStep4').addEventListener('click', function () {
        checkValidation();
        if(countErr == 0){
            showStep(4);
            currentStep = 4;
            document.getElementById('bookNotesDiv').style.display = 'none';
            document.getElementById('booknotesServiceRequestDiv').style.display = 'none';
            if (selectedClinicianOption === 'Booked') {
                document.getElementById('bookNotesDiv').style.display = 'block';
                document.getElementById('booknotesServiceRequestDiv').style.display = 'block';
            } else {
                document.getElementById('otherResolutionDiv').style.display = 'block';
            }
            updateStepSummary();
        }
    });

    // Back buttons
    document.querySelectorAll('.backStepBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (currentStep === 2) {
                showStep(1);
                currentStep = 1;
            } else if (currentStep === 3) {
                showStep(2);
                currentStep = 2;
            } else if (currentStep === 4) {
                showStep(3);
                currentStep = 3;
            }
        });
    });

    // Reset modal when opened
    $(document).on('click', '[data-fancybox]', function () {
        currentStep = 1;
        selectedTeam = null;
        selectedTeamHtml = null;
        selectedClinicianOption = null;
        showStep(1);
        document.getElementById('toStep2').disabled = true;
        document.getElementById('toStep3').disabled = true;
        document.getElementById('stepSummary').style.display = 'none';
        document.getElementById('teamForm').reset();
        document.getElementById('step2Form').reset();
        document.getElementById('lastStepForm').reset();
        document.getElementById('cancelledReasonDiv').style.display = 'none';
        document.getElementById('refusedReasonDiv').style.display = 'none';
        document.getElementById('notesDiv').style.display = 'none';
        document.getElementById('otherResolutionDiv').style.display = 'none';
        document.querySelectorAll('.radio-label').forEach(function (label) {
            label.classList.remove('selected');
        });
        updateStepSummary();
        
    });

    $('input[name="cancel_reason"]').change(function () {
        let selectedText  = $('input[name="cancel_reason"]:checked').parent('label').text().trim()
        // Example: show/hide another field
        if (selectedText === 'Other') {
            $('#cancelOtherTextDiv').show();
        } else {
            $('#cancelOtherTextDiv').hide();
        }
    });

    $('input[name="refuse_reason"]').change(function () {
        let selectedText  = $('input[name="refuse_reason"]:checked').parent('label').text().trim()
        // Example: show/hide another field
        if (selectedText === 'Other') {
            $('#refuseOtherTextDiv').show();
        } else {
            $('#refuseOtherTextDiv').hide();
        }
    });

    $('.toStepSubmit').on('click', function () {
        saveResolutionData();
    });

    function saveResolutionData() {
        cnt = 0;
        $('#cancel_notes_error').html('');
        $('#refuse_notes_error').html('');
        $('#notes_error').html('');
        $('#cancel_error').html('');
        $('#refuse_error').html('');
        $('#res_notes_error').html('');
        $('#notes_request_error').html('');
        $('#cancel_request_error').html('');
        $('#refuse_request_error').html('');
        $('#book_request_error').html('');
        $('#other_cancel_reason_error').html('');
        $('#other_refuse_reason_error').html('');
        $('#res_chart_services_error').html('');
        let cancel_reason = selectedClinicianOption == 'Cancelled' ? $('input[name="cancel_reason"]:checked').val() : '';
        let refuse_reason = selectedClinicianOption == 'Refused' ? $('input[name="refuse_reason"]:checked').val() : '';
        let cancel_reason_text = selectedClinicianOption == 'Cancelled' ? $('input[name="cancel_reason"]:checked').parent('label').text().trim() : '';
        let refuse_reason_text = selectedClinicianOption == 'Refused' ? $('input[name="refuse_reason"]:checked').parent('label').text().trim() : '';
        let notes = "";
        let services = "";
        let services_requested_id = "";
        if (cancel_reason != "") {
            notes = $('#cancelledReason').val();
            services_requested_id = $("#cancel_form_request_service_id").val();
        } else if (refuse_reason != "") {
            notes = $('#refusedReason').val();
            services_requested_id = $("#refuse_form_request_service_id").val();
        } else {
            notes = $("#notes").val();
            if(selectedClinicianOption == "New Order Received" || selectedClinicianOption == "New Form Requested"){
                services = $("#form_res_service_id").val();
            }else{
                if(selectedClinicianOption == "Booked"){
                    services_requested_id = $("#book_form_request_service_id").val();
                }else{
                    services_requested_id = $("#form_request_service_id").val();
                }
            }
            if(selectedClinicianOption == 'Booked'){
                notes = $("#res_notes").val();
            }
        }
        if(selectedClinicianOption == 'Cancelled' && cancel_reason == undefined){
            $('#cancel_error').html('Please select reason.');
            cnt =1;
        }else if(selectedClinicianOption == 'Refused' && refuse_reason == undefined){
            $('#refuse_error').html('Please select reason.');
            cnt =1;
        }else if(selectedClinicianOption == 'Cancelled' && services_requested_id == ""){
            $('#cancel_request_error').html('Please select service request');
            cnt =1;
        } else if(selectedClinicianOption == 'Refused' && services_requested_id == ""){
            $('#refuse_request_error').html('Please select service request');
            cnt =1;
        } else if(selectedClinicianOption == 'Booked' && services_requested_id == ""){
            $('#book_request_error').html('Please select service request');
            cnt =1;
        } else if(selectedClinicianOption == "New Order Received" || selectedClinicianOption == "New Form Requested"){
            if(services == ""){
                $('#res_chart_services_error').html('Please select service');
                cnt =1;
            }
        } else if(selectedClinicianOption != "New Order Received" || selectedClinicianOption != "New Form Requested" || selectedClinicianOption != 'Cancelled' || selectedClinicianOption == 'Refused' || selectedClinicianOption == 'Booked'){
            if(services_requested_id == ""){
                $('#notes_request_error').html('Please select service request');
                cnt =1;
            }
        } 
        if(selectedClinicianOption == 'Cancelled' && cancel_reason_text == 'Other' && $('#other_cancel_reason').val() == ""){
            $('#other_cancel_reason_error').html('Please enter other reason.');
            cnt =1;
        }
        if(selectedClinicianOption == 'Refused' && refuse_reason_text == 'Other' && $('#other_refuse_reason').val() == ""){
            $('#other_refuse_reason_error').html('Please enter other reason.');
            cnt =1;
        }
        if(cnt == 0){
            $('.loader_class').attr('style','display:');
            $.ajax({
                type: "POST",
                url: _SAVE_RESOLUTION_DATA,
                data: {
                    '_token': _CSRF_TOKEN,
                    'id': _RECORD_ID,
                    'team': selectedTeamHtml,
                    'resolution': selectedClinicianOption,
                    'notes': notes,
                    'cancel_reason': cancel_reason,
                    'refuse_reason': refuse_reason,
                    'services': services??'',
                    'services_requested_id': services_requested_id??'',
                    'other_cancel_reason': $('#other_cancel_reason').val(),
                    'other_refuse_reason': $('#other_refuse_reason').val(),
                },
                success: function (res) {
                    if (selectedClinicianOption == "Booked") {
                        if(_RECORD_TYPE == 'Caregiver'){
                            saveBookedData();
                        }else{
                            patientTeleSubmit();
                        }
                    }
                    if (res.data.status) {
                        updateStatus(res.data.status);
                    }
                    $.fancybox.close();
                    
                    toastr.success(res.error_msg);
                    $('.loader_class').attr('style','display:none');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                },
                error: function (jqhr) {
                    $('.loader_class').attr('style','display:none');
                    showErrorAndLoginRedirection(jqhr);
                }
            })
        }
    }

    function checkValidation(){

        countErr = 0;
        if(_RECORD_TYPE == 'Caregiver'){
            var date = $('#res_date_id').val();
            var time = $('#res_timeid').val();
            var location_id = $('#res_location_id').val();
            var times_id = $('#res_times_id').val();
            var service_id = $('#res_service_id').val();
            $('#res_date_error').html("");
            $('#res_time_error').html("");
            $('#res_location_error').html("");
            $('#res_date_error').html("");            

            if (location_id == '') {
                $('#res_location_error').html("Please select Location");
                countErr = 1;
            }
            if (service_id.length == 0) {
                $('#res_service_error').html("Please select Services");
                countErr = 1;
            }

            if (date.trim() == '') {
                $('#res_date_error').html("Please select Appointment Date ");
                countErr = 1;
            }
            if (_RECORD_TYPE == 'Caregiver') { 
                if (time.trim() == '') {
                    $('#res_time_error').html("Please select Appointment Time");
                    countErr = 1;
                }
                } else { 
                if (times_id.trim() == '') {
                    $('#res_time_error').html("Please select Appointment Time");
                    countErr = 1;
                }
            }
        }else{
            var nurse = $('#res_telehealth_nurse').val();
            var patient_telehealth_date_id = $('#res_patient_telehealth_date_id').val();
            var patient_telehealth_time_slot = $('#res_patient_telehealth_time_slot').val();
            $('#res_telehealth_nurse_error').html("");
            $('#res_patient_telehealth_date_id_error').html("");
            $('#res_patient_telehealth_time_slot_error').html("");
            $('#res_tele_patient_service_error').html("");

            if (nurse == '') {
                $('#res_telehealth_nurse_error').html("Please select Nurse");
                countErr = 1;
            }
            if (patient_telehealth_date_id == '') {
                $('#res_patient_telehealth_date_id_error').html("Please select Date");
                countErr = 1;
            }
            if (patient_telehealth_time_slot == '') {
                $('#res_patient_telehealth_time_slot_error').html("Please select Time Slot");
                countErr = 1;
            }
            if ($('#res_tele_patient_service_id').val() == '') {
                $('#res_tele_patient_service_error').html("Please select Service");
                countErr = 1;
            }
        }
    }
});

function loadResolutionData(page="") {
    $.ajax({
        type: "GET",
        url: _GET_RESOLUTION_DATA,
        data: {
            'id': _RECORD_ID,
            'page': page
        },
        success: function (res) {
            $('.resolutionLoader').attr('style', 'display:none');
            $('#resolution_log').attr('style', '');
            $('#resolution_log').html('');
            $('#resolution_log').html(res);
        },
        error: function (jqhr) {
            showErrorAndLoginRedirection(jqhr)
        }
    })
}

function updateStatus(status) {
    switch (status) {
        case 'pending':
            label = 'Pending';
            className = 'badge badge-warning';
            break;
        case 'booked':
            label = 'Booked';
            className = 'badge badge-info';
            break;
        case 'completed':
            label = 'Completed';
            className = 'badge badge-success';
            break;
        case 'in process':
        case 'processing':
            label = 'Processing';
            className = 'badge badge-secondary';
            break;
        case 'cancel':
        case 'refuese':
        case 'no show':
        case 'no answer':
            label = 'Cancelled';
            className = 'badge badge-danger';
            break;
        case 'noshow':
            label = 'No Show';
            className = 'badge badge-light';
            break;
        case 'arrived':
            label = 'Arrived';
            className = 'badge badge-primary';
            break;
        case 'refused':
            label = 'Refused';
            className = 'badge badge-light';
            break;
        case 'hospitalized/rehab':
            label = 'Hospitalized/Rehab';
            className = 'badge badge-info';
            break;
        case 'pending termination':
            label = 'Pending Termination';
            className = 'badge badge-danger';
            break;
        case 'on hold':
            label = 'On Hold';
            className = 'badge badge-secondary';
            break;
        case 'on leave':
            label = 'On Leave';
            className = 'badge badge-info';
            break;
        case 'terminated':
            label = 'Terminated';
            className = 'badge badge-danger';
            break;
        case 'unableToContact':
            label = 'Unable To Contact';
            className = 'badge badge-danger';
            break;
        case '1st Attempt - Unable to Contact':
        case '2nd Attempt - Unable to Contact':
        case '3rd Attempt - Unable to Contact':
        case 'Patient Asked to Reschedule':
        case 'New Order Received':
            label = status;
            className = 'badge badge-info';
            break;

        // Success
        case 'Telehealth Completed':
        case 'Telehealth Completed , Pending Forms':
        case 'Form Completed':
        case 'Service Provided':
            label = status;
            className = 'badge badge-success';
            break;

        // Danger
        case 'Patient Deceased':
        case 'Appointment was missed':
        case 'Appointment Missed':
        case 'Closed Temporarily':    
            label = status;
            className = 'badge badge-danger';
            break;

        // Primary
        case 'Signed':
        case 'Signed & Sent Back to the Agency':
        case 'New Form Requested':
            label = status;
            className = 'badge badge-primary';
            break;
        default:
            label = status;
            className = 'badge badge-dark';
    }

    $('#view_status_id').html(`<label class="${className}">${label}</label>`);
}

function getTimeSearchResolution() {
    var location_id = $('#res_location_id').val();
    var date_id = $('#res_date_id').val();
    var existId = APT_ID?? 0;
    if (location_id != '' && date_id != '') {
        $.ajax({

            url: APPOINTMENT_DATA,
            type: "GET",
            data: {
                "location_id": location_id,
                'start_time': date_id
            },
            async:false,
            success: function (resp) {
                var json = JSON.parse(resp);
                var htmls = '';
                $('#res_timeid').html("");
                if (json.length != 0) {
                    htmls = '<option value="">Select Appointment Time</option>';
                    $.each(json, function (i, v) {
                        var selected = '';
                        if (existId == v.id) {
                            selected = 'selected="selected"';
                        }
                        htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                            .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                    });

                } else {
                    htmls = '<option value="">No appointment schedule</option>'
                }

                $('#res_timeid').html(htmls);
                $('#res_date_time_count_div').html('');
            }

        })

    }

}

$('#res_timeid,#res_location_id').on('change', function() {
    var location_id = $('#res_location_id').val();
    var date_id = $('#res_date_id').val();
    var time_id = $('#res_timeid').val();
    if (location_id !== '' && date_id !== '' && time_id !== '') {
        $.ajax({
            url: _SCEDULE_TOTAL_TIME_COUNT,
            type: "GET",
            async:false,
            data: {
                "location_id": location_id,
                'start_time': date_id,
                'timeId': time_id
            },
            success: function(resp) {
                // Parse the JSON response if not already an object
                var json = (typeof resp === "object") ? resp : JSON.parse(resp);
                var total_slot = json.totalSloat || 0;
                var total_booked = json.totalBokked || 0;
                var total_available = json.totalRemaining || 0;
                if (Object.keys(json).length !== 0) {
                    $('#res_date_time_count_div').html(
                        `<p style="display: inline-flex;gap: 75px;align-items: center;font-size: 14px;margin-top: 9px;margin-bottom: 0px;">
                            <span><b>Total Slot:</b> <span style="color: blue; font-weight: bold;">${total_slot}</span></span>
                            <span><b>Total Booked:</b> <span style="color: red; font-weight: bold;">${total_booked}</span></span>
                            <span><b>Total Available:</b> <span style="color: green; font-weight: bold;">${total_available}</span></span>
                        </p>`
                    );
                } else {
                    $('#res_date_time_count_div').html('<p>No schedule available</p>');
                }
            },
            error: function(xhr) {
                $('#res_date_time_count_div').html('<p>Error retrieving schedule data</p>');
            }
        });
    }else{
        if ($('#res_timeid option').val() == ""){
            $('#res_date_time_count_div').html('<p>No schedule available</p>');
        }
    }
});

$('#res_date_id,#res_location_id').on('change', function() {
    var location_id = $('#res_location_id').val();
    var date_id = $('#res_date_id').val();

    if (location_id !== '' && date_id !== '') {
        $.ajax({
            url: _SCEDULE_TOTAL_COUNT,
            type: "GET",
            async:false,
            data: {
                "location_id": location_id,
                'start_time': date_id
            },
            success: function(resp) {
                // Parse the JSON response if not already an object
                var json = (typeof resp === "object") ? resp : JSON.parse(resp);
                var total_slot = json.totalSloat || 0;
                var total_booked = json.totalBokked || 0;
                var total_available = json.totalRemaining || 0;
                if (Object.keys(json).length !== 0) {
                    $('#res_date_time_div').html(
                        `<p style="display: inline-flex;gap: 75px;align-items: center;font-size: 14px;margin-top: 9px;margin-bottom: 0px;">
                            <span><b>Total Slot:</b> <span style="color: blue; font-weight: bold;">${total_slot}</span></span>
                            <span><b>Total Booked:</b> <span style="color: red; font-weight: bold;">${total_booked}</span></span>
                            <span><b>Total Available:</b> <span style="color: green; font-weight: bold;">${total_available}</span></span>
                        </p>`
                    );
                } else {
                    $('#res_date_time_div').html('<p>No schedule available</p>');
                }
            },
            error: function(xhr) {
                $('#res_date_time_div').html('<p>Error retrieving schedule data</p>');
            }
        });
    }
});

$('.resolution_date').datepicker('destroy').datepicker({
    dateFormat: "mm/dd/yy",
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    minDate:new Date(),
    beforeShowDay: unavailable
})

function selected() {
    $('#res_date_id').val('');
    $('#res_location_id').val('');
    $('#res_timeid').val('');
    $('#res_date_time_div').html('');
    $('#res_date_time_count_div').html('');
    setTimeout(() => {
        var response = SERVICEARR;
        var final = [];
        $.each(response, function (item, val) {

            final.push(val);
        })
        $(".res_new_service_id").val(final).trigger('change');
    }, 1000);
}

function saveBookedData(){
     $.ajax({
            async: false,
            global: false,
            url: APPADD,
            type: "POST",
            data: {
                "_token": _CSRF_TOKEN,
                "id" : _RECORD_ID,
                "location_id": $('#res_location_id').val(),
                "date" : $("#res_date_id").val(),
                "time" : _RECORD_TYPE =='Caregiver' ? $("#res_timeid").val() : $("#res_times_id").val() ,
                "service_id" : $("#res_service_id").val(),
            },
            success: function(res) {
                console.log('success');
            }
        })
} 

function patientTeleSubmit(){
    var formData = {
        telehealth_nurse: $('#res_telehealth_nurse').val(),
        patient_telehealth_date_id: $('#res_patient_telehealth_date_id').val(),
        patient_telehealth_time_slot: $('#res_patient_telehealth_time_slot').val(),
        id: _RECORD_ID,
        _token: _CSRF_TOKEN,
        type: _RECORD_TYPE,
        tele_patient_service_id: $('#res_tele_patient_service_id').val(),
        is_from_chart: 1,
    };
    $.ajax({
        url: TELEHEALTH_PATIENT_SCHEDULE,
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.status) {
                console.log('success');
            }
        }
    });
    return false;
}

$('#res_patient_telehealth_date_id,#res_telehealth_nurse').on('change', function() {
    var day = $('#res_patient_telehealth_date_id').val(); // Adjust selector as per your modal's day dropdown/input
    var nurse = $('#res_telehealth_nurse').val(); // Adjust selector as per your modal's day dropdown/input
    var slotDropdown = $('#res_patient_telehealth_time_slot'); // Adjust selector as per your modal's slot dropdown
    // Clear previous options
    slotDropdown.empty();
    slotDropdown.append('<option value="">Loading...</option>');
    if(day != "" && nurse != ""){
        $.ajax({
            url: '/get-telehealth-slots', // Create this endpoint in your controller
            method: 'POST',
            data: {
                day: day,
                nurse: nurse,
                _token: CSRF_TOKEN,
                type: _RECORD_TYPE
            },
            success: function(response) {
                slotDropdown.empty();
                if (response.status && response.slots.length > 0) {
                    slotDropdown.append('<option value="">Select Slot</option>');
                    response.slots.forEach(function(slot) {
                        // slot example: {start_time: "09:00", end_time: "09:15", slot: 6, booked: false}
                        var start = moment('1970-01-01 ' + slot.start_time);
                        var end = moment('1970-01-01 ' + slot.end_time);
                        var label = start.format('hh:mm A') + ' to ' + end.format('hh:mm A');
                        slotDropdown.append('<option value="' + slot.id + '">' + label + '</option>');
                    });
                } else {
                    slotDropdown.append('<option value="">No slots available</option>');
                }
            },
            error: function() {
                slotDropdown.empty();
                slotDropdown.append('<option value="">Error loading slots</option>');
            }
        });
    }
});

$('body').on('click', '.resolution-data a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadResolutionData(page);
});
getServiceRequested();
function getServiceRequested() {
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _RESOLUTION_REQUEST_SERVICE,
        data: {
            "id": _RECORD_ID,
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }
            $('#form_request_service_id').html(htmlsresp);
            $('#cancel_form_request_service_id').html(htmlsresp);
            $('#refuse_form_request_service_id').html(htmlsresp);
            $('#book_form_request_service_id').html(htmlsresp);
        }
    })
}


document.addEventListener('DOMContentLoaded', function () {
    let serviceCurrentStep = 1;
    const serviceTotalSteps = 3;
    let serviceSelectedTeam = null;
    let serviceSelectedTeamHtml = null;
    let servicesselectedClinicianOption = null;
    document.getElementById('serviceStepSummary').style.display = 'none';

    function serviceShowStep(step) {
        for (let i = 1; i <= serviceTotalSteps; i++) {
            document.getElementById('service-step-' + i).style.display = (i === step) ? 'block' : 'none';
        }
        document.getElementById('serviceProgressIndicator').style.width = (step / serviceTotalSteps * 100) + '%';
        serviceUpdateStepSummary();
    }

    function serviceUpdateStepSummary() {
        let summary = '';
        if (serviceSelectedTeamHtml) {
            summary += '<span class="summary-label">Team:</span> <b>' + serviceSelectedTeamHtml + '</b>';
            document.getElementById('serviceStepSummary').innerHTML = summary;
            document.getElementById('serviceStepSummary').style.display = '';
        }
    }

    // Highlight selected radio for team
    document.querySelectorAll('input[name="service_team"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="service_team"]').forEach(function (r) {
                r.parentElement.classList.remove('selected');
            });
            this.parentElement.classList.add('selected');
            serviceSelectedTeam = this.value;
            serviceSelectedTeamHtml = this.parentElement.textContent.trim();
            document.getElementById('serviceToStep2').disabled = false;
            serviceUpdateStepSummary();
        });
    });

    // Step 2: Show options based on team
    document.getElementById('serviceToStep2').addEventListener('click', function () {
        if (!serviceSelectedTeam) return;
        serviceShowStep(2);
        const divs = document.querySelectorAll('#serviceStep2div div');
        divs.forEach(div => {
            div.style.display = 'none';
        });
        if (serviceSelectedTeam) {
            document.getElementById('service_'+serviceSelectedTeam).style.display = 'block';
            document.getElementById('serviceOtherTeamOptions').style.display = 'none';
        } else {
            document.getElementById('serviceOtherTeamOptions').style.display = 'block';
        }
        // Reset selection for step 2
        document.querySelectorAll('input[name="services-step2-option"]').forEach(function (radio) {
            radio.checked = false;
            radio.parentElement.classList.remove('selected');
        });
        servicesselectedClinicianOption = null;
        serviceUpdateStepSummary();
        serviceCurrentStep = 2;
    });

    document.querySelectorAll('input[name="services-step2-option"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="services-step2-option"]').forEach(function (r) {
                r.parentElement.classList.remove('selected');
            });
            this.parentElement.classList.add('selected');
            servicesselectedClinicianOption = this.value;
            if(servicesselectedClinicianOption == 'Cancelled'){
                document.getElementById('serviceToStepSubmitId').style.display = 'none';
                document.getElementById('serviceToStep3').style.display = '';
                document.getElementById('serviceProgressIndicator').style.width = (2 / serviceTotalSteps * 100) + '%';
            }else if(servicesselectedClinicianOption == 'Refused'){
                document.getElementById('serviceToStepSubmitId').style.display = 'none';
                document.getElementById('serviceToStep3').style.display = '';
                document.getElementById('serviceProgressIndicator').style.width = (2 / serviceTotalSteps * 100) + '%';
            }else{
                document.getElementById('serviceToStepSubmitId').style.display = '';
                document.getElementById('serviceToStep3').style.display = 'none';
                document.getElementById('serviceProgressIndicator').style.width = (3 / serviceTotalSteps * 100) + '%';
            }
            serviceUpdateStepSummary();
        });
    });

    document.getElementById('serviceToStep3').addEventListener('click', function () {
        if (!servicesselectedClinicianOption) return;
        serviceShowStep(3);
        const divs = document.querySelectorAll('#serviceStep3div div');
        divs.forEach(div => {
            div.style.display = 'none';
        });
        document.getElementById('serviceCancelledReasonDiv').style.display = 'none';
        document.getElementById('serviceRefusedReasonDiv').style.display = 'none';
        if (servicesselectedClinicianOption == 'Cancelled') {
            document.getElementById('serviceCancelledReasonDiv').style.display = 'block';
            document.getElementById('serviceOtherTeamOptions').style.display = 'none';
        } else if (servicesselectedClinicianOption == 'Refused') {
            document.getElementById('serviceRefusedReasonDiv').style.display = 'block';
            document.getElementById('serviceOtherTeamOptions').style.display = 'none';
        } else {
            document.getElementById('serviceOtherTeamOptions').style.display = 'block';
        }
        // Reset selection for step 2
        document.querySelectorAll('input[name="services-step3-option"]').forEach(function (radio) {
            radio.checked = false;
            radio.parentElement.classList.remove('selected');
        });
        serviceUpdateStepSummary();
        serviceCurrentStep = 3;
    });
    // Back buttons
    document.querySelectorAll('.serviceBackStepBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (serviceCurrentStep === 2) {
                serviceShowStep(1);
                serviceCurrentStep = 1;
            } else if (serviceCurrentStep === 3) {
                serviceShowStep(2);
                serviceCurrentStep = 2;
            } else if (serviceCurrentStep === 4) {
                serviceShowStep(3);
                serviceCurrentStep = 3;
            }
        });
    });

    // Reset modal when opened
    $(document).on('click', '[data-fancybox]', function () {
        serviceCurrentStep = 1;
        serviceSelectedTeam = null;
        serviceSelectedTeamHtml = null;
        servicesselectedClinicianOption = null;
        serviceId = $(this).data('id');
        serviceShowStep(1);
        document.getElementById('serviceToStep2').disabled = true;
        document.getElementById('serviceStepSummary').style.display = 'none';
        document.getElementById('serviceTeamForm').reset();
        document.getElementById('serviceStep2Form').reset();
        document.querySelectorAll('.service-radio-label').forEach(function (label) {
            label.classList.remove('selected');
        });
        serviceUpdateStepSummary();
        
    });

    $('input[name="service_cancel_reason"]').change(function () {
        let selectedText  = $('input[name="service_cancel_reason"]:checked').parent('label').text().trim()
        // Example: show/hide another field
        if (selectedText === 'Other') {
            $('#serviceCancelOtherTextDiv').show();
        } else {
            $('#serviceCancelOtherTextDiv').hide();
        }
    });

    $('input[name="service_refuse_reason"]').change(function () {
        let selectedText  = $('input[name="service_refuse_reason"]:checked').parent('label').text().trim()
        // Example: show/hide another field
        if (selectedText === 'Other') {
            $('#serviceRefuseOtherTextDiv').show();
        } else {
            $('#serviceRefuseOtherTextDiv').hide();
        }
    });

    $('.serviceToStepSubmit').on('click', function () {
        saveServiceRequested(serviceId,servicesselectedClinicianOption,serviceSelectedTeamHtml);
    });
});


function saveServiceRequested(serviceId,status,team) {
    let cancel_reason = status == 'Cancelled' ? $('input[name="service_cancel_reason"]:checked').val() : '';
    let refuse_reason = status == 'Refused' ? $('input[name="service_refuse_reason"]:checked').val() : '';
    let cancel_reason_text = status == 'Cancelled' ? $('input[name="service_cancel_reason"]:checked').parent('label').text().trim() : '';
    let refuse_reason_text = status == 'Refused' ? $('input[name="service_refuse_reason"]:checked').parent('label').text().trim() : '';
    if(status == null || status == "" || status == undefined){
        toastr.error('Please select status');
    }else  if(status == 'Cancelled' && cancel_reason == undefined){
        $('#service_cancel_error').html('Please select reason.');
        cnt =1;
    }else if(status == 'Refused' && refuse_reason == undefined){
        $('#service_refuse_error').html('Please select reason.');
        cnt =1;
    }else if(status == 'Cancelled' && cancel_reason_text == 'Other' && $('#service_other_cancel_reason').val() == ""){
        $('#service_other_cancel_reason_error').html('Please enter other reason.');
        cnt =1;
    }else if(status == 'Refused' && refuse_reason_text == 'Other' && $('#service_other_refuse_reason').val() == ""){
        $('#service_other_refuse_reason_error').html('Please enter other reason.');
        cnt =1;
    }else{
        $.confirm({
            title: 'Confirmation',
            columnClass: "col-md-6",
            content: 'Are you sure you want to change the status ?',
            buttons: {
                formSubmit: {
                    text: 'Yes',
                    btnClass: 'btn-primary',
                    action: function() {
                        $.ajax({
                            async: false,
                            global: false,
                            url: SERVICE_STATUS_CHANGES,
                            type: "POST",
                            data: {
                                '_token' : _CSRF_TOKEN,
                                'patient_id' : _RECORD_ID,
                                'serviceId': serviceId,
                                'status': status,
                                'team':team,
                                'refuse_reason' : refuse_reason,
                                'cancel_reason' : cancel_reason,
                                'other_cancel_reason': $('#service_other_cancel_reason').val(),
                                'other_refuse_reason': $('#service_other_refuse_reason').val(),
                            },
                        
                            success: function (response) {
                                toastr.success(response.error_msg);
                                location.reload();
                            },
                            error: function (jqr) {
                                showErrorAndLoginRedirection(jqr)
                            },
                        });
                    }
                },
                cancel: {
                    'text' : 'No'
                },
            }
        });
    }   
}


 $(document).ready(function() {
    $('#cancel_form_request_service_id').select2();
    $('#refuse_form_request_service_id').select2();
    $('#book_form_request_service_id').select2();
    $('#form_request_service_id').select2();
  });
