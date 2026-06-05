
function showWebHook(){
    $('#webhook_error').html("");
    $('#authentication_type_error').html("");
    $('#username_error').html("");
    $('#password_error').html("");
    $('#token_error').html("");
    $('#key_error').html("");
    $('#value_error').html("");
   

    $('#ModalLabelNew').html("Add Webhook")
    $('#text_submit_button_web').html("Add");
    $('#add_agency_webhook_form_id')[0].reset()
    $('.divSectionId').addClass('hide');
    $('#webhook_id').val("");
}

function ChangeAuthentication(){
    var authentication_type = $('#authentication_type').val();
    $('.divSectionId').addClass('hide');
    $('#'+authentication_type).removeClass('hide');
    $('.form-reset').val("");
    $('.error_web').html("")
}

function saveWebHook(){
    var authentication_type = $('#authentication_type').val();
    var webhook = $('#webhook').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var token = $('#token').val();
    var key = $('#key').val();
    var value = $('#value').val();
    $('#authentication_type_error').html("")
    $('#webhook_error').html("")
    $('#username_error').html("")
    $('#password_error').html("")
    $('#token_error').html("")
    $('#key_error').html("")
    $('#value_error').html("")

    var cnt =0;

    if(authentication_type ==""){
        $('#authentication_type_error').html("Please select Authentication Type");
        cnt =1;
    }
    
    if(webhook.trim() ==""){
        $('#webhook_error').html("Please enter Webhook URL");
        cnt =1;
    }

    if(authentication_type !=""){
        if(authentication_type =='basic_auth'){
            if(username.trim() ==""){
                $('#username_error').html("Please enter Username");
                cnt =1
            }

            if(password.trim() ==""){
                $('#password_error').html("Please enter Password");
                cnt =1
            }
        }
        if(authentication_type =='bearer_token'){
            if(token.trim() ==""){
                $('#token_error').html("Please enter Token");
                cnt =1
            }
        }
        if(authentication_type =='api_key'){
            if(key.trim() ==""){
                $('#key_error').html("Please enter Key");
                cnt =1
            }

            if(value.trim() ==""){
                $('#value_error').html("Please enter Value");
                cnt =1
            }
        }
    }


    if(cnt ==1){
        return false;
    }else{
        var forms = $('#add_agency_webhook_form_id')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        newForms.append('agency_id', _AGENCY_ID);

        $.ajax({
            async:false,
            global:false,
            url: _SAVE_AGENCY_WEB_HOOK,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                loadAgencyWebHook(1);
                $('#close_webhook').click();
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
}

function loadAgencyWebHook(page=1){
    $.ajax({
        url: _LOAD_AGENCY_WEB_HOOK,
        type: "GET",
        data: {
            'agency_id':_AGENCY_ID,
            'type':'webhook',
            'page':page,
            
        },

        success: function(response) {
            $('#web_ajax_id').html("")
            $('#web_ajax_id').html(response)

        },
        error: function(xhr, status, error) { 
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
    return false;
}

function editWebHook(id){
    $.ajax({
        url: _EDIT_AGENCY_WEB_HOOK,
        type: "GET",
        data: {
            'id':id,
        },
        success: function(response) {
           
            $('#view_web_hook_modal_id').click();
            $('#ModalLabelNew').html("Edit Webhook")
            $('#webhook').val(response.data.webhook_url)
            $('#authentication_type').val(response.data.authentication_type)
            
            if(response.data.authentication_type =="basic_auth"){
                $('#username').val(response.data.user_name)
                $('#password').val(response.data.password)
            }
            if(response.data.authentication_type =="bearer_token"){
                $('#webhook').val(response.data.token)
            }
            if(response.data.authentication_type =="api_key"){
                $('#key').val(response.data.key)
                $('#value').val(response.data.value)
            }
            
            $('.divSectionId').addClass('hide');

            $('#'+response.data.authentication_type).removeClass('hide');
            $('#webhook_id').val(response.data.id);
            $('#text_submit_button_web').html("Update");
           
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

function deleteWebHook(id){
    $.confirm({
        title: 'Are you sure ?',
        content:"You want to delete this record.",
        columnClass: "col-md-6",

        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        async:false,
                        global:false,
                        url: _DELETE_AGENCY_WEB_HOOK,
                        type: "POST",
                        data: {
                            'id':id,
                            '_token':_CSRF_TOKEN
                        },
                      
                        success: function(response) {
                            toastr.success(response.error_msg);
                            loadAgencyWebHook(1);
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

$('body').on('click', '.webhook_generate_token .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    
    var page = $(this).attr('href').split('page=')[1];
    loadAgencyWebHook(page); 
});