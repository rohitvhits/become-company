 $(document).on("change", ".show_hub", function() {
    var show_hub = $(this).prop('checked') == true ? 1 : 0;
    let content = '';
    if(show_hub == 0){
        content = 'Would you like to deactivate your access to the Hub module?';
    }else{
        content = 'Would you like to activate your access to the Hub module?';
    }
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_HUB_RECORD_URL,
                        data: {
                            'show_hub': show_hub,
                            'agency_id': AGENCY_ID,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            $('.show_hub').val(show_hub)
                        }
                    });
                }
            },
            cancel: function() {
                //close
                let lastStatus = $('#show_hub').val();
                if(lastStatus ==1){
                    $('#show_hub').prop("checked",true);
                }else{
                    $('#show_hub').prop("checked",false);
                }
            },
        },
    });
});