async function searchThirdParty() {
    var firstName = $('#third_party_first_name').val().trim();
    var lastName = $('#third_party_last_name').val().trim();
    var employeeCode = $('#third_party_employee_code').val().trim();
    var dob = $('#third_party_dob').val().trim();
    var phone = $('#third_party_phone').val().trim();

    // Check if at least one field is filled
    if (firstName === '' && lastName === '' && employeeCode === '' && dob === '' && phone === '') {
        toastr.warning('Please enter at least one search criteria');
        return false;
    }

    // Show loader
    $('#visiting_third_party_results_section').attr('style', '');
    $('#visitingThirdPartyResultsLoader').attr('style', '');
    $('#visitingThirdPartyResults').html('');
    
    // Button loading state
    $('#btn_search_visiting').addClass('disabled').css('pointer-events', 'none');
    $('#btn_search_visiting_icon').addClass('d-none');
    $('#btn_search_visiting_spinner').removeClass('d-none');
    $('#btn_search_visiting_text').text(' Searching...');

    setTimeout(async function(){
        let responseData = await commonAjaxForVisitingAid();
      
        $('#visitingThirdPartyResultsLoader').hide();
        $('#btn_search_visiting').removeClass('disabled').css('pointer-events', '');
        $('#btn_search_visiting_icon').removeClass('d-none');
        $('#btn_search_visiting_spinner').addClass('d-none');
        $('#btn_search_visiting_text').text(' Search');
        if(responseData !=""){
       
            if(responseData.data){
                var response = responseData.data.data.employees;
                var tableResponse = "";
                $('#visiting_third_party_results_section').attr('style', '');
                $('#visitingThirdPartyResults').html("");
        
                if (response && response.length > 0) {
                    var cnt = 1;
                    $.each(response, function(i, v) {
                        var firstName = (v.first_name || '');
                        var lastName = (v.last_name || '');
                        var fullName = firstName + ' ' + lastName;
                        var employeeCode = v.employee_code || v.code || '';
                        var phone = v.phone || v.cell || '';
                        var id = v.employee_id || v.employee_id || '';
        
                        tableResponse += `<tr>
                            <td nowrap class="small">${cnt++}</td>
                            <td nowrap class="small">${id}</td>
                            <td nowrap class="small">${fullName.trim()}</td>
                            <td nowrap class="small">${employeeCode}</td>
                            <td nowrap class="small">${phone}</td>
                            <td nowrap class="small">
                                <input type="radio" name="visting_third_party_radio" id="tp${id}"
                                    
                                    value="${id}"
                                    data-first-name="${firstName}"
                                    data-last-name="${lastName}"
                                    data-code="${employeeCode}">
                            </td>
                        </tr>`;
                    });
        
                    $('#visiting_third_party_results_section').append("<span class='text-danger' id='visiting_third_party_error'></span>")
                    
                    $('#visitingThirdPartyResults').html(tableResponse);
                } else {
                    $('#visitingThirdPartyResults').html('<tr><td colspan="6" class="text-center small">No record available</td></tr>');
                }
            }else{
                let xhr = responseData;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('An error occurred while searching');
                }
            }
        }
        
    },1000);
}

$('#exampleModal-link-visiting-id').on('hidden.bs.modal', function () {
    $('#form_visiting_search_id')[0].reset()
    $('#visitingThirdPartyResultsLoader').attr('style','display:none');
    $('#visitingThirdPartyResults').html("");
})

