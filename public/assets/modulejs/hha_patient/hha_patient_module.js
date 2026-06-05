// Pagination variables for Notes
let HHA_PATIENT_NOTES_PER_PAGE = 10;
let hhaPatientCurrentNotesPage = 1;
let hhaPatientAllNotesData = [];

// Pagination variables for Authorization
let HHA_PATIENT_AUTHORIZATION_PER_PAGE = 50;
let hhaPatientCurrentAuthorizationPage = 1;
let hhaPatientAllAuthorizationData = [];

// Pagination variables for Clinical Info
let HHA_PATIENT_CLINICAL_PER_PAGE = 50;
let hhaPatientCurrentClinicalPage = 1;
let hhaPatientAllClinicalData = [];

// Pagination variables for POC Info
let HHA_PATIENT_POC_PER_PAGE = 50;
let hhaPatientCurrentPOCPage = 1;
let hhaPatientAllPOCData = [];
let loadPOCTask = [];

// Pagination variables for Document
let HHA_PATIENT_DOC_PER_PAGE = 50;
let hhaPatientCurrentDOCPage = 1;
let hhaPatientAllDOCData = [];

// Pagination variables for Contract
let HHA_PATIENT_CONTRACT_PER_PAGE = 50;
let hhaPatientCurrentCONTRACTPage = 1;
let hhaPatientAllCONTRACTData = [];

// Pagination variables for Discipline
let HHA_PATIENT_DISPLINE_PER_PAGE = 50;
let hhaPatientCurrentDISPLINEPage = 1;
let hhaPatientAllDISPLINEData = [];

// Pagination variables for Preferences
let HHA_PATIENT_PREFERENCES_PER_PAGE = 50;
let hhaPatientCurrentPREFERENCESPage = 1;
let hhaPatientAllPREFERENCESData = [];

// Pagination variables for MDOrder
let HHA_PATIENT_MDO_PER_PAGE = 50;
let hhaPatientCurrentMDOPage = 1;
let hhaPatientAllMDOData = [];


$(function() {
    $(".wmd-view-topscroll").scroll(function() {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function() {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
});

$('body').on('click', '#cboxid', function(e) {
    var checked = $(this).is(":checked");
    if (checked == true) {
        $('.cbox').prop('checked', true);
    } else {
        $('.cbox').prop('checked', false);
    }
})

function hhaPatientAjax(page) {
    $('.shimmer_id').removeClass('hide');
    $('#response_patient_list').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url:_HHA_PATIENT_AJAX+"?page="+page,
        type: "get",
        data: {
            'agency_fk':$('#agency_fk').val(),
            'full_name':$('#full_name').val(),
            'admission_id':$('#admission_id').val(),
            'home_phone':$('#home_phone').val(),
            'coordinator_name':$('#coordinator_name').val(),
            'service_start_date':$('#service_start_date').val(),
            'dob':$('#dob').val(),
            'status':$('#status').val(),
            'sorting_column':'id',
            'sorting_order':'desc',
            'hhasyncdatetime':$('#hhasyncdatetime').val(),
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide')
            $('#response_patient_list').html(res)
            $('.location-wise-data-loader').attr('style', 'display:none');
        
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
    return  false;
}

function loadDateAndDateRangePicker(){
    $('.datepicker').datepicker();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Service Start Date': [start, end],
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

    $('.hhasyncdatetime').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Last Sync Date': [start, end],
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

        $('.hhasyncdatetime').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

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

                var values = $(this).val();
                final_array.push(values);
              
            }
        });
        $('#appointments_id').val(final_array);
        $('#show-patient-services').modal('show');
    
    }
}

function singleDataAppointment(id,agencyId) {
    $('#appointments_id').val(id);
    fetchPatientDemographics(id);
    loadExistingData(id, agencyId);
    $('#show-patient-services').modal('show');

}

function singleDataAppointmentNew(){
            
    var service_id = $('#service_id').val();
    $("#hha_document_complience_type_id_error").html("");
    if (service_id == "") {
        $('#hha_document_complience_type_id_error').html("Please select Service");
        return false;
    }
    $.confirm({
        title: "Are you sure?",
        content:"You want to create new appointment?",
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    $('#loader-update-appointment').removeClass('d-none');
                    $('#msg-save-appointment-text').text('Creating ...');
                    var formData = new FormData($('#hha-appointment-save')[0]);
                        formData.append('_token',_CSRF_TOKEN); 
                        $.ajax({
                            url: _ADD_HHA_PATIENT,
                            type: "post",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                $('#loader-update-appointment').addClass('d-none');
                                $('#msg-save-appointment-text').text('Create Record');
                                $('.cbox').prop('checked',false);
                                toastr.success(res.error_msg);
                                hhaPatientAjax(1);
                                hideDataAppointment();
                                
                                $('#show-patient-services').modal('hide');

                            },
                            error: function(xhr, status, error) {
                                $('#loader-update-appointment').addClass('d-none');
                                $('#msg-save-appointment-text').text('Create Record');
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        })
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {
                    var btn =  this.buttons.submit;
                    btn.enable();
                }
            }
        }
    });
    
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    hhaPatientAjax(page);
});

$('body').on('click', '.record_id', function(e) {
    var fields = $(this).attr('data-field');
    var sort = $(this).attr('data-sort');

    $('#sorting_column').val(fields);
    $('#sorting_order').val(sort);
    hhaPatientAjax(1);
})

function hideDataAppointment(){
    $('#service_id').val("").trigger('change');
    $('#hha-appointment-save')[0].reset();
}

