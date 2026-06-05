function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'question_name':$('#question_name').val(),
            'template_type':$('#template_type').val(),
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
    $('#question_name').val("");
    $('#template_type').val('');
    loadAjaxList(1);
}

function resetAddQuestion(){
    $('#form_create_question_id')[0].reset();
    $('#question_name_error').html("");
    $('#template_type_error').html("");
    $('#add_template_type').val('');
}

function createQuestion(){
    var question_name = $('#add_question_name').val();
    var add_template_type = $('#add_template_type').val();
    $('#question_name_error').html('');
    $('#template_type_error').html('');

    $('#create-question').removeClass('d-none');
    $('#btn-save-question').text('Saving ...')
    var cnt = 0;

    if(question_name.trim() == ''){
        $('#question_name_error').html('Please enter Question Name');
        cnt = 1;
    }
    if(add_template_type == ''){
        $('#template_type_error').html('Please select Template Type');
        cnt = 1;
    }
    if(cnt == 1){
        $('#create-question').addClass('d-none');
        $('#btn-save-question').text('Save')
        return false;
    } else {
        var formData = new FormData($('#form_create_question_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _SAVE_QUESTION,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#create-question').addClass('d-none');
                $('#btn-save-question').text('Save')
            },
            error: function (jqXHR) {
                $('#create-question').addClass('d-none');
                $('#btn-save-question').text('Save')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function getDetails(id){
    $.ajax({
        type:"GET",
        url:_EDIT_QUESTION+'/'+id,

        success:function(res){
           $('#record_id').val(res.data.id);
           $('#edit_question_name').val(res.data.question_name);

           // Set the selected template in dropdown
           if(res.data.template_type) {
               $('#edit_template_type').val(res.data.template_type);
           } else {
               $('#edit_template_type').val('');
           }

           $('#exampleModal-edit-modal-question').modal('show')
        },
        error: function (jqXHR) {
            toastr.error(jqXHR.responseJSON.error_msg);
        },
    })
}

function updateQuestion(){
    var question_name = $('#edit_question_name').val();
    var edit_template_type = $('#edit_template_type').val();
    $('#edit_question_name_error').html('');
    $('#edit_template_type_error').html('');

    $('#update-question').removeClass('d-none');
    $('#btn-update-question').text('Updating ...')
    
    var cnt = 0;

    if(question_name.trim() == ''){
        $('#edit_question_name_error').html('Please enter Question Name');
        cnt = 1;
    }

    if(edit_template_type == ''){
        $('#edit_template_type_error').html('Please select Template Type');
        cnt = 1;
    }
    if(cnt == 1){
        $('#update-question').addClass('d-none');
        $('#btn-update-question').text('Update')
        return false;
    } else {
        var formData = new FormData($('#form_edit_question_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _UPDATE_QUESTION,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#update-question').addClass('d-none');
                $('#btn-update-question').text('Update')
            },
            error: function (jqXHR) {
                $('#update-question').addClass('d-none');
                $('#btn-update-question').text('Update')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function questionDelete(id){
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
                        url:_DELETE_VNS_QUESTION+'/'+id,
                        data:{
                            '_token':_CSRF_TOKEN
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            loadAjaxList(1);
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
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
        'question_name': $('#question_name').val(),
        'template_type': $('#template_type').val()
    });
    window.location.href = url + '?' + params.toString();
}
