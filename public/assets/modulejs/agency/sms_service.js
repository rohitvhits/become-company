function loadSMSServices(page=1) {
    $('.smsServiceLoader').attr('style','');
    $('#response_view_id').html("");
    $.ajax({
        type: "get",
        url: _AGENCY_SMS_SERVICE_BY_ID,
        data: {
            'id': _AGENCY_ID,
            'page':page
        },
        success: function (res) {
            $('.smsServiceLoader').attr('style','display:none');
            
            $('#response_view_id').html(res);
        }
    })
}

$(document).on('click', '.pagination-sms-service .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];

    loadSMSServices(page);
});


function disabledServiceStatus(id){
    $.ajax({
        type: "post",
        url: _DISABLED_AGENCY_WISE_SMS_SERVICES,
        data: {
            'id': _AGENCY_ID,
            'service_id':id,
            '_token':_CSRF_TOKEN
        },
        success: function (res) {
          toastr.success(res.error_msg);
        },
        error:function(jqr){
            toastr.error(jqr.responseJSON.error_msg)
        }
    })
}