function fetchPatientDemographics(id) {
    $.ajax({
        url: _FETCH_HHA_PATIENT,
        type: "get",
        data: {
            'patient_id': id
        },
        success: function(res) {
            if (res.data && res.data.length > 0) {
                var patientData = res.data[0];
                displayPatientDemographics(patientData);
            }
           
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

function displayPatientDemographics(patientData) {
    var htmlResponse = "";
    if (patientData.length !== 0) {
        var pfname = (patientData.firstName !="")?patientData.firstName:"";
        var pMname = (patientData.middleName !="")?patientData.middleName:"";
        var pLname = (patientData.lastName !="")?patientData.lastName:"";
        var address1 =(patientData.address1 !="")?patientData.address1:"";
        var address2 = (patientData.address2 !="")?patientData.address2:"";
        var cross_street = (patientData.cross_street !="")?patientData.cross_street:"";
        var city = (patientData.city !="")?patientData.city:"";
        var state = (patientData.state !="")?patientData.state:"";
        var county = (patientData.county !="")?patientData.county:"";
        var zip = (patientData.zip5 !="")?patientData.zip5:"";

        htmlResponse += `
            <div class="col-md-12">
                <div class="patient-info-header">
                    <i class="mdi mdi-account-circle mr-2"></i>Patient Information
                </div>
                <div class="patient-info-body">

                    <!-- Personal Information Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-account"></i>
                                    Personal Information
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Full Name</div>
                                            <div class="info-value">${pfname +' '+pMname+' '+pLname}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">${(patientData.gender !="")?patientData.gender:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">${(patientData.dob !="")?moment(patientData.dob).format('MM/DD/YYYY'):"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">SSN</div>
                                            <div class="info-value">${(patientData.ssn !="")?patientData.ssn:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Admission ID</div>
                                            <div class="info-value">${patientData.admission_id}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Insurance & Service Information Section -->
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-card-account-details"></i>
                                    Insurance & Service Details
                                </div>
                                <div class="row" style="height:105px">
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Medicare Number</div>
                                            <div class="info-value">${(patientData.medicare_number !="")?patientData.medicare_number:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Medicaid Number</div>
                                            <div class="info-value">${(patientData.medicaid_number !="")?patientData.medicaid_number:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Service Start Date</div>
                                            <div class="info-value">${(patientData.service_start_date !="")?moment(patientData.service_start_date).format('MM/DD/YYYY'):"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Priority Code</div>
                                            <div class="info-value">${(patientData.PriorityCode !="")?patientData.PriorityCode:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Discipline</div>
                                            <div class="info-value">${(patientData.discipline !="")?patientData.discipline:"N/A"}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Address Information Section -->
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-map-marker"></i>
                                    Address Information
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-left:5px">
                                        <div class="info-item">
                                            <div class="info-label">Full Address</div>
                                            <div class="info-value">${address1}, ${address2}, ${cross_street}, ${city}, ${state}, ${county}, ${zip}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Address1</div>
                                            <div class="info-value">${address1}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Address2</div>
                                            <div class="info-value">${(address2 !=null)?address2:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Cross Street</div>
                                            <div class="info-value">${(cross_street !=null)?cross_street:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">City</div>
                                            <div class="info-value">${city}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">State</div>
                                            <div class="info-value">${state}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">County</div>
                                            <div class="info-value">${county}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Zipcode</div>
                                            <div class="info-value">${zip}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Contact Information Section -->
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-phone"></i>
                                    Contact Information
                                </div>
                                <div class="row" style="height:180px">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-item">
                                                    <div class="info-label">Home Phone</div>
                                                    <div class="info-value">${(patientData.home_phone !="")?patientData.home_phone:"N/A"}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-item">
                                                    <div class="info-label">Phone 2</div>
                                                    <div class="info-value">${(patientData.phone2 !="")?patientData.phone2:"N/A"}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-item">
                                                    <div class="info-label">Phone 3</div>
                                                    <div class="info-value">${(patientData.phone3 !="")?patientData.phone3:"N/A"}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-item">
                                                    <div class="info-label">Emergency Contact Name</div>
                                                    <div class="info-value">${(patientData.emergencyContactName !="")?patientData.emergencyContactName:"N/A"}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-12" style="margin-left:10px">
                                        <div class="info-item">
                                            <div class="info-label">Phone 2 Description</div>
                                            <div class="info-value" style="white-space:pre-line">${(patientData.phone2Description !="")?patientData.phone2Description:"N/A"}</div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12" style="margin-left:10px">
                                        <div class="info-item">
                                            <div class="info-label">Phone 3 Description</div>
                                            <div class="info-value" style="white-space:pre-line">${(patientData.phone3Description !="")?patientData.phone3Description:"N/A"}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Care Team Section -->
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-account-group"></i>
                                    Care Team
                                </div>
                                <div class="row" style="height:101px">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Coordinator</div>
                                            <div class="info-value">${(patientData.coordinator_name !="")?patientData.coordinator_name+' ('+patientData.coordinator_id+')':"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Nurse</div>
                                            <div class="info-value">${(patientData.nurseName !="")?patientData.nurseName+' ('+patientData.nurseId+')':"N/A"}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Organization Details Section -->
                            <div class="info-section scroll-section">
                                <div class="section-title">
                                    <i class="mdi mdi-office-building"></i>
                                    Organization Details
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Office ID</div>
                                            <div class="info-value">${(patientData.officeId !="")?patientData.officeId:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Branch Name</div>
                                            <div class="info-value">${(patientData.branchName !="")?patientData.branchName:"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Location</div>
                                            <div class="info-value">${(patientData.locationName !="")?patientData.locationName+' ('+patientData.locationId+')':"N/A"}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Team</div>
                                            <div class="info-value">${(patientData.teamName !="")?patientData.teamName+' ('+patientData.teamId+')':"N/A"}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Alerts Section -->
                            <div class="info-section">
                                <div class="section-title">
                                    <i class="mdi mdi-alert-circle"></i>
                                    Alerts
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-value">${(patientData.alerts !="")?patientData.alerts:"No alerts available"}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    $('#patient-demographics').html(htmlResponse);
}

function loadExistingData(id, agencyId) {
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _FETCH_EXISTING_PATIENT,
        data: {
            'id': id,
            'agency_fk': agencyId
        },
        success: function(res) {
            var json = res.data;
            var tableResponse = "";
          
            if (json.length != 0) {
                var cnt = 1;
                $.each(json, function(i, v) {
                    tableResponse +=
                        `<tr><td>${cnt++}</td><td>${v.id}</td><td>${v.agency_detail.agency_name}</td><td><a href="${_PATIENT_VIEW}/${v.id}">${v.first_name+' '+v.last_name}</a></td><td>${v.status}</td><td>${v.type}</td><td><input type="radio" name="existing[]" value="${v.id}"></td></tr>`
                })
            } else {
                tableResponse = `<tr><td colspan="4">No record available</td></tr>`
            }

            $('#existing_record_id').html("");
            $('#existing_record_id').html(tableResponse);
        }
    })
}

$('#link-modal-popup').click(function(e) {
    var record_id = $('input[name="existing[]"]:checked').val();
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _LINK_PATIENT,
        data: {
            'id': record_id,
            'patient_id': $('#appointments_id').val(),
            'service_id':$('#service_id').val()
        },
        success: function(res) {
            toastr.success(res.error_msg);
            hideDataAppointment();
            $('#show-patient-services').modal('hide');
            hhaPatientAjax(1);
        },
        error: function(jqr) {
            toastr.error("Sorry, something went wrong. Please try again.")
        }
    });
});

function exportCsv(){
    $('#exportLoader').removeClass('d-none');
    var url = _HHA_PATIENT_EXPORT_CSV+"?agency_fk="+$('#agency_fk').val()+"&full_name="+$('#full_name').val()+"&admission_id="+$('#admission_id').val()+"&home_phone="+$('#home_phone').val()+"&coordinator_name="+$('#coordinator_name').val()+"&service_start_date="+$('#service_start_date').val()+"&dob="+$('#dob').val()+"&status="+$('#status').val()+"&sorting_column=id&sorting_order=desc&hhasyncdatetime="+$('#hhasyncdatetime').val()
    window.location.href=url
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function refresh(){
    $('#agency_fk').val('');
    $('#full_name').val('');
    $('#admission_id').val('');
    $('#home_phone').val('');
    $('#coordinator_name').val('');
    $('#service_start_date').val('');
    $('#dob').val('');
    $('#status').val('');
    
    $('#hhasyncdatetime').val('');
    hhaPatientAjax(1);
}

/**
     * Open Caregiver View Modal
     * @param {string|number} caregiverId - The ID of the caregiver to view
     * @param {string} caregiverName - The name of the caregiver (optional, for display)
     *
     * Usage: Add this to your table row action buttons:
     * <button onclick="openCaregiverModal(123, 'John Doe')" class="btn btn-sm btn-info">
     *     <i class="mdi mdi-eye"></i> View
     * </button>
*/
        
function openHHAPatientModal(agencyFk,hhaPatientId, hhaPatientFullName) {
    if (!hhaPatientId) {
        toastr.error('No patient ID set');
        return false;
    }
    
    currentPatientId = hhaPatientId;
    agencyId = agencyFk;
    loadedTabs = {}; // Reset loaded tabs

    // Set caregiver name in modal title
    $('#hhaPatientName').text(hhaPatientFullName || 'Loading...');

    // Show modal
    $('#hhaPatientViewModal').addClass('show');
    $('body').css('overflow', 'hidden'); // Prevent background scrolling

    // Load demographic details (first tab) immediately
    loadPatientTab('demographicPatient');

    // Set focus to close button for accessibility
    $('.hha-patient-modal-close').focus();
}


 /**
         * Close Caregiver View Modal
         */
 function closePatientModal() {
    $('#hhaPatientViewModal').removeClass('show');
    $('body').css('overflow', ''); // Restore scrolling
    currentPatientId = null;
    agencyId = null;
    // Reset to first tab
    $('.hha-patient-tab-button').removeClass('active');
    $('.hha-patient-tab-button').first().addClass('active');
    $('.hha-patient-tab-content').removeClass('active');
    $('.hha-patient-tab-content').first().addClass('active');
}

function loadPatientTab(tabName) {
    if (!currentPatientId) {
        toastr.error('No Patient ID set');
        return;
    }

    var contentElementId = tabName + 'Content';

    // Show shimmer loading effect
    $('#' + contentElementId).html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>

            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Make AJAX call to load tab data
    // TODO: Replace with your actual API endpoint
    loadedTabs[tabName] = true;
    if(tabName !="calendar"){
        let tabParams = {
            patient_id: currentPatientId,
            agency_id: agencyId
        };
    
        if(tabName =='notes'){
            tabParams.date = $('#notesDateRangePicker').val();
        }
        if(tabName =='medical'){
            tabParams.status = $('#hha_status_medical_id').val();
        }

        let tabNames = tabName;
        if(tabName =='demographicPatient'){
            tabNames = 'demographic';
        }
        let url = _HHA_PATIENT_DETAIL_URL + '/' + tabNames;
        if(tabName =='mdorder'){
            url =_GET_HHA_MDO_ORDER
        }
        $.ajax({
            async: false,
            global: false,
            url: url,
            type: 'GET',
            data: tabParams,

            success: function(response) {
                // Mark tab as loaded
                loadedTabs[tabName] = true;
    
                // Check if response is valid
                if (response && response.data) {
                    // Render the content based on tab type
                    renderHHAPatientTabContent(tabName, response.data);
                } else {
                    $('#' + contentElementId).html(`
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i> ${response.message || 'No data available'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let message = extractErrorMessage(xhr);

                if(tabName =='mdorder'){
                    let docHtml = `
                    <div class="card">
                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                            <h6 class="mb-0">
                                <i class="mdi mdi-shield-check text-primary"></i> Patient Documents
                               
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Document ID</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Doc Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr><td colspan="6">No documents found</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
        
                       
                    </div>
                `;
                $('#' + contentElementId).html(docHtml);
                    toastr.error(message)
                }else{
                    showErrorAndLoginRedirection(xhr)
                    $('#' + contentElementId).html(`
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert"></i> ${message}
                        </div>
                    `);
                }
                
            }
        });
    } else {
        // Calendar tab - no AJAX needed, initialize directly
        renderHHAPatientTabContent(tabName, []);
    }
}

/**
 * Attach individual click event handlers for all tabs
 */
function attachTabClickEvents() {
    // Demographic Tab
    $('#hha-patient-demographic-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadDemographicTab();
    });

    // Calendar Tab
    $('#hha-patient-calendar-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadCalendarTab();
    });

    // Availability Tab
    $('#hha-patient-authorization-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadAuthorizationTab();
    });

    // Notes Tab
    $('#hha-patient-notes-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadNotesTab();
    });

    // InService Tab
    $('#hha-patient-clinical-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadClinicTab();
    });

    // Medical Tab
    $('#hha-patient-poc-info-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadPOCInfoTab();
    });


    // Document Tab
    $('#hha-patient-document-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadDocumentTab();
    });

    $('#hha-patient-contract-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadContractsTab();
    });
    
    $('#hha-patient-discipline-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadDisciplineTab();
    });
    
    // Preferences Tab
    $('#hha-patient-preferences-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadPreferencesTab();
    });

    // Preferences Tab
    $('#hha-patient-mdorder-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadMDOTab();
    });
}