function saveVisitingAid(){
    var checked = $('input[name="visting_third_party_radio"]').is(":checked");
    $('#visiting_third_party_error').html("");
    if(!checked){
        $('#visiting_third_party_error').html("Please select a Third Party employee");
        return false;
    }

    let responseData = $('input[name="visting_third_party_radio"]:checked')
    $.confirm({
        title: 'Link Visiting Aid',
        columnClass: "col-md-6",
        content: 'Are you sure you want to link to Visiting aid?',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-success',
                action: function() {
                    $('#btn_save_visiting_spinner').removeClass('d-none');
                    $('#btn_submit_visiting_text').text('Saving...')
                    $.ajax({
                        type: "POST",
                        url: _SAVE_VISITING_THIRD_PARTY,
                        data: {
                            'id': responseData.val(),
                            'first_name': responseData.attr('data-first-name'),
                            'last_name': responseData.attr('data-last-name'),
                            'employee_code': responseData.attr('data-code'),
                            'agency_id': _AGENCYID,
                            'patient_id':_RECORD_ID,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            let response = responseData.attr('data-first-name') +' '+responseData.attr('data-last-name')+ '('+ responseData.attr('data-code')+')';
                            $('#link_visiting_aid_id').html(response);
                            $('#show_visiting_aid_tabing').attr('style','display:""')
                            $('#btn_save_visiting_spinner').addClass('d-none');
                            $('#btn_submit_visiting_text').text('Save')
                            $('#close_visiting_aids').click();
                            location.reload();
                        },
                        error:function(jqr){
                            $('#btn_save_visiting_spinner').addClass('d-none');
                            $('#btn_submit_visiting_text').text('Save')
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function() {
                //close
            },
        },
    });
    
}

// Store visiting aid data globally
let visitingAidData = null;
let visitingMedicalData = [];
let filteredMedicalData = [];

async function getVisitingDemographic(){
    // Show loader and hide content
    $('#visiting-demographic-loader').show();
    $('#visiting-demographic-content').hide();

    try {
    setTimeout(async function(){
        let response = await commonAjaxForVisitingAid(_VISITING_THIRD_PARTY_CODE);

        if(response && response.data !="" && response.data.data.employees.length > 0) {
            // Get the first employee data
            visitingAidData = response.data.data.employees[0];
            populateVisitingDemographic(visitingAidData);
        } else {

            $('#visiting-demographic-data').html('<div class="col-12 text-center py-5"><i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 48px;"></i><p class="text-muted mt-2">No demographic data available.</p></div>');
        }

        // Hide loader and show content
        $('#visiting-demographic-loader').hide();
        $('#visiting-demographic-content').show();
    },1000);

    } catch(error) {

        $('#visiting-demographic-data').html('<div class="col-12 text-center py-5"><i class="mdi mdi-close-circle-outline text-danger" style="font-size: 48px;"></i><p class="text-muted mt-2">Failed to load demographic data.</p></div>');
        // Hide loader and show content even on error
        $('#visiting-demographic-loader').hide();
        $('#visiting-demographic-content').show();
    }
}

function populateVisitingDemographic(data) {
    let html = '';

    // Personal Information Section
    html += '<div class="col-md-12 mb-2"><h6 class="text-primary mb-3 border-bottom pb-2"><i class="mdi mdi-account-circle"></i> Personal Information</h6></div>';
    html += createDemographicField('Employee ID', data.employee_id || '-', 4);
    html += createDemographicField('Employee Code', data.employee_code || data.code || '-', 4);
    html += createDemographicField('Full Name', (data.first_name || '') + ' ' + (data.last_name || ''), 4);
    html += createDemographicField('First Name', data.first_name || '-', 4);
    html += createDemographicField('Last Name', data.last_name || '-', 4);
    html += createDemographicField('Date of Birth', data.dob || data.date_of_birth || '-', 4);
    html += createDemographicField(
        'Gender',
        data.gender === 'F' ? 'Female' :
        data.gender === 'M' ? 'Male' : '-',
        4
    );
    html += createDemographicField('SSN', data.ssn ? '***-**-' + data.ssn.slice(-4) : '-', 4);

    // Status badge
    let statusBadge = '<span class="badge badge-success">Active</span>';
    if(data.status && data.status.toLowerCase() === 'inactive') {
        statusBadge = '<span class="badge badge-danger">Inactive</span>';
    } else if(data.status && data.status.toLowerCase() === 'pending') {
        statusBadge = '<span class="badge badge-warning">Pending</span>';
    }
    html += '<div class="col-md-4 mb-3"><label class="font-weight-bold small text-muted">Status:</label><div>' + statusBadge + '</div></div>';

    // Contact Information Section
    html += '<div class="col-md-12 mb-2 mt-3"><h6 class="text-info mb-3 border-bottom pb-2"><i class="mdi mdi-phone"></i> Contact Information</h6></div>';
    html += createDemographicField('Primary Phone', data.phone || data.primary_phone || '-', 6);
    html += createDemographicField('Cell Phone', data.cell || data.cell_phone || '-', 6);

    // Address Information Section
    html += '<div class="col-md-12 mb-2 mt-3"><h6 class="text-secondary mb-3 border-bottom pb-2"><i class="mdi mdi-map-marker"></i> Address Information</h6></div>';
    html += createDemographicField('Street Address', data.address || data.street_address || '-', 12);


    $('#visiting-demographic-data').html(html);
}

function createDemographicField(label, value, colSize = 3) {
    return `
        <div class="col-md-${colSize} col-sm-6 mb-3">
            <div class="form-group mb-0">
                <label class="font-weight-bold small text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">${label}</label>
                <div class="text-dark" style="font-size: 14px; padding: 5px 0;">${value}</div>
            </div>
        </div>
    `;
}

async function getVisitingPendingMedical() {
    // Show shimmer effect
    showMedicalShimmer();

    try {
        // TODO: Replace with actual medical API endpoint
        let response = await fetchVisitingMedicalData(_VISITING_THIRD_PARTY_CODE);
        visitingMedicalData = [];
        filteredMedicalData = [];
        if(response.data.length >0) {
            visitingMedicalData = response.data;
            filteredMedicalData = [...visitingMedicalData];
        }
        renderVisitingMedicalTable();
    } catch(error) {

        visitingMedicalData = [];
        filteredMedicalData = [];
        renderVisitingMedicalTable();
    }
}

async function fetchVisitingMedicalData(employeeCode) {
    // TODO: Replace this with actual API endpoint for medical data
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "GET",
            url: _GET_VISITING_THIRD_PARTY_PENDING_MEDICAL, // Replace with actual medical endpoint
            data: {
                'patient_id': _RECORD_ID,
                'agency_id': _AGENCYID,
                
            },
            success: function(res) {
            
                resolve({
                    data: res.data.pendingmedicals || []
                });
            },
            error: function(jqr) {
                reject(jqr);
            }
        });
    });
}

