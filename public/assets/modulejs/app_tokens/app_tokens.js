function loadAjaxList(page=1){
    var app_name = $('#search_app_name').val();
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _APP_TOKENS_AJAX_LIST,
        data:{
            'page':page,
            'app_name':app_name,
        },
        type: "GET",
        success:function(res){
            $('.shimmer_id').addClass('hide')
            $('#response_requested_id').html(res)
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    });
}

loadAjaxList(1);

function refresh(){
    $('#search-form')[0].reset();
    loadAjaxList(1);
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

function createAppToken(){
    clearErrors();
    
    var appName = $('#add_app_name').val();
    var referral_type = $('#referral_type').val();
    var appName = $('#add_app_name').val();
    $('#app_name_error').html("");
    $('#referral_type_error').html("");
    var cnt =0;
    if(appName.trim() ==''){
        $('#app_name_error').html("Please enter App Name");
        cnt =1;
    }
    if(referral_type ==''){
        $('#referral_type_error').html("Please select Referral Type");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }
    $('#create-app-token').removeClass('d-none');
    $('#btn-save-text-app-token').text('Saving...');

    const formData = {
        app_name: $('#add_app_name').val(),
        description: $('#add_description').val(),
        referral_type: $('#referral_type').val(),
        _token: _CSRF_TOKEN
    };

    $.ajax({
        url: _SAVE_APP_TOKEN,
        type: 'POST',
        data: formData,
        success: function(response) {
            // Hide spinner
            $('#create-app-token').addClass('d-none');
            $('#btn-save-text-app-token').text('Save');
            $('#exampleModal-add-app-token').modal('hide');
            toastr.success(response.error_msg);
            loadAjaxList(1);
        },
        error: function(xhr) {
            // Hide spinner
            $('#create-app-token').addClass('d-none');
            $('#btn-save-text-app-token').text('Save');
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function clearErrors() {
    $('.error').text('');
    $('.form-control').removeClass('is-invalid');
}

function updateAppToken(){
    clearErrors();
    var appName = $('#edit_app_name').val();
    var edit_referral_type = $('#edit_referral_type').val();
    $('#edit_app_name_error').html("");
    $('#edit_referral_type_error').html("");

    var cnt =0;
    if(appName.trim() ==''){
        $('#edit_app_name_error').html("Please enter App Name");
        cnt =1;
    }

    if(edit_referral_type ==''){
        $('#edit_referral_type_error').html("Please select Referral Type");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }
    $('#update-app-token').removeClass('d-none');
    $('#btn-update-text-app-token').text('Updating...');

    const tokenId = $('#edit_token_id').val();
    const formData = {
        app_name: $('#edit_app_name').val(),
        description: $('#edit_description').val(),
        referral_type: $('#edit_referral_type').val(),
        _token: _CSRF_TOKEN,
        _method: 'PUT'
    };

    $.ajax({
        url:_SAVE_APP_TOKEN+'/'+tokenId,
        type: 'POST',
        data: formData,
        success: function(response) {
            // Hide spinner
            $('#update-app-token').addClass('d-none');
            $('#btn-update-text-app-token').text('Update');
            $('#exampleModal-edit-app-token').modal('hide');
            const row = $(`#row-${tokenId}`);
            row.find('td:eq(1)').text(response.data.app_name);
            row.find('td:eq(3)').text(response.data.description || '-');
            toastr.success(response.error_msg);
        },
        error: function(xhr) {
            // Hide spinner
            $('#update-app-token').addClass('d-none');
            $('#btn-update-text-app-token').text('Update');

            showErrorAndLoginRedirection(xhr);
        }
    });
}

function appTokenDelete(id){
    $.confirm({
        title: "Are you sure?",
        content:"you want to delete this record.",
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                     
                        url:_SAVE_APP_TOKEN+'/'+id,
                        type: 'POST',
                        data: {
                            _token: _CSRF_TOKEN,
                            _method: 'DELETE'
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            loadAjaxList(1);
                        },
                        error:function(jqr){
                           showErrorAndLoginRedirection(jqr);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',

            }
        }
    });
}

$(document).ready(function() {

    $('#filter-btn').on('click', function() {
        $('#search-filter-btn').slideToggle();
    });

    /**
     * Reset form on modal close
     */
    $('#exampleModal-add-app-token').on('hidden.bs.modal', function () {
        $('#form_create_app_token_id')[0].reset();
        clearErrors();
    });

    $('#exampleModal-edit-app-token').on('hidden.bs.modal', function () {
        $('#form_edit_app_token_id')[0].reset();
        clearErrors();
    });

    /**
     * Open Edit Modal and load data
     */
    $(document).on('click', '.btn-edit', function() {
        const tokenId = $(this).data('id');
        openEditModal(tokenId);
    });

    function openEditModal(id) {
        clearErrors();

        $.ajax({
            url:_SAVE_APP_TOKEN+"/"+id+"/json",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;

                    $('#edit_token_id').val(data.id);
                    $('#edit_app_name').val(data.app_name);
                    $('#edit_description').val(data.description);
                    $('#current_token').text(data.token);
                    $('#edit_referral_type').val(data.referral_type);
                    $('#exampleModal-edit-app-token').modal('show');
                }
            },
            error: function() {
                showAlert('Failed to load app token data.', 'danger');
            }
        });
    }
});