/**
 * Individual tab loading functions
 */
function loadDemographicTab() {
    activateTab('demographicPatient', $('#hha-patient-demographic-tab'));
}

function loadCalendarTab() {
    activateTab('pcalendar', $('#hha-patient-calendar-tab'));
}

function loadAuthorizationTab() {
    activateTab('authorization', $('#hha-patient-authorization-tab'));
}

function loadNotesTab() {
    activateTab('pnotes', $('#notes-tab'));
}

function loadClinicTab() {
    activateTab('clinical', $('#hha-patient-clinical-tab'));
}

function loadPOCInfoTab() {
    activateTab('pocInfo', $('#hha-patient-poc-info-tab'));
}

function loadDocumentTab() {
    activateTab('pdocument', $('#hha-patient-document-tab'));
}

function loadContractsTab() {
    activateTab('contract', $('#hha-patient-contract-tab'));
}

function loadDisciplineTab() {
    activateTab('discipline', $('#hha-patient-discipline-tab'));
}

function loadPreferencesTab() {
    activateTab('ppreferences', $('#hha-patient-preferences-tab'));
}

function loadMDOTab() {
    activateTab('mdorder', $('#hha-patient-mdorder-tab'));
}

/**
 * Activate a specific tab
 */
function activateTab(tabName, $tabButton) {
    // Remove active class from all tabs
    $('.hha-patient-tab-button').removeClass('active');
    $('.hha-patient-tab-button').attr('aria-selected', 'false');

    // Add active class to clicked tab
    $tabButton.addClass('active');
    $tabButton.attr('aria-selected', 'true');
    // Hide all tab content
    $('.hha-patient-tab-content').removeClass('active');

    // Show selected tab content
    $('#' + tabName + '-panel').addClass('active');

    // Load tab data if not already loaded
    loadPatientTab(tabName);
}

// ============================================
// KEYBOARD ACCESSIBILITY
// ============================================

// Close modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#hhaPatientViewModal').hasClass('show')) {
        closePatientModal();
    }
});

// Close modal when clicking outside
$(document).on('click', '#hhaPatientViewModal', function(e) {
    if (e.target.id === 'hhaPatientViewModal') {
        closePatientModal();
    }
});

