$(document).on('click', '.log-pegination .pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getData(page);
});
$(document).ready(function () {
    $('#loadertag').show();
    getData(1);
});

function getData(page) {

    var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    $.ajax({
        method: 'GET',
        url: _PATIENT_WISE_SERVICE_LIST + "?page=" + page,
        data: {
            'id': _RECORD_ID,
            '_token': _CSRF_TOKEN
        },
        beforeSend: function () {
            $('#loadertag').show();
        },
        success: function success(response) {

            $('#loadertag').hide();
            $('#logList').html("");
            $('#logList').html(response);
        },
        error: function error(_error) {

            toastr.error('Something happened. Try again');
        }
    });
}

function uploadDocumentServices(documentId = '') {
    var cnt = 0;
    var file = $('#fileInput_' + documentId).prop('files');
 
    $(".document_upload_" + documentId + "_error").html("");
    if (file.length == 0) {
        $(".document_upload_"+documentId+"_error").html("Please upload files");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        var file = $('#fileInput_' + documentId).prop('files')[0];
        
        var formData = new FormData();
        formData.append('document', file);
        formData.append('id', documentId);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            async: false,
            global: false,
            url: _UPLOAD_DOCUMENT_REQUEST_SERVICE,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (resp) {
                toastr.success(resp.error_msg);
                getData();
            },
            error: function (jqXHR) {
              
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    }
}