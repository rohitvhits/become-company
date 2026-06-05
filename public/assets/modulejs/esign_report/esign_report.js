document.addEventListener('DOMContentLoaded', function () {
    loadEsignReportList(1);

    $('#search-data').on('click', function () {
        loadEsignReportList(1);
    });

    $(document).on('click', 'a[data-toggle="modal"]', function () {
        var templateId = $(this).data('templete-id');
        var patientId = $(this).data('patient-id');
        var type = $(this).data('patient-type');
        var group_id = $(this).data('group-id');
        var agency_form_id = $(this).data('agency-form-id')

        $('#template_id').val(templateId);
        $('#type').val(type);
        $('#patient_id').val(patientId);
        $('#group_id').val(group_id);
        $('#agency_form_id').val(agency_form_id);
    });

});

function loadEsignReportList(page) {
    $('.shimmer_id').removeClass('hide')
    $('#resp').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    var formsubmit = $('#formsubmit').serialize();
    $.ajax({
        url: _ESIGN_REPORT_LIST + "?page=" + page,
        type: "get",
        data: formsubmit,
        success: function (response) {
            $('#loadertag1').addClass('hide');
            $('#resp').html("")
            $('.location-wise-data-loader').attr('style', 'display:none');
            $('#resp').html(response);
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('#loadertag1').removeClass('hide');
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadEsignReportList(page);
});


$(function () {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepicker1').daterangepicker({
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
    }, function (chosen_date, end_date) {

        $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

$(function () {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepicker2').daterangepicker({
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
    }, function (chosen_date, end_date) {

        $('.datepicker2').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

function refresh() {
    $('#loadertag1').removeClass('hide');
    $('.datepicker1, .datepicker2').val('');
    $('#template_name').val(null).trigger('change');
    $('#patient_name').tokenInput("clear");
    $('#created_by').tokenInput("clear");
    $('#sender_name').tokenInput("clear");
    $('#agency_fk').val(null).trigger('change');
    $('#status').val(null).trigger('change');
    loadEsignReportList(1);
}

var empId = '';
var empName = '';
$("#patient_name").tokenInput(urlToken, {

    tokenLimit: 1,
    zindex: 9999,
    prePopulate: empId !== "" && empName !== "" ? [{
        id: empId,
        name: empName
    }] : [],
    onAdd: function (item) {
        $('#patient_name_id').val(item.id);
        $('#patientName').val(item.name);
    },
    onDelete: function (item) {
        $('#patient_name_id').val('');
        $('#patientName').val('');
    },
    
});

var createdById = '';
var createdByName = '';
$("#created_by").tokenInput(urlUserToken, {

    tokenLimit: 1,
    zindex: 9999,
    prePopulate: createdById !== "" && createdByName !== "" ? [{
        id: createdById,
        name: createdByName
    }] : [],
    onAdd: function (item) {
        $('#created_by_id').val(item.id);
        $('#created_by_name').val(item.name);
    },
    onDelete: function (item) {
        $('#created_by_id').val('');
        $('#created_by_name').val('');
    },
    onResult: function (results) {
        setTimeout(function(){
            $(".token-input-dropdown").css({
                "max-height": "200px",
                "overflow-y": "auto",
                "overflow-x": "hidden"
            });
        }, 0);
        return results; // don’t forget to return results!
    }
});

var senderNameId = '';
var senderName = '';
$("#sender_name").tokenInput(urlUserToken, {

    tokenLimit: 1,
    zindex: 9999,
    prePopulate: senderNameId !== "" && senderName !== "" ? [{
        id: senderNameId,
        name: senderName
    }] : [],
    onAdd: function (item) {
        $('#sender_name_id').val(item.id);
        $('#senderName').val(item.name);
    },
    onDelete: function (item) {
        $('#sender_name_id').val('');
        $('#senderName').val('');
    }
});

function exportCsv() {
    $('#loadertag1').removeClass('hide');
    var formsubmit = $('#formsubmit').serialize();

    $.ajax({
        type: "GET",
        url: _ESIGN_REPORT_EXPORT_URL,
        data: formsubmit,

        success: function (res) {
            $('#loadertag1').addClass('hide');
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);

            link.download = "esign_report" + _DATE_TIME + ".csv";
            link.click();
        }
    });
}


function getSendSMSSubmitEsignReport() {
    var userNewId = $('#user_new_id').val();
    var mobile_no_id_caregiver = $('#esign_report_mobile').val();
    var email = $('#esign_report_mobile').val();
    $('#mobile_no_id_caregiver_error').html("");
    var cnt = 0;
    if (mobile_no_id_caregiver.trim() == '' && email.trim() == '') {
        $('#mobile_no_id_caregiver_error').html("Please enter Email or Mobile");
        cnt = 1;
    }
    if (cnt == 0) {
        var cons = confirm("Are you sure you want to send sms?");


        if (cons == true) {
            var foms = $('#sms_esign')[0];
            var formData = new FormData(foms);
            formData.append("_token", _CSRF_TOKEN);
            formData.append("hhaCaregiverId", userNewId);
            $.ajax({
                async: false,
                global: false,
                url: _SMS_EMAIL_ESIGN_TEMPLATE,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res == 1) {
                        toastr.success('Email successfully sent.');
                    } else {
                        toastr.error('Sorry, something went wrong. Please try again.');
                    }
                    $('#sendSMSEsignReport').modal('hide');

                }
            })
        }
    } else {
        $('#sendSMSEsignReport').modal('show');

        return false;
    }
}

function getSendSMSEsignReport(id, mobile, email, patient_id) {

    $('#main_caregiver_esign_id').val(id);
    $('#user_new_id').val(patient_id)
    $('#esign_report_email').val(email);
    $('#esign_report_mobile').val(mobile)
    $('#sendSMSEsignReport').modal('show');
    $('#sendSMSEsignReport').css({
        zIndex: '99999'
    })
}

function getDeleteEsignReport(docId) {
    if (docId != '') {
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to delete this record.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            global: false,
                            url: _DELETE_ESIGN_TEMPLATE + '/' + docId,
                            type: "GET",
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadEsignReportList(1);
                            },
                            error: function (xhr, status, error) {
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        });
                    }
                },
                cancel: function () {

                }
            }
        })

    }
    return false;
}