// Focus trap within modal (cycle through focusable elements)
$('#hhaPatientViewModal').on('keydown', function(e) {
    if (e.key === 'Tab') {
        var focusableElements = $('#hhaPatientViewModal').find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        var firstElement = focusableElements.first();
        var lastElement = focusableElements.last();

        if (e.shiftKey) { // Shift + Tab
            if (document.activeElement === firstElement[0]) {
                e.preventDefault();
                lastElement.focus();
            }
        } else { // Tab
            if (document.activeElement === lastElement[0]) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
});

$(function() {

    attachTabClickEvents();
});

/**
 * Render content for each tab
 * @param {string} tabName - The name of the tab
 * @param {object} data - The data returned from the API
 *
 * Customize this function based on your data structure
 */
function renderHHAPatientTabContent(tabName, data, page = 1) {
    var contentElementId = tabName + 'Content';
    var html = '';

    switch(tabName) {
        case 'demographicPatient':
            html = renderDemographicContent(data);
            break;
        case 'pcalendar':
            html = renderPatientCalendarContent(data);
            break;
        case 'authorization':
            let final = {};
            final.authorization = data;
            html = renderAuthorizationContent(final, page);
            break;
        case 'pnotes':
            // Store original notes data for filtering
            originalNotesData = data;
            html = renderHHAPatientNotesContent(data,page);
            // Initialize date range picker after rendering
            initializeNotesDateRangePicker();
            break;
        case 'clinical':
            html = renderClinicalContent(data,page);
            break;
        case 'pocInfo':
            html = renderPOCContent(data,page);
            break;
     
        case 'pdocument':
            html = renderDocumentContent(data,page);
            break;
        
        case 'contract':
            html = renderContractContent(data,page);
            break;
    
        case 'discipline':
            html = renderDisciplineContent(data,page);
            break;

        case 'ppreferences':
            html = renderPreferencesContent(data,page);
            break;

        case 'mdorder':
                html = renderMDOContent(data,page);
                break;
        default:
            html = '<p>Content not available</p>';
    }

    $('#' + contentElementId).html(html);
}

// ============================================
// TAB CONTENT RENDERING FUNCTIONS
// Customize these based on your data structure
// ============================================

function renderDemographicContent(data) {
    // Determine status badge color
    let statusBadge = 'secondary';
    if (data.patientStatusName === 'Active') {
        statusBadge = 'success';
    } else if (data.patientStatusName === 'Inactive') {
        statusBadge = 'warning';
    } else if (data.patientStatusName === 'Terminated') {
        statusBadge = 'danger';
    }

    var coordinator_name = data.coordinator_name??"N/A";
    var coordinator_id = "";
    if(data.coordinator_id !=""){
        coordinator_id = '( ' +data.coordinator_id+' )';
    }
    return `
        <!-- Main Header -->
        <div class="row">
            <!-- Personal Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                        <h6 class="mb-0"><i class="mdi mdi-account-circle text-primary"></i> Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Patient ID:</div>
                            <div class="col-7">${data.PatientID || ''}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Full Name:</div>
                            <div class="col-7">${data.firstName || ''} ${data.middleName || ''} ${data.lastName || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Admission ID:</div>
                            <div class="col-7"><span class="badge badge-info">${data.admission_id || 'N/A'}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Date of Birth:</div>
                            <div class="col-7">${data.dob || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Gender:</div>
                            <div class="col-7">${data.gender || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">SSN:</div>
                            <div class="col-7">${data.ssn ? '***-**-' + data.ssn.slice(-4) : 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #17a2b8;">
                        <h6 class="mb-0"><i class="mdi mdi-phone text-info"></i> Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Home Phone:</div>
                            <div class="col-7">${data.home_phone || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Mobile/SMS:</div>
                            <div class="col-7">${data.notificationMobile || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Address:</div>
                            <div class="col-7">${data.address || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">City:</div>
                            <div class="col-7">${data.city || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">State/Zip:</div>
                            <div class="col-7">${data.state || 'N/A'} ${data.zip || ''}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #28a745;">
                        <h6 class="mb-0"><i class="mdi mdi-briefcase text-success"></i> Employment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Status:</div>
                            <div class="col-7"><span class="badge badge-${statusBadge}">${data.patientStatusName || 'N/A'}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Office Name:</div>
                            <div class="col-7">${data.office_name || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Team Name:</div>
                            <div class="col-7">${data.teamName || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Application Date:</div>
                            <div class="col-7">${data.applicationDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Hire Date:</div>
                            <div class="col-7">${data.hireDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">First Work Date:</div>
                            <div class="col-7">${data.firstWorkDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Last Work Date:</div>
                            <div class="col-7">${data.lastWorkDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Employment Type:</div>
                            <div class="col-7">${data.employment_type || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #ffc107;">
                        <h6 class="mb-0"><i class="mdi mdi-school text-warning"></i> Professional Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Discipline:</div>
                            <div class="col-7">${data.EmploymentTypesDiscipline || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Location:</div>
                            <div class="col-7">${data.location || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 1:</div>
                            <div class="col-7">${data.lang || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 2:</div>
                            <div class="col-7">${data.lang2 || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 3:</div>
                            <div class="col-7">${data.lang3 || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Coordinator Name :</div>
                            <div class="col-7">${coordinator_name } ${coordinator_id}</div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Card -->
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dc3545;">
                        <h6 class="mb-0"><i class="mdi mdi-phone-alert text-danger"></i> Emergency Contact</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Contact Name:</div>
                                    <div class="col-7">${data.emergencyName || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Phone:</div>
                                    <div class="col-7">${data.emergencyPhone1 || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Relationship:</div>
                                    <div class="col-7">${data.emergencyRelationShip || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderPatientCalendarContent(data = []){
    setTimeout(() => {
        $('#patientFullCalendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay,listWeek,print'
            },
            aspectRatio: 1.5,
            eventLimit: true,
            dayMaxEvents: 3,
            defaultView: 'month',
            navLinks: true,
            editable: true,
            events: function(start, end, timezone, callback) {

                var startDate = moment(start).format("YYYY-MM-DD");
                var endDate = moment(end).format("YYYY-MM-DD");

                $('#loadertag12').show();

                var id = currentPatientId;
               
                var url = _HHA_PATIENT_CALENDER_LIST+"?patient_id="+currentPatientId+"&agency_id="+agencyId;

                if (id !== "") {
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: {
                            start: startDate,
                            end: endDate,
                        },
                        success: function (res) {
                            var doc = res.data.visits;
                            $('#loadertag12').hide();
                            callback(doc);
                        }
                    });
                }
            },

            eventRender: function (event, eventElement) {
                eventElement.find(".fc-time").remove();
                eventElement.find(".fc-title").append("<br/><b>" + event.label + "</b>");
            },

        });
    }, 200);
    return `<div class="card">
        <div class="card-body">
            <!-- Calendar Legend -->
            <div class="calendar-legend">
                <div class="calendar-legend-item">
                    <div class="calendar-legend-color" style="background-color: #007bff;"></div>
                    <span>Scheduled</span>
                </div>
                <div class="calendar-legend-item">
                    <div class="calendar-legend-color" style="background-color: #28a745;"></div>
                    <span>Completed</span>
                </div>
                <div class="calendar-legend-item">
                    <div class="calendar-legend-color" style="background-color: #ffc107;"></div>
                    <span>Pending</span>
                </div>
                <div class="calendar-legend-item">
                    <div class="calendar-legend-color" style="background-color: #dc3545;"></div>
                    <span>Cancelled</span>
                </div>
                <div class="calendar-legend-item">
                    <div class="calendar-legend-color" style="background-color: #e83e8c;"></div>
                    <span>Missed</span>
                </div>
            </div>

            <!-- FullCalendar Container -->
            <div id="patientFullCalendar"></div>
        </div>
    </div>`
}


/**
 * Render Authorization Content with Table View and Pagination
 * @param {object} data - Authorization data
 * @param {number} page - Current page number
 * @returns {string} HTML content
 */
function renderAuthorizationContent(data, page = 1) {
    let authorizationHtml = '';

    if (data.authorization && data.authorization.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllAuthorizationData = data;

        let totalAuthorizations = data.authorization.length;
        let totalPages = Math.ceil(totalAuthorizations / HHA_PATIENT_AUTHORIZATION_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_AUTHORIZATION_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_AUTHORIZATION_PER_PAGE;
        let paginatedAuthorizations = data.authorization.slice(startIndex, endIndex);

        hhaPatientCurrentAuthorizationPage = page;

        // Build table rows
        let tableRows = '';
        paginatedAuthorizations.forEach(function(auth, index) {
            let authId = auth.AuthorizationID || auth.authorization_id || auth.id || 'N/A';
            let authNumber = auth.AuthorizationNumber || auth.authorization_number || 'N/A';
            let serviceType = auth.ContractName || 'N/A';
            let startDate = auth.StartDate || auth.start_date || 'N/A';
            let endDate = auth.EndDate || auth.end_date || 'N/A';
            let authorizedUnits = auth.MaxUnits  || '0';
            let usedUnits = auth.RemainingUnits || '0';
            let bankedHours = auth.BankedHours || 'N/A';
            let period = auth.Period || 'N/A';
            let weekMaxAuthorizations = auth.WeeklyMaxAuthorization  || 'N/A';
            let entirePeriodMaxAuthorization = auth.EntirePeriodMaxAuthorization  || 'N/A';
            let monthlyMaxAuthorization = auth.MonthlyMaxAuthorization || 'N/A';
            let weekday = auth.Weekday || 'N/A';
            let weekend = auth.Weekend || 'N/A';
            // Format dates
            let formattedStartDate = startDate !== 'N/A' ? moment(startDate).format('MM/DD/YYYY') : 'N/A';
            let formattedEndDate = endDate !== 'N/A' ? moment(endDate).format('MM/DD/YYYY') : 'N/A';

            // Calculate progress percentage
           
            let rowNumber = startIndex + index + 1;

            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${authId}</td>
                    <td>${serviceType} - ${auth.ContractID}</td>
                    <td><strong>${authNumber}</strong></td>
                    
                    <td>${formattedStartDate}</td>
                    <td>${formattedEndDate}</td>
                    <td>${authorizedUnits}</td>
                    <td >${usedUnits}</td>
                    <td >${bankedHours}</td>
                    <td >${period}</td>
                    <td >${weekMaxAuthorizations}</td>
                    <td >${entirePeriodMaxAuthorization}</td>
                    <td >${monthlyMaxAuthorization}</td>
                    <td>${weekday}</td>
                    <td>${weekend}</td>
                </tr>
            `;
        });

        authorizationHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Authorization Records
                        <span class="badge badge-primary ml-2">${totalAuthorizations} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>AuthorizationID</th>
                                    <th>Contract Name</th>
                                    <th>Authorization Number</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Max Units</th>
                                    <th>Remaining Units</th>
                                    <th>Banked Hours</th>
                                    <th>Period</th>
                                    <th>Weekly Max Authorization</th>
                                    <th>Entire Period Max Authorization</th>
                                    <th>Monthly Max Authorization</th>
                                    <th>Weekday</th>
                                    <th>Weekend</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                ${hhaPatientRenderAuthorizationPagination(totalPages)}
            </div>
        `;
    } else {
        authorizationHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No authorization records available</p>
                </div>
            </div>
        `;
    }

    return authorizationHtml;
}

