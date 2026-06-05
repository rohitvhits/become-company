$(document).ready(function () {
    $('.fancybox').fancybox({
        toolbar  : false,
        smallBtn : true,
        iframe : {
          preload : false
        }
      })
 })

function saveFormBtn(id) {

    var temp = 0;
    var fid = $(`.save-form-btn${id}`).data('fid');
    var doctor_id = $(`#input-field-${id}-${fid}-doctor_name`).val();
    var selectedStatus = $('input[name="status"]').val();

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
            window.parent.agencyAllFormTableResponse(selectedStatus);
            window.parent.$.fancybox.close();

        },

        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

