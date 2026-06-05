document.addEventListener('DOMContentLoaded', function () {
    $('#search-data').on('click', function () {
        $('#loadertag1').removeClass('hide');
        var formsubmit = $('#formsubmit').serialize();
        loadEsignReportList(1, formsubmit);
    });

    $('.fancybox').fancybox({
        toolbar: false,
        smallBtn: true,
        iframe: {
            preload: false
        }
    })
});

$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    var formsubmit = $('#formsubmit').serialize();
    loadEsignReportList(1, formsubmit);
});

function loadEsignReportList(page, formsubmit) {
    $('#loadertag1').removeClass('hide');
    $.ajax({
        url: _ESIGN_REPORT_LIST + "?page=" + page,
        type: "get",
        data: formsubmit,
        success: function (response) {
            $('#loadertag1').addClass('hide');
            $('#resp').html("")
            $('#resp').html(response);

            var totalpending = $('#pending_count').val();
            var totalcomplete = $('#completed_count').val();
            $('.completed-count').html(totalcomplete);
            $('.pending-count').html(totalpending);
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('#loadertag1').removeClass('hide');
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var formsubmit = $('#formsubmit').serialize();
    loadEsignReportList(page, formsubmit);
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
    $('#form_name').val(null).trigger('change');
    $('#agency_fk').val(null).trigger('change');
    $('#patient_name').val(null).trigger('change');
    $('#created_by').val(null).trigger('change');
    $('#mark_as_completed_by').val(null).trigger('change');
    $('#status').val(null).trigger('change');
    var formsubmit = $('#formsubmit').serialize();
    loadEsignReportList(1, formsubmit);
}

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

$(document).on("click", ".addMoveToEsign", function () {
    var $this = $(this);
    var template_id = $(this).data("template-id");
    var id = $(this).data("data-id");
    var eid = $(this).data("eid");
    var eidc = $(this).data("eidc");
    var receipt_name = $(this).data("receipt-name");
    var type = $(this).data("type");
    var formsubmit = $('#formsubmit').serialize();

    $.confirm({
        title: 'Move to Esign',
        columnClass: "col-md-6",
        content: 'Are you sure you want to move to Esign?',
        buttons: {
            formSubmit: {
                text: 'Yes, Move it!',
                btnClass: 'btn-success',
                action: function () {
                    $this.prop('disabled', true);
                    $.ajax({
                        url: storeMoveToEsignData,
                        type: "POST",
                        data: {
                            _token: _CSRF_TOKEN,
                            template_id: template_id,
                            eid: eid,
                            eidc: eidc,
                            receipt_name: receipt_name,
                            type: type,
                        },
                        success: function (response) {
                            toastr.success(response.msg);
                            $this.prop('disabled', false);
                            window.parent.loadEsignReportList(1, formsubmit);
                            window.parent.$.fancybox.close();
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.errors);
                            $this.prop('disabled', false);
                        }
                    });
                }
            },
            cancel: function () {
                // Cancel action
            }
        }
    });
});


$(document).on("click", ".downloadIcon", function () {
    var id = $(this).data("id");
    var form_id = $(this).data("form-id");
    var patient_id = $(this).data("patient-id");
    var template_id = $(this).data("template-id");
    var agency_id = $(this).data("agency-id");
    var form_name = $(this).data("form-name");

    $.ajax({
        url: getTemplateData,
        type: "get",
        data: {
            template_id: template_id,
            form_id: form_id,
            patient_id: patient_id,
            agency_id: agency_id,
            id: id
        },
        xhrFields: {
            responseType: 'blob'  // Ensures the response is treated as a Blob
        },
        success: function (response) {
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);

            link.download = form_name + ".pdf";
            link.click();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});


function saveFormBtn(id) {
    var temp = 0;
    var fid = $(`.save-form-btn${id}`).data('fid');
    var doctor_id = $(`#input-field-${id}-${fid}-doctor_name`).val();
    var formsubmit = $('#formsubmit').serialize();

    if (doctor_id.trim() === "") {
        $(".doctor_id_error").html("Please enter Doctor Name");
        temp++;
    } else {
        $(".doctor_id_error").html("");
    }

    if (temp > 0) {
        return false;
    }

    var formAppend = $('#dynamicAgencyForm_' + id)[0];
    var formData = new FormData(formAppend);
    formData.append('_token', _CSRF_TOKEN)

    $.ajax({
        url: storePatientCustomData,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.success('Data saved successfully');
            window.parent.loadEsignReportList(1, formsubmit);
            window.parent.$.fancybox.close();
        },

        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

