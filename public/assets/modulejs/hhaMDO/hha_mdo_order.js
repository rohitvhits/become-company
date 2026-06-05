let hhaMDOResponseArray = [];
let hhaMDOPatientListResponse = [];
let optimizeHHARecord = [];
function hhaMDOOrderDocument(){
    $('.loader-overlay').addClass('active');
    $.ajax({
        url: _GET_HHA_MDO_ORDER,
        type: "get",
        data: {
            'agency_id': _AGENCYID,
            'patient_id':_RECORD_ID
        },
        success: function (res) {
            $('.loader-overlay').removeClass('active');
            if (res.data.length > 0) {
                hhaMDOResponseArray = res.data;
                populateTable(res.data);
               updateStatistics(res.data);
            }
           
        }, error: function (jqr) {
            $('.loader-overlay').removeClass('active');
            $('#loadertag2client').attr('style', 'display:none');
            var message = jqr.responseJSON?.error_msg 
           || jqr.responseJSON?.message 
           || "Something went wrong. Please try again.";
            toastr.error(message);
            populateTable([]);
        }
    })
   
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Calculate days between dates
function calculateDays(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Get status badge HTML
function getStatusBadge(status) {
    const badgeClass = status === 'current' ? 'badge-status-current' : 'badge-status-superseded';
    return `<span class="badge ${badgeClass}">${status}</span>`;
}

// Get doc status badge HTML
function getDocStatusBadge(docStatus) {
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

// Populate table with data
function populateTable(data) {
    const tbody = $('#documentsTableBody');
    tbody.empty();

    if (data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center">No documents found</td>
            </tr>
        `);
        return;
    }

    var cnt =1;
    data.forEach(function(doc) {
   
        const row = `
            <tr>
                <td>
                    <div class="document-info">
                        <div class="document-id">${doc.id}</div>
                       
                    </div>
                </td>
                 <td>
                    <div class="document-info">
                       
                        <div class="document-title">${doc.start_date}</div>
                    </div>
                </td>
                
                <td>
                    <div class="document-info">
                       
                        <div class="document-title">${doc.end_date}</div>
                    </div>
                </td>
                
                <td>${getDocStatusBadge(doc.docStatus)}</td>
                <td>
                    <div class="action-buttons d-flex gap-2 align-items-center justify-content-center flex-wrap">
                        <button class="btn btn-sm btn-action btn-action-download" onclick="downloadHHAMDODocument('${doc.id}')" title="Download Document">
                            <span class="spinner-border spinner-border-sm d-none docs-doc-${doc.id}" role="status" aria-hidden="true"></span>
                            <span class="btn-icon-text">
                                <i class="fa fa-download"></i>
                               
                            </span>
                        </button>
                        <button class="btn btn-sm btn-action btn-action-send" onclick="viewHHAMDODocument('${doc.id}')" title="Send Document" data-toggle="modal" data-target="#exampleModal-signed-mdorder-hha" >
                            <span class="spinner-border spinner-border-sm d-none docs-send-${doc.id}" role="status" aria-hidden="true"></span>
                            <span class="btn-icon-text">
                                <i class="fa fa-paper-plane"></i>
                               
                            </span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        cnt++;
        tbody.append(row);
    });
}

// Update statistics
function updateStatistics(data) {
    const total = data.length;
    const sent = data.filter(d => d.docStatus === 'sent').length;
    const pending = data.filter(d => d.docStatus === 'pending').length;
    const received = data.filter(d => d.docStatus === 'printed').length;
    const signed = data.filter(d => d.docStatus === 'signed').length;
    $('#totalDocuments').text(total);
    $('#sentDocuments').text(sent);
    $('#pendingDocuments').text(pending);
    $('#receivedDocuments').text(received);
    $('#signedDocuments').text(signed);
}

function downloadHHAMDODocument(id){
    $('.docs-doc-'+id).removeClass('d-none');
    
    const foundDoc = hhaMDOResponseArray.find(doc => doc.id === id);
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

function viewHHAMDODocument(id){

    $('#upload_doc_mdo_id').val(id);
    $('#form_mdo_signed')[0].reset();
    $('#mdo_signed_date_error').html("");
    $('#mdo_signed_upload_document_error').html("");
}

function sendHHAMDODocument(){
    $('#create-hha-mdo-order').removeClass('d-none');
    $('#btn-save-text-hha-mdo-order').text('Saving...');
    var mdo_signed_date = $('.mdo_signed_date').val();
    var mdo_signed_upload_document = $('.mdo_signed_upload_document').prop('files');
    var cnt = 0;
    $('#mdo_signed_date_error').html("");
    $('#mdo_signed_upload_document_error').html(""); 

    if(mdo_signed_date.trim() ==""){
        $('#mdo_signed_date_error').html("Please select Complete Date");
        cnt =1;
    }

    if(mdo_signed_upload_document.length ==0){
        $('#mdo_signed_upload_document_error').html("Please select Document File");
        cnt =1;
    }else{
        let file = $('.mdo_signed_upload_document')[0].files[0];
        if (file.type !== 'application/pdf') {
            $('#mdo_signed_upload_document_error').html("Only PDF files are allowed");
            cnt =1;
           
        }
    }

    if(cnt ==1){
        $('#create-hha-mdo-order').addClass('d-none');
        $('#btn-save-text-hha-mdo-order').text('Save');
        return false;
    }else{
      
        let data = hhaMDOResponseArray.find(doc => doc.id === $('#upload_doc_mdo_id').val());
        $('.docs-send-'+$('#upload_doc_mdo_id').val()).removeClass('d-none');
        var formData = new FormData($('#form_mdo_signed')[0]);
        formData.append('agency_id',_AGENCYID);
        formData.append('patient_id',_RECORD_ID);
        formData.append('document_id',data.id);
        formData.append('document_url',data.document_download_url);
        formData.append('_token',_CSRF_TOKEN);
        $.ajax({
            url: _SEND_HHA_MD_ORDER,
            type: "post",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#create-hha-mdo-order').addClass('d-none');
                $('#btn-save-text-hha-mdo-order').text('Save');
                //$('.docs-send-'+$('#upload_doc_mdo_id').val()).addClass('d-none');
                toastr.success(res.error_msg || "Document sent successfully!");
                $('#form_mdo_signed')[0].reset();
                $('#exampleModal-signed-mdorder-hha').modal('hide');
                // Optionally refresh the document list
                hhaMDOOrderDocument();
            },
            error: function (jqr) {
                $('.docs-send-'+id).addClass('d-none');
                var message = jqr.responseJSON?.error_msg
                    || jqr.responseJSON?.message
                    || "Failed to send document. Please try again.";
                toastr.error(message);
            }
        })
    }
    
}

if(typeof _AUTO_LOAD !="undefined"){
    function hhaMDODocumentAjax(page=1){
        $('.shimmer_id').removeClass('hide');
        $('#response_hha_mdo_list').html("");
        $('.location-wise-data-loader').attr('style', 'display:flex');
        $.ajax({
            url: _HHA_MDO_DOCUMENT_AJAX + "?page=" + page,
            type: "get",
            data: {
                'agency_id': $('#agency_id').val(),
                'patient_name': $('#patient_name').val(),
                'created_date': $('#created_date').val(),
                'sorting_column': 'created_date',
                'sorting_order': 'desc',
            },
            success: function(res) {
                $('.shimmer_id').addClass('hide');
                $('#response_hha_mdo_list').html(res);
                $('.location-wise-data-loader').attr('style', 'display:none');
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg || 'An error occurred');
            }
        });
    
        return false;
    }

    hhaMDODocumentAjax(1);
    var start = moment().subtract(0, 'days');
            var end = moment();
            $('#created_date').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: false,
                startOfWeek: 'sunday',
                ranges: {
                    'Select Date': [start, end],
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

                $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                    'MM/DD/YYYY'));
            })

            $('#created_date').on('apply.daterangepicker', function(ev, picker) {
            // Detect "Select Date"
            if (picker.chosenLabel === 'Select Date') {
                $(this).val('');
            } else {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            }
        });

    
    $('#agency_id').select2({
        placeholder: 'Select Agency',
        allowClear: true
    });

   
    // Pagination click handler
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        hhaMDODocumentAjax(page);
    });
}


/**
 * Filter Toggle
 */
$('#filter-btn').click(function() {
    $('#search-filter-btn').slideToggle();
});

function refresh(){
    $('#search-form')[0].reset();
   
    $('#agency_id').val('').trigger('change');
    hhaMDODocumentAjax(1);
}

function viewDocumentSendLog(id){
    $.ajax({
        url: _HHA_MDO_DOCUMENT_VIEW_LOG,
        type: "get",
        data: {
            'id': id,
        },
        success: function(res) {
            let old_response = res.data.send_response;
            let new_response = res.data.return_response;
            $('#log-model').modal('show');
            let content = '';
            content += `<div class=\"row\">`;
            content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-primary text-white\" style="padding:10px !important"><b>Send Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
            content += highlightJson(old_response);
            content += `</div></div></div>`;
            content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-success text-white\"  style="padding:10px !important"><b>Return Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
            content += highlightJson(new_response);
            content += `</div></div></div>`;
            content += `</div>`;
            $('.dataContainer').html(content);
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg || 'An error occurred');
        }
    });
}

function highlightJson(jsonInput) {
    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    let pretty = JSON.stringify(obj, null, 4);
    // Basic syntax highlighting
    pretty = pretty.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    pretty = pretty.replace(/("[^"]+": )/g, '<span style="color:#007bff;">$1</span>'); // keys
    pretty = pretty.replace(/(:\s?)("[^"]*")/g, '$1<span style="color:#28a745;">$2</span>'); // string values
    pretty = pretty.replace(/(:\s?)(\d+\.?\d*)/g, '$1<span style="color:#d18f00;">$2</span>'); // numbers
    pretty = pretty.replace(/(:\s?)(true|false|null)/g, '$1<span style="color:#aa0d91;">$2</span>'); // booleans/null
    return '<pre style="word-break:break-all;white-space:pre-wrap;">' + pretty + '</pre>';
}

function getAllPatientListHHAMdo(page=1){
    $('.shimmer_id').removeClass('hide');
    $('#response_patient_list').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url:_HHA_MDO_PATIENT_AJAX_LIST+"?page="+page,
        type: "get",
        data: {
            'agency_fk':$('#agency_fk').val(),
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');
            const tableHtml = renderPatientsTable(res?.data?.patient_list,page, 50,res?.data?.existing_mdo);
          
            let paginationHtml = renderPagination(
                res?.data?.patient_list?.length,
               page,
                50
            );
            
            var tableView = `<table class="table table-bordered">
        <thead>
            <tr>
            
                <th>No</th>
                <th nowrap>Patient ID</th>
                <th nowrap>Patient Full Name</th>
                <th nowrap>Gender</th>
                <th nowrap>DOB</th>
                <th nowrap>Phone Numbers</th>
                <th nowrap>Address</th>
                
                <th nowrap>Status</th>
                <th nowrap>Action</th>
            </tr>
        </thead>
        <tbody> ${tableHtml}</tbody></table><div id="paginationContainer">${paginationHtml}</div>`;

      
            $("#response_patient_list").html(tableView);
           
        },
        error: function(xhr, status, error) {
            showErrorAndLoginRedirection(xhr);
            $('.shimmer_id').addClass('hide')
            $("#response_patient_list").html(`<table class="table table-bordered">
        <thead>
            <tr>
            
                <th>No</th>
                <th nowrap>Patient ID</th>
                <th nowrap>Patient Full Name</th>
                <th nowrap>Gender</th>
                <th nowrap>DOB</th>
                <th nowrap>Phone Numbers</th>
                <th nowrap>Address</th>
                <th nowrap>Status</th>
                <th nowrap>Action</th>
            </tr>
        </thead>
        <tbody> <tr>
                <td colspan="10" class="text-center" style="padding: 30px 20px; background-color: #f8f9fa;">
                    <i class="fa fa-users" style="font-size: 32px; color: #6c757d; margin-bottom: 10px;"></i>
                    <p class="mb-1" style="font-size: 16px; font-weight: 600; color: #495057;">No Patients Found</p>
                    <p class="mb-0" style="font-size: 13px; color: #6c757d;">Try selecting a different agency</p>
                </td>
            </tr></tbody></table>`);
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    });
    return  false;
}

function renderPatientsTable(patients, current_page, per_page,existing_mdo) {
    let html = "";
    hhaMDOPatientListResponse =[];
    if (patients?.length > 0) {

        patients.forEach((entry, index) => {
            
            const patient = entry.resource || {};
            const patientId = patient.id ?? "N/A";
            hhaMDOPatientListResponse[patient.id] =[];
            hhaMDOPatientListResponse[patient.id].push(patient);
            // ---- Name ----
            const nameObj = (patient.name && patient.name[0]) ? patient.name[0] : {};
            const family = nameObj.family || "";
            const given = nameObj.given ? nameObj.given.join(" ") : "";
            const fullName = (given + " " + family).trim();

            // ---- Gender ----
            const gender = patient.gender ? capitalize(patient.gender) : "N/A";

            // ---- DOB ----
            let birthDate = patient.birthDate ?? "N/A";
            if (birthDate !== "N/A") {
                birthDate = formatDate(birthDate);
            }

            // ---- Phone Numbers ----
            let phonesList = "N/A";
            if (Array.isArray(patient.telecom)) {
                const phones = patient.telecom
                    .filter(t => t.system === "phone")
                    .map(t => t.value + (t.use ? ` (${t.use})` : ""));
                
                if (phones.length > 0) {
                    phonesList = phones.join("<br>");
                }
            }

            // ---- Address ----
            const addr = (patient.address && patient.address[0]) ? patient.address[0] : {};
            const addressLine = Array.isArray(addr.line) ? addr.line.join(", ") : "";
            const city = addr.city || "";
            const state = addr.state || "";
            const postal = addr.postalCode || "";
            const country = addr.country || "";
            let fullAddress = `${addressLine}, ${city}, ${state} ${postal}, ${country}`.trim();
            if (!fullAddress || fullAddress === ", ,    ,") fullAddress = "N/A";

            // ---- Status ----
            let statusBadge = `<span class="badge badge-secondary">Unknown</span>`;
            const active = patient.active;

            if (active === true || active === "true" || active === 1 || active === "1") {
                statusBadge = `<span class="badge badge-success">Active</span>`;
            } else if (active === false || active === "false" || active === 0 || active === "0") {
                statusBadge = `<span class="badge badge-danger">Inactive</span>`;
            }

            // ---- Row Number ----
            const rowNumber = ((current_page - 1) * per_page) + index + 1;

            let value = existing_mdo[patientId];
            var linkView="";
            if(_CREATE_APPOINTMENT){
                linkView=`<a onclick="addAppointmentForMedical(${patientId})">
                <i class="fa fa-calendar"></i>
            </a>`;
            }
            
            if(typeof value !="undefined"){
                linkView ='<a title="Patient View" href="'+_PATIENT_VIEW+'/'+value+'" target="_blank"><i class="fa fa-eye"></i></a>'
            }
            // ---- Build Table Row ----
            html += `
                <tr>
                    <td>${rowNumber}</td>
                    <td>${patientId}</td>
                    <td>${fullName}</td>
                    <td>${gender}</td>
                    <td>${birthDate}</td>
                    <td>${phonesList}</td>
                    <td>${fullAddress}</td>
                   
                    <td>${statusBadge}</td>
                    <td>${linkView}</td>
                </tr>`;
        });

    } else {

        html += `
            <tr>
                <td colspan="10" class="text-center" style="padding: 30px 20px; background-color: #f8f9fa;">
                    <i class="fa fa-users" style="font-size: 32px; color: #6c757d; margin-bottom: 10px;"></i>
                    <p class="mb-1" style="font-size: 16px; font-weight: 600; color: #495057;">No Patients Found</p>
                    <p class="mb-0" style="font-size: 13px; color: #6c757d;">Try selecting a different agency</p>
                </td>
            </tr>`;
    }

    return html;
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    const yyyy = d.getFullYear();
    return `${mm}/${dd}/${yyyy}`;
}

function renderPagination(totalPatients, current_page, per_page) {

    const disablePrev = current_page <= 1 ? "disabled" : "";
    const disableNext = totalPatients < per_page ? "disabled" : "";

    if(typeof totalPatients !='undefined'){
        return `
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                
                <div class="showing-info text-muted">
                    <strong>Showing ${totalPatients} patients</strong> (Page ${current_page})
                </div>

                <div class="btn-group" role="group" aria-label="Pagination">

                    <!-- Previous Button -->
                    <button type="button"
                        class="btn btn-sm btn-secondary"
                        onclick="getAllPatientListHHAMdo(${current_page - 1})"
                        ${disablePrev}>
                        <i class="fa fa-chevron-left"></i> Previous
                    </button>

                    <!-- Next Button -->
                    <button type="button"
                        class="btn btn-sm btn-primary"
                        onclick="getAllPatientListHHAMdo(${current_page + 1})"
                        ${disableNext}>
                        Next <i class="fa fa-chevron-right"></i>
                    </button>

                </div>
            </div>
        </div>`;
    }
    return '';
    
}

function addAppointmentForMedical(id){
    if(!hhaMDOPatientListResponse[id] || !hhaMDOPatientListResponse[id][0]) {
        toastr.error('Patient data not found');
        return;
    }

    const patient = hhaMDOPatientListResponse[id][0];

    // Populate modal with patient demographic data
    $('#modal_patient_id').val(patient.id || 'N/A');

    // Name
    const name = (patient.name && patient.name[0]) || {};
    const fullName = ((name.given || []).join(' ') + ' ' + (name.family || '')).trim() || 'N/A';
    // Gender
    const gender =patient.gender ? capitalize(patient.gender) : 'N/A';

    // DOB
    let birthDate = patient.birthDate || 'N/A';
    if(birthDate !== 'N/A') {
        birthDate = formatDate(birthDate);
    }
   

    // Status
    let statusBadge = '<span class="badge badge-secondary badge-custom">Unknown</span>';
    const active = patient.active;
    if(active === true || active === 'true' || active === 1 || active === '1') {
        statusBadge = '<span class="badge badge-success badge-custom">Active</span>';
    } else if(active === false || active === 'false' || active === 0 || active === '0') {
        statusBadge = '<span class="badge badge-danger badge-custom">Inactive</span>';
    }
 
    // Phones
    const phones = [];
    const mobilesData = [];
    if(Array.isArray(patient.telecom)) {
        patient.telecom.forEach(t => {
            if(t.system === 'phone') {
                phones.push('<div class="phone-item"><i class="fa fa-mobile" style="margin-right: 8px; color: #667eea;"></i>' +
                           t.value + (t.use ? ' <span class="badge badge-info badge-sm">' + t.use + '</span>' : '') + '</div>');
                           mobilesData.push(t.value)
            }
            
        });
    }
    // Address
    const address = (patient.address && patient.address[0]) || {};
    const addressLine = Array.isArray(address.line) ? address.line.join(', ') : '';
    const fullAddress = [addressLine, address.city, address.state, address.postalCode, address.country]
        .filter(x => x).join(', ') || 'N/A';
        
    let tempResponseCreate = [];
    tempResponseCreate['first_name'] = name.given?.[0]??"";
    tempResponseCreate['last_name'] = name.family;
    tempResponseCreate['dob'] = birthDate;
    tempResponseCreate['mobile'] = mobilesData[0]??"";
    tempResponseCreate['phone'] = mobilesData[1]??"";
    tempResponseCreate['address1'] = address.line[0];
   
    tempResponseCreate['city'] = address.city;
    tempResponseCreate['state'] = address.state;
    tempResponseCreate['country'] = address.country;
    tempResponseCreate['gender'] = gender;
    tempResponseCreate['zip'] = address.postalCode;
    optimizeHHARecord = [];
    optimizeHHARecord[patient.id] = [];
    optimizeHHARecord[patient.id] =tempResponseCreate;

    // Show the demographic modal
    var htmlResponse = "";
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
                                            <div class="info-label">Patient ID</div>
                                            <div class="info-value">${patient.id}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">fullName</div>
                                            <div class="info-value">${fullName}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">${gender}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">${birthDate}</div>
                                        </div>
                                    </div>
                                   <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Status</div>
                                            <div class="info-value">${statusBadge}</div>
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
                                            <div class="info-value">${fullAddress}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Address1</div>
                                            <div class="info-value">${addressLine}</div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">City</div>
                                            <div class="info-value">${address.city}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">State</div>
                                            <div class="info-value">${address.state}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">County</div>
                                            <div class="info-value">${address.country}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item">
                                            <div class="info-label">Zipcode</div>
                                            <div class="info-value">${ address.postalCode}</div>
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
                                                    <div class="info-label">Phone</div>
                                                    <div class="info-value">${phones}</div>
                                                </div>
                                            </div>
                                            
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    $('#patient-demographics').html(htmlResponse);
    $('#service_id').val("").trigger('change');
    $('#hha_mdo_patient_add_modal').modal('show');
}

function singleMDODataAppointmentNew(){
   var service_id = $('#service_id').val();

   $('#hha_mdo_patient_service_id_error').html("");
   if(service_id.length ==0){
    $('#hha_mdo_patient_service_id_error').html("Please select Services");
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
                    $('#loader-hha-mdo-appointment').removeClass('d-none');
                    $('#hha-mdo-save-appointment-text').text('Creating ...');
                    var formData = new FormData($('#submitHHAMdoPatient')[0]);
                        formData.append('_token',_CSRF_TOKEN);
                        formData.append('first_name',optimizeHHARecord[$('#modal_patient_id').val()].first_name);
                        formData.append('last_name',optimizeHHARecord[$('#modal_patient_id').val()].last_name);
                        formData.append('gender',optimizeHHARecord[$('#modal_patient_id').val()].gender);
                        formData.append('address1',optimizeHHARecord[$('#modal_patient_id').val()].address1);
                        formData.append('city',optimizeHHARecord[$('#modal_patient_id').val()].city);
                        formData.append('dob',optimizeHHARecord[$('#modal_patient_id').val()].dob);
                        formData.append('state',optimizeHHARecord[$('#modal_patient_id').val()].state);
                        formData.append('country',optimizeHHARecord[$('#modal_patient_id').val()].country);
                        formData.append('zip',optimizeHHARecord[$('#modal_patient_id').val()].zip);
                        formData.append('mobile',optimizeHHARecord[$('#modal_patient_id').val()].mobile);
                        formData.append('phone',optimizeHHARecord[$('#modal_patient_id').val()].phone);
                        formData.append('agency_id',$('#agency_fk').val());
                        $.ajax({
                            url: SAVE_HHA_MDO_PATIENT,
                            type: "post",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                $('#loader-hha-mdo-appointment').addClass('d-none');
                                $('#hha-mdo-save-appointment-text').text('Create Record');
                              
                                toastr.success(res.error_msg);
                                getAllPatientListHHAMdo(1);
                                optimizeHHARecord = [];
                                $('#hha_mdo_patient_add_modal').modal('hide');

                            },
                            error: function(xhr, status, error) {
                                $('#loader-hha-mdo-appointment').addClass('d-none');
                                $('#hha-mdo-save-appointment-text').text('Create Record');
                                showErrorAndLoginRedirection(xhr);
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

function refreshMDO(){
  
    $('#agency_fk').val('').trigger('change');

    getAllPatientListHHAMdo(1);
}