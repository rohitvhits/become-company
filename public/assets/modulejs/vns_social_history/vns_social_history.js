function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'name':$('#name').val(),
            'template_name':$('#template_name').val(),
            'template_id':$('#template_id').val(),
        },
        type: "GET",
        success:function(res){
            $('.shimmer_id').addClass('hide')
            $('#response_requested_id').html(res)
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    });
}

loadAjaxList(1)

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

function refresh(){
    $('#name').val("");
    $('#template_name').val("");
    $('#template_id').val('');
    loadAjaxList(1);
}

function resetAddSocialHistory(){
    $('#form_create_social_history_id')[0].reset();
    $('#template_id_error').html("");
    $('#names_error').html("");
    $('#default_value_error').html("");
    $('#add_template_id').val('');
}


function createSocialHistory(){
    var template_id = $('#add_template_id').val();
    var social_history_name =  $('.social-history-name-input').val();
    $('#template_id_error').html('');
    $('#names_error').html('');
    $('#default_value_error').html('');
    $('#create-social-history').removeClass('d-none');
    $('#btn-save-text').text('Saving ...')
    var cnt = 0;

    if(template_id.trim() == ''){
        $('#template_id_error').html('Please select Template');
        cnt = 1;
    }

    if(social_history_name.trim() ==""){
        $('#names_error').html('Please enter Social History Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#create-social-history').addClass('d-none');
        $('#btn-save-text').text('Save')
        return false;
    } else {
        var formData = new FormData($('#form_create_social_history_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _SAVE_SOCIAL_HISTORY,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#create-social-history').addClass('d-none');
                $('#btn-save-text').text('Save')
            },
            error: function (jqXHR) {
                $('#create-social-history').addClass('d-none');
                $('#btn-save-text').text('Save')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function getDetails(id){
    $.ajax({
        type:"GET",
        url:_EDIT_SOCIAL_HISTORY+'/'+id,

        success:function(res){
           $('#record_id').val(res.data.id);

           // Set the selected Template in dropdown
           $('#edit_template_id').val(res.data.template_id);
           $('.edit-social-history-name-input').val(res.data.name);
           $('.edit-default-value-input').val(res.data.default_value);
           $('#exampleModal-edit-modal-social-history').modal('show')
        },
        error: function (jqXHR) {
            toastr.error(jqXHR.responseJSON.error_msg);
        },
    })
}


function updateSocialHistory(){
    var template_id = $('#edit_template_id').val();
    var social_history_name =  $('.edit-social-history-name-input').val();
    $('#edit_template_id_error').html('');
    $('#edit_names_error').html('');
    $('#edit_default_value_error').html('');
    var cnt = 0;
    $('#update-social-history').removeClass('d-none');
    $('#btn-update-text').text('Updating ...')
    if(template_id.trim() == ''){
        $('#edit_template_id_error').html('Please select Template');
        cnt = 1;
    }

    if(social_history_name.trim() ==''){
        $('#edit_names_error').html('Please enter Social History Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#update-social-history').addClass('d-none');
        return false;
    } else {
        var formData = new FormData($('#form_edit_social_history_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _UPDATE_SOCIAL_HISTORY,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#update-social-history').addClass('d-none');
                $('#btn-update-text').text('Update')
            },
            error: function (jqXHR) {
                $('#update-social-history').addClass('d-none');
                $('#btn-update-text').text('Update')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function socialHistoryDelete(id){
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
                        type:"DELETE",
                        url:_DELETE_VNS_SOCIAL_HISTORY+'/'+id,
                        data:{
                            '_token':_CSRF_TOKEN
                        },
                        success:function(res){
                            toastr.success(res.error_msg)

                            loadAjaxList(1);
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg)

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

function exportCSV(){
    var url = _EXPORT_CSV;
    var params = new URLSearchParams({
        'name': $('#name').val(),
        'template_id': $('#template_id').val()
    });
    window.location.href = url + '?' + params.toString();
}
