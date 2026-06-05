var activityFlag = 1;
var isLoading = false;
var noMore = false;
var ACTIVITY_PAGE_SIZE = 10;

var statusFlag = 1;
var isLastStatusLoading = false;
var lastNoMore = false;
var STATUS_PAGE_SIZE = 10;

var activityUserFlag = 1;
var isUserLoading = false;
var noUserMore = false;
var ACTIVITY_USER_PAGE_SIZE = 10;

$('.activity_div').on('scroll', function () {
    var activeTab = $('.nav-tabs .nav-link.active').text();
    var div = $(this);
    var nearBottom = (div.scrollTop() + div.innerHeight() >= div[0].scrollHeight - 50);
    if(activeTab == 'LocationAppointment'){
        if (nearBottom && !isLoading && !noMore) {
            activityFlag++;
            getActivityFeedData();
        }
    }else if(activeTab == 'LocationUser'){
        if (nearBottom && !isUserLoading && !noUserMore) {
            activityUserFlag++;
            getActivityFeedUserData();
        }
    }
});
$(function(){ getActivityFeedData(); });


$('.status_div').on('scroll', function () {
    var div = $(this);
    var nearBottom = (div.scrollTop() + div.innerHeight() >= div[0].scrollHeight - 50);
    if (nearBottom && !isLastStatusLoading && !lastNoMore) {
        statusFlag++;
        getLastStatusNotUpdatedData();
    }
});
$(function(){ getLastStatusNotUpdatedData(); });

