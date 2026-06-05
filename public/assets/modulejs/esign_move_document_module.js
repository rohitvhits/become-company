$(document).ready(function () {
    $(document).on('click', 'a[data-toggle="modal"]', function () {
        var templateId = $(this).data('templete-id');
        var type = $(this).data('type');
        var group_id = $(this).data('group-id');
        var agency_form_id = $(this).data('agency-form-id');
        var esign_doc_id = $(this).data('esign-doc-id');

        $('#template_id').val(templateId);
        $('#type').val(type);
        $('#group_id').val(group_id);
        $('#agency_form_id').val(agency_form_id);
        $('#esign_doc_id').val(esign_doc_id);

    });
});

$('#esignMoveDocumentSave').click(function (e) {
    // var esign_request_service_id = $("#esign_request_service_id").val();
    // var esign_document_service_id = $("#esign_document_service_id").val();

    // $("#esign_request_service_id_error").html("");
    // $("#esign_document_service_id_error").html("");
    // var cnt = 0;

    // if (String(esign_request_service_id).trim() === "") {
    //     $("#esign_request_service_id_error").html("Please enter Request Services");
    //     cnt = 1;
    // }
    // if (String(esign_document_service_id).trim() === "") {
    //     $("#esign_document_service_id_error").html("Please select Services");
    //     cnt = 1;
    // } else {
    //     $("#esign_document_service_id_error").html("");
    // }

    // if (cnt == 0) {
        $("#esignMoveDocumentSave").prop("disabled", true);
        var formData = new FormData($('#esignMoveDocumentForm')[0]);
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
                $("#esignMoveDocumentForm")[0].reset();
                $("#esignMoveDocumentSave").prop('disabled', false);
                $('#esignMoveDocumentModal-1').modal('hide')
                $('#document_service_id').val("").change();

            },
            error: function (jqXHR) {
                $("#esignMoveDocumentSave").prop('disabled', false);
                toastr.error(jqXHR.responseJSON.error_msg)
            }
        })
    // } else {
    //     return false;
    // }

})