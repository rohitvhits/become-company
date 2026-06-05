let selectedCreateUploadHHAX = [];
let selectedCreateUploadHHAXFlag = false;
let selectedCreateUploadHHAXOtherFlag = false;
let selectedCreateUploadHHAXOther = [];
function hhaRefreshClient() {

    $('#loadertag2client').attr('style', '');
    $.ajax({
        url: _HHA_FETCH_MEDICAL,
        type: "get",
        data: {
            'agency_id': _AGENCYID,
        },
        success: function (res) {

           
        }, error: function (jqr) {
            $('#loadertag2client').attr('style', 'display:none');
            toastr.error(jqr.responseJSON.error_msg);
        }
    })
}

function fetchCargiver() {
    $('#loadertagCaregiver').attr('style', '');
    $.ajax({
        url: _HHA_FETCH_CAREGIVER,
        type: "get",
        data: {
            'agency_id': _AGENCYID,
        },
        success: function (res) {

            $('#loadertagCaregiver').attr('style', 'display:none');
            $('.hha_refresh_caregiver_id').attr('style', '');
            $('#hhasyncCaregiver').attr('style', 'display:none');
            $('.hha_total_caregiver_id').attr('style', 'display:none');
            $('#hha_total_caregiver_id').html('');
            if (res.data.total != 0) {
                $('.hha_refresh_caregiver_id').attr('style', 'display:none');
                $('#hhasyncCaregiver').attr('style', '');
                $('.hha_total_caregiver_id').attr('style', '');
                $('#hha_total_caregiver_id').html(res.data.total);
            }
        }, error: function (jqr) {
            $('#loadertagCaregiver').attr('style', 'display:none');
            toastr.error(jqr.responseJSON.error_msg);
        }
    })
}

function getHHADemographic() {

    $('#loadertag121Demo').attr('style', '');
    $.ajax({
        url: _HHA_CAREGIVER_DETAILS,
        type: "get",
        data: {
            'caregiver_id': _CAREGIVER_ID,
            'agency_id':_AGENCYID
        },
        success: function (res) {
            $('#loadertag121Demo').attr('style', 'display:none');

            var htmlResponse = "";
            if (res.data.length != 0) {
                var officeCode ="";
                if(res.data[0].office_code !=""){
                    officeCode = res.data[0].office_code+ ' - ';
                }
                htmlResponse += `<div class="col-md-4">
                        <dl class="">
                        <dt>Caregiver Id</dt>
                            <dd>${res.data[0].caregiver_id}<br></dd>
                            <dt>FullName</dt>
                            <dd>${res.data[0].firstName} ${res.data[0].middleName} ${res.data[0].lastName}<br></dd>
                            
                            <dt> Date of Birth</dt>
                            <dd> ${res.data[0].dob}<br></dd>
                           
                            <dt>Status</dt>
                            <dd>${res.data[0].status} <br></dd>
                            
                            <dt>Employeement Type</dt>
                            <dd>${res.data[0].empType}</dd>
                            
                            <dt>Hire Date</dt>
                            <dd>${res.data[0].hireDate}</dd>
                            
                           
                            <dt>Notification Mobile</dt>
                            <dd>${res.data[0].notificationMobile}</dd>

                            <dt>Notification Email</dt>
                        <dd>${res.data[0].notificationEmail}</dd>
                    
                        </dl>
                </div>
                <div class="col-md-4">
                    <dl class="">
                        <dt>Caregiver Code</dt>
                        <dd>
                        
                        ${officeCode}${res.data[0].caregiverCode}<br></dd>
                       
                        <dt> SSN</dt>
                        <dd>${res.data[0].ssn} <br></dd>
                         
                        <dt>Branch Name</dt>
                        <dd>${res.data[0].branch} <br></dd> 

                        <dt>Application Date</dt>
                        <dd>${res.data[0].applicationDate}</dd>
                        
                        <dt>First Work Date</dt>
                        <dd>${res.data[0].firstWorkDate}</dd>
                        
                        <dt>Home Phone</dt>
                        <dd>${res.data[0].phone}</dd>

                        <dt>Coordinator Name</dt>
                        <dd>${res.data[0].coordinatorName}</dd>

                        
                        
                    </dl>
                </div>
                <div class="col-md-4">
                    <dl class="">
                        <dt> Gender</dt>
                        <dd> ${res.data[0].gender}<br></dd>
                        
                        <dt> Full Address</dt>
                        <dd>${res.data[0].address}, ${res.data[0].address2},${res.data[0].city},${res.data[0].state},${res.data[0].zip}<br></dd>

                       
                        <dt>Location</dt>
                        <dd>${res.data[0].location} <br></dd>  
                        
                        <dt>Team Name</dt>
                        <dd>${res.data[0].teamName}</dd>
                        
                        <dt>Last Work Date</dt>
                        <dd>${res.data[0].lastWorkDate}</dd>
                        
                        <dt>Phone2</dt>
                        <dd>${res.data[0].phone2}</dd>

                        <dt>Employee Type</dt>
                        <dd>${res.data[0].employment_type}</dd>
                    </dl>
                </div>`;

            }

            $('#hha_caregiver_basic').html("")
            $('#hha_caregiver_basic').html(htmlResponse)
        }
    });
}

function getHHAPatientCoordinator() {
    $.ajax({
        url: _HHA_PATIENT_COORDINATOR,
        type: "get",
        data: {
            'patient_id': _CAREGIVER_ID,
            'serchDate': $('#hha_patient_coordinator_date').val()
        },
        success: function (res) {

        }
    });
}

function fetchPatient() {
    $('#loadertagPatient').attr('style', '');
    $.ajax({
        url: _HHA_FETCH_PATIENT,
        type: "get",
        data: {
            'agency_id': _AGENCYID,
        },
        success: function (res) {

            $('#loadertagPatient').attr('style', 'display:none');
            $('.hha_refresh_patient_id').attr('style', '');
            $('#hhasyncPatient').attr('style', 'display:none');
            $('.hha_total_patient_id').attr('style', 'display:none');
            $('#hha_total_patient_id').html('');
            if (res.data.total != 0) {
                $('.hha_refresh_patient_id').attr('style', 'display:none');
                $('#hhasyncPatient').attr('style', '');
                $('.hha_total_patient_id').attr('style', '');
                $('#hha_total_patient_id').html(res.data.total);
            }
        }, error: function (jqr) {
            $('#loadertagPatient').attr('style', 'display:none');
            toastr.error(jqr.responseJSON.error_msg);
        }
    })
}