function getActivityFeedData() {
    if (isLoading) return;
    isLoading = true;
    if (activityFlag === 1) {
        $('#agency_activity_feed').html('');
    }
    $('.agency-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: ACTIVITY_FEED_DATA,
        data: {
            'page': activityFlag,
            'per_page': ACTIVITY_PAGE_SIZE
        },
        success: function (res) {
            var list = Array.isArray(res && res.data) ? res.data : [];
            var html = '';
            if (list.length) {
                list.forEach(function (item) {
                    var type = item.record_type || '';
                    var title = item.title || '-';
                    var message = '';
                    if((type == 'Appointment' || type == 'Document' || type == 'Notes' ) && item.id != null){
                        pName = item.p_first_name+' '+item.p_last_name;
                        message = '<a href="'+PATIENT_URL+'/'+(item.record_id)+'">#' +(item.record_id) +' ' +(pName || '') + '</a>' + ' ('+ item.patientType+')';
                    }
                    var createdAt = (window.moment ? moment(item.created_at).format('MM/DD/YYYY HH:mm') : (item.created_at || ''));
                    var userName = ((item.uname || '') + ' ' + (item.lname || '')).trim();
                    var agencyName = (item.agency_name).trim();
                    var badgeClass = 'badge-secondary';
                    if (type === 'Document' || (type || '').toLowerCase().indexOf('update') !== -1) { badgeClass = 'badge-info'; }
                    else if (type === 'Notes') { badgeClass = 'badge-warning'; }
                    else if (type === 'Appointment') { badgeClass = 'badge-success'; }
                    else if (type === 'User') { badgeClass = 'badge-primary'; }

                    var icon = (typeof iconClass !== 'undefined' && iconClass) ? iconClass : 'mdi-bell';
                    html += ''
                        + '<div class="activity-item">'
                        + '<div class="activity-icon"><i class="mdi ' + icon + '"></i></div>'
                        + '<div class="activity-content">'
                        + '<div class="activity-head">'
                        + '<h6 class="activity-title">' + (title || '') + '</h6>'
                        + '<span class="badge '+badgeClass+' ">' + (type || '') + '</span>'
                        + '</div>'
                        + (message ? '<p class="activity-text">' + message + '</p>' : '')
                        + '<p class="activity-meta">'
                        + '<span><i class="mdi mdi-office-building"></i> ' + (agencyName || '') + '</span>'
                        + '<span><i class="mdi mdi-account-outline"></i> ' + (userName || '') + '</span>'
                        + ' ' + '<span><i class="mdi mdi-clock-outline"></i> ' + (createdAt || '') + '</span>'
                        + '</p>'
                        + '</div>'
                        + '</div>';
                });
            } 
            if(list.length == 0 && activityFlag == 1){
                console.log(html);
                html += `<div class="col-md-10">
                    <div class="border-bg-div">
                        <h6 class="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">You're all caught up! No notifications at the moment.</h6>
                    </div>
                </div>`;
            }
            if (activityFlag === 1) {
                $('#agency_activity_feed').html(html);
            } else if (html != "") {
                $('#agency_activity_feed').append(html);
            }

            if (list.length < ACTIVITY_PAGE_SIZE) {
                noMore = true;
            }
            $('.agency-data-loader').attr('style', 'display:none');
            isLoading = false;
        }
    })
}
function getLastStatusNotUpdatedData() {
    if (isLastStatusLoading) return;
    isLastStatusLoading = true;
    if (statusFlag === 1) {
        $('#status_not_updated').html('');
    }
    $('.status-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: LAST_STATUS_DATA,
        data: {
            'page': statusFlag,
            'per_page': ACTIVITY_PAGE_SIZE
        },
        success: function (res) {
            var list = Array.isArray(res && res.data) ? res.data : [];
            var html = '';
            if (list.length) {
                list.forEach(function (item) {
                    var last_status_update = (window.moment ? moment(item.last_status_update).format('MM/DD/YYYY HH:mm') : (item.last_status_update || ''));
                    var userName = ((item.uname || '') + ' ' + (item.lname || '')).trim();
                    var pName = ((item.first_name || '') + ' ' + (item.last_name || '')).trim();
                    var agencyName = (item.agency_name).trim();
                    var status = (item.status).trim();
                    var statusLower = status.toLowerCase().trim();
                    var badgeClass;
                    if (statusLower === 'pending') {
                        badgeClass = 'badge-warning';
                    } else if (statusLower === 'booked' || statusLower === 'processing' || statusLower === 'on leave') {
                        badgeClass = 'badge-info';
                    } else if (statusLower === 'completed') {
                        badgeClass = 'badge-success';
                    } else if (statusLower === 'cancelled' || statusLower === 'pending termination' || statusLower === 'refused' || statusLower === 'terminated') {
                        badgeClass = 'badge-danger';
                    } else if (statusLower === 'noshow' || statusLower === 'hospitalized/rehab' || statusLower === 'on hold') {
                        badgeClass = 'badge-secondary';
                    } else if (statusLower === 'arrived' || statusLower === 'checkin' || statusLower === 'not interested' || statusLower === 'unabletocontact') {
                        badgeClass = 'badge-primary';
                    } else if (statusLower === '1st attempt - unable to contact' || statusLower === '2nd attempt - unable to contact' || statusLower === '3rd attempt - unable to contact' || statusLower === 'patient asked to reschedule' || statusLower === 'new order received') {
                        badgeClass = 'badge-info';
                    } else if (statusLower === 'telehealth completed' || statusLower === 'telehealth completed , pending forms' || statusLower === 'form completed' || statusLower === 'service provided') {
                        badgeClass = 'badge-success';
                    } else if (
                        statusLower === 'patient deceased' ||
                        statusLower === 'appointment was missed' ||
                        statusLower === 'appointment missed' ||
                        statusLower === 'closed temporarily' ||
                        statusLower === 'inactive'
                    ) {
                        badgeClass = 'badge-danger';
                    }
                    else if (
                        statusLower === 'signed' ||
                        statusLower === 'signed & sent back to the agency' ||
                        statusLower === 'new form requested'
                    ) {
                        badgeClass = 'badge-primary';
                    }
                    else if (
                        statusLower === '1st attempt - unable to contact' ||
                        statusLower === '2nd attempt - unable to contact' ||
                        statusLower === '3rd attempt - unable to contact' ||
                        statusLower === 'patient asked to reschedule' ||
                        statusLower === 'new order received'
                    ) {
                        badgeClass = 'badge-info';
                    } else {
                        badgeClass = 'badge-primary';
                    }            
                    var icon = (typeof iconClass !== 'undefined' && iconClass) ? iconClass : 'mdi-lock-outline';
                    html += ''
                        + '<div class="activity-item">'
                        + '<div class="activity-content">'
                        + '<div class="activity-head">'
                        + '<h6 class="activity-title"> ' + '<a href="'+PATIENT_URL+'/'+(item.id)+'">#' +(item.id) +' ' +(pName || '') + '</a>'+ ' ('+ item.type+')'+'</h6>'
                        + '<span class="badge '+badgeClass+' ">' + (status || '') + '</span>'
                        + '</div>'
                        + '<p class="activity-meta">'
                        + '<span><i class="mdi mdi-office-building"></i> ' + (agencyName || '') + '</span>'
                        + '<span><i class="mdi mdi-account-outline"></i> ' + (userName || '') + '</span>'
                        + ' ' + '<span><i class="mdi mdi-clock-outline"></i> ' + (last_status_update || '') + '</span>'
                        + '</p>'
                        + '</div>'
                        + '</div>';
                });
            } 

            if(list.length == 0 && statusFlag == 1){
                html += `<div class="col-md-10">
                    <div class="border-bg-div">
                        <h6 class="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">You're all caught up! No Records at the moment.</h6>
                    </div>
                </div>`;
            }

            if (statusFlag === 1) {
                $('#status_not_updated').html(html);
            } else if (html != "") {
                $('#status_not_updated').append(html);
            }

            if (list.length < ACTIVITY_PAGE_SIZE) {
                lastNoMore = true;
            }
            $('.status-data-loader').attr('style', 'display:none');
            isLastStatusLoading = false;
        }
    })
}