function renderVisitingMedicalTable() {
    let tbody = $('#visiting_medical_tbody');
    tbody.empty();

    if(filteredMedicalData.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0">No medical records found</p>
                </td>
            </tr>
        `);
        return;
    }

    // Render rows
    filteredMedicalData.forEach((medical, index) => {
        let statusBadge = getStatusBadge(medical.Status);

        let row = `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${medical.MedicalID || '-'}</td>
                <td>${medical.MedicalName || '-'}</td>
                <td>${medical.MedicalDue || '-'}</td>
                
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getStatusBadge(status) {
    if(!status) return '-';

    let badgeClass = 'badge-secondary';
    let displayStatus = status;

    switch(status.toLowerCase()) {
        case 'completed':
            badgeClass = 'badge-success';
            break;
        case 'pending':
            badgeClass = 'badge-warning';
            break;
        case 'expired':
            badgeClass = 'badge-danger';
            break;
        case 'scheduled':
            badgeClass = 'badge-info';
            break;
    }

    return `<span class="badge ${badgeClass}">${displayStatus}</span>`;
}

function filterVisitingMedical() {
    let statusFilter = $('#visiting_medical_status_filter').val().toLowerCase();

    if(!statusFilter) {
        filteredMedicalData = [...visitingMedicalData];
    } else {
        filteredMedicalData = visitingMedicalData.filter(medical => {
            return medical.status && medical.status.toLowerCase() === statusFilter;
        });
    }

    renderVisitingMedicalTable();
}

function refreshVisitingMedical() {
    getVisitingPendingMedical();
}

function addVisitingMedical() {
    // TODO: Implement modal for adding medical records
    toastr.info('Add Medical functionality - To be implemented');
}

async function commonAjaxForVisitingAid(code=""){
    var firstName = $('#third_party_first_name').val().trim();
    var lastName = $('#third_party_last_name').val().trim();
    var inputCode = $('#third_party_employee_code').val().trim();
    var employeeCode = inputCode !== '' ? inputCode : code;
    var dob = $('#third_party_dob').val().trim();
    var phone = $('#third_party_phone').val().trim();

    let finalResponse = '';
    $.ajax({
        async:false,
        global:false,
        type: "POST",
        url: _ADVANCED_SEARCH_THIRD_PARTY,

        data: {
            'first_name': firstName,
            'last_name': lastName,
            'employee_code': employeeCode,
            'dob': dob,
            'phone': phone,
            'agency_id': _AGENCYID,
            '_token': _CSRF_TOKEN
        },
        success: function(res) {
            finalResponse = res;
        },
        error:function(jqr){
            finalResponse = jqr;
        }
    })

    return finalResponse;
}

// Shimmer effect functions for medical table
function showMedicalShimmer() {
    let tbody = $('#visiting_medical_tbody');
    tbody.empty();

    // Create shimmer rows
    let shimmerRows = '';
    for(let i = 0; i < 5; i++) {
        shimmerRows += `
            <tr class="shimmer-table-row">
                <td class="shimmer-table-cell text-center">
                    <div class="shimmer shimmer-line" style="width: 30px; margin: 0 auto;"></div>
                </td>
                <td class="shimmer-table-cell">
                    <div class="shimmer shimmer-line" style="width: 80%;"></div>
                </td>
                <td class="shimmer-table-cell">
                    <div class="shimmer shimmer-line" style="width: 90%;"></div>
                </td>
                <td class="shimmer-table-cell">
                    <div class="shimmer shimmer-line" style="width: 90%;"></div>
                </td>
                <td class="shimmer-table-cell text-center">
                    <div class="shimmer shimmer-line" style="width: 70px; margin: 0 auto;"></div>
                </td>
            </tr>
        `;
    }

    tbody.html(shimmerRows);
}

function hideMedicalShimmer() {
    // Shimmer will be replaced by actual data in renderVisitingMedicalTable
}