function getHHADemographicDetails() {
    $('#loader-patient-demographic-details').attr('style', '');
    $.ajax({
        url: _HHAPATIENTDEMOGRAPHICSDETAILS,
        type: "get",
        data: {
            'patient_id': _LINK_HHA_PATIENT_ID
        },
        success: function (res) {
            $('#loader-patient-demographic-details').attr('style', 'display:none');
            var htmlResponse = "";
            if (res.data.length != 0) {
                var d = res.data[0];

                // Helper: return value or styled N/A
                function val(v) {
                    if (v !== "" && v !== null && v !== undefined) return '<span class="demo-row-value">' + v + '</span>';
                    return '<span class="demo-row-value na-text">N/A</span>';
                }

                // Helper: build inline label-value row
                function row(label, value) {
                    return '<div class="demo-row"><span class="demo-row-label">' + label + ':</span>' + value + '</div>';
                }
                // Helper: status badge
                function badge(text) {
                    if (!text || text === "") return '<span class="demo-badge demo-badge-gray">N/A</span>';
                    var s = text.toLowerCase();
                    var cls = 'demo-badge-gray';
                    if (s.indexOf('active') !== -1 || s.indexOf('admit') !== -1) cls = 'demo-badge-green';
                    else if (s.indexOf('discharg') !== -1 || s.indexOf('inactive') !== -1 || s.indexOf('closed') !== -1) cls = 'demo-badge-red';
                    else if (s.indexOf('pend') !== -1 || s.indexOf('hold') !== -1) cls = 'demo-badge-orange';
                    return '<span class="demo-badge ' + cls + '">' + text + '</span>';
                }

                // Helper: value with ID in parentheses
                function valWithId(name, id) {
                    if (name && name !== "") return '<span class="demo-row-value">' + name + ' ( ' + id + ' )</span>';
                    return '<span class="demo-row-value na-text">N/A</span>';
                }

                // Helper: build diagnosis table from array
                function buildDiagnosisTable(diagList) {
                    if (!diagList || !Array.isArray(diagList) || diagList.length === 0) {
                        return '<div class="demo-table-empty">No diagnosis records found.</div>';
                    }

                    var html = '<div style="overflow-x:auto;">';
                    html += '<table class="demo-table">';
                    html += '<thead><tr>';
                    html += '<th>#</th>';
                    html += '<th>ICD</th>';
                    html += '<th>Code</th>';
                    html += '<th>Description</th>';
                  
                    html += '<th>Date</th>';
                    html += '<th>Date Type</th>';
                    html += '<th>Historical as of</th>';
                    html += '<th>Ident. During</th>';
                      html += '<th>Primary</th>';
                    html += '</tr></thead>';
                    html += '<tbody>';

                    diagList.forEach(function(diag, index) {
                        var icd = diag.icd|| '';
                        var code = diag.code || diag.Code || diag.diagnosisCode || diag.diagnosiscode || '';
                        var desc = diag.description || diag.Description || diag.diagnosisDescription || diag.DiagnosisDescription || '';
                        var isPrimary = diag.isPrimary || diag.IsPrimary || diag.isprimarydiagnosis|| false;
                        var startDate = diag.startDate || diag.StartDate || diag.start_date || diag.effectiveDate || '';
                        var endDate = diag.endDate || diag.EndDate || diag.end_date || diag.resolvedDate || '';
                        var ordering = diag.ordering || diag.Ordering || (index + 1);

                        html += '<tr>';
                        html += '<td>' + ordering + '</td>';
                        html += '<td>' + icd + '</td>';
                        
                        html += '<td><span class="diag-code">' + (code || 'N/A') + '</span></td>';
                        html += '<td>' + (desc || '<span class="na-text">N/A</span>') + '</td>';
                        
                        html += '<td>' + (startDate || 'N/A') + '</td>';
                        html += '<td>' + (endDate || 'N/A') + '</td>';
                        html += '<td>-</td>';
                        html += '<td>-</td>';
                        html += '<td>' + (isPrimary ? '<span class="demo-primary-badge">Yes</span>' : '-') + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table></div>';
                    return html;
                }

                htmlResponse += `<div class="row">

                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="demo-card">
                            <div class="demo-card-header header-blue">
                                <i class="mdi mdi-account-circle"></i> Personal Information
                            </div>
                            <div class="demo-card-body">
                                ${row('Patient ID', val(d.admission_id))}
                                ${row('Full Name', val([d.firstName, d.middleName, d.lastName].filter(function(n){ return n && n !== ""; }).join(' ')))}
                                ${row('Admission ID', badge(d.admission_id))}
                                ${row('Date of Birth', val(d.dob))}
                                ${row('Gender', val(d.gender))}
                                ${row('SSN', val(d.ssn))}
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <div class="demo-card">
                            <div class="demo-card-header header-green">
                                <i class="mdi mdi-phone"></i> Contact Information
                            </div>
                            <div class="demo-card-body">
                                ${row('Home Phone', val(d.home_phone))}
                                ${row('Mobile/SMS', val(d.phone2))}
                                ${row('Address', val(d.address1))}
                                ${row('City', val(d.city))}
                                ${row('State/Zip', val([d.state, d.zip5].filter(function(n){ return n && n !== ""; }).join(' / ')))}
                            </div>
                        </div>
                    </div>

                    <!-- Employment / Service Information -->
                    <div class="col-md-6">
                        <div class="demo-card">
                            <div class="demo-card-header header-orange">
                                <i class="mdi mdi-briefcase"></i> Service Information
                            </div>
                            <div class="demo-card-body">
                                ${row('Status', badge(d.patientStatusName))}
                                ${row('Office Name', val(d.officeId))}
                                ${row('Team Name', valWithId(d.teamName, d.teamId))}
                                ${row('Service Start Date', val(d.service_start_date))}
                                ${row('Priority Code', val(d.PriorityCode))}
                                ${row('Branch Name', val(d.branchName))}
                                ${row('Alerts', val(d.alerts))}
                            </div>
                        </div>
                    </div>

                    <!-- Professional Details -->
                    <div class="col-md-6">
                        <div class="demo-card">
                            <div class="demo-card-header header-purple">
                                <i class="mdi mdi-star-circle"></i> Professional Details
                            </div>
                            <div class="demo-card-body">
                                ${row('Discipline', val(d.discipline))}
                                ${row('Location', valWithId(d.locationName, d.locationId))}
                                ${row('Medicare Number', val(d.medicare_number))}
                                ${row('Medicaid Number', val(d.medicaid_number))}
                                ${row('Coordinator Name', valWithId(d.coordinator_name, d.coordinator_id))}
                                ${row('Nurse Name', valWithId(d.nurseName, d.nurseId))}
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact (full width) -->
                    <div class="col-12">
                        <div class="demo-card">
                            <div class="demo-card-header header-red">
                                <i class="mdi mdi-phone-in-talk"></i> Emergency Contact
                            </div>
                            <div class="demo-emergency-row">
                                <div class="demo-emergency-item">
                                    <span class="demo-row-label">Contact Name:</span>
                                    ${val(d.emergencyContactName)}
                                </div>
                                <div class="demo-emergency-item">
                                    <span class="demo-row-label">Phone:</span>
                                    ${val(d.phone3)}
                                </div>
                                <div class="demo-emergency-item">
                                    <span class="demo-row-label">Relationship:</span>
                                    ${val(d.phone3Description)}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis Section (full width table) -->
                    <div class="col-12">
                        <div class="demo-card">
                            <div class="demo-card-header header-teal">
                                <i class="mdi mdi-stethoscope"></i> Diagnosis
                            </div>
                            <div class="demo-card-body" style="padding:0;">
                                ${buildDiagnosisTable(d.diagnosis)}
                            </div>
                        </div>
                    </div>

                </div>`;
            }

            $('#hha-patient-demographic-details-id').html(htmlResponse);
        }
    })
}

let _patientAuthorizationData = [];
let _patientAuthorizationCurrentPage = 1;
let _patientAuthorizationPerPage = 10;

function getPatientAuthorizationShimmer() {
    var shimmer = '';
    for (var i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function GetPatientAuthorizationInfo() {
    $('#hha-patient-authorization-details-id').html(getPatientAuthorizationShimmer());
    $('#patient-authorization-pagination').html('');
    _patientAuthorizationCurrentPage = 1;

    $.ajax({
        url: _HHAPATIENTAUTHORIZATIONINFO,
        type: "get",
        data: {
            'patient_id': _LINK_HHA_PATIENT_ID
        },
        success: function (res) {
            _patientAuthorizationData = res.data || [];
            renderPatientAuthorizationPage(_patientAuthorizationCurrentPage);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    })
}

function renderPatientAuthorizationPage(page) {
    _patientAuthorizationCurrentPage = page;
    $('#hha-patient-authorization-details-id').html(getPatientAuthorizationShimmer());
    $('#patient-authorization-pagination').html('');

    setTimeout(function() {
        let data = _patientAuthorizationData;
        let totalPages = Math.ceil(data.length / _patientAuthorizationPerPage);
        let start = (page - 1) * _patientAuthorizationPerPage;
        let end = start + _patientAuthorizationPerPage;
        let pageData = data.slice(start, end);
        let htmlResponse = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                let globalIndex = start + i;
                htmlResponse += '<tr>';
                htmlResponse += '<td>' + (globalIndex + 1) + '</td>';
                htmlResponse += '<td>' + v.AuthorizationID + '</td>';
                htmlResponse += '<td>' + v.ContractName + ' - ' + v.ContractID + '</td>';
                htmlResponse += '<td>' + v.AuthorizationNumber + '</td>';
                htmlResponse += '<td>' + v.StartDate + '</td>';
                htmlResponse += '<td>' + v.StopDate + '</td>';
                htmlResponse += '<td>' + v.MaxUnits + '</td>';
                htmlResponse += '<td>' + v.RemainingUnits + '</td>';
                htmlResponse += '<td>' + v.BankedHours + '</td>';
                htmlResponse += '<td>' + v.Period + '</td>';
                htmlResponse += '<td>' + v.WeeklyMaxAuthorization + '</td>';
                htmlResponse += '<td>' + v.EntirePeriodMaxAuthorization + '</td>';
                htmlResponse += '<td>' + v.MonthlyMaxAuthorization + '</td>';
                htmlResponse += '<td>' + v.Weekday + '</td>';
                htmlResponse += '<td>' + v.Weekend + '</td>';
                htmlResponse += '</tr>';
            });
        }

        if (htmlResponse === '') {
            $('#hha-patient-authorization-details-id').html('<tr><td colspan="15">No record available</td></tr>');
        } else {
            $('#hha-patient-authorization-details-id').html(htmlResponse);
        }

        if (pageData.length != 0) {
            renderPatientAuthorizationPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientAuthorizationPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-authorization-pagination').html('');
        return;
    }

    let pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientAuthorizationPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (let i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientAuthorizationPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientAuthorizationPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-authorization-pagination').html(pagination);
}


function getCargiverAvaibility(){
    $('#loadertag8866').attr('style','');
    $.ajax({
        url: _HHA_CAREGIVER_AVAILABILITY,
        type: "get",
        data: {
            id: _CAREGIVER_ID,
        },
        success: function(response) {
            
           var html = "";
           if(response.data.length != 0){
            var cnt = 1;
    
            $('#hha_caregiver_avaibility_id').html('');
            $.each(response.data, function(index, response) {
                var Sunday = "";
                    var Monday = "";
                    var Tuesday = "";
                    var Wednesday = "";
                    var Thursday = "";
                    var Friday = "";
                    var Saturday = "";
                  

                    response.sunday_from = (response.sunday_from) == null ? '' : response.sunday_from;
                    response. sunday_to = (response.sunday_to) == null ? '' : response.sunday_to;
                    response.sunday_live_in = (response.sunday_live_in) == null ? '' : response.sunday_live_in;

                    response.monday_from = (response.monday_from) == null ? '' : response.monday_from;
                    response.monday_to = (response.monday_to) == null ? '' : response.monday_to;
                    response.monday_live_in = (response.monday_live_in) == null ? '' : response.monday_live_in;

                    response.tuesday_from = (response.tuesday_from) == null ? '' : response.tuesday_from;
                    response.tuesday_to = (response.tuesday_to) == null ? '' : response.tuesday_to;
                    response.tuesday_live_in = (response.tuesday_live_in) == null ? '' : response.tuesday_live_in;

                    response.wednesday_from = (response.wednesday_from) == null ? '' : response.wednesday_from;
                    response.wednesday_to = (response.wednesday_to) == null ? '' : response.wednesday_to;
                    response.wednesday_live_in = (response.wednesday_live_in) == null ? '' : response.wednesday_live_in;

                    response.thursday_from = (response.thursday_from) == null ? '' : response.thursday_from;
                    response.thursday_to = (response.thursday_to) == null ? '' : response.thursday_to;
                    response.thursday_live_in = (response.thursday_live_in) == null ? '' : response.thursday_live_in;

                    response.friday_from = (response.friday_from) == null ? '' : response.friday_from;
                    response.friday_to = (response.friday_to) == null ? '' : response.friday_to;
                    response.friday_live_in = (response.friday_live_in) == null ? '' : response.friday_live_in;

                    response.saturday_from = (response.saturday_from) == null ? '' : response.saturday_from;
                    response.saturday_to = (response.saturday_to) == null ? '' : response.saturday_to;
                    response.saturday_live_in = (response.saturday_live_in) == null ? '' : response.saturday_live_in;
             


                    var h = `<tr>
                    <td># ${cnt++}</td>
                    <td>${response.sunday_from} - ${response.sunday_to}</td>           
                    <td>${response.monday_from} - ${response.monday_to}</td>
                    <td>${response.tuesday_from} - ${response.tuesday_to}</td>
                    <td>${response.wednesday_from} - ${response.wednesday_to}</td>
                    <td>${response.thursday_from} - ${response.thursday_to}</td>
                    <td>${response.friday_from} - ${response.friday_to}</td>
                    <td>${response.saturday_from} - ${response.saturday_to}</td>
                    </tr>
                    <tr>
                    <td>Live In</td>
                    <td>${response.sunday_live_in} </td>           
                    <td>${response.monday_live_in}</td>
                    <td>${response.tuesday_live_in}</td>
                    <td>${response.wednesday_live_in}</td>
                    <td>${response.thursday_live_in}</td>
                    <td>${response.friday_live_in}</td>
                    <td>${response.saturday_live_in}</td>
                    <td></td>
                    </tr>
                    `;
                    $('#hha_caregiver_avaibility_id').append(h);
            })
           }else{
            html ='<tr><td colspan="9">No Record Available</td></tr>'; 
            $('#hha_caregiver_avaibility_id').html(html);
           }
           $('#loadertag8866').attr('style','display:none');
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

var _patientNotesData = [];
let _patientNotesCurrentPage = 1;
let _patientNotesPerPage = 10;

function getPatientNotesShimmer() {
    let shimmer = '';
    for (let i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function GetPatientNotes(){
    $('#chat-messages-patient').html(getPatientNotesShimmer());
    $('#patient-notes-pagination').html("");
    _patientNotesCurrentPage = 1;

    $.ajax({
        url:_HHA_PATIENT_NOTES+'?id='+_PATIENT_ID,
        type: "GET",

        success: function(res) {
            _patientNotesData = res.data || [];
           renderPatientNotesPage(_patientNotesCurrentPage);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    });
    return false;
}

function renderPatientNotesPage(page) {
    _patientNotesCurrentPage = page;
    $('#chat-messages-patient').html(getPatientNotesShimmer());
    $('#patient-notes-pagination').html('');

    setTimeout(function() {
        let data = _patientNotesData;
        let totalPages = Math.ceil(data.length / 10);
        let start = (page - 1) * 10;
        let end = start + 10;
        let pageData = data.slice(start, end);
        let response = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                response += '<tr id="msg-' + v.PatientNoteID + '"><td>' + (start + i + 1) + '</td><td>' + v.Note + '</td><td>' + v.NoteDate + '</td></tr>';
            });
        }

        $('#chat-messages-patient').html(response);
        if (pageData.length != 0) {
            renderPatientNotesPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientNotesPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-notes-pagination').html('');
        return;
    }

    let pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientNotesPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (let i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientNotesPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientNotesPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-notes-pagination').html(pagination);
}

let _patientClinicsData = [];
let _patientClinicsCurrentPage = 1;
let _patientClinicsPerPage = 10;

function getPatientClinicsShimmer() {
    let shimmer = '';
    for (let i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function GetPatientClinics(){
    $('#hha-patient-clinical-details-id').html(getPatientClinicsShimmer());
    $('#patient-clinics-pagination').html('');
    _patientClinicsCurrentPage = 1;

    $.ajax({
        url:_HHA_PATIENT_CLINICS+'?id='+_PATIENT_ID,
        type: "GET",

        success: function(res) {
            _patientClinicsData = res.data || [];
            renderPatientClinicsPage(_patientClinicsCurrentPage);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    });
    return false;
}

function renderPatientClinicsPage(page) {
    _patientClinicsCurrentPage = page;
    $('#hha-patient-clinical-details-id').html(getPatientClinicsShimmer());
    $('#patient-clinics-pagination').html('');

    setTimeout(function() {
        let data = _patientClinicsData;
        let totalPages = Math.ceil(data.length / _patientClinicsPerPage);
        let start = (page - 1) * _patientClinicsPerPage;
        let end = start + _patientClinicsPerPage;
        let pageData = data.slice(start, end);
        let response = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                response += '<tr id="msg-' + v.PatientNoteID + '"><td>' + (start + i + 1) + '</td><td>' + v.NursingVisitsDue + '</td><td>' + v.MDOrderRequired + '</td><td>' + v.MDOrderDue + '</td><td>' + v.MDVisitDue + '</td></tr>';
            });
        }

        $('#hha-patient-clinical-details-id').html(response);
        if (pageData.length != 0) {
            renderPatientClinicsPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientClinicsPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-clinics-pagination').html('');
        return;
    }

    let pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientClinicsPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientClinicsPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientClinicsPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-clinics-pagination').html(pagination);
}


function searchPatientPocInfo(){
    $.ajax({
        url:_HHA_SEARCH_PATIENT_POC+'?id='+_PATIENT_ID,
        type: "GET",

        success: function(res) {
            var json = res.data;
            var htmlResponse = "";
            htmlResponse= "<option value=''>Search Patient POC Info</option>"
            if(json.length !=0){
                $.each(json,function(i,v){
                    htmlResponse +=`<option value="${v.ID}">${v.POCNumber}</option>`;
                })
            }

            $('#search_patient_poc_id').html("");
            $('#search_patient_poc_id').html(htmlResponse)
        }
    });
}

let _patientPOCData = [];
let _patientPOCCurrentPage = 1;
let _patientPOCPerPage = 10;

function getPatientPOCShimmer() {
    let shimmer = '';
    for (let i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function GetPatientPOCInfo(){
    let poc_id = $('#search_patient_poc_id').val();
    $('.hideShow').removeClass('hide');
    $('#poc_patient_info_tbody').html(getPatientPOCShimmer());
    $('#patient-poc-pagination').html('');
    _patientPOCCurrentPage = 1;

    $.ajax({
        url:_HHA_SEARCH_PATIENT_POC+'?id='+_PATIENT_ID+'&poc_id='+poc_id,
        type: "GET",

        success: function(res) {
            let json = res.data;

            // Transform API response to match the new table structure
            let pocTableData = [];

            if(json.length != 0){
                $.each(json, function(i, cs){
                    // Transform tasks to match expected format
                    var transformedTasks = [];
                    if(cs.Tasks && cs.Tasks.length > 0) {
                        $.each(cs.Tasks, function(e, v){
                            transformedTasks.push({
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

                    // Create POC data object for table
                    pocTableData.push({
                        id: cs.PatientInfo.ID || '-',
                        admission_number: cs.PatientInfo.AdmissionNumber || '-',
                        first_name: cs.PatientInfo.FirstName || '-',
                        last_name: cs.PatientInfo.LastName || '-',
                        poc_id: cs.PatientInfo.POCID || '-',
                        start_date: cs.PatientInfo.StartDate || '-',
                        stop_date: cs.PatientInfo.StopDate || '-',
                        created_date: cs.PatientInfo.CreatedDate || '-',
                        tasks: transformedTasks,
                        notes: cs.PatientInfo.Notes || '-',
                    });
                });
            }

            // Store data globally for modal access
            if (typeof pocDataGlobal !== 'undefined') {
                pocDataGlobal = pocTableData;
            } else {
                window.pocDataGlobal = pocTableData;
            }

            _patientPOCData = pocTableData;
            renderPatientPOCPage(_patientPOCCurrentPage);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function searchPatient(){
    var hha_patient_code_id = $('#hha_patient_code_id').val();
    var hha_patient_first_name = $('#hha_patient_first_name').val();
    var hha_patient_last_name = $('#hha_patient_last_name').val();
    var hha_patient_phone_no = $('#hha_patient_phone_no').val();
    var hha_patient_ssn = $('#hha_patient_ssn').val();
    $('#hhas_patient_id').attr('style','display:none');
    $('#hhaAppendPIdLoader').removeClass('hide');
    $('#hhaPatientAppID').html('');
    if (hha_patient_code_id.trim() != '' || hha_patient_first_name.trim() != '' || hha_patient_last_name.trim() != ''  || hha_patient_phone_no.trim() != ''  || hha_patient_ssn.trim() != '') {
        $.ajax({
            type:"get",
            url:_SEARCH_HHA_PATIENT,
            data:{
                // 'q':hha_patient_code_id,
                'hha_patient_code_id': hha_patient_code_id,
                'hha_patient_first_name': hha_patient_first_name,
                'hha_patient_last_name': hha_patient_last_name,
                'hha_patient_phone_no': hha_patient_phone_no,
                'agency_id':_AGENCYID,
                'hha_patient_ssn':hha_patient_ssn
            },
            success:function(res){
                var response = res.data;
                var tableResponse = "";
                $('#hhas_patient_id').attr('style','');
                $('#hhaPatientAppID').html("")
                $('#view_admission_id').html(hha_patient_code_id);
                if(response.length !=0){
                   var cnt = 1;
                    $.each(response,function(i,v){
                    
                        if(!v.id){
                            tableResponse +=`<tr>
                            <td nowrap>${cnt++}</td>
                            <td>${v.patient_id}</td>
                            <td>${v.patient_name +' ('+v.admission_id+')'}</td>
                            <td nowrap>${v.gender}</td>
                           
                            <td>${v.status}</td>
                            <td><input type="radio" name="pid"  id="hha_patient${v.patient_id}" onclick="selectedPatient(${v.patient_id})" data-type="hha" value="${v.patient_id}" data-name="${v.patient_name}" data-code="${v.admission_id}"></td>
                        </tr>`;
                        }else{
                           
                            tableResponse +=`<tr>
                            <td nowrap>${cnt++}</td>
                            <td nowrap>${v.id}</td>
                            <td nowrap>${v.name+' ('+v.admission_id+')'}</td>
                            <td nowrap>${v.gender}</td>
                            
                            <td nowrap>${(v.status !=null)?v.status:""}</td>
                            <td nowrap><input type="radio" name="pid" id="hha_patient${v.id}" onclick="selectedPatient(${v.id})" data-type="local" value="${v.id}"  data-name="${v.name}" data-code="${v.admission_id}"></td>
                        </tr>`; 
                        }
                        
                    });

                  
                    $('#hhaPatientAppID').html(tableResponse)
                }else{
             
                    $('#hhaPatientAppID').html('<tr><td colspan="6">No record available</td></tr>')   
                }
                $('#hhaAppendPIdLoader').addClass('hide');
              
            },
            error:function(xhr){
                toastr.error(xhr.responseJSON.message);
                $('#hhas_caregiver_id').attr('style', 'display:none');
                $('#hhaAppendPIdLoader').addClass('hide');
                $('#hhaPatientAppID').html("")
            }
        })
    }
    
}

function selectedPatient(id){
            
    var hhx_patients_name = $('#hha_patient'+id).attr('data-name')
    var link_hha_patients = id;
    hhx_patients_name = hhx_patients_name +' ('+$('#hha_patient'+id).attr('data-code')+')';
    $('.token-input-list').remove();
    var urlToken = _LINK_TO_HHA_PATIENT+"?agency_id="+_AGENCYID;
    $("#hha_profile_patient_id").tokenInput(urlToken, {

        prePopulate: link_hha_patients !== "" && hhx_patients_name !== "" ? [{ id: link_hha_patients, name: hhx_patients_name}] : [],

        tokenLimit: 1,
        zindex: 9999
    });

    $('#dataTypePatient').val($('#hha_patient'+id).attr('data-type'));
}

function getHHXPatientDetails(){
    $('#hhas_patient_id').attr('style','display:none');
    $('#hha_patient_code_id').val("");
    $('.token-input-list').remove();
    var agencyId = _AGENCYID;
    var urlToken =_LINK_TO_HHA_PATIENT+"?agency_id="+agencyId;
    var link_hha_caregiver = $('#hha_patient_ids').val();
    var hhx_caregiver_name =  $('#hha_patient_names').val();
    
    $("#hha_profile_patient_id").tokenInput(urlToken, {

        prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name}] : [],
      
        tokenLimit: 1,
        zindex: 9999
    });
}

function getHhxProfilePatient(){
    var hha_profile_id =  $('#hha_profile_patient_id').val();
    $('.hha_profile_patient_error').html("");
    var cnt =0;
    if(hha_profile_id ==''){
        $('.hha_profile_patient_error').html("Patient Link is required");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            type:"post",
            url:_SAVE_PATIENT_LINK_TO_HHA,
            data:{
                'patient_id':_RECORD_ID,
                'agency_id':_AGENCYID,
                'hha_profile_id':hha_profile_id,
                '_token':_CSRF_TOKEN,
                'dataTypeId':$('#dataTypePatient').val(),
            },
            success:function(res){
                toastr.success(res.message);
                var fullName = res.data.first_name+' '+res.data.last_name+' ( '+res.data.admission_id+')';
                $('#hhx_patient_id').html(fullName);
                $('#lnkhhx_patient_pdf_id')[0].reset();
                $('#hha_patient_ids').val(res.data.patient_id);
                $('#hha_patient_names').val(fullName);
                $('#closedsNewPatient').click();
                location.reload();
            },
            error:function(xhr){
                toastr.error(xhr.responseJSON.message);
            }
        })
    }
}

function getNotesHHACaregiverSubject(){
    if(_RECORD_TYPE =='Caregiver'){
        var url =_CAREGIVER_HHA_SUBJECT;
        var id = caregiverId;
    }else{
        var url =_PATIENT_HHA_SUBJECT;
        var id = patientId;
    }
   var checked = $('#notes_message_id').is(":checked");
   $('#subjectNotesId').html("<option value=''>Select Reason</option>");
   $('#send_hha_subject_id').addClass('hide')
   if(checked){
        $.ajax({
            url: url+"?id=" + id,
            type: "GET",
            success: function(res) {
                var json  = res.data;
                var option ="";
                if(json.length !=0){
                    option ='<option value="">Select Reason</option>';
                    $.each(json,function(i,v){
                        option +='<option value="'+v.ID+'">'+v.Name+'</option>';
                    })
                }

                $('#subjectNotesId').html("");
                $('#subjectNotesId').html(option);
            }
        });
        $('#send_hha_subject_id').removeClass('hide')
   }
}


function loadHHACaregiverrCalender(){
    var calnedr = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,agendaDay,listWeek,print'
        },
        aspectRatio: 1.5,
        eventLimit: true,
        dayMaxEvents: 3,
        defaultView: 'month',
        navLinks: true, // can click d,ay/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        //  events: JSON.parse(json_events),
        events: function(start, end, timezone, callback) {
            var startDate = moment(start).format("YYYY-MM-DD");
            var endDate = moment(end).format("YYYY-MM-DD");
            $('#loadertag12').attr('style', '');
            var id = _CAREGIVER_ID;
            var type = _RECORD_TYPE;
            var url = '';

            if (type == 'Caregiver') {
                url = CALENDER_PATIENT_SYNC+"?id=" + _CAREGIVER_ID
            } else {
                url = CALENDER_SYNC_HHA+"?patientId=" + _PATIENT_ID;
            }
            if (id != "") {
                $.ajax({

                    url: url,
                    type: "GET",
                    data: {
                        start: startDate,
                        end: endDate,

                    },
                    success: function(res) {
                        //toastr.success("Visit successfully fetch")
                        var hhaUrl = '';
                       if (type == 'Caregiver') {
                            hhaUrl = CAREGIVER_VISIT;
                        } else {
                            hhaUrl = PATIENT_VISIT;
                        }
                        $.ajax({

                            url: hhaUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                start: startDate,
                                end: endDate,
                                id: id,
                                _token: _CSRF_TOKEN,
                            },
                            success: function success(doc) {

                                $('#loadertag12').attr('style', 'display:none');
                                callback(doc);
                            }
                        });

                    }
                });
            }


        },
        eventRender: function(event, eventElement, eventColor) {

            eventElement.find(".fc-time").remove();
            eventElement.find(".fc-title").append("<br/><b>" + event.label + "</b>");
        },

    })
}

var selectedComplienceArray = [];
$('#update-hha-complience-id').click(function(e) {
    $('#create-hha-complience').removeClass('d-none');
    $('#btn-save-text-hha-complience').text('Saving...');
  
    var hha_document_type_id = $('#hha_document_complience_type_id').val();
    var completed_date = $('#completed_date_complience').val();
    var hha_document_complience_id = $('#hha_document_complience_id').val();
    var other_complience_due_date = $('#other_complience_due_date').val();

    var create_document_other_type = $('#create_document_other_type').val();
    var show_new_other_compliance_need = $('#show_new_other_compliance_need').is(":checked")

    var cnt = 0;
    $('#hha_complience_result_id_error').html("");
    $('#complience_completed_date_error').html("");
    $('#hha_document_complience_type_id_error').html("");
    $('#other_complience_due_date_error').html("");
    $('#hha_document_complience_id_error').html("");
    $('#hha_document_other_id_error').html("");
    if (hha_document_type_id.trim() == '') {
        $('#hha_document_complience_type_id_error').html("Please select HHX Document Type")
        cnt = 1;
    }
  
    if (completed_date.trim() == '') {
        $('#complience_completed_date_error').html("Please enter Date Performed")
        cnt = 1;
    }

    if(show_new_other_compliance_need){
        if (hha_document_complience_id.length == 0 && create_document_other_type.length ==0) {
            $('#hha_document_other_id_error').html("You must select either HHA Other Compliance or Create HHA Other Compliance to continue")
            cnt = 1;
        }
    }else{
        if (hha_document_complience_id.length == 0) {
            $('#hha_document_complience_id_error').html("Please select HHX Compliance Name")
            cnt = 1;
        }
    }
    
    if (other_complience_due_date.trim() == '') {
        $('#other_complience_due_date_error').html("Please enter Due Date")
        cnt = 1;
    }

    if (selectedComplienceArray.length != 0) {
        $.each(selectedComplienceArray, function(key, v) {
            var hha_complience_result_id = $('#hha_complience_result_id' + v).val();
            $('#hha_complience_result_id_' + v + '_error').html("");
            if (hha_complience_result_id == '') {
                $('#hha_complience_result_id_' + v + '_error').html("Required");
                cnt = 1;
            }
        })
    }

    if(show_new_other_compliance_need){
        if (create_document_other_type.length != 0) {
            $.each(create_document_other_type, function(i, v) {
                var hha_medical_result_ids = $('#hha_create_other_compliance_result_id' + v).val();
                $('#hha_create_other_compliance_result_id_' + v + '_error').html("");
                if (hha_medical_result_ids == '') {
                    $('#hha_create_other_compliance_result_id_' + v + '_error').html("Required");
                    cnt = 1;
                }
            })
        }
    }

    if (cnt == 1) {
        $('#create-hha-complience').addClass('d-none');
        $('#btn-save-text-hha-complience').text('Save');
        return false;
    } else {
        var newForm = $('#formnew-other-compienece-hha-update')[0];
        var formData = new FormData(newForm);

        $.ajax({

            url: _HHA_UPDATE_COMPLIANCE_DOCUMENT,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {

                toastr.success(response.error_msg);
                $('#create-hha-complience').addClass('d-none');
                $('#btn-save-text-hha-complience').text('Save');
                hideOtherComplianceToHHXDocument();
                $('#other-complience-hha-update').modal('hide');
                loadDocumentAjaxList()
               
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
                $('#create-hha-complience').addClass('d-none');
                $('#btn-save-text-hha-complience').text('Save');
            }

        })

    }
})

function hideOtherComplianceToHHXDocument() {
    $('#multipleComplienceResultId').html("");
    $('#formnew-other-compienece-hha-update')[0].reset();
}

function GetPatientChangesV2Info(){
    $.ajax({
        url: _HHA_PATIENT_CHANGES_V2,
        type: "GET",
        data: {
            'id':_PATIENT_ID
        },
        success: function(response) {

           
        },
        error: function(xhr, status, error) {
          
        }

    })


}

function GetPatientChangesAuthorizationInfo(){
    $.ajax({
        url: _HHA_PATIENT_AUTHORIZATION_CHNAGES_V2,
        type: "GET",
        data: {
            'id':_PATIENT_ID
        },
        success: function(response) {

           
        },
        error: function(xhr, status, error) {
          
        }

    })
}

// Create POC button click handler
$('#createPOCBtn').click(function(e) {
    e.preventDefault();
    openCretaeModal();
});

function openCretaeModal(){
    clearPaientPOCData();
    $('#show-patient-cretae-poc-modal').modal('show');
    $('.loader-inner').removeClass('d-none');
    $.ajax({
        url: _HHA_PATIENT_POC_OFFICE_DETAILS,
        type: "GET",
        data:{
            'id':_PATIENT_ID
        },
        contentType: false,
        success: function(response) {
            $('#hha_office_poc_id').html(response.data[0].name);
            getTaskDetails(response.data[0].id);
            clearTaskRows();
            $('.loader-inner').addClass('d-none');
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
            $('.loader-inner').addClass('d-none');
            $('#savePatientPOCdetails').removeAttr('disabled');
        }
    })
}

// ---- Dynamic Task Row Management ----
var pocTaskCounter = 1; // tracks the next row id to assign
var pocTaskOptions = []; // cached task options from server

function getPocTaskRowCount() {
    return $('#pocTaskTableBody .poc-task-row').length;
}

function renumberPocTaskRows() {
    $('#pocTaskTableBody .poc-task-row').each(function(index) {
        $(this).find('.poc-row-number').text(index + 1);
    });
    // Disable remove button when only 1 row remains
    if (getPocTaskRowCount() <= 1) {
        $('#pocTaskTableBody .remove-task-row-btn').prop('disabled', true);
    } else {
        $('#pocTaskTableBody .remove-task-row-btn').prop('disabled', false);
    }
}

function buildTaskRowHtml(rowId) {
    var days = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    var optionsHtml = '<option value="">Select Task</option>';
        let grouped = {};

        // Grouping
        $.each(pocTaskOptions, function(key, value) {
            if (!grouped[value.task_category]) {
                grouped[value.task_category] = [];
            }
            grouped[value.task_category].push(value);
        });

        // Sort categories
        let sortedCategories = Object.keys(grouped).sort();

        // Build HTML (string version instead of jQuery append)
        $.each(sortedCategories, function(index, category) {

            let items = grouped[category];

            // Sort items
            items.sort(function(a, b) {
                return a.name.localeCompare(b.name);
            });

            optionsHtml += `<optgroup label="${category}">`;

            $.each(items, function(i, item) {
                optionsHtml += `<option value="${item.id}">${item.name}</option>`;
            });

            optionsHtml += `</optgroup>`;
        });
    var daysHtml = '';
    $.each(days, function(idx, day) {
        daysHtml += '<div class="form-check form-check-inline mb-0" style="min-width: 50px;">' +
            '<label class="form-check-label" style="font-size: 0.85rem;">' +
            '<input type="checkbox" class="form-check-input" name="days_' + rowId + '[]" value="' + day + '"> ' + day +
            ' <i class="input-helper"></i></label></div>';
    });

    return '<tr class="poc-task-row" data-row="' + rowId + '">' +
        '<td class="text-center align-middle font-weight-bold poc-row-number"></td>' +
        '<td>' +
            '<select id="task_id_' + rowId + '" name="task_id[]" class="form-control form-control-sm task">' + optionsHtml + '</select>' +
            '<span id="task_id_' + rowId + '_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>' +
        '</td>' +
        '<td>' +
            '<input type="text" onkeypress="return isNumber(event)" class="form-control form-control-sm text-center" id="minutes_' + rowId + '" name="minutes[]" placeholder="0">' +
            '<span id="minutes_' + rowId + '_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>' +
        '</td>' +
        '<td class="text-center align-middle">' +
            '<div class="form-check d-flex justify-content-center">' +
            '<input type="checkbox" name="as_requested[]" value="0" class="form-check-input" id="as_requested_' + rowId + '" style="position: relative; margin: 0;">' +
            '</div>' +
        '</td>' +
        '<td>' +
            '<div class="d-flex align-items-center">' +
            '<input type="text" onkeypress="return isNumber(event)" class="form-control form-control-sm text-center" id="min_time_' + rowId + '" name="mintime[]" placeholder="Min" style="width: 60px;">' +
            '<span class="mx-1">-</span>' +
            '<input type="text" onkeypress="return isNumber(event)" class="form-control form-control-sm text-center" id="maxtime_' + rowId + '" name="maxtime[]" placeholder="Max" style="width: 60px;">' +
            '</div>' +
            '<span id="times_week_' + rowId + '_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>' +
        '</td>' +
        '<td>' +
            '<input type="text" class="form-control form-control-sm" name="instruction[]" id="instruction_' + rowId + '" placeholder="Task instructions...">' +
            '<span id="instruction_' + rowId + '_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>' +
        '</td>' +
        '<td>' +
            '<div class="d-flex flex-wrap">' + daysHtml + '</div>' +
            '<span id="days_' + rowId + '_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>' +
        '</td>' +
        '<td class="text-center align-middle">' +
            '<button type="button" class="btn btn-outline-danger btn-sm remove-task-row-btn" title="Remove Task"><i class="mdi mdi-trash-can-outline"></i></button>' +
        '</td>' +
        '</tr>';
}

// Add Task Row button
$(document).on('click', '#addTaskRowBtn', function() {
    pocTaskCounter++;
    var newRow = buildTaskRowHtml(pocTaskCounter);
    $('#pocTaskTableBody').append(newRow);
    renumberPocTaskRows();
});

// Remove Task Row button
$(document).on('click', '.remove-task-row-btn', function() {
    if (getPocTaskRowCount() <= 1) return;
    $(this).closest('.poc-task-row').remove();
    renumberPocTaskRows();
});

function populateTaskDropdowns() {
    $('#pocTaskTableBody .poc-task-row').each(function() {
        var rowId = $(this).data('row');
        var $select = $('#task_id_' + rowId);
        var currentVal = $select.val();
        $select.html('<option value="">Select Task</option>');
        // Step 1: Group data
        let grouped = {};

        $.each(pocTaskOptions, function(key, value) {
            if (!grouped[value.task_category]) {
                grouped[value.task_category] = [];
            }
            grouped[value.task_category].push(value);
        });

        // Step 2: Sort category names
        let sortedCategories = Object.keys(grouped).sort();

        // Step 3: Loop sorted categories
        $.each(sortedCategories, function(index, category) {

            let items = grouped[category];

            // Step 4: Sort items inside category (by name)
            items.sort(function(a, b) {
                return a.name.localeCompare(b.name);
            });

            let $optgroup = $('<optgroup>', {
                label: category
            });

            $.each(items, function(i, item) {
                $optgroup.append(
                    $('<option>', {
                        value: item.id,
                        text: item.name
                    })
                );
            });

            $select.append($optgroup);
        });

        // Step 5: Restore selected value
        if (currentVal) {
            $select.val(currentVal);
        }

        $select.prop('disabled', false);
    });
}

function getTaskDetails(office_id){
    var officeId = office_id;

    $('#office_id_error').html('');

    if(!officeId || officeId == '') {
        $('#office_id_error').html('Please select an office first');
        clearTaskRows();
        return;
    }

    // Show loading state
    $('.loader-inner').removeClass('d-none');

    // Disable task dropdowns while loading
    $('#pocTaskTableBody .poc-task-row').each(function() {
        var rowId = $(this).data('row');
        $('#task_id_' + rowId).prop('disabled', true);
        $('#task_id_' + rowId).html($("<option></option>").attr("value", "").text('Loading tasks...'));
        $('#minutes_' + rowId).val('');
        $('#as_requested_' + rowId).prop('checked', false);
        $('#min_time_' + rowId).val('');
        $('#maxtime_' + rowId).val('');
        $('#instruction_' + rowId).val('');
        $('input[name="days_' + rowId + '[]"]').prop('checked', false);
    });

    $.ajax({
        url: _HHA_PATIENT_POC_TASK_DETAILS,
        type: "GET",
        data:{
            'officeId': officeId,
            'id': _PATIENT_ID
        },
        success: function(response) {
            $('.loader-inner').addClass('d-none');
            pocTaskOptions = (response.data && response.data.length > 0) ? response.data : [];
            populateTaskDropdowns();

            if(pocTaskOptions.length > 0) {
                toastr.success(pocTaskOptions.length + ' tasks loaded successfully');
            } else {
                toastr.info('No tasks available for this office');
            }
        },
        error: function(xhr, status, error) {
            $('.loader-inner').addClass('d-none');
            pocTaskOptions = [];
            populateTaskDropdowns();

            if(xhr.responseJSON && xhr.responseJSON.error_msg) {
                toastr.error(xhr.responseJSON.error_msg);
            } else {
                toastr.error('Failed to load tasks. Please try again.');
            }
        }
    });
}

// Helper function to clear all task rows (reset to single empty row)
function clearTaskRows() {
    // Remove all rows except the first one
    $('#pocTaskTableBody .poc-task-row').not(':first').remove();
    // Clear the first row
    var firstRow = $('#pocTaskTableBody .poc-task-row:first');
    var rowId = firstRow.data('row');
    $('#task_id_' + rowId).html($("<option></option>").attr("value", "").text('Select Task'));
    $('#minutes_' + rowId).val('');
    $('#as_requested_' + rowId).prop('checked', false);
    $('#min_time_' + rowId).val('');
    $('#maxtime_' + rowId).val('');
    $('#instruction_' + rowId).val('');
    $('input[name="days_' + rowId + '[]"]').prop('checked', false);
    pocTaskOptions = [];
    pocTaskCounter = 1;
    firstRow.attr('data-row', 1);
    firstRow.find('select.task').attr('id', 'task_id_1');
    firstRow.find('select.task ~ span').attr('id', 'task_id_1_error');
    renumberPocTaskRows();
}

$('#savePatientPOCdetails').click(function(e) {
    e.preventDefault();

    var temp = 0;
    var officeId = $('#office_id').val();
    var startdate = $('#start_date_id').val();
    var stopdate = $('#stop_date_id').val();
    var shift = $('#shift').val();

    // Clear previous error messages
    $('#office_id_error').html('');
    $('#start_date_id_error').html('');
    $('#stop_date_id_error').html('');
    $('#shift_error').html('');

    // Clear all task row error messages dynamically
    $('#pocTaskTableBody .poc-task-row').each(function() {
        var i = $(this).data('row');
        $('#task_id_' + i + '_error').html('');
        $('#minutes_' + i + '_error').html('');
        $('#times_week_' + i + '_error').html('');
        $('#instruction_' + i + '_error').html('');
        $('#days_' + i + '_error').html('');
    });

    // Validate basic fields
    if(officeId == ''){
        $('#office_id_error').html('Please select office id');
        temp++;
    }
    if(shift == ''){
        $('#shift_error').html('Please select shift');
        temp++;
    }
    if(startdate == ''){
        $('#start_date_id_error').html('Please enter start date');
        temp++;
    }
    if(stopdate == ''){
        $('#stop_date_id_error').html('Please enter stop date');
        temp++;
    }

    // Validate task rows dynamically
    var hasAtLeastOneTask = false;
    $('#pocTaskTableBody .poc-task-row').each(function() {
        var i = $(this).data('row');

        var taskId = $('#task_id_' + i).val();
        var minutes = $('#minutes_' + i).val().trim();
        var minTime = $('#min_time_' + i).val().trim();
        var maxTime = $('#maxtime_' + i).val().trim();
        var instruction = $('#instruction_' + i).val().trim();

        // Check if at least one day is selected for this row
        var daysChecked = $('input[name="days_' + i + '[]"]:checked').length;

        hasAtLeastOneTask = true;

        if(taskId ==""){
            $('#task_id_'+i+'_error').html('Please select Task');
            temp++;
        }
        // Validate minutes
        if(minutes == '') {
            $('#minutes_' + i + '_error').html('Please enter Minutes');
            temp++;
        } else if(parseFloat(minutes) <= 0) {
            $('#minutes_' + i + '_error').html('Must be > 0');
            temp++;
        }

        // Validate times per week (at least one should be filled)
        if(minTime == '' && maxTime == '') {
            $('#times_week_' + i + '_error').html('Enter min or max');
            temp++;
        } else if(minTime != '' && maxTime != '') {
            var minVal = parseInt(minTime);
            var maxVal = parseInt(maxTime);
            if(minVal > maxVal) {
                $('#times_week_' + i + '_error').html('Min must be ≤ Max');
                temp++;
            }
            if(minVal < 0 || minVal > 7) {
                $('#times_week_' + i + '_error').html('Min must be 0-7');
                temp++;
            }
            if(maxVal < 0 || maxVal > 7) {
                $('#times_week_' + i + '_error').html('Max must be 0-7');
                temp++;
            }
        } else if(minTime != '') {
            var minVal = parseInt(minTime);
            if(minVal < 0 || minVal > 7) {
                $('#times_week_' + i + '_error').html('Min must be 0-7');
                temp++;
            }
        } else if(maxTime != '') {
            var maxVal = parseInt(maxTime);
            if(maxVal < 0 || maxVal > 7) {
                $('#times_week_' + i + '_error').html('Max must be 0-7');
                temp++;
            }
        }

        // Validate at least one day is selected
        if(daysChecked == 0) {
            // $('#days_' + i + '_error').html('Select at least one day');
            // temp++;
        }
    });

    // If validation passes, show jquery-confirm
    if(temp == 0){
        $.confirm({
            title: 'Confirm Action',
            content: 'Are you sure you want to create this Patient POC?',
            type: 'blue',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-success',
                    action: function () {
                        var newForm = $('#add_patient_poc')[0];
                        var formData = new FormData(newForm);
                        formData.append('id',_LINK_HHA_PATIENT_ID);
                        formData.append('portal_id',_PATIENT_ID);

                        // Show loader
                        $('.loader-inner').removeClass('d-none');
                        $('#savePatientPOCdetails').attr('disabled', 'disabled');

                        $.ajax({
                            url: _HHA_ADD_PATIENT_POC_DETAILS,
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                toastr.success(response.error_msg);
                                $('.loader-inner').addClass('d-none');
                                $('#savePatientPOCdetails').removeAttr('disabled');
                                $('#show-patient-cretae-poc-modal').modal('hide');
                                GetPatientPOCInfo();
                            },
                            error: function(xhr, status, error) {
                                toastr.error(xhr.responseJSON.error_msg);
                                $('.loader-inner').addClass('d-none');
                                $('#savePatientPOCdetails').removeAttr('disabled');
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary',
                    action: function () {
                        // Do nothing on cancel
                    }
                }
            }
        });
    }
});

// Initialize input masks for date fields
$('#start_date_id').inputmask("mm/dd/yyyy", {
    "placeholder": "mm/dd/yyyy",
    "clearIncomplete": true
});

$('#stop_date_id').inputmask("mm/dd/yyyy", {
    "placeholder": "mm/dd/yyyy",
    "clearIncomplete": true
});

// Initialize datetimepicker for Shift Start date
$('#start_date_id').datetimepicker({
    "allowInputToggle": true,
    "showClose": true,
    "showClear": true,
    "showTodayButton": true,
    "format": "MM/DD/YYYY",
});

// Initialize datetimepicker for Shift Stop date
$('#stop_date_id').datetimepicker({
    "allowInputToggle": true,
    "showClose": true,
    "showClear": true,
    "showTodayButton": true,
    "format": "MM/DD/YYYY",
});

function isNumber(evt) {

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

        return false;
    }
    return true;
}

function clearPaientPOCData() {
    // Clear all error messages
    $('.error').html("");

    // Reset the form
    $('#add_patient_poc')[0].reset();

    // Reset task rows back to a single empty row
    clearTaskRows();
}

function loadHHaSection(){
    if(_RECORD_TYPE =='Patient'){
        getHHADemographicDetails();
    }else{
        getHHADemographic();
    }
   
    
}

function clearCaregiverDocData() {
    $('.error').html("");
    $('#add_caregiver_doc')[0].reset();
}

function getHHAdocumentData(){
    $('#show-create-caregiver-document-modal').modal('show');
    $('.loader-inner').removeClass('d-none');
    $('#caregiver_id').val(_CAREGIVER_ID);
    $.ajax({
        url: _HHA_CAREGIVER_DOCUMENT_TYPE_DETAILS,
        type: "GET",
        data:{
            'id':_CAREGIVER_ID
        },
        contentType: false,
        success: function(response) {
            $('#document_type_id').html($("<option></option>")
            .attr("value", "")
            .text('Select Document Type'));
            $('#document_type_id').html($("<option></option>")
            .attr("value", "1")
            .text('Document 1'));
            $.each(response.data, function(key, value) {   
                $('#document_type_id')
                    .append($("<option></option>")
                               .attr("value", value.id)
                               .text(value.name)); 
           });
            $('.loader-inner').addClass('d-none');
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
            $('.loader-inner').addClass('d-none');
        }
    })
}

function refreshDocumentData(){
    $('#loader_caregiver_doc').attr('style','');
    $.ajax({
        url: _HHA_CAREGIVER_DOCUMENT_DETAILS,
        type: "GET",
        contentType: false,
        data:{
            'id': _CAREGIVER_ID,
            'agency': _AGENCYID
        },
        success: function(response) {
            $('#loader_caregiver_doc').attr('style','display:none');
            // Show data on table
            $('#document-caregiver-table-data').html("");
            var tableResponse = "";
            var response = response.data;
            if(response.length !=0){
                var cnt = 1;
                 $.each(response,function(i,v){
                 
                     if(!v.id){
                         tableResponse +=`<tr>
                         <td nowrap>${cnt++}</td>
                         <td>${v.caregiverDocId}</td>
                         <td>${v.caregiverDocumentType}</td>
                         <td>${v.description}</td>
                         <td>${v.fileName}</td>
                         <td>${moment(v.CreatedOn).format('MM/DD/YYYY hh:mm A')} <br> ${v.CreatedBy} </td>
                          <td id="td_${v.caregiverDocId}"><a id="document_${v.caregiverDocId}" onclick="getDowloadCaregiverDocumentPatients(${v.caregiverDocId});"><i class="fa fa-download"></i> Download</a></td>
                     </tr>`;
                     }else{
                        
                         tableResponse +=`<tr><td colspan="6">No record available</td></tr>`; 
                     }
                     
                 });
                 $('#document-caregiver-table-data').html(tableResponse)
             }else{
          
                 $('#document-caregiver-table-data').html('<tr><td colspan="6">No record available</td></tr>')   
             }
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
            $('#loader_caregiver_doc').attr('style','display:none');
        }
    });
}

$('#saveCaregiverDocument').click(function(e) {
    var newForm = $('#add_caregiver_doc')[0];
    var formData = new FormData(newForm);
    $('.loader-inner').removeClass('d-none');
    $('#saveCaregiverDocument').removeAttr('disabled');

    var temp = 0;
    var document_type_id = $('#document_type_id').val();
    var description = $('#description').val();

    if(document_type_id == ''){
        $('#document_type_id_error').html('Please select document type');
        temp++;
    }
    if(description == ''){
        $('#description_error').html('Please enter description');
        temp++;
    }
    
    if(temp == 0){
        $.ajax({
            url: _SAVE_HHA_CAREGIVER_DOCUMENT,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('.loader-inner').addClass('d-none');
                $('#show-create-caregiver-document-modal').modal('hide');
                refreshDocumentData();
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
                $('.loader-inner').addClass('d-none');
                $('#saveCaregiverDocument').removeAttr('disabled');
            }
    
        })
    }else{
        $('.loader-inner').addClass('d-none');
    }
});

// Patient Document
function clearPatientDocData() {
    $('.error').html("");
    $('#add_patient_doc')[0].reset();
}

function getHHAPatientdocumentData(){
    $('#show-create-patient-document-modal').modal('show');
    $('.loader-inner').removeClass('d-none');
    $('#patient_id').val(_PATIENT_ID);
    $.ajax({
        url: _HHA_PATIENT_DOCUMENT_TYPE_DETAILS,
        type: "GET",
        data:{
            'id':_PATIENT_ID
        },
        contentType: false,
        success: function(response) {
            $('#patient_document_type_id').html($("<option></option>")
            .attr("value", "")
            .text('Select Document Type'));
           
            $.each(response.data, function(key, value) {   
                $('#patient_document_type_id')
                    .append($("<option></option>")
                               .attr("value", value.id)
                               .text(value.name)); 
           });
            $('.loader-inner').addClass('d-none');
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
            $('.loader-inner').addClass('d-none');
        }
    })
}

var _patientDocumentData = '';
var _patientDocumentCurrentPage = 1;
let _patientDocumentPerPage1 = 10;

function getPatientDocumentShimmer() {
    var shimmer = '';
    for (var i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function refreshPatientDocumentData(){
    $('#document-patient-table-data').html(getPatientDocumentShimmer());
    $('#patient-document-pagination').html('');
    _patientDocumentCurrentPage = 1;

    $.ajax({
        url: _HHA_PATIENT_DOCUMENT_DETAILS,
        type: "GET",
        contentType: false,
        data:{
            'id': _PATIENT_ID
        },
        success: function(response) {
            _patientDocumentData = response.data || [];
            renderPatientDocumentPage(_patientDocumentCurrentPage);
            
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr);
            $('#document-patient-table-data').html('<tr><td colspan="7">No record available</td></tr>');
        }
    });
}

function renderPatientDocumentPage(page) {
    _patientDocumentCurrentPage = page;
    $('#document-patient-table-data').html(getPatientDocumentShimmer());
    $('#patient-document-pagination').html('');

    setTimeout(function() {
      let data = _patientDocumentData;
        var totalPages = Math.ceil(data.length / 10);
        var start = (page - 1) * 10;
        var end = start + 10;
        var pageData = data.slice(start, end);
        var tableResponse = '';
        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                var globalIndex = start + i;
                if (!v.id) {
                    tableResponse += '<tr>';
                    tableResponse += '<td nowrap>' + (globalIndex + 1) + '</td>';
                    tableResponse += '<td>' + v.patientDocId + '</td>';
                    tableResponse += '<td>' + v.patientDocumentType + '</td>';
                    tableResponse += '<td>' + v.description + '</td>';
                    tableResponse += '<td>' + v.fileName + '</td>';
                    tableResponse += '<td>' + moment(v.CreatedOn).format('MM/DD/YYYY hh:mm A') + ' <br> ' + v.CreatedBy + '</td>';
                    tableResponse += '<td id="td_' + v.patientDocId + '"><a target="_blank" id="document_' + v.patientDocId + '" onclick="getDowloadPatientDocument(' + v.patientDocId + '); return false;"><i class="fa fa-download"></i> Download</a></td>';
                    tableResponse += '</tr>';
                }
            });
        }

        if (tableResponse === '') {
            $('#document-patient-table-data').html('<tr><td colspan="7">No record available</td></tr>');
        } else {
            $('#document-patient-table-data').html(tableResponse);
        }

        if (pageData.length != 0) {
            renderPatientDocumentPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientDocumentPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-document-pagination').html('');
        return;
    }

    var pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDocumentPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDocumentPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDocumentPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-document-pagination').html(pagination);
}

$('#savePatientDocument').click(function(e) {
    var newForm = $('#add_patient_doc')[0];
    var formData = new FormData(newForm);
    $('.loader-inner').removeClass('d-none');
    $('#savePatientDocument').removeAttr('disabled');

    var temp = 0;
    var patient_document_type_id = $('#patient_document_type_id').val();
    var patient_description = $('#patient_description').val();
    $('#patient_document_type_id_error').html('');
    $('#patient_description_error').html('');

    if(patient_document_type_id == ''){
        $('#patient_document_type_id_error').html('Please select document type');
        temp++;
    }
    if(patient_description == ''){
        $('#patient_description_error').html('Please enter description');
        temp++;
    }
    
    if(temp == 0){
        $.ajax({
            url: _SAVE_HHA_PATIENT_DOCUMENT,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('#loader_patient_doc').attr('style','display:none');
                $('#show-create-patient-document-modal').modal('hide');
                refreshPatientDocumentData();
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
                $('#loader_patient_doc').attr('style','display:none');
                $('#savePatientDocument').removeAttr('disabled');
            }
    
        })
    }else{
        $('#loader_patient_doc').attr('style','display:none');
    }
});

var _patientContractData = [];
var _patientContractCurrentPage = 1;
var _patientContractPerPage = 10;

function getPatientContractShimmer() {
    var shimmer = '';
    for (var i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function refreshPatientContactData(){
    $('#contract-patient-table-data').html(getPatientContractShimmer());
    $('#patient-contract-pagination').html('');
    _patientContractCurrentPage = 1;

    $.ajax({
        url: _HHA_PATIENT_CONTRACT,
        type: "GET",
        contentType: false,
        data:{
            'id': _PATIENT_ID
        },
        success: function(response) {
            _patientContractData = response.data || [];
            renderPatientContractPage(_patientContractCurrentPage);
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr);
           
            $('#contract-patient-table-data').html('<tr><td colspan="10">No record available</td></tr>');
        }
    });
}

function renderPatientContractPage(page) {
    _patientContractCurrentPage = page;
    $('#contract-patient-table-data').html(getPatientContractShimmer());
    $('#patient-contract-pagination').html('');

    setTimeout(function() {
        let data = _patientContractData;
        let totalPages = Math.ceil(data.length / _patientContractPerPage);
        let start = (page - 1) * _patientContractPerPage;
        let end = start + _patientContractPerPage;
        let pageData = data.slice(start, end);
        let tableResponse = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                let globalIndex = start + i;
                if (!v.id) {
                    tableResponse += '<tr>';
                    tableResponse += '<td nowrap>' + (globalIndex + 1) + '</td>';
                    tableResponse += '<td>' + v.placementID + '</td>';
                    tableResponse += '<td>' + v.contract + '</td>';
                    tableResponse += '<td>' + v.isPrimaryContract + '</td>';
                    tableResponse += '<td>' + v.altPatientID + '</td>';
                    tableResponse += '<td>' + v.serviceStartDate + '</td>';
                    tableResponse += '<td>' + v.sourceOfAdmission + '</td>';
                    tableResponse += '<td>' + v.serviceCode + '</td>';
                    tableResponse += '<td>' + v.dischargeDate + '</td>';
                    tableResponse += '<td>' + v.dischargeTo + '</td>';
                    tableResponse += '</tr>';
                }
            });
        }

        if (tableResponse === '') {
            $('#contract-patient-table-data').html('<tr><td colspan="10">No record available</td></tr>');
        } else {
            $('#contract-patient-table-data').html(tableResponse);
        }

        if (pageData.length != 0) {
            renderPatientContractPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientContractPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-contract-pagination').html('');
        return;
    }

    var pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientContractPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientContractPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientContractPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-contract-pagination').html(pagination);
}

var _patientDisciplineData = [];
var _patientDisciplineCurrentPage = 1;
var _patientDisciplinePerPage = 10;

function getPatientDisciplineShimmer() {
    let shimmer = '';
    for (let i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function refreshPatientDisciplineData(){
    $('#discipline-patient-table-data').html(getPatientDisciplineShimmer());
    $('#patient-discipline-pagination').html('');
    _patientDisciplineCurrentPage = 1;
    $.ajax({
        url: _HHA_PATIENT_DISCIPLINE,
        type: "GET",
        contentType: false,
        data:{
            'id': _PATIENT_ID
        },
        success: function(response) {
            _patientDisciplineData = response.data || [];
            renderPatientDisciplinePage(_patientDisciplineCurrentPage);
            
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr)
            $('#discipline-patient-table-data').html('<tr><td colspan="3">No record available</td></tr>');
        }
    });
}

function renderPatientDisciplinePage(page) {
    _patientDisciplineCurrentPage = page;
    $('#discipline-patient-table-data').html(getPatientDisciplineShimmer());
    $('#patient-discipline-pagination').html('');

    setTimeout(function() {
        let data = _patientDisciplineData;
        let totalPages = Math.ceil(data.length / _patientDisciplinePerPage);
        let start = (page - 1) * _patientDisciplinePerPage;
        let end = start + _patientDisciplinePerPage;
        let pageData = data.slice(start, end);
        let tableResponse = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                let globalIndex = start + i;
                if (!v.id) {
                    tableResponse += '<tr>';
                    tableResponse += '<td nowrap>' + (globalIndex + 1) + '</td>';
                    tableResponse += '<td>' + v.disciplineID + '</td>';
                    tableResponse += '<td>' + v.disciplineName + '</td>';
                    tableResponse += '</tr>';
                }
            });
        }

        if (tableResponse === '') {
            $('#discipline-patient-table-data').html('<tr><td colspan="3">No record available</td></tr>');
        } else {
            $('#discipline-patient-table-data').html(tableResponse);
        }

        if (pageData.length != 0) {
            renderPatientDisciplinePagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientDisciplinePagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-discipline-pagination').html('');
        return;
    }

    var pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDisciplinePage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDisciplinePage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientDisciplinePage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-discipline-pagination').html(pagination);
}

var _patientPreferencesData = [];
var _patientPreferencesCurrentPage = 1;
var _patientPreferencesPerPage = 10;

function getPatientPreferencesShimmer() {
    var shimmer = '';
    for (var i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function refreshPatientPreferencesData(){
    $('#prefrences-patient-table-data').html(getPatientPreferencesShimmer());
    $('#patient-preferences-pagination').html('');
    _patientPreferencesCurrentPage = 1;

    $.ajax({
        url: _HHA_PATIENT_PREFERENCES,
        type: "GET",
        contentType: false,
        data:{
            'id': _PATIENT_ID
        },
        success: function(response) {
            _patientPreferencesData = response.data || [];
            renderPatientPreferencesPage(_patientPreferencesCurrentPage);
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr)
            $('#prefrences-patient-table-data').html('<tr><td colspan="4">No record available</td></tr>');
        }
    });
}

function renderPatientPreferencesPage(page) {
    _patientPreferencesCurrentPage = page;
    $('#prefrences-patient-table-data').html(getPatientPreferencesShimmer());
    $('#patient-preferences-pagination').html('');

    setTimeout(function() {
        let data = _patientPreferencesData;
        let totalPages = Math.ceil(data.length / _patientPreferencesPerPage);
        let start = (page - 1) * _patientPreferencesPerPage;
        let end = start + _patientPreferencesPerPage;
        let pageData = data.slice(start, end);
        let tableResponse = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                let globalIndex = start + i;
                if (!v.id) {
                    tableResponse += '<tr>';
                    tableResponse += '<td nowrap>' + (globalIndex + 1) + '</td>';
                    tableResponse += '<td>' + v.preferenceName + '</td>';
                    tableResponse += '<td>' + v.preferenceValue + '</td>';
                    tableResponse += '<td>' + v.PreferenceType + '</td>';
                    tableResponse += '</tr>';
                }
            });
        }

        if (tableResponse === '') {
            $('#prefrences-patient-table-data').html('<tr><td colspan="4">No record available</td></tr>');
        } else {
            $('#prefrences-patient-table-data').html(tableResponse);
        }
        if (pageData.length != 0) {
            renderPatientPreferencesPagination(totalPages, page);
        }
        
    }, 300);
}

function renderPatientPreferencesPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-preferences-pagination').html('');
        return;
    }

    let pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPreferencesPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPreferencesPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPreferencesPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-preferences-pagination').html(pagination);
}

function getDowloadPatientDocument(docId){
    $.ajax({
        url: _HHA_PATIENT_DOWNLOAD_DOCUMENT,
        type: "GET",
        contentType: false,
        data:{
            'docid': docId,
            'id': _PATIENT_ID
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
                $("#td_"+docId).html('<a target="_blank" id="document_'+docId+'" onclick="getDowloadPatientDocument('+docId+')"><i class="fa fa-download"></i> Download</a>')
            }else{
                $('#document_'+docId).attr('href','');
            }
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr)
        }
    });
}

function getDowloadCaregiverDocumentPatients(docId){
    $.ajax({
        url: _HHA_CAREGIVER_DOWNLOAD_DOCUMENT,
        type: "GET",
        contentType: false,
        data:{
            'id': _CAREGIVER_ID,
            'docid': docId
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
                $("#td_"+docId).html('<a target="_blank" id="document_'+docId+'" onclick="getDowloadCaregiverDocumentPatients('+docId+')"><i class="fa fa-download"></i> Download</a>')
            }else{
                $('#document_'+docId).attr('href','');
            }
        },
        error: function(xhr, status, error) {
           showErrorAndLoginRedirection(xhr)
        }
    });
}

function refreshCaregiverPreferencesData(){
    $('#loader-caregiver-prefrences').attr('style','');
    $.ajax({
        url: _HHA_CAREGIVER_PREFERENCES,
        type: "GET",
        contentType: false,
        data:{
            'id': _CAREGIVER_ID
        },
        success: function(response) {
            $('#loader-caregiver-prefrences').attr('style','display:none');
            var response = response.data;
            $('#prefrences-caregiver-table-data').html("");
            var tableResponse = "";
            if(response.length !=0){
                var cnt = 1;
                 $.each(response.preferenceInfo,function(i,v){
                     if(v.preferenceID !=""){
                         tableResponse +=`<tr>
                         <td nowrap>${cnt++}</td>
                         <td>${v.preferenceID}</td>
                         <td>${v.preferenceName}</td>
                         <td>${v.preferenceValue}</td>
                         <td>${v.PreferenceType}</td>
                     </tr>`;
                     }else{  
                         tableResponse +=`<tr><td colspan="5">No record available</td></tr>`; 
                     }
                 });
                 $('#prefrences-caregiver-table-data').html(tableResponse)
             }else{
                 $('#prefrences-caregiver-table-data').html('<tr><td colspan="5">No record available</td></tr>')   
             }
            // Show data on table
        },
        error: function(xhr, status, error) {
           showErrorAndLoginRedirection(xhr)
            $('#loader-caregiver-prefrences').addClass('d-none');
        }
    });
}

function unlinkHHACaregiver(){
    var hha_profile_id =  $('#hha_caregiver_ids').val();
   let cnt = 0;
   if(hha_profile_id ==''){
       $('.hha_profile_patient_error').html("Caregiver Link is required");
       cnt =1;
   }
   if(cnt == 0){
        $.confirm({
           title:"Unlink HHA Caregiver",
           content: 'Are you sure you want to unlink this caregiver?',
           type: 'blue',
           columnClass: 'col-md-6',
           buttons: {
               submit: {
                   text: 'Confirm',
                   btnClass: 'btn-blue',
                   action: function () {
                       $.ajax({
                           type: "POST",
                           url: _UNLINK_HHA_CAREGIVER,
                           data: {
                               'patient_id':_RECORD_ID,
                               'agency_id':_AGENCYID,
                               'hha_profile_id':hha_profile_id,
                               '_token':_CSRF_TOKEN,                          
                           },
                           success: function (res) {
                               toastr.success(res.error_msg);
                               $('#hha_caregiver_ids').val('');
                               $('#hha_caregiver_names').val('');
                               $('#hhx_caregiver_id').html('N/A');
                               $('#hhx_caregiver_link_id').addClass('hide');
                               location.reload();
                           },
                           error: function (jqhr) {
                               toastr.error(jqhr.responseJSON.error_msg)
                           }

                       })
                   }
               },
               cancel: {
                   text: 'Cancel',
                   action: function () {

                   }
               }

           },
       });
   }
}

function unlinkHHAPatient(){    
    var hha_profile_id =  $('#hha_patient_ids').val();
    let cnt = 0;
    if(hha_profile_id ==''){
        $('.hha_profile_patient_error').html("Patient Link is required");
        cnt =1;
    }
    if(cnt == 0){
        $.confirm({
            title:"Unlink HHA Pateint",
            content: 'Are you sure you want to unlink this patient?',
            type: 'blue',
            columnClass: 'col-md-6',
            buttons: {
                submit: {
                    text: 'Confirm',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.ajax({
                            type: "POST",
                            url: _UNLINK_HHA_PATIENT,
                            data: {
                                'patient_id':_RECORD_ID,
                                'agency_id':_AGENCYID,
                                'hha_profile_id':hha_profile_id,
                                '_token':_CSRF_TOKEN,                          
                            },
                            success: function (res) {
                                toastr.success(res.error_msg);
                                $('#hha_patient_ids').val('');
                                $('#hha_patient_names').val('');
                                $('#hhx_patient_id').html('N/A');
                                $('#hhx_patient_link_id').addClass('hide');
                                location.reload();
                            },
                            error: function (jqhr) {
                                toastr.error(jqhr.responseJSON.error_msg)
                            }

                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {

                    }
                }

            },
        });
    }
}

function addRefreshMedical(){
    $('#exampleModal-add-hha-medical').modal('show');
    $('#save-hha-add-medical-form')[0].reset();
    $('#hha_medical_document_medical_error').html("");
    getHHAMedicalList();
}

async function getHHAMedicalList(){
    let commonResponse = await commonHHAMedicalList();
    var responseHtml ="";
    responseHtml ='<option value="">Select Medical</option>'
    if(commonResponse.length !=0){
        $.each(commonResponse,function(i,v){
            responseHtml +='<option value="'+v.id+'">'+v.name+'</option>'
        })
    }
    
    $('#hha_medical_document_medical_id').html("")
    $('#hha_medical_document_medical_id').html(responseHtml);
}

async function getHHAMedicalResults(){
    let resultData = await commonMedicalResult($('#hha_medical_document_medical_id').val())
    var htmlrs = '<option value="">Select Result</option>';
    if (resultData.length != 0) {
        $.each(resultData, function (i, v) {
            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
        })
    }

    $('#hha_medical_document_result').html("");
    $('#hha_medical_document_result').html(htmlrs);

    $("#hha_medical_document_result").select2({
        placeholder: "Select Result",
        allowClear: true
    });
}

function saveHHAMedical(){
    var hha_medical_document_medical_id = $('#hha_medical_document_medical_id').val();
    var cnt =0;
    $('#hha_medical_document_medical_error').html("");

    if(hha_medical_document_medical_id ==''){
        $('#hha_medical_document_medical_error').html("Please select Medical ID");
        cnt =1;
    }
    if(cnt ==1){
        return false;
    }else{
        var formData = $('#save-hha-add-medical-form')[0];
        var newFormData = new FormData(formData);
        newFormData.append('_token',_CSRF_TOKEN);
        newFormData.append('patient_id',_RECORD_ID);
        $.ajax({
            async:false,
            global: false,
            method:"POST",
            url: _SAVE_HHA_MEDICAL_DETAILS,
            data: newFormData,
            processData: false,   // important
            contentType: false,  
            success: function (response) {
                toastr.success(response.error_msg)
                $('.closeMedical').click();
                refreshMedical()
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function commonHHAMedicalList(){
    return $.ajax({
        url: _HHA_CAREGIVER_MEDICAL_LIST,
        type: "GET",
        data: {
            id: _CAREGIVER_ID,
            agency_id: _AGENCYID
        },
        dataType: "json"
    }).then(function(response) {
        return response.data;
    }).catch(function(xhr, status, error) {
        toastr.error('Error fetching medical list')
        return [];
    });
}

async function getAllMedicalList(){
    let data = await commonHHAMedicalList();
  
    var responseHtml ="";
    responseHtml ='<option value="">Select Medical</option>'
    if(data.length !=0){
        $.each(data,function(i,v){
            responseHtml +='<option value="'+v.id+'">'+v.name+'</option>'
        })
    }
 
    $('#create_document_medical_id').html("")
    $('#create_document_medical_id').html(responseHtml);

    $("#create_document_medical_id").select2({
        placeholder: "Select Medical",
        allowClear: true
    });
}


async function commonMedicalResult(medicalId){
    return $.ajax({
        url: _HHA_CAREGIVER_MEDICAL_RESULT_LIST,
        type: "GET",
        data: {
           'agencyId': _AGENCYID,
            'id': _RECORD_ID,
            'medicaid_id':medicalId ,
            'patientId': _RECORD_ID
        },
        dataType: "json"
    }).then(function(response) {
        return response.data;
    }).catch(function(xhr, status, error) {
        toastr.error('Error fetching medical list')
        return [];
    });
}

async function GetCreateMedicalResultList(value){

    if (selectedCreateUploadHHAXFlag) {
        var selectedID = $('.create-upload-hhax').val();
        var values = value;
        if (selectedCreateUploadHHAX.length != 0) {
            $.each(selectedID, function (key, v) {
                var select = selectedCreateUploadHHAX.includes(v);

                if (!select) {
                    selectedCreateUploadHHAX.push(v);
                    values = v;
                }
            })

        } else {
            selectedCreateUploadHHAX.push(value)
        }

        var selectedCreateMedicalText = '';
        var selectedCreateMedicalTextData = $('.create-upload-hhax').select2("data");

        for (var i = 0; i <= selectedCreateMedicalTextData.length - 1; i++) {

            if (selectedCreateMedicalTextData[i].id == values) {
                selectedCreateMedicalText = selectedCreateMedicalTextData[i].text;
            }
        }

        let resultData = await commonMedicalResult(values)
        var htmlrs = '<option value="">Select ' + selectedCreateMedicalText + ' Result</option>';
        if (resultData.length != 0) {
            $.each(resultData, function (i, v) {
                
                htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
            })
        }
        var selectHtml = `<div class="col-md-6"  id="create_medical_result_${values}"><div class="form-group">
                    <label for="recipient-name" class="col-form-label">${selectedCreateMedicalText} Results<span style="color:red">*</span>:</label>
                        <select name="hha_create_medical_result[${values}]" class="form-control" id="hha_create_medical_result_id${values}">${htmlrs}</select>
                        <span id="create_medical_result_id_${values}_error" style="color:red" class="error"></span>
                </div></div>`;
            if (selectedCreateUploadHHAX.length == 1) {
                $('#createMultipleMedicalResultId').attr('style', '');
            }

            $('#createMultipleMedicalResultId').append(selectHtml)
    }
}

function refreshCaregiverI9DocumentRequirement(){
    getABCCaregiverI9DocumentRequirement();
    $('#save-caregiver-i9-form')[0].reset()
    $('#exampleModal-caregiver-i9-requirement').modal('show');
    getDetailsByUpdatedCaregiverI9("edit");
}

function getABCCaregiverI9DocumentRequirement(){
    $.ajax({
        url: _GET_CAREGIVER_I9_ABC_DOCUMENT,
        type: "GET",
        data: {
           'agencyId': _AGENCYID,
        },
        dataType: "json",
        success:function(res){
            var jsonABDocument = res.data.ab_document;
            var jsonCDocument = res.data.caregiveri9_cdocument;

            $('#hha_caregiver_i9_requirement_ab_document').html("");
            $('#hha_caregiver_i9_requirement_cdocument').html("");
            var abDocumentResponse = "<option value=''>Select Column A+B Documents</option>";

            if(jsonABDocument.length !=0){
                $.each(jsonABDocument,function(i,v){
                    abDocumentResponse +='<option value="'+v.id+'">'+v.name+'</option>';
                })
            }
            $('#hha_caregiver_i9_requirement_ab_document').html(abDocumentResponse);

            var cDocumentResponse = "<option value=''>Select Column C Documents</option>";
            
            if(jsonCDocument.length !=0){
                $.each(jsonCDocument,function(i,v){
                    cDocumentResponse +='<option value="'+v.id+'">'+v.name+'</option>';
                })
            }
            $('#hha_caregiver_i9_requirement_cdocument').html(cDocumentResponse);
        },
    });
}

function updateCaregiverI9Requirements(){
    var formData = $('#save-caregiver-i9-form')[0];
    var newFormData = new FormData(formData);
    newFormData.append('_token',_CSRF_TOKEN);
    newFormData.append('patient_id',_RECORD_ID);
    newFormData.append('agency_id',_AGENCYID);
    var abDocumentValue = $('#hha_caregiver_i9_requirement_ab_document').val();
    var selected_ab_document_text = '';
    if(abDocumentValue !=""){
        selected_ab_document_text = $('#hha_caregiver_i9_requirement_ab_document option:selected').text();
    }
    newFormData.append('column_ab_document',selected_ab_document_text);

    var cDocumentValue = $('#hha_caregiver_i9_requirement_cdocument').val();
    var selected_c_document_text = '';
    if(cDocumentValue !=""){
        selected_c_document_text = $('#hha_caregiver_i9_requirement_cdocument option:selected').text();
    }
    newFormData.append('columnc_document',selected_c_document_text);

    $.ajax({
        async:false,
        global: false,
        method:"POST",
        url: _UPDATE_CAREGIVER_I9_REQUIREMENT,
        data: newFormData,
        processData: false,   // important
        contentType: false,  
        success: function (response) {
            toastr.success(response.error_msg)
            $('#exampleModal-caregiver-i9-requirement').modal('hide');
            getDetailsByUpdatedCaregiverI9();
        },
        error:function(jqr){
            toastr.error(jqr.responseJSON.error_msg);
        }
    });
}

function getDetailsByUpdatedCaregiverI9(type =""){
    $.ajax({
        url: _GET_CAREGIVER_I9_REQUIREMENT_DETAILs,
        type: "GET",
        data: {
           'patient_id': _RECORD_ID,
        },
        dataType: "json",
        success:function(res){
            var data = res.data;

            var i9_notes_new_details = "N/A";
            if(data.i9_notes !=""){
                i9_notes_new_details =`<span style='display:none' id='i9_notes_new_details_${data.id}'>${data.i9_notes}</span><a onclick="viewHHAUpdateCaregiverI9Notes(${data.id})">${data.i9_notes_new}</a>`;
            }

            if(type =='edit'){
                $('#hha_caregiver_i9_requirement_hire_date').val(data.hire_date);
                $('#hha_caregiver_i9_requirement_doc_exp_date').val(data.expiration_date);
                $('#hha_caregiver_i9_requirement_note').val(data.i9_notes);
                $('#hha_caregiver_i9_requirement_ab_document').val(data.column_ab_document);
                $('#hha_caregiver_i9_requirement_cdocument').val(data.columnc_document);
                $('#hha_caregiver_i9_requirement_verified').val(data.i9_verified);
                $('#hha_caregiver_i9_requirement_verify_number').val(data.everify_number);
            }else{
                $('#html_hha_caregiver_i9_hire_date').html(data.hire_date);
                $('#html_hha_caregiver_i9_expiredate_date').html(data.expiration_date);
                $('#html_hha_caregiver_i9_notes').html(i9_notes_new_details);
                $('#html_hha_caregiver_i9_ab_document').html(data.column_ab_document_name);
                $('#html_hha_caregiver_i9_cdocument').html(data.columnc_document_name);
                $('#html_hha_caregiver_i9_verified').html(data.i9_verified);
                $('#html_hha_caregiver_i9_everify_no').html(data.everify_number);
            }
            
        }
    });
}

function viewHHAUpdateCaregiverI9Notes(id){
    var htmlData = $('#i9_notes_new_details_'+id).html();
    $.confirm({
        title: 'I9 Note',
        columnClass: "col-md-6",
        content: '<div style="white-space:pre-line">' + htmlData + '</div>',
        type: 'blue',
        buttons: {
            cancel:{
                text: 'Ok',
                btnClass: 'btn-primary',
            }
        }
    });
}

async function getAllOtherComplianceListList(){
    let data = await commonHHAOtherComplianceList();
  
    var responseHtml ="";
   
    if(data.length !=0){
        $.each(data,function(i,v){
            responseHtml +='<option value="'+v.id+'">'+v.name+'</option>'
        })
    }
 
    $('#create_document_other_type').html("")
    $('#create_document_other_type').html(responseHtml);

}

function commonHHAOtherComplianceList(){
    return $.ajax({
        url: _HHA_CAREGIVER_OTHER_COMPLIANCE_LIST,
        type: "GET",
        data: {
            id: _CAREGIVER_ID,
            agency_id: _AGENCYID
        },
        dataType: "json"
    }).then(function(response) {
        return response.data;
    }).catch(function(xhr, status, error) {
        toastr.error('Error fetching medical list')
        return [];
    });
}


async function getAllComplienceResultList(values){
    var value =hhaOtherComplianceCaregiverMedical.find(item => item.key === values);
    var finalValue = values;
    if(value){
        finalValue = value.value
    }
    return $.ajax({
        url: _HHA_CAREGIVER_OTHER_COMPLIANCE_RESULT,
        type: "GET",
        data: {
            id: _RECORD_ID,
            agency_id: _AGENCYID,
            medicaid_id:finalValue
        },
        dataType: "json"
    }).then(function(response) {
        return response;
    }).catch(function(xhr, status, error) {
        toastr.error('Error fetching medical list')
        return [];
    });
    
}

async function GetCreateOtherComplianceResultList(value){

    if (selectedCreateUploadHHAXOtherFlag) {
        var selectedIDOther = $('.create_document_other_type').val();
      
        var values = value;
        if (selectedCreateUploadHHAXOther.length != 0) {
            $.each(selectedIDOther, function (key, v) {
                var select = selectedCreateUploadHHAXOther.includes(v);

                if (!select) {
                    selectedCreateUploadHHAXOther.push(v);
                    values = v;
                }
            })

        } else {
            selectedCreateUploadHHAXOther.push(value)
        }

        var selectedCreateMedicalText = '';
        var selectedCreateMedicalTextData = $('.create_document_other_type').select2("data");
       
        for (var i = 0; i <= selectedCreateMedicalTextData.length - 1; i++) {

            if (selectedCreateMedicalTextData[i].id == values) {
                selectedCreateMedicalText = selectedCreateMedicalTextData[i].text;
            }
        }

        let resultData1 = await getAllComplienceResultList(values)
        var htmlrs = '<option value="">Select ' + selectedCreateMedicalText + ' Result</option>';
        if (resultData1.data.length != 0) {
            $.each(resultData1.data, function (i, v) {

                htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
            })
        }
        var selectHtml = `<div class="col-md-6"  id="create_other_compliance_result_${values}"><div class="form-group">
                    <label for="recipient-name" class="col-form-label">${selectedCreateMedicalText} Results<span style="color:red">*</span>:</label>
                        <select name="hha_create_other_compliance_result[${values}]" class="form-control" id="hha_create_other_compliance_result_id${values}">${htmlrs}</select>
                        <span id="hha_create_other_compliance_result_id_${values}_error" style="color:red" class="error"></span>
                </div></div>`;
            if (selectedCreateUploadHHAXOther.length == 1) {
                $('#createMultipleOtherComplianceResultId').attr('style', '');
            }

            $('#createMultipleOtherComplianceResultId').append(selectHtml)
    }
}

function syncHHAMedicalDocument() {

    $.ajax({
        url: _SYNC_AGENCY_WISE_MEDICAL,
        type: "get",
        data: {

            'id': _AGENCY_ID,

        },
        success: function(response) {
            toastr.success("Medical Successfully sync");

        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr)
        }
    });
}

var _hhaCaregiverNotesData = [];
var _hhaCaregiverNotesCurrentPage = 1;
var _hhaCaregiverNotesPerPage = 10;

function getHHACaregiverNotesShimmer() {
    var shimmer = '';
    for (var i = 0; i < 5; i++) {
        shimmer += '<tr>';
        shimmer += '<td><div class="shimmer shimmer-line short"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line long"></div></td>';
        shimmer += '<td><div class="shimmer shimmer-line medium"></div></td>';
        shimmer += '</tr>';
    }
    return shimmer;
}

function refreshHHA() {
    $('#chat-messages-news').html(getHHACaregiverNotesShimmer());
    $('#hha-caregiver-notes-pagination').html('');
    _hhaCaregiverNotesCurrentPage = 1;

    $.ajax({
        url: _HHA_CAREGIVER_PATIENT_NOTES+"?id=" + caregiverId,
        type: "GET",

        success: function(res) {
            _hhaCaregiverNotesData = res.data || [];
            renderHHACaregiverNotesPage(_hhaCaregiverNotesCurrentPage);
        },
        error:function(jqr){
             showErrorAndLoginRedirection(jqr)
        }
    });
    return false;
}

function renderHHACaregiverNotesPage(page) {
    _hhaCaregiverNotesCurrentPage = page;
    $('#chat-messages-news').html(getHHACaregiverNotesShimmer());
    $('#hha-caregiver-notes-pagination').html('');

    setTimeout(function() {
        var data = _hhaCaregiverNotesData;
        var totalPages = Math.ceil(data.length / 10);
        var start = (page - 1) * 10;
        var end = start + 10;
        var pageData = data.slice(start, end);
        var response = '';

        if (pageData.length != 0) {
            $.each(pageData, function(i, v) {
                response += '<tr id="msg-' + v.CaregiverNoteID + '"><td>' + (start + i + 1) + '</td><td>' + v.Note + '</td><td>' + v.NoteDate + '</td></tr>';
            });
        }

        if (response === '') {
            $('#chat-messages-news').html('<tr><td colspan="3">No record available</td></tr>');
        } else {
            $('#chat-messages-news').html(response);
        }

        renderHHACaregiverNotesPagination(totalPages, page);
    }, 300);
}

function renderHHACaregiverNotesPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#hha-caregiver-notes-pagination').html('');
        return;
    }

    var pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderHHACaregiverNotesPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderHHACaregiverNotesPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderHHACaregiverNotesPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#hha-caregiver-notes-pagination').html(pagination);
}