function getActivityFeedUserData() {
    if (isUserLoading) return;
    isUserLoading = true;
    if (activityUserFlag === 1) {
        $('#agency_user_activity_feed').html('');
    }
    $('.agency-data-loader').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: ACTIVITY_FEED_USER_DATA,
        data: {
            'page': activityUserFlag,
            'per_page': ACTIVITY_USER_PAGE_SIZE
        },
        success: function (res) {
            var list = Array.isArray(res && res.data) ? res.data : [];
            var html = '';
            if (list.length) {
                list.forEach(function (item) {
                    var type = item.record_type || '';
                    var title = item.title || '-';
                    var message = '';
                    uName = item.runame+' '+item.rlname;
                    if(type == 'User'){
                        message = '<a href="'+USER_URL+'/'+(item.record_id)+'">#' +(item.record_id) +' ' +(uName || '') + '</a>';
                    }
                    var createdAt = (window.moment ? moment(item.created_at).format('MM/DD/YYYY HH:mm') : (item.created_at || ''));
                    var userName = ((item.uname || '') + ' ' + (item.lname || '')).trim();
                    var agencyName = (item.agency_name).trim();
                    var badgeClass = 'badge-secondary';
                    if (type === 'Document' || (type || '').toLowerCase().indexOf('update') !== -1) { badgeClass = 'badge-info'; }
                    else if (type === 'Notes') { badgeClass = 'badge-warning'; }
                    else if (type === 'Appointment') { badgeClass = 'badge-success'; }
                    else if (type === 'User') { badgeClass = 'badge-primary'; }

                    var icon = (typeof iconClass !== 'undefined' && iconClass) ? iconClass : 'mdi-bell';
                    html += ''
                        + '<div class="activity-item">'
                        + '<div class="activity-icon"><i class="mdi ' + icon + '"></i></div>'
                        + '<div class="activity-content">'
                        + '<div class="activity-head">'
                        + '<h6 class="activity-title">' + (title || '') + '</h6>'
                        + '<span class="badge '+badgeClass+' ">' + (type || '') + '</span>'
                        + '</div>'
                        + (message ? '<p class="activity-text">' + message + '</p>' : '')
                        + '<p class="activity-meta">'
                        + '<span><i class="mdi mdi-office-building"></i> ' + (agencyName || '') + '</span>'
                        + '<span><i class="mdi mdi-account-outline"></i> ' + (userName || '') + '</span>'
                        + ' ' + '<span><i class="mdi mdi-clock-outline"></i> ' + (createdAt || '') + '</span>'
                        + '</p>'
                        + '</div>'
                        + '</div>';
                });
            } 
            if(list.length == 0 && activityUserFlag == 1){
                html += `<div class="col-md-10">
                    <div class="border-bg-div">
                        <h6 class="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">You're all caught up! No notifications at the moment.</h6>
                    </div>
                </div>`;
            }
            if (activityUserFlag === 1) {
                $('#agency_user_activity_feed').html(html);
            } else if (html != '') {
                $('#agency_user_activity_feed').append(html);
            }

            if (list.length < ACTIVITY_USER_PAGE_SIZE) {
                noMore = true;
            }
            $('.agency-data-loader').attr('style', 'display:none');
            isUserLoading = false;
        }
    })
}