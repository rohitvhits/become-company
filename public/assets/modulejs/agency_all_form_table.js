document.addEventListener('DOMContentLoaded', function () {

    $("#addAgencyFormModal").on("hidden.bs.modal", function () {
        $("#agencyFormAdd")[0].reset();
    });

    $('#addAgencyFormModal').on('shown.bs.modal', function () {
        $('.select_class').select2({
            placeholder: "Select Values",
            allowClear: true
        });
    });

    $(document).on("click", ".addFormModal", function (e) {
        e.preventDefault();
        $("#form_id").val("");
        $("#doctor_id").val("");
        $("#addAgencyForm").text("Save");
        $("#ModalLabel").text("Add Form");
        $("#addAgencyForm").attr("data-uid", "");
        $(".form_id_error, .doctor_id_error").html("");
        $("#addAgencyFormModal").modal("show");
    });

    $(document).on("click", "#addAgencyForm", function (e) {
        e.preventDefault();
        var temp = 0;
        var form_id = $("#f_id").val();
        var selectedStatus = $('input[name="status"]:checked').val();
        
        $(this).prop("disabled", true);

        if (form_id.trim() == "") {
            $(".form_id_error").html("Please enter Form Name");
            temp++;
        } else {
            $(".form_id_error").html("");
        }

        if (temp > 0) {
            $(this).prop("disabled", false);
            return false;
        }
        var formAppend = $('#agencyFormAdd')[0];
        var formData = new FormData(formAppend);
        formData.append('_token', _CSRF_TOKEN)

        $.ajax({

            url: storeData,
            type: "POST",
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () { },
            success: function (response) {
                console.log(response);
                if (response.status === false) {
                    if (response.error) {
                        $.each(response.error, function (prefix, val) {
                            $("span." + prefix + "_error").text(val[0]);
                        });
                        toastr.error(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                    $("#addAgencyForm").prop("disabled", false);

                } else {
                    agencyAllFormTableResponse(selectedStatus);
                    $("#addAgencyFormModal").modal("hide");
                    $("#agencyFormAdd")[0].reset();
                    $("#addAgencyForm").prop("disabled", false);

                    toastr.success(response.msg);

                }
            },
            error: function (error) {
                $("#addAgencyForm").prop("disabled", false);
                toastr.error(error.responseJSON.errors);
            }
        });
    });

    $('input[name="status"]').on('change', function () {
        var selectedStatus = $(this).val();
        agencyAllFormTableResponse(selectedStatus);
    });

});

function agencyAllFormTableResponse(status) {
    $.ajax({
        url: _AGENCY_ALL_FORM_TABLE_LIST,
        type: "get",
        data: {
            status: status,
            patient_id: _RECORD_ID,
            agency_id: _AGENCYID,
        },
        success: function (response) {
            var json = response.data.data;

            var tableResponse = "";
            var cnt = 1;
            if (json.length != 0) {
                console.log(json)
                $.each(json, function (i, v) {                  
                    if (v.mark_as_completed == "1") {
                        var status_badge =
                            '<label class="badge badge-success">Completed</label>';
                    } else {
                        var status_badge = '<label class="badge badge-warning">Pending</label>';
                    }

                    var createdDate = new Date(v.created_at);
                    var year = createdDate.getFullYear();
                    var month = ("0" + (createdDate.getMonth() + 1)).slice(-2);
                    var day = ("0" + createdDate.getDate()).slice(-2);
                    var formattedDate = `${month}/${day}/${year}`;

                    var markAsCompletedDate = new Date(v.mark_as_completed_date);
                    var year = markAsCompletedDate.getFullYear();
                    var month = ("0" + (markAsCompletedDate.getMonth() + 1)).slice(-2);
                    var day = ("0" + markAsCompletedDate.getDate()).slice(-2);
                    if (v.mark_as_completed_date != null) {
                        var formattedMarkAsCompletedDate = `${month}/${day}/${year}`;
                    } else {
                        formattedMarkAsCompletedDate = '-';
                    }

                    var templateId = v.template_by_id ? v.template_by_id.id : '-';

                    var show = '';

                    show = `<a href="${_AGENCY_ALL_FORM_TABLE_VIEW}/${v.id}?status=${status}" data-fancybox data-type="iframe" class="fancybox dropdown-item">View</a>`;

                    var moveToEsign = '';
                    var download = '';

                    if (v.template_by_id && v.template_by_id.id) {
                        if (v.mark_as_completed == '1') {
                            if (agencyFormMoveToEsignPermission) {
                                var moveToEsign = `<a href="javascript:void(0)" class="moveToEsign${v.id} addMoveToEsign dropdown-item" data-template-id="${templateId}"
                                data-id="${v.id}" data-eid="${v.patient_id}"
                                data-eidc="${v.patient.patient_code}"
                                data-receipt-name="${v.patient.first_name} ${v.patient.last_name}"
                                data-type="caregiver">Move To Esign</a>`;
                            }
                        }

                        if (agencyFormDownloadPermission) {
                            var download = `<a href="javascript:void(0)" class="download-icon downloadIcon disabled-icon formdownloadbtn${v.form_id} dropdown-item"
                            data-id="${v.id}"
                            data-form-id="${v.form_id}"
                            data-patient-id="${v.patient_id}"
                            data-agency-id="${v.agency_id}"
                            data-template-id="${templateId}"
                            data-form-name="${v.forms.title}"
                            >Download PDF</a>`;
                        }
                    }

                    var actionButton = `<div class="btn-group pull-right status-dropdoown mr-2"  style="overflow: unset !important">
                                                <button type="button" class="btn btn-warning"
                                                    title="Action">Action</button>
                                                <button type="button"
                                                    class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuSplitButton6" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                                    ${show + ' ' + moveToEsign + ' ' + download}
                                                </div>
                                            </div>`;

                    // var action = show + ' ' + moveToEsign + ' ' + download;
                    var action = actionButton;
                   
                    tableResponse += `
                    <tr>
                        <td>${cnt++}</td>
                        <td>${v.forms.title}</td>
                        <td>${status_badge}</td>
                        <td>${formattedDate}<br>${v.users && v.users.full_name ? v.users.full_name : ''}</td>
                        <td>${formattedMarkAsCompletedDate}<br>${v.mark_as_completed_by && v.user_mark_as_complated_details && v.user_mark_as_complated_details.full_name ? v.user_mark_as_complated_details.full_name : ''}</td>
                        <td>${action}</td>
                    </tr>`;
                });
            } else {
                tableResponse = '<tr><td colspan="6">No record available</td></tr>';
            }

            $(".completed-count").html(response.data.completed_count);
            $(".pending-count").html(response.data.pending_count);
            $("#agency_all_form_table_id").html("");
            $("#agency_all_form_table_id").html(tableResponse);

        },
    });
}

