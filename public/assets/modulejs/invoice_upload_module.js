
document.addEventListener('DOMContentLoaded', function () {

    $("#exampleModal-invoice").on("hidden.bs.modal", function () {
        $("#formInvoice")[0].reset();
    });

    $('#exampleModal-invoice').on('shown.bs.modal', function () {
        $('.select_class').select2({
            placeholder: "Select Values",
            allowClear: true
        });
    });

    $(document).on("click", ".addInvoice", function (e) {
        e.preventDefault();
        $("#invoice_request_service_id").val("");
        $("#invoice_document_service_id").val("");
        $("#attachmentImg").val("");
        $("#invoice_request_service_id_error, #invoice_document_service_id_error,#imagess_error").html("");
        $("#exampleModal-invoice").modal("show");
    });

    $(document).on("click", ".upload-invoice-document", function (e) {
        e.preventDefault();
        $("#exampleModal-invoice-upload-doc").modal("show");
    });

    $(document).on("click", ".invoice-document-edit", function (e) {
        e.preventDefault();
        $("#edit-exampleModal-invoice-document").modal("show");
    });

    $('#invoiceDocumentSave').click(function (e) {
        var invoice_request_service_id = $('#invoice_request_service_id').val();
        var invoice_document_service_id = $('#invoice_document_service_id').val();
        var attachmentImg = $('#attachmentImg').val();

        $('#invoice_request_service_id_error').html("");
        $('#invoice_document_service_id_error').html("");
        $('#imagess_error').html("");

        var cnt = 0;

        if (String(invoice_request_service_id).trim() == '') {
            $('#invoice_request_service_id_error').html("Please enter Request Service");
            cnt = 1;
        }
        if (String(invoice_document_service_id).trim() == '') {
            $('#invoice_document_service_id_error').html("Please enter Service");
            cnt = 1;
        }
        if (String(attachmentImg).trim() == '') {
            $('#imagess_error').html("Please select Attachment");
            cnt = 1;
        } else {
            var fileExtensionType = ['pdf', 'csv', 'xlsx', 'xls', 'docx', 'doc'];
            var files = $('input[name="attachment"]')[0].files;
            var fileName = files[0].name;
            var fileType = fileName.substr(fileName.lastIndexOf('.') + 1);
            $('#imagess_error').html("");
            if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtensionType) == -1) {
                $('#imagess_error').html("Please select only pdf or csv file");
                cnt = 1;
            }
        }

        if (cnt == 0) {
            $("#invoiceDocumentSave").prop('disabled', true);
            var formData = new FormData($('#formInvoice')[0]);
            formData.append('_token', _CSRF_TOKEN)

            $.ajax({
                async: false,
                global: false,
                method: "POST",
                url: _INVOICE_URL,
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    toastr.success(res.error_msg);
                    $("#formInvoice")[0].reset();
                    $("#invoiceDocumentSave").prop('disabled', false);
                    $('#exampleModal-invoice').modal('hide')
                    $('#invoice_document_service_id').val("").change();
                    loadInvoiceUploadAjaxList();

                },
                error: function (jqXHR) {
                    $("#invoiceDocumentSave").prop('disabled', false);
                    toastr.error(jqXHR.responseJSON.error_msg)
                }
            })
            return false;
        }
    });
});


function loadInvoiceUploadAjaxList(status) {
    $.ajax({
        url: _INVOICE_TABLE_LIST,
        type: "get",
        data: {
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
                    var createdDate = new Date(v.created_at);
                    var year = createdDate.getFullYear();
                    var month = ("0" + (createdDate.getMonth() + 1)).slice(-2);
                    var day = ("0" + createdDate.getDate()).slice(-2);
                    var formattedDate = `${month}/${day}/${year}`;

                    var deleteData = '';
                    if(deletePermission){
                        deleteData = `<a href="javascriopt:void(0);" class="dropdown-item"onclick="deleteInvoiceRecordDocument('${v.id}')" title="Delete">Delete</a>`;
                    }

                    var actionButton = `<div class="btn-group pull-right status-dropdoown mr-2">
                                                <button type="button" class="btn btn-warning"
                                                    title="Action">Action</button>
                                                <button type="button"
                                                    class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuSplitButton6" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                                    ${deleteData}
                                                </div>
                                            </div>`;

                    var action = actionButton;

                    var attachment = '';

                    if (v.attachment != '' && v.attachment != null) {
                        attachment = `<a target="_blank" href="${_BASE_URL}/invoice-upload-document/${v.id}"><i class="fa fa-download"></i> Download</a>`;
                    }else {
                        attachment = `<a data-toggle="modal" onclick="getUploadDocumentInvoice(${v.id})"><i class="fa fa-upload upload-invoice-document"></i> Upload document </a>`;
                    }

                    tableResponse += `
                    <tr>
                        <td>${cnt++}</td>
                        <td nowrap>${attachment}</td>
                        <td><span class="badge badge-primary">${v.get_service.name}</span></td>
                        <td>${formattedDate}<br>${v.users.full_name}</td>
                        <td>${action}</td>
                    </tr>`;
                });
            } else {
                tableResponse = '<tr><td colspan="6">No record available</td></tr>';
            }

            $("#invoice_table_id").html("");
            $("#invoice_table_id").html(tableResponse);

        },
    });
}

function deleteInvoiceRecordDocument(id) {
    if (id != '') {
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to delete this record.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-danger',
                    action: function () {
                        $.ajax({
                            global: false,
                            url: _INVOICE_DOCUMENT + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadInvoiceUploadAjaxList();
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

function getUploadDocumentInvoice(val) {
    $('#upload_invoice_document_id').val(val);
}