function renderHHAPatientNotesContent(data,page){
    let notesHtml = '';

    if (data && Array.isArray(data) && data.length > 0) {
        // Sort notes by created_date (newest first)
        let sortedNotes = data;
        let totalNotes = sortedNotes.length;
        let totalPages = Math.ceil(totalNotes / HHA_PATIENT_NOTES_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_NOTES_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_NOTES_PER_PAGE;
        let paginatedNotes = sortedNotes.slice(startIndex, endIndex);
        
        hhaPatientAllNotesData = data;
        hhaPatientCurrentNotesPage = page;
        let sortedNotess = paginatedNotes.sort(function(a, b) {
            let dateA = new Date(a.created_date || 0);
            let dateB = new Date(b.created_date || 0);
            return dateB - dateA;
        });
        // Build note cards based on NOTES_UI_DESIGN.md structure
        let noteCards = '';
        sortedNotess.forEach(function(note, index) {
            // Get note fields from JSON structure
            let caregiverNoteId = note.CaregiverNoteID || note.caregiver_note_id || '';
            let caregiverId = note.CaregiverID || note.caregiver_id || '';
            let noteDate = note.NoteDate || note.note_date || note.created_date || 'N/A';
            let noteContent = note.Note || note.note || 'No content';
            let createdDate = note.created_date || note.NoteDate || 'N/A';

            // Analyze note content for categorization
            let categoryColor = 'primary';
            let categoryIcon = 'mdi-note-text';
            let noteContentLower = noteContent.toLowerCase();
            // Format dates
            let formattedNoteDate = 'N/A';
            let timeAgo = '';
            if (createdDate !== 'N/A') {
                formattedNoteDate = moment(createdDate).format('MMM DD, YYYY h:mm A');
                timeAgo = moment(createdDate).fromNow();
            }

            // Truncate long notes for preview
            let notePreview = noteContent;
            let isLongNote = noteContent.length > 200;
            if (isLongNote) {
                notePreview = noteContent.substring(0, 200) + '...';
            }

            noteCards += `
                <div class="card mb-2 note-card" style="border-left: 3px solid primary">
                    <div class="card-body" style="padding: 12px;">
                        <div class="row">
                            <div class="col-auto">
                                <div class="bg-${categoryColor} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="mdi ${categoryIcon}" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">#${caregiverNoteId}</h6>
                                        
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline"></i> ${timeAgo}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-1" style="line-height: 1.5; font-size: 0.9rem;" id="note-preview-${caregiverNoteId}">
                                    ${notePreview}
                                    ${isLongNote ? ' <a href="javascript:void(0);" onclick="toggleNoteContent(\'' + caregiverNoteId + '\')" class="text-primary">more</a>' : ''}
                                </p>
                                <div style="display: none;" id="note-full-${caregiverNoteId}">
                                    <p class="mb-1" style="line-height: 1.5; font-size: 0.9rem;">
                                        ${noteContent}
                                        ${isLongNote ? ' <a href="javascript:void(0);" onclick="toggleNoteContent(\'' + caregiverNoteId + '\')" class="text-primary">less</a>' : ''}
                                    </p>
                                </div>
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar"></i> ${noteDate}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        notesHtml = `
            <!-- Notes List -->
            <div class="row">
                <div class="col-12">
                    ${noteCards}
                    ${hhaPatientRenderNotesPagination(totalPages)}
                </div>
            </div>
        `;
    } else {
        notesHtml = `
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="mdi mdi-note-text-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No notes available</p>
                </div>
            </div>
        `;
    }

    return notesHtml;
}

function renderClinicalContent(data, page = 1){
    let authorizationHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllClinicalData = data;

        let totalClinics = data.length;
        let totalPages = Math.ceil(totalClinics / HHA_PATIENT_CLINICAL_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_CLINICAL_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_CLINICAL_PER_PAGE;
        let paginatedClinic = data.slice(startIndex, endIndex);

        hhaPatientCurrentClinicalPage = page;

        // Build table rows
        let tableRows = '';
        paginatedClinic.forEach(function(auth, index) {
            let nurse_visitis_due = auth.NursingVisitsDue || 'N/A';
            let mdorder_required = auth.MDOrderRequired || 'N/A';
            let mdorder_due = auth.MDOrderDue || 'N/A';
            let mdo_visitis_due = auth.MDVisitDue || 'N/A';
           
            // Calculate progress percentage
           
            let rowNumber = startIndex + index + 1;

            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${nurse_visitis_due}</td>
                    <td>${mdorder_required}</td>
                    <td>${mdorder_due}</td>
                    <td>${mdo_visitis_due}</td>
                    
                </tr>
            `;
        });

        authorizationHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Clinical Records
                        <span class="badge badge-primary ml-2">${totalClinics} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nursing Visits Due</th>
                                    <th>MDOrder Required</th>
                                    <th>MDOrder Due</th>
                                    <th>MDVisit Due</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                ${hhaPatientRenderClinicPagination(totalPages)}
            </div>
        `;
    } else {
        authorizationHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Clinical records available</p>
                </div>
            </div>
        `;
    }

    return authorizationHtml;
}

function renderPOCContent(data,page=1){
    let pocHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllPOCData = data;

        let totalPocs = data.length;
        let totalPages = Math.ceil(totalPocs / HHA_PATIENT_POC_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_POC_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_POC_PER_PAGE;
        let paginatedPoc = data.slice(startIndex, endIndex);

        hhaPatientCurrentPOCPage = page;

        // Build table rows
        let tableRows = '';
        loadPOCTask = [];
        paginatedPoc.forEach(function(auth, index) {
            let poc_id = auth.PatientInfo.POCID || 'N/A';
            let poc_no = auth.PatientInfo.POCNumber || 'N/A';
            let poc_start_date = auth.PatientInfo.StartDate || 'N/A';
            let poc_end_date = auth.PatientInfo.StopDate || 'N/A';
            
            let poc_created_date = moment(auth.PatientInfo.CreatedDate).format('MM/DD/YYYY hh:mm A') || 'N/A';
            let poc_notes_raw = auth.PatientInfo.Notes || 'N/A';
            let poc_notes = (function(text) {
                var limit = 60;
                if (text === 'N/A' || text.length <= limit) return text;
                var uid = 'pocnote_' + Date.now() + '_' + index;
                var short = text.substring(0, limit).trim();
                return '<span id="short_' + uid + '">' + short + '... ' +
                    '<a href="javascript:void(0)" style="font-size:11px;" onclick="togglePocNote(\'' + uid + '\')">Read more</a>' +
                    '</span>' +
                    '<span id="full_' + uid + '" style="display:none;">' + text + ' ' +
                    '<a href="javascript:void(0)" style="font-size:11px;" onclick="togglePocNote(\'' + uid + '\')">Read less</a>' +
                    '</span>';
            })(poc_notes_raw);
            // Calculate progress percentage
           
            let rowNumber = startIndex + index + 1;
            
            if(auth.Tasks && auth.Tasks.length > 0) {
                loadPOCTask[poc_id] = [];
                $.each(auth.Tasks, function(e, v){
                    loadPOCTask[poc_id].push({
                        code: v.Code || '-',
                        category_name: v.CategoryName || '-',
                        task_name: v.Name || '-',
                        as_needed: v.AsNeeded || '-',
                        weekly_min: v.WeeklyMin || '-',
                        weekly_max: v.WeeklyMax || '-',
                        sunday: v.Sunday || '-',
                        monday: v.Monday || '-',
                        tuesday: v.Tuesday || '-',
                        wednesday: v.Wednesday || '-',
                        thursday: v.Thursday || '-',
                        friday: v.Friday || '-',
                        saturday: v.Saturday || '-'
                    });
                });
            }
           
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${poc_id}</td>
                    <td>${poc_no}</td>
                    <td>${poc_start_date}</td>
                    <td>${poc_end_date}</td>
                    <td>${poc_created_date}</td>
                    <td>${poc_notes}</td>
                    <td><a href="javascript:void(0)" onclick="viewPOCTasks(${poc_id})"><i class="fa fa-eye"></i></a></td>
                </tr>
            `;
        });

        pocHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> POC Information Records
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>POC ID</th>
                                    <th>POC Number</th>
                                    <th>Start Date</th>
                                    <th>Stop Date</th>
                                    <th>Created Date</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                ${hhaPatientRenderPOCPagination(totalPages)}
            </div>
        `;
    } else {
        pocHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Clinical records available</p>
                </div>
            </div>
        `;
    }

    return pocHtml;
}

function renderDocumentContent(data,page=1){
    let docHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllDOCData = data;

        let totalPocs = data.length;
        let totalPages = Math.ceil(totalPocs / HHA_PATIENT_DOC_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_DOC_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_DOC_PER_PAGE;
        let paginatedDoc = data.slice(startIndex, endIndex);

        hhaPatientCurrentPOCPage = page;

        // Build table rows
        let tableRows = '';
        loadDOCList = [];
        paginatedDoc.forEach(function(auth, index) {
            let poc_id = auth.patientDocId || 'N/A';
            let poc_no = auth.patientDocumentType || 'N/A';
            let poc_start_date = auth.description || 'N/A';
            let poc_end_date = auth.fileName || 'N/A';
            
            let poc_created_date = moment(auth.CreatedOn).format('MM/DD/YYYY hh:mm A') || 'N/A';
            // Calculate progress percentage
           
            let rowNumber = startIndex + index + 1;
            
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${poc_id}</td>
                    <td>${poc_no}</td>
                    <td>${poc_start_date}</td>
                    <td>${poc_end_date}</td>
                    <td>${poc_created_date} <br> ${auth.CreatedBy}</td>
                    <td  id="td_${auth.patientDocId}"><a target="_blank" id="document_${auth.patientDocId}" onclick="getDowloadPatientDocumentWithPortal(${auth.patientDocId}); return false;"><i class="fa fa-download"></i> Download</a></td>
                </tr>
            `;
        });

        docHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Document
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Doc Id</th>
                                    <th>Document Type</th>
                                    <th>Description</th>
                                    <th>File Name</th>
                                    <th>Created Date/Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                ${hhaPatientRenderDOCPagination(totalPages)}
            </div>
        `;
    } else {
        docHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Document records available</p>
                </div>
            </div>
        `;
    }

    return docHtml;
}

function renderContractContent(data,page){
    let docHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllCONTRACTData = data;

        let totalPocs = data.length;
        let totalPages = Math.ceil(totalPocs / HHA_PATIENT_CONTRACT_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_CONTRACT_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_CONTRACT_PER_PAGE;
        let paginatedDoc = data.slice(startIndex, endIndex);

        hhaPatientCurrentCONTRACTPage = page;

        // Build table rows
        let tableRows = '';
        
        paginatedDoc.forEach(function(auth, index) {
            let placementID = auth.placementID || 'N/A';
            let contract = auth.contract || 'N/A';
            let isPrimaryContract = auth.isPrimaryContract || 'N/A';
            let altPatientID = auth.altPatientID || 'N/A';
            let serviceStartDate = auth.serviceStartDate || 'N/A';
            let sourceOfAdmission = auth.sourceOfAdmission || 'N/A';
            let serviceCode = auth.serviceCode || 'N/A';
            let dischargeDate = auth.dischargeDate || 'N/A';
            let dischargeTo = auth.dischargeTo || 'N/A';
            // Calculate progress percentage
           
            let rowNumber = startIndex + index + 1;
            
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${placementID}</td>
                    <td>${contract}</td>
                    <td>${isPrimaryContract}</td>
                    <td>${altPatientID}</td>
                    <td>${serviceStartDate}</td>
                    <td>${sourceOfAdmission}</td>
                    <td>${serviceCode}</td>
                    <td>${dischargeDate}</td>
                    <td>${dischargeTo}</td>
                    
                </tr>
            `;
        });

        docHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Contract
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Placement ID</th>
                                    <th>Contract</th>
                                    <th>Is Primary Contract</th>
                                    <th>Alt Patient ID</th>
                                    <th>Service Start Date</th>
                                    <th>Source Of Admission</th>
                                    <th>Service Code</th>
                                    <th>Discharge Date</th>
                                    <th>Discharge To</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                ${hhaPatientRenderCONTRACTPagination(totalPages)}
            </div>
        `;
    } else {
        docHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Document records available</p>
                </div>
            </div>
        `;
    }

    return docHtml;
}

function renderDisciplineContent(data,page){
    let docHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllDISPLINEData = data;

        let totalPocs = data.length;
        let totalPages = Math.ceil(totalPocs / HHA_PATIENT_DISPLINE_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_DISPLINE_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_DISPLINE_PER_PAGE;
        let paginatedDoc = data.slice(startIndex, endIndex);

        hhaPatientCurrentDISPLINEPage = page;

        // Build table rows
        let tableRows = '';
        
        paginatedDoc.forEach(function(auth, index) {
            let disciplineID = auth.disciplineID || 'N/A';
            let disciplineName = auth.disciplineName || 'N/A';
           
            let rowNumber = startIndex + index + 1;
            
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${disciplineID}</td>
                    <td>${disciplineName}</td>
                   
                </tr>
            `;
        });

        docHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Discipline
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Discipline Id</th>
                                    <th>Discipline Name</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>

                ${hhaPatientRenderDISPLINEPagination(totalPages)}
            </div>
        `;
    } else {
        docHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Document records available</p>
                </div>
            </div>
        `;
    }

    return docHtml;
}

function renderPreferencesContent(data,page=1){
    let docHtml = '';
    if (data && data.length > 0) {
        // Store all authorization data for pagination
        hhaPatientAllPREFERENCESData = data;
  
        let totalPocs = data.length;
        let totalPages = Math.ceil(totalPocs / HHA_PATIENT_PREFERENCES_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_PREFERENCES_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_PREFERENCES_PER_PAGE;
        let paginatedDoc = data.slice(startIndex, endIndex);

        hhaPatientAllPREFERENCESData = page;

        // Build table rows
        let tableRows = '';
        
        paginatedDoc.forEach(function(auth, index) {
            let preferenceID = auth.preferenceID || 'N/A';
            let preferenceName = auth.preferenceName || 'N/A';
            let preferenceValue = auth.preferenceValue || 'N/A';
            let preferenceType = auth.preferenceType || 'N/A';
            let rowNumber = startIndex + index + 1;
            
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${preferenceID}</td>
                    <td>${preferenceName}</td>
                    <td>${preferenceValue}</td>
                    <td>${preferenceType}</td>
                </tr>
            `;
        });

        docHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Preferences
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Preference Name</th>
                                    <th>Preference Value</th>
                                    <th>Preference Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>

                ${hhaPatientRenderPREFERENCESPagination(totalPages)}
            </div>
        `;
    } else {
        docHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-shield-alert-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Preferences records available</p>
                </div>
            </div>
        `;
    }

    return docHtml;
}

function renderMDOContent(data, page = 1){
    hhaPatientAllMDOData = data;
    hhaPatientCurrentMDOPage = page;

    let docHtml = "";
    if (data && data.length > 0) {
        let totalItems = data.length;
        let totalPages = Math.ceil(totalItems / HHA_PATIENT_MDO_PER_PAGE);
        let startIndex = (page - 1) * HHA_PATIENT_MDO_PER_PAGE;
        let endIndex = startIndex + HHA_PATIENT_MDO_PER_PAGE;
        let paginatedDoc = data.slice(startIndex, endIndex);
        if(page ==1){
            updateStatistics(data);
        }
    
        let tableRows = '';
        
        paginatedDoc.forEach(function(doc, index) {
            let id = doc.id || 'N/A';
            let start_date = doc.start_date || 'N/A';
            let end_date = doc.end_date || 'N/A';
            let docStatus = getDocStatusBadgeNew(doc.docStatus);
            let rowNumber = startIndex + index + 1;
            
            tableRows += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${id}</td>
                    <td>${start_date}</td>
                    <td>${end_date}</td>
                    <td>${docStatus}</td>
                    <td>
                    <div class="action-buttons d-flex gap-2 align-items-center justify-content-center flex-wrap">
                        <button class="btn btn-sm btn-action btn-action-download" onclick="downloadHHAMDODocumentNew('${doc.id}')" title="Download Document">
                            <span class="spinner-border spinner-border-sm d-none docs-doc-${doc.id}" role="status" aria-hidden="true"></span>
                            <span class="btn-icon-text">
                                <i class="fa fa-download"></i>
                               
                            </span>
                        </button>
                        
                    </div>
                </td>
                </tr>
            `;
        });

        docHtml = `
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                    <h6 class="mb-0">
                        <i class="mdi mdi-shield-check text-primary"></i> Patient Documents
                        <span class="badge badge-primary ml-2">${totalPocs} Total</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Document ID</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Doc Status</th>
                                    <th>Actions</th>s
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>

                ${hhaPatientRenderMDOPagination(totalPages)}
            </div>
        `;
        // Render pagination
        
    } else {
        updateStatistics(data);
    }
}

function hhaPatientRenderNotesPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `<nav class="mt-3"><ul class="hha_caregiver_notes pagination justify-content-center">`;

    html += `
        <li class="page-item ${hhaPatientCurrentNotesPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${hhaPatientCurrentNotesPage - 1})">Prev</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentNotesPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${i})">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${hhaPatientCurrentNotesPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${hhaPatientCurrentNotesPage + 1})">Next</a>
        </li>
    `;

    html += `</ul></nav>`;
    return html;
}

/**
 * Change Notes Page
 * @param {number} page - Page number to navigate to
 */
function changePatientNotesPage(page) {
    $('#notesContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('pnotes', hhaPatientAllNotesData, page);
    }, 1000);
}