$('#esignMoveDocumentSave').click(function (e) {
    var esign_report_request_service_id = $("#esign_report_request_service_id").val();
    var esign_report_document_service_id = $("#esign_report_document_service_id").val();

    $("#esign_report_request_service_id_error").html("");
    $("#esign_report_document_service_id_error").html("");
    var cnt = 0;

    if (String(esign_report_request_service_id).trim() === "") {
        $("#esign_report_request_service_id_error").html("Please enter Request Services");
        cnt = 1;
    }
    if (String(esign_report_document_service_id).trim() === "") {
        $("#esign_report_document_service_id_error").html("Please select Services");
        cnt = 1;
    } else {
        $("#esign_report_document_service_id_error").html("");
    }

    if (cnt == 0) {
        $("#esignMoveDocumentSave").prop("disabled", true);
        var formData = new FormData($('#esignReportMoveDocumentForm')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _ESIGN_MOVE_DOCUMENT_STORE,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#esignReportMoveDocumentForm")[0].reset();
                $("#esignMoveDocumentSave").prop('disabled', false);
                $('#esignMoveDocumentModal-1').modal('hide')
                $('#document_service_id').val("").change();
                loadEsignReportList(1);

            },
            error: function (jqXHR) {
                $("#esignMoveDocumentSave").prop('disabled', false);
                toastr.error(jqXHR.responseJSON.error_msg)
            }
        })
    } else {
        return false;
    }

})


function viewServices(record_type, agency_id) {
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_SERVICES,
        data: {
            "id": record_type,
            "agency_id": agency_id
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }

            $('#esign_report_document_service_id').html(htmlsresp);

        }
    })
}


function requestsServices(patient_id) {

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_REQUEST_SERVICES,
        data: {
            "id": patient_id,
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }

            $('#esign_report_request_service_id').html(htmlsresp);

        }
    })
}


function requestSelectService() {

    var request_service_id = $('#esign_report_request_service_id').val();

    if (request_service_id != "") {
        patientRequestService();
    } else {
        viewServices();
    }
}


function patientRequestService() {

    var request_service_id = $('#esign_report_request_service_id').val();
    var patient_id =$('#patient_id').val();
    var type =$('#type').val();
    
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_REQUESTED_BY_ID_SERVICES,
        data: {
            "type": type,
            "patient_id": patient_id,
            "selected_services_id": request_service_id,
           
        },
        success: function (response) {

            var res = response.data;

            var htmlsresp = '';
            if (res && res.length > 0) {

                res.forEach(function (service) {
                    var selected = '';
                   
                    htmlsresp += '<option value="' + service.id + '" ' + selected + ' >' + service.name + '</option>';
                });
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }
               $('#esign_report_document_service_id').html(htmlsresp);

        }
    })
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});