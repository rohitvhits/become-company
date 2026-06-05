$(document).on("click", ".addMoveToEsign", function () {
    var $this = $(this);
    var template_id = $(this).data("template-id");
    var id = $(this).data("id");
    var eid = $(this).data("eid");
    var eidc = $(this).data("eidc");
    var receipt_name = $(this).data("receipt-name");
    var type = $(this).data("type");
    var type_value = $('input[name="type_value"]').val();
    if(type_value == "FancyBox"){
        var selectedStatus = $('input[name="status"]').val();
    }else{
        var selectedStatus = $('input[name="status"]:checked').val();
    }

    $.confirm({
        title: 'Move to Esign',
        columnClass: "col-md-6",
        content: 'Are you sure you want to move to Esign?',
        buttons: {
            formSubmit: {
                text: 'Yes, Move it!',
                btnClass: 'btn-success',
                action: function() {
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
                            id:id
                        },
                        success: function (response) {
                            toastr.success(response.msg);
                            $this.prop('disabled', false);
                            window.parent.agencyAllFormTableResponse(selectedStatus);
                            window.parent.$.fancybox.close();
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.errors);
                            $this.prop('disabled', false);
                        }
                    });
                }
            },
            cancel: function() {
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
            id:id
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

