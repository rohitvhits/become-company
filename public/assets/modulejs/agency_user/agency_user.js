
/* ..Start.. For page refresh when search data then show search area */
$(document).ready(function () {
    var url = window.location.search;
    var arguments = url.split('?')[1];
    if (arguments) {
        var searchText = arguments.split('=')[0];
        if (searchText == 'first_name') {
            $("#search-div").show();
        }
    }

    $('ul.left-section-ul li').click(function() {
        $('ul.left-section-ul li').removeClass('active');
        $(this).addClass('active');
    })

    $('ul.right-section-ul li').click(function() {
        $('ul.right-section-ul li').removeClass('active');
        $(this).addClass('active');
    
    })
    loadUserList(1);
});
/* ..End.. For page refresh when search data then show search area */
$("#searchbtns").click(function () {
    $("#search-div").toggle();
});

function export_data() {
    var agency_fk = $('#agency_fk').val();
    var login_type = "Agency Rep";
    var user_type = "Agency";
    var full_name = $('#full_name').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var status = $('#status').val();
    var created_by = $('#created_by').val();
    var created_date = $('#created_date').val();

    $.ajax({
        url: AGENCY_EXPORT,
        type: "get",
       data:{
        'agency_fk':agency_fk,
        'login_type' : login_type,
        'user_type' : user_type,
        'full_name' : full_name,
        'email' : email,
        'phone' : phone,
        'status' : status,
        'created_by' : created_by,
        'created_date' : created_date,
       },
        success: function (response) {
            var blob = new Blob([response]);
            if(response == ""){
                toastr.error('Please check there is no data to export.');
                return false;
            }else{
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                var form_name = "user_"+_DATE_TIME;
                link.download = form_name + ".csv";
                link.click();
            }
            
        }
    });
}

// new
$(document).on('click', '#dropdownnew a', function () {
    $('#dropdownMenuSizeButton3').html($(this));
});

function deleteUserData(id) {
    var url = AGENCY_USER_DELETE;
    $.confirm({
        title: 'Delete',
        columnClass: "col-md-6",
        content: 'Are you sure delete this record?',
        buttons: {
            formSubmit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function () {
                    window.location.href = url + id;
                }
            },
            cancel: function () {

            },
        },
    });
}

function getStatus(record_id, status) {
    var id = record_id;
    // var status = $('#dropdownnew').val();
    if (status != '') {
        var msg = "you want to " + status + ' this user?';
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",

            content: msg,
            buttons: {
                formSubmit: {
                    text: 'Yes',
                    btnClass: 'btn-danger',
                    action: function () {
                        $.ajax({
                            url: AGENCY_USER_STATUS_CHANGE,
                            type: "POST",
                            data: {
                                'status': status,
                                'user_id': id,
                                '_token': CSRF_TOKEN
                            },
                            success: function (res) {
                                var status = '';
                                $("#status_id").html('');
                                if (res.data.status == 'active') {
                                    status =
                                        '<span class="badge badge-success">Active</span>';
                                    $("#status_id").html('<a class="dropdown-item" href="javascript::void(0)" id="active" onclick="getStatus(\'' + id + '\',\'inactive\')">Inactive</a><a class="dropdown-item" href="javascript::void(0)" id="block" onclick="getStatus(\'' + id + '\',\'block\')">Block</a>');

                                }
                                if (res.data.status == 'inactive') {
                                    status =
                                        '<span class="badge badge-danger">Inactive</span>';
                                    $("#status_id").html('<a class="dropdown-item" href="javascript::void(0)" id="active" onclick="getStatus(\'' + id + '\',\'active\')">Active</a><a class="dropdown-item" href="javascript::void(0)" id="unblock" onclick="getStatus(\'' + id + '\',\'unblock\')">Unblock</a>');
                                }
                                if (res.data.status == 'block') {
                                    status =
                                        '<span class="badge badge-danger">Block</span>';
                                    $("#status_id").html('<a class="dropdown-item" href="javascript::void(0)" id="unblock" onclick="getStatus(\'' + id + '\',\'unblock\')">Unblock</a><a class="dropdown-item" href="javascript::void(0)" id="active" onclick="getStatus(\'' + id + '\',\'active\')">Active</a>');
                                }

                                if (res.data.status == 'unblock') {
                                    status =
                                        '<span class="badge badge-info">Unblock</span>';
                                    $("#status_id").html('<a class="dropdown-item" href="javascript::void(0)" id="block" onclick="getStatus(\'' + id + '\',\'block\')">Block</a><a class="dropdown-item" href="javascript::void(0)" id="inactive" onclick="getStatus(\'' + id + '\',\'inactive\')">Inactive</a>');
                                }
                                $('#status' + id).html(status);
                                toastr.success(res.error_msg);

                                //    $('#dropdownnew').val();

                            }
                        })
                    }
                },
                cancel: function () {
                    //close
                },
            },
            onContentReady: function () {
                // bind to events

            }
        });
    }
}