/**
 * Render Authorization Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderAuthorizationPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentAuthorizationPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${hhaPatientCurrentAuthorizationPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentAuthorizationPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentAuthorizationPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentAuthorizationPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentAuthorizationPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${hhaPatientCurrentAuthorizationPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change Authorization Page
 * @param {number} page - Page number to navigate to
 */
function changePatientAuthorizationPage(page) {
    if (!hhaPatientAllAuthorizationData || !hhaPatientAllAuthorizationData.authorization) {
        toastr.error('No authorization data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllAuthorizationData.authorization.length / HHA_PATIENT_AUTHORIZATION_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#authorizationContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('authorization', hhaPatientAllAuthorizationData.authorization, page);
    }, 500);
}

/**
 * Toggle Note Content (Expand/Collapse)
 * @param {string} noteId - The note ID to toggle
 */
function toggleNoteContent(noteId) {
    var preview = $('#note-preview-' + noteId);
    var full = $('#note-full-' + noteId);

    if (full.is(':visible')) {
        full.hide();
        preview.show();
    } else {
        preview.hide();
        full.show();
    }
}

/**
 * Initialize notes date range picker
 */
function initializeNotesDateRangePicker() {
    setTimeout(function() {
        if ($('#notesDateRangePicker').length) {
            $('#notesDateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months').startOf('month'), moment().endOf('month')]
                },
                opens: 'left'
            });

            $('#notesDateRangePicker').off('apply.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                filterNotesByDateRange(picker.startDate, picker.endDate);
            });

            $('#notesDateRangePicker').off('cancel.daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
                clearNotesDateFilter();
            });
        }
    }, 200);
}

/**
 * Filter Notes by Date Range
 * @param {moment} startDate - Start date
 * @param {moment} endDate - End date
 */
function filterNotesByDateRange(startDate, endDate) {
    if (!hhaPatientAllNotesData || hhaPatientAllNotesData.length === 0) {
        return;
    }

    // Filter notes by date range
    var filteredNotes = hhaPatientAllNotesData.filter(function(note) {
        var noteDate = moment(note.created_date || note.NoteDate);
        return noteDate.isBetween(startDate, endDate, 'day', '[]');
    });

    // Reset to page 1 and render filtered notes
    hhaPatientCurrentNotesPage = 1;
    renderHHAPatientTabContent('pnotes', { notes: filteredNotes }, 1);
}

/**
 * Clear Notes Date Filter
 */
function clearNotesDateFilter() {
    $('#notesDateRangePicker').val('');

    // Reset to all notes
    if (hhaPatientAllNotesData && hhaPatientAllNotesData.length > 0) {
        hhaPatientCurrentNotesPage = 1;
        renderHHAPatientTabContent('notes', { notes: hhaPatientAllNotesData }, 1);
    }
}

/**
 * Render Authorization Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderClinicPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentClinicalPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${hhaPatientCurrentClinicalPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentClinicalPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentClinicalPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientClinicalPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentClinicalPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientClinicalPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientClinicalPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentAuthorizationPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientClinicalPage(${hhaPatientCurrentAuthorizationPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change Authorization Page
 * @param {number} page - Page number to navigate to
 */
function changePatientClinicalPage(page) {
    if (!hhaPatientAllClinicalData || !hhaPatientAllClinicalData) {
        toastr.error('No clinical data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllClinicalData.length / HHA_PATIENT_CLINICAL_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#clinicalContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('clinical', hhaPatientAllClinicalData, page);
    }, 500);
}

/**
 * Render POC Information Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderPOCPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentPOCPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientAuthorizationPage(${hhaPatientCurrentPOCPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentPOCPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentPOCPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientPOCPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentPOCPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientPOCPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientPOCPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentPOCPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientPOCPage(${hhaPatientCurrentPOCPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change POC Page
 * @param {number} page - Page number to navigate to
 */
function changePatientPOCPage(page) {
    if (!hhaPatientAllPOCData || !hhaPatientAllPOCData) {
        toastr.error('No poc data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllPOCData.length / HHA_PATIENT_POC_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#pocInfoContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('pocInfo', hhaPatientAllPOCData, page);
    }, 500);
}

const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

function viewPOCTasks(pocId){
    showTasksModal([]);
    if (loadPOCTask[pocId] && loadPOCTask[pocId]) {
        showTasksModal(loadPOCTask[pocId]);
    }
}

function showTasksModal(tasks) {
    // Generate headers dynamically
    generateTasksTableHeaders();

    // Populate task rows
    populateTasksTable(tasks);

    // Show the modal (Bootstrap 4 syntax)
    $('#pocTasksModal').modal('show');
}

function togglePocNote(uid) {
    var $short = $('#short_' + uid);
    var $full  = $('#full_'  + uid);
    if ($short.is(':visible')) {
        $short.hide();
        $full.show();
    } else {
        $full.hide();
        $short.show();
    }
}

/**
 * Generate dynamic table headers for tasks table
 * This creates the header row with fixed columns plus dynamic day columns
 */
function generateTasksTableHeaders() {
    const headerRow = document.getElementById('tasksTableHeader');

    // Fixed columns
    const fixedHeaders = ['Code', 'Category Name', 'Task Name', 'As Needed', 'Weekly Min - Max'];

    let headerHTML = '';

    // Add fixed headers
    fixedHeaders.forEach(header => {
        headerHTML += `<th nowrap>${header}</th>`;
    });

    // Dynamically add day headers using the days array
    daysOfWeek.forEach(day => {
        headerHTML += `<th nowrap>${day}</th>`;
    });

    headerRow.innerHTML = headerHTML;
}

function populateTasksTable(tasks) {
    const tbody = document.getElementById('tasksTableBody');

    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center">No tasks available</td></tr>';
        return;
    }

    let rowsHTML = '';

    tasks.forEach(task => {
        rowsHTML += '<tr>';

        // Fixed columns
        rowsHTML += `<td>${task.code || '-'}</td>`;
        rowsHTML += `<td>${task.category_name || '-'}</td>`;
        rowsHTML += `<td>${task.task_name || '-'}</td>`;
        rowsHTML += `<td>${task.as_needed || '-'}</td>`;
        rowsHTML += `<td>${task.weekly_min +' - '+task.weekly_max || '-'}</td>`;

        // Dynamically populate day columns
        // Loop through days array and get values from task object
        daysOfWeek.forEach(day => {
            const dayKey = day.toLowerCase(); // Convert to lowercase to match object keys
            rowsHTML += `<td>${task[dayKey] || '-'}</td>`;
        });

        rowsHTML += '</tr>';
    });

    tbody.innerHTML = rowsHTML;
}

/**
 * Render Document Information Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderDOCPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentDOCPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientDOCPage(${hhaPatientCurrentDOCPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentDOCPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentDOCPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDOCPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentDOCPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDOCPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDOCPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentDOCPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientDOCPage(${hhaPatientCurrentDOCPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change POC Page
 * @param {number} page - Page number to navigate to
 */
function changePatientDOCPage(page) {
    if (!hhaPatientAllDOCData || !hhaPatientAllDOCData) {
        toastr.error('No poc data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllDOCData.length / HHA_PATIENT_DOC_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#pdocumentContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('pdocument', hhaPatientAllDOCData, page);
    }, 500);
}

/**
 * Render Contract Information Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderCONTRACTPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentCONTRACTPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientCONTRACTPage(${hhaPatientCurrentCONTRACTPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentCONTRACTPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentCONTRACTPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientCONTRACTPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentCONTRACTPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientCONTRACTPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientCONTRACTPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentCONTRACTPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientCONTRACTPage(${hhaPatientCurrentCONTRACTPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change POC Page
 * @param {number} page - Page number to navigate to
 */
function changePatientCONTRACTPage(page) {
    if (!hhaPatientAllCONTRACTData || !hhaPatientAllCONTRACTData) {
        toastr.error('No poc data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllCONTRACTData.length / HHA_PATIENT_CONTRACT_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#contractContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('contract', hhaPatientAllCONTRACTData, page);
    }, 500);
}

/**
 * Render Contract Information Pagination
 * @param {number} totalPages - Total number of pages
 * @returns {string} HTML pagination
 */
function hhaPatientRenderDISPLINEPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentDISPLINEPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientDISPLINEPage(${hhaPatientCurrentDISPLINEPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentDISPLINEPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentDISPLINEPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDISPLINEPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentDISPLINEPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDISPLINEPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientDISPLINEPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentDISPLINEPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientDISPLINEPage(${hhaPatientCurrentDISPLINEPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `
                </ul>
            </nav>
        </div>
    `;

    return html;
}

/**
 * Change POC Page
 * @param {number} page - Page number to navigate to
 */
function changePatientDISPLINEPage(page) {
    if (!hhaPatientAllDISPLINEData || !hhaPatientAllDISPLINEData) {
        toastr.error('No poc data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllDISPLINEData.length / HHA_PATIENT_DISPLINE_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#disciplineContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('discipline', hhaPatientAllDISPLINEData, page);
    }, 500);
}

function hhaPatientRenderPREFERENCESPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `<nav class="mt-3"><ul class="hha_caregiver_notes pagination justify-content-center">`;

    html += `
        <li class="page-item ${hhaPatientCurrentPREFERENCESPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${hhaPatientCurrentPREFERENCESPage - 1})">Prev</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentPREFERENCESPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${i})">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${hhaPatientCurrentPREFERENCESPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientNotesPage(${hhaPatientCurrentPREFERENCESPage + 1})">Next</a>
        </li>
    `;

    html += `</ul></nav>`;
    return html;
}

/**
 * Change POC Page
 * @param {number} page - Page number to navigate to
 */

function changePatientPREFERENCESPage(page) {
    if (!hhaPatientAllPREFERENCESData || !hhaPatientAllPREFERENCESData) {
        toastr.error('No poc data available');
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllPREFERENCESData.length / HHA_PATIENT_PREFERENCES_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#ppreferencesContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('ppreferences', hhaPatientAllPREFERENCESData, page);
    }, 500);
}


function updateStatistics(data) {
    const total = data.length;
    const sent = data.filter(d => d.docStatus === 'sent').length;
    const pending = data.filter(d => d.docStatus === 'pending').length;
    const received = data.filter(d => d.docStatus === 'printed').length;
    const signed = data.filter(d => d.docStatus === 'signed').length;
    $('#totalDocuments_patient').text(total);
    $('#sentDocuments_patient').text(sent);
    $('#pendingDocuments_patient').text(pending);
    $('#receivedDocuments_patient').text(received);
    $('#signedDocuments_patient').text(signed);
}

function hhaPatientRenderMDOPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <div class="card-footer" style="background-color: #f8f9fa;">
            <nav>
                <ul class="pagination justify-content-center mb-0">
    `;

    // Previous button
    html += `
        <li class="page-item ${hhaPatientCurrentMDOPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientMDOPage(${hhaPatientCurrentMDOPage - 1})">
                <i class="mdi mdi-chevron-left"></i> Prev
            </a>
        </li>
    `;

    // Page numbers
    let startPage = Math.max(1, hhaPatientCurrentMDOPage - 2);
    let endPage = Math.min(totalPages, hhaPatientCurrentMDOPage + 2);

    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientMDOPage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Middle pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === hhaPatientCurrentMDOPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientMDOPage(${i})">${i}</a>
            </li>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePatientMDOPage(${totalPages})">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${hhaPatientCurrentMDOPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePatientMDOPage(${hhaPatientCurrentMDOPage + 1})">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    html += `</ul></nav></div>`;
    return html;
}

function changePatientMDOPage(page) {
    if (!hhaPatientAllMDOData || !hhaPatientAllMDOData.length) {
        return;
    }

    let totalPages = Math.ceil(hhaPatientAllMDOData.length / HHA_PATIENT_MDO_PER_PAGE);

    if (page < 1 || page > totalPages) {
        return;
    }

    $('#preferencesContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Table Shimmer -->
            <div class="shimmer-card">
                <div class="shimmer shimmer-line title"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
                <div class="shimmer shimmer-line short"></div>
                <div class="shimmer shimmer-line medium"></div>
                <div class="shimmer shimmer-line long"></div>
            </div>
        </div>
    `);

    setTimeout(() => {
        renderHHAPatientTabContent('mdorder',hhaPatientAllMDOData, page);
    }, 500);

}

function getDocStatusBadgeNew(docStatus) {
    const badgeClass = `badge-docstatus-${docStatus}`;
    var style="";
    if(badgeClass =='badge-docstatus-signed'){
        style = "background:#d4edda !important;color:#155724 !important"
    }
    if(badgeClass =='badge-docstatus-preliminary' || badgeClass =='badge-docstatus-final'){
        style = "background:#d4edda !important;color:#155724 !important"
    }
    return `<span class="badge ${badgeClass}" style="${style}">${docStatus}</span>`;
}

function downloadHHAMDODocumentNew(id){
    $('.docs-doc-'+id).removeClass('d-none');
    
    const foundDoc = hhaPatientAllMDOData.find(doc => doc.id === id);
    let data = foundDoc;
    const dateTime = new Date().toLocaleDateString('en-US').replace(/\//g, '-');
    $.ajax({
        url: _DOWNLOAD_HHA_MD_ORDER,
        type: "get",
        data: {
            'agency_id': _AGENCYID,
            'patient_id':_RECORD_ID,
            'document_download_url':data.document_download_url
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function (res) {
            $('.docs-doc-'+id).addClass('d-none');
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download =data.id+"MDO"+dateTime+'.pdf';
            link.click();

        }, error: function (jqr) {
            $('.docs-doc-'+id).addClass('d-none');
            $('#loadertag2client').attr('style', 'display:none');
            toastr.error(jqr.responseJSON.error_msg);
        }
    })
}

function getDowloadPatientDocumentWithPortal(docId){
    $.ajax({
        url: _HHA_PATIENT_DOWNLOAD_DOCUMENT,
        type: "GET",
        contentType: false,
        data:{
            'docid': docId,
            'id': currentPatientId
        },
        success: function(response) {
            var response = response.data;
            if(response.length !=0){
                $("#td_"+docId).html('');
                
                var base64String = response.streamData;
                // Use a generic MIME type if unknown
                var fileType = "application/octet-stream"; // This works for binary data of any type
                var fileName = response.fileName; // Set a generic filename without an extension
                var base64String = "data:" + fileType + ";base64," + base64String; // Replace with actual Base64 data    
                // Set the href and download attributes on the anchor element
                var link = document.createElement("a");
                link.href = base64String;
                link.download = fileName;
                link.click();
                $("#td_"+docId).html('');
                // Append Html 
                $("#td_"+docId).html('<a target="_blank" id="document_'+docId+'" onclick="getDowloadPatientDocumentWithPortal('+docId+')"><i class="fa fa-download"></i> Download</a>')
            }else{
                $('#document_'+docId).attr('href','');
            }
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}