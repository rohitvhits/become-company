function getuploadAttachment() {
    var attchmentPdf = $("#attchment_pdf")[0].files;
    $(".attchment_pdf_error").html("");
    if (attchmentPdf.length == 0) {
        $(".attchment_pdf_error").html("Please select Attachment");
        return false;
    }
    var forn = $("#attachment_pdf_id")[0];
    var formData = new FormData(forn);
    formData.append("_token", _CSRF_TOKEN);
    formData.append("id", _RECORD_ID);
    $.ajax({
        async: false,
        global: false,
        url: _PATIENT_ATTACHMENT,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.status == 1) {
                toastr.success(res.error_msg);
                var url = "/uploadedfiles/attachment/" + res.data.attachment;
                $("#attachment_pdf_ids").html(
                    '<a href="' + url + '">Download <i class="fa fa-download"></a>'
                );
                $('#attachment_pdf_ids').next().hide();
                $("#closeds").click();
            } else {
                toastr.error(res.error_msg);
            }
        },
        error: function (jqXHR) {
            if (jqXHR.status === 400) {
                toastr.error("No file uploaded. Please select a file and try again.");
            } else if (jqXHR.status === 500) {
                toastr.error("Failed to update attachment. Please try again.");
            } else {
                toastr.error("Sorry, something went wrong. Please try again.");
            }
        },
    });
}