// chnage status
function changeStatus(record_id) {
    var id = ID;
    if (record_id == 1) {
        var status = 'N'
        var msg = 'No';
    } else {
        var status = 'Y'
        var msg = 'Yes';
    }
    if (status != '') {
        var msg = "Limit Access " + msg + "";
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",

            content: msg,
            buttons: {
                formSubmit: {
                    text: 'Submit',
                    btnClass: 'btn-danger',
                    action: function () {
                        $.ajax({
                            url: CHNAGESTATUS,
                            type: "POST",
                            data: {
                                'status': status,
                                'user_id': id,
                                '_token': CSRF_TOKEN
                            },
                            success: function (res) {
                                if (res.data.status == 'Y') {
                                    status =
                                        '<span class="badge badge-success" onclick="changeStatus(1)">Yes</span>';
                                }
                                if (res.data.status == 'N') {
                                    status =
                                        '<span class="badge badge-danger" onclick="changeStatus(0)">No</span>';
                                }

                                $('#chnagestatus').html(status);
                            }
                        })
                    }
                },
                cancel: function () { },
            },
            onContentReady: function () { }
        });
    }
}

function updateUser() {

    var first_name = $('#first_name_id').val();
    var last_name = $('#last_name_id').val();
    var email = $('#email_id').val();
    var phone = $('#phone_id').val();
    var cnt = 0;
    var emailRegex = /^[A-Za-z0-9`!#$%^&*()_=+\\';:\/?>.<,-]*$/;
    var number = /^[0-9]+$/;
    $('#first_name_error').html('');
    $('#last_name_error').html('');
    $('#email_error').html('');
    $('#phone_error').html('');


    if (first_name.trim() == '') {
        $('#first_name_error').html("First Name is required");
        cnt = 1;
    }

    if (last_name.trim() == '') {
        $('#last_name_error').html("Last Name is required");
        cnt = 1;
    }

    if (email.trim() == '') {
        $('#email_error').html("Email is required");
        cnt = 1;
    }
    if (email.trim() != '') {

        if (email.trim() != '') {

            if (!email.match(emailRegex)) {
                $('#email_error').html("Only name allowed");
                cnt = 1;
            }

            if (email.length > 50) {
                $('#email_error').html("Invalid Email Name");
                cnt = 1;
            }

        }
    }

    if (phone.trim() != '') {

        if (!phone.match(number)) {
            $('#phone_error').html("Only number allowed");
            cnt = 1;
        }

    }

    if (cnt == 1) {
        $('#detail-div-class').attr('style', 'display:block');
        
        $('#loader').attr('style', 'display:none');
        return false;
    } else {
        var forms = $('#task_patient_id')[0];
        var formData = new FormData(forms);
        formData.append('_token', CSRF_TOKEN);
        formData.append('role_access', $("#role_access").is(':checked') == true ? '1' : '0');
        $.ajax({

            url: AGENCY_USER_UPDATE,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {

                toastr.success(response.error_msg);
                $('#view_first_id').html(response.data.first_name);
                $('#view_last_id').html(response.data.last_name);
                $('#view_email_id').html(response.data.email);
                $('#view_phone_id').html(response.data.phone);
                $('#view_ext_id').html(response.data.ext);
                $('#view_record_access').html(response.data.record_access);
                $('#view_is_admin').html(response.data.role_access == 1? 'Yes' : 'No');
                $('#exampleModal-task').modal('hide');
                setBasicDetails();
                $('#detail-div-class').attr('style', 'display:flex');
                $('#loader').attr('style', 'display:none');
            },
            error: function (xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
}

function changeRecordType() {
    var recordType = $('#record_access  option:selected').val();
    if (recordType != "") {
        var formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('record_type', recordType);
        formData.append('id', RECORD_ID);

        $.ajax({
            url: CHANGE_RECORD_TYPE,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg);
            },
            error: function (xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

}

function setBasicDetails() {
    $('#first_name_error').html('');
    $('#last_name_error').html('');
    $('#email_error').html('');
    $('#phone_error').html('');
    $('.basic-detail-div').find('.show, .hide').toggleClass('show hide');
    $('#loader').attr('style', 'display:none');
}

function getBasicDetails(id) {
    $('#first_name_error').html('');
    $('#last_name_error').html('');
    $('#email_error').html('');
    $('#phone_error').html('');
    $('#loader').attr('style', 'display:none');
    $.ajax({
        url: AGENCY_USER_DETAIL+"/" + id,
        type: "GET",
        success: function (response) {
            $('#first_name_id').val(response.data.userDetails.first_name);
            $('#last_name_id').val(response.data.userDetails.last_name);
            $('#email_id').val(response.data.userDetails.email);
            $('#phone_id').val(response.data.userDetails.phone);
            $('#ext_no_id').val(response.data.userDetails.ext);
            $('#record_access').val(response.data.userDetails.record_access);
            $('#exampleModal-task').modal('hide');
            setBasicDetails();
            $('#detail-div-class').attr('style', 'display:');
            $('#loader').attr('style', 'display:none');
        },
    });
}

function notificationEmailList(page) {
    $('#notification_email_id').html("");
    $('.shimmer_id').attr('style','display:flex');
    $.ajax({
        url: _AGENCY_NOTIFICATION_EMAIL_LIST,
        type: "GET",
        data: {
            'type': 'notifiction-email',
            'agency_id':_AGENCYID,
            'page': page,
        },
        success: function(response) {
            $('#notification_email_id').html("");
            $('#notification_email_id').html(response);
            $('.shimmer_id').attr('style','display:none');
        }
    });
}


$(document).on('click', '.add-notification-email', function(e) {
    $('.notification-emails').html("Add Notification Email")
    $('#add-notification-email-popup').modal('show');
    $('#notification_email_error').html("");
    $('#service_id').html("");
    $('#discipline_id').html("");
    $('#notificationEmail').val("");
    getResponse();
    getDiscipline();
    $('input[name="patient[]"]').prop('checked', false);
    $('input[name="caregiver[]"]').prop('checked', false);
})


function getResponse(existingId = "") {
    $.ajax({
        type: "GET",
        url: AJAX_ALL_SERVICE,
        success: function(res) {
            var response = "";
            var split = existingId.split(',');
            if (res.data.length != 0) {

                response = ''
                $.each(res.data, function(i, v) {
                    if (v.types != "" || v.types != "") {
                        var selected = split.find(o => o == v.id);
                        var selecteds = '';
                        if (selected) {
                            selecteds = "selected='selected'";
                        }
                        response += '<option value="' + v.id + '" ' + selecteds + '>' + v.name + ' ( ' + v.types + ' ) </option>';

                    }
                })
            }

            $('#service_id').html("");
            $('#service_id').html(response);

        }
    })

}

$('#notification-email-saveId').click(function(e) {
    var selectedPatients = [];
    var selectedCaregivers = [];
    var selectedCaregiversId = [];
    var selectedPatientId = [];
    var selectedServiceId = [];
    $('#notifications_email_error').html("");
    $(".patient_checkbox:checked").each(function() {
        selectedPatients.push($(this).val());
        selectedPatientId.push($(this).attr('data-id'));
    });

    $(".caregiver_checkbox:checked").each(function() {
        selectedCaregivers.push($(this).val());
        selectedCaregiversId.push($(this).attr('data-id'))

    });


    var cnt = 0;
    var notificationEmail = $('#notificationEmail').val();
    var validEmail = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/;
    if (notificationEmail.trim() == '') {
        $('#notifications_email_error').html("Email is required");
        cnt = 1;
    }

    if (notificationEmail.trim() != '') {
        if (!validEmail.test(notificationEmail)) {
            $('#notifications_email_error').html("Invalid Email Address");
            cnt = 1;
        }

    }

    var serviceId = $('#service_id').val();
    $.each(serviceId,function(i,v){

        if(v.trim() !=""){
            selectedServiceId.push(v.trim());
        }
    })

    if (selectedCaregivers.length == 0 && selectedPatients.length == 0 && selectedServiceId.length == 0) {
        $('#notification_email_error').html("Patient or Caregiver or Service is required");
        cnt = 1;

    }



    if (cnt == 1) {
        return false;
    } else {
        var forms = $('#addnotificationemail')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', CSRF_TOKEN);
        newForms.append('patient_id', selectedPatientId);
        newForms.append('caregivers_id', selectedCaregiversId);


        $.ajax({
            url: SAVE_AGENCY_NOTIFICATION,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('#add-notification-email-popup').modal('hide');
                $('#addnotificationemail')[0].reset();
                notificationEmailList(1);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });

    }
})

function editNotificationEmail(id) {
    $.ajax({
        method: 'GET',
        url: EDIT_EMAIL_NOTIFICATION,
        data: {
            'id': id,
        },
        success: function(response) {
            $('#service_id').html("");
            $('#discipline_id').html("");
            $('#add-notification-email-popup').modal('show');
            if (response.data.caregivers_id != "") {
                var splitData = response.data.caregivers_id.split(',');
                $.each(splitData, function(i, v) {
                    $('#caregiver_notification_email' + v).prop("checked", true);
                })
            }

            if (response.data.patients_id != "") {
                var splitData = response.data.patients_id.split(',');
                $.each(splitData, function(i, v) {

                    $('#patient_notification_email' + v).prop("checked", true);
                })
            }
            $('#notificationId').val(id)
            $('#notificationEmail').val(response.data.email)
            $('.notification-emails').html("Edit Notification Email")
            getResponse(response.data.service_id);
            getDiscipline(response.data.discipline_id);
        },
        error: function(jxr) {

        }

    });
}

function getDiscipline(existingId = ""){
    $.ajax({
        type: "GET",
        url: _DISCIPLINE_LIST,
        success: function(res) {
            var response = "";
            var split = existingId.split(',');
            if (res.data.length != 0) {
                response = ''
                $.each(res.data, function(i, v) {
                    if (v.types != "" || v.types != "") {
                        var selected = split.find(o => o == v.name);
                        var selecteds = '';
                        if (selected) {
                            selecteds = "selected='selected'";
                        }
                        response += '<option value="' + v.name + '" ' + selecteds + '>' + v.name +'</option>';
                    }
                })
            }
            $('#discipline_id').html("");
            $('#discipline_id').html(response);
        }
    })
}

function deleteNotificationEmail(id) {
    $.confirm({
        title: 'Are you sure delete notification email?',
        columnClass: "col-md-6",
        content: "",

        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: DELETE_AGENCY_USER_NOTIFICATION,
                        type: "get",
                        data: {
                            'id': id,

                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            notificationEmailList(1);
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

function resetNotificationEmail() {
    $('#notificationId').val('');
    $('.error').html('');
    $('#addnotificationemail')[0].reset();
    $('.notification-emails').html("Add Notification Email")
}
$('#add-notification-email-popup').on('hidden.bs.modal', function() {
    resetNotificationEmail();
});

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function loadUserList(page){
    $('#user_response_requested_id').html("");
    $('.shimmer_id').attr('style','display:flex');
    var full_name = $('#full_name').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var status = $('#status').val();
    var created_by = $('#created_by').val();
    var created_date = $('#created_date').val();
    $.ajax({
        url: AGENCY_WISE_USER,
        type: "GET",
        data: {
            'page': page,
            'full_name': full_name,
            'email': email,
            'phone': phone,
            'status': status,
            'created_by': created_by,
            'created_date': created_date,
        },
        success: function(response) {
            $('#user_response_requested_id').html("");
            $('#user_response_requested_id').html(response);
            $('.shimmer_id').attr('style','display:none');
        }
    });
}

function refresh(){
    $('#full_name').val("");
    $('#last_name').val("");
    $('#email').val("");
    $('#record_access').val("").change();
    $('#phone').val("");
    $('#status').val("");
    $('#created_date').val("");
    $('#created_by').tokenInput("clear");
    $('#role_access').prop("checked",false);
    loadUserList(1)
}

var start = moment().subtract(0, 'days');
var end = moment();
$('#created_date').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    startOfWeek: 'sunday',
    ranges: {
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

$("#created_by").tokenInput(_SEARCH_CREATED_BY_USER, {
    tokenLimit: 1,
    zindex: 9999,
   
    onAdd: function (item) {
       
    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto"
            });
        }, 500);
    }
});