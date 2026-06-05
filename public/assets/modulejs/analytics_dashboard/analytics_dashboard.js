loadCurrentInprogrress();
loadRecentNotes();
loadVisitingAidType();
loadRecentlyUpdates();
loadVisitingDueDateData();
loadLocationWiseStatusData();
loadDocumentRecentData();
countData();
loadAgencyWiseStatusData();

$('#agency_id,#record_type').change(function(){
    loadCurrentInprogrress();
    loadRecentNotes();
    loadVisitingAidType();
    loadRecentlyUpdates();
    loadVisitingDueDateData();
    loadLocationWiseStatusData();
    loadAgencyWiseStatusData();
    loadDocumentRecentData();
    countData();
});

function loadCurrentInprogrress() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#current_inprogress').html('');
    $('.current-inprogress-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: CURRENT_INPROGRESS,
        data: {
           'agency_id' : agency_id,
           'record_type' : record_type
        },    
        success: function (res) {
            var json = res.data;
            var html = '';
            if (res.data.length != 0) {
                var html = '';
                json.forEach(function (item) {
                    var status_html = '';
                    var urls = _VIEW_URL + "/" + item.patient_id;
                    if(item.status == 'processing'){
                        status_html = `<label class="badge badge-info">Processing</label>`;
                    }else if(item.status == 'arrived'){
                        status_html = `<label class="badge badge-primary">Arrived</label>`
                    }
                    html += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                     <h6 class="mb-1">
                                        <a target="_blank" href="${urls}">#<b>${item.patient_id}</b> - ${item.patient.first_name} ${item.patient.last_name} (${item.patient.type})</a>
                                    </h6>
                                </div>
                                <div class="col-md-6" style="display: flex;justify-content: flex-end;">
                                    <dt class="mb-1">${status_html}</dt>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <dt class="mb-1"> <b>Agency</b>: ${(item.patient.agency_detail.agency_name)}</dt>
                                <div class="row">
                                    <div class="col-md-5">
                                        <dt class="mb-1"> <b>Mobile</b>: ${(item.patient != null) && (item.patient.mobile != null) ? item.patient.mobile : 'N/A'}</dt>
                                    </div>
                                    <div class="col-md-7">
                                        <dt class="mb-1"> <b>Updated By</b>: ${(item.status_user_details.full_name != null) && (item.status_user_details.full_name != null) ? item.status_user_details.full_name : 'N/A'}</dt>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-12" style="display:flex">
                                <div class="col-md-6">
                                    <dt> <p class="text-muted mb-0 tx-12"> ${(item.patient.locations != null ? '<i class="mdi mdi-map-marker mr-1"></i>'+item.patient.locations.location_name : '')}</dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(item.last_status_update)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }
            $('.current-inprogress-loader').attr('style', 'display:none');
            $('#current_inprogress').html(html);
        }
    })
}

function loadRecentNotes() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#recent_notes').html("");
    $('.recent-notes-loader').attr('style', 'display:flex');
    $.ajax({
        url: RECENT_NOTES,
        type: 'GET',
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            var json = res.data;
            var htmlResponse = "";
            if (res.data.length != 0) {
                $.each(json, function (i, v) {
                    var urls = _VIEW_URL + "/" + v.patient_id;
                    var agency_name = '';
                    if (v.user_details.agency_details != null) {
                        agency_name = v.user_details.agency_details.agency_name
                    }

                    htmlResponse += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="col-md-12">
                                <h6>
                                    <a target="_blank" href="${urls}">#${v.patient_id} ${v.patient.first_name + ' ' + v.patient.last_name + ' (' + (v.type) + ')'}</a>
                                </h6>
                                <dt style="white-space: pre-wrap;">${v.message}</dt>
                                <dt class="mb-1"> <b>Agency</b>:  ${agency_name}</dt>
                            </div>
                            <div class="row col-md-12" style="display:flex">
                                <div class="col-md-6">
                                    <dt> <p class="text-muted mb-0 tx-12">${v.user_details.first_name + ' ' + v.user_details.last_name}</p></dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(v.created_date)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`
                })
            } else {
                htmlResponse += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }
            $('#recent_notes').html("");
            $('#recent_notes').html(htmlResponse);
            $('.recent-notes-loader').attr('style', 'display:none');
        }
    });
    return false;
}

function loadVisitingAidType() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#visiting_aid_type').html('');
    $('.visiting-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: VISITING_AID_TYPE,
        data: {
            'agency_id' : agency_id,
            'record_type' :record_type
         },
        success: function (res) {
            var json = res.data;
            var html = '';
            if (res.data.length != 0) {
                json.forEach(function (item) {
                    var agency_name = '';
                    if (item.agency_details != null) {
                        agency_name = item.agency_details.agency_name
                    }
                    var urls = _VIEW_URL + '/' + item.patient_id;
                    html += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="col-md-12">
                                <h6>
                                    <b style="color:#007bff">${item.first_name} ${item.last_name} (${item.type})</b>
                                </h6>
                                </dt>
                                <dt> <b>Agency</b>: ${(agency_name)}</dt>
                                <div class="row">
                                    <div class="col-md-5">
                                        <dt> <b>mobile</b>: ${(item.mobile != null ? item.mobile : 'N/A')}</dt>
                                    </div>
                                    <div class="col-md-7">
                                        <dt class="mb-1"> <b>Created By</b>: ${(item.user_details != null ? item.user_details.full_name : '-' )}</dt>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row col-md-12">
                               <div class="col-md-6">
                                    <dt>
                                        <p class="text-muted mb-0 tx-12">
                                            <b>Linked Patient</b>: 
                                            ${item.patient_id == null
                            ? 'Pending'
                            : `<a target="_blank" href="${urls}">${item.patient_id}</a>`
                        }
                                        </p>
                                    </dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(item.created_date)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }
            $('.visiting-data-loader').attr('style', 'display:none');
            $('#visiting_aid_type').html(html);
        }
    })
}

function loadRecentlyUpdates() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#recent_updated_status').html('');
    $('.recent-updates-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: RECENTLY_UPDATED_STATUS,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            var json = res.data;
            var html = '';
            if (res.data.length != 0) {
                json.forEach(function (item) {
                    var status_html = '';
                    var urls = _VIEW_URL + "/" + item.patient_id;
                    if(item.status.toLowerCase() =="pending"){
                        var status_html ="<label class='badge badge-warning'>Pending</label>";
                    }
                    if(item.status.toLowerCase() =="booked"){
                        var status_html ="<label class='badge badge-info'>Booked</label>"; 
                    }
                    if(item.status.toLowerCase() =="completed"){
                        var status_html ="<label class='badge badge-success'>Completed</label>";
                    }
                    if(item.status.toLowerCase() =="cancelled"){
                        var status_html ="<label class='badge badge-danger'>Cancelled</label>";
                    }
                    if(item.status.toLowerCase() =="noshow"){
                        var status_html ="<label class='badge badge-danger'>No Show</label>";
                    }
                    if(item.status.toLowerCase() =="refused"){
                        var status_html ="<label class='badge badge-danger'>Refused</label>";
                    }
                    if(item.status.toLowerCase() =="processing"){
                        var status_html ="<label class='badge badge-info'>Processing</label>";
                    }
                    if(item.status.toLowerCase() =="arrived"){
                        var status_html ="<label class='badge badge-primary'>Arrived</label>";
                    }
                    if(item.status.toLowerCase() =="checkin"){
                        var status_html ="<label class='badge badge-primary'>Mark as ClockIn</label>";
                    }
                    if(item.status.toLowerCase() =="not interested"){
                        var status_html ="<label class='badge badge-primary'>Not Interested</label>";
                    }
                    if(item.status.toLowerCase() =="hospitalized/rehab"){
                        var status_html ="<label class='badge badge-secondary'>Hospitalized/Rehab</label>";
                    }
                    if(item.status.toLowerCase() =="unabletocontact"){
                        var status_html ="<label class='badge badge-primary'>Unable To Contact</label>";
                    }
                    if(item.status.toLowerCase() =="pending termination"){
                        var status_html ="<label class='badge badge-danger'>Pending Termination</label>";
                    }

                    if(item.status.toLowerCase() =="on hold" ){
                        var status_html ="<label class='badge badge-secondary'>On Hold</label>";
                    }
                    if(item.status.toLowerCase() =="on leave"){
                        var status_html ="<label class='badge badge-info'>On Leave</label>";
                    }
                    if(item.status.toLowerCase() =="terminated"){
                        var status_html ="<label class='badge badge-danger'>Terminated</label>";
                    }

                    if(item.status.toLowerCase() =="in process"){
                        var status_html ="<label class='badge badge-secondary'>In process</label>";
                    }

                    if(item.status.toLowerCase() =="undo"){
                        var status_html ="<label class='badge badge-primary'>Undo</label>";
                    }
                    html += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                     <h6 class="mb-1">
                                        <a target="_blank" href="${urls}">#<b>${item.patient_id}</b> - ${item.patient.first_name} ${item.patient.last_name} (${item.patient.type})</a>
                                    </h6>
                                </div>
                                <div class="col-md-6" style="display: flex;justify-content: flex-end;">
                                    <dt class="mb-1">${status_html}</dt>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <dt class="mb-1"> <b>Agency</b>: ${(item.patient.agency_detail.agency_name)}</dt>
                            </div>
                            <div class="col-md-12">
                                <dt class="mb-1"> <b>Updated By</b>: ${(item.status_user_details.full_name)}</dt>
                            </div>
                            <div class="row col-md-12" style="display:flex">
                                <div class="col-md-6">
                                    <dt> <p class="text-muted mb-0 tx-12"> ${(item.patient.locations != null ? '<i class="mdi mdi-map-marker mr-1"></i>'+item.patient.locations.location_name : '')}</dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(item.last_status_update)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }

            $('.recent-updates-loader').attr('style', 'display:none');
            $('#recent_updated_status').html(html);
        }
    })
}

function loadVisitingDueDateData() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#visiting_due_data').html('');
    $('.visiting-due-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: VISITING_DUE_DATE,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            var json = res.data;
            var html = '';
            if (res.data.length != 0) {
                json.forEach(function (item) {
                    var agency_name = '';
                    if (item.agency_details != null) {
                        agency_name = item.agency_details.agency_name
                    }
                    var urls = _VIEW_URL + '/' + item.patient_id;
                    html += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="col-md-12">
                                <h6>
                                    <b style="color:#007bff">${item.first_name} ${item.last_name} (${item.type}) </b>
                                </h6>
                                <dt> 
                                </dt>
                                <dt class="mb-1"> <b>Agency</b>: ${(agency_name)}</dt>
                                <dt class="mb-1"> <b>Created By</b>: ${(item.user_details != null ? item.user_details.full_name : '-' )}</dt>
                            </div>
                            <div class="row col-md-12">
                                <div class="col-md-5">
                                    <dt class="mb-0"><b>Mobile</b>: ${(item.mobile != null ? item.mobile : 'N/A')}</dt>
                                </div>
                                <div class="col-md-7" style="display:flex;justify-content: end">
                                    <dt><b>Due Date</b>: ${(moment(item.due_date).format('MM/DD/YYYY'))}</dt>
                                </div>
                            </div>
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                    <dt>
                                        <p class="text-muted mb-0 tx-12">
                                            <b>Linked Patient</b>: 
                                            ${item.patient_id == null? 'Pending': `<a target="_blank" href="${urls}">${item.patient_id}</a>`}
                                        </p>
                                    </dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(item.created_date)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }

            $('.visiting-due-data-loader').attr('style', 'display:none');
            $('#visiting_due_data').html(html);
        }
    })
}

function loadLocationWiseStatusData() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#location_wise_status_data').html('');
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: LOCATION_WISE_STATUS_DATA,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            $('#location_wise_status_data').html('');
            $('#location_wise_status_data').html(res);
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    })
}

function countData(){
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#arrived').html('0');
    $('#processing').html('0');
    $('#check_in').html('0');
    $('.bg-info,.bg-success,.bg-warning').addClass('shimmer');
    let capitalizedRecordType = record_type.charAt(0).toUpperCase() + record_type.slice(1);
    $.ajax({
        type: "GET",
        url: COUNT_DATA,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            $('#arrived').html(res.data.arrived);
            $('#processing').html(res.data.processing);
            $('#check_in').html(res.data.check_in);
            agency_id = getUrl('agency_fk', agency_id);
            $('#arrived_link').attr('href',_SERVICE_REQUEST_VIEW_URL+'?status[]=arrived'+agency_id+'&type='+capitalizedRecordType);
            $('#processing_link').attr('href',_SERVICE_REQUEST_VIEW_URL+'?status[]=processing'+agency_id+'&type='+capitalizedRecordType);
            $('#checkin_link').attr('href',_SERVICE_REQUEST_VIEW_URL+'?status[]=checkin'+agency_id+'&type='+capitalizedRecordType);
            $('.bg-info,.bg-success,.bg-warning').removeClass('shimmer');
        }
    })
}

function loadDocumentRecentData() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#document_recent_data').html('');
    $('.document-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: DOCUMENT_RECENT_DATA,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            var json = res.data;
            var html = '';
            if (res.data.length != 0) {
                json.forEach(function (item,key) {
                    var agency_name = '';
                    if (item.patient_details.agency_detail != null) {
                        agency_name = item.patient_details.agency_detail.agency_name
                    }
                    var urls = _VIEW_URL + '/' + item.patient_details.id;
                    html += `<div class="col-md-12">
                        <div class="row btm-brder">
                            <div class="col-md-12">
                                <h6>
                                    <b style="color:#007bff"><a href="${urls}" target="_blank">${item.patient_details.first_name} ${item.patient_details.last_name} (${item.patient_details.type}) </a></b>
                                </h6>
                                <dt class="mb-1"> <b>Agency</b>: ${(agency_name)}</dt>
                                <dt class="mb-0"><b>Document Name</b>: ${(item.document_name != null ? item.document_name : 'N/A')}</dt>
                            </div>
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                    <dt>
                                        <p class="text-muted mb-0 tx-12">
                                            ${(item.user_details != null ? item.user_details.full_name : '-' )}
                                        </p>
                                    </dt>
                                </div>
                                <div class="col-md-6" style="display:flex;justify-content: end">
                                    <dt> <p class="text-muted mb-0 tx-12"><i class="mdi mdi-timer mr-1"></i>${(item.created_date)} </p></dt>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `<div class="d-flex align-items-center py-2">
                                <div class="ml-3">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }
            $('.document-data-loader').attr('style', 'display:none');
            $('#document_recent_data').html(html);
        }
    })
}

function getUrl(field, fieldValue) {
    var iscomm = isCommaSeparated(fieldValue);
    if(fieldValue != undefined){
        if (iscomm) {
            str = '';
            $.each(fieldValue, function (i, v) {
                str += '&' + field + '[]=' + v
            });
        } else {
            str = '&' + field + '[]=' + fieldValue;
        }
    }else{
        str = '&' + field + '[]='; 
    }
    
    return str;
}

function isCommaSeparated(str) {
    return /^([^,]+,)+[^,]+$/.test(str);
}

function loadAgencyWiseStatusData() {
    var agency_id = $('#agency_id').val();
    var record_type = $('#record_type').val();
    $('#agency_wise_status_data').html('');
    $('.agency-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: AGENCY_WISE_STATUS_DATA,
        data: {
            'agency_id' : agency_id,
            'record_type' : record_type
         },
        success: function (res) {
            $('#agency_wise_status_data').html('');
            $('#agency_wise_status_data').html(res);
            $('.agency-wise-data-loader').attr('style', 'display:none');
        }
    })
}