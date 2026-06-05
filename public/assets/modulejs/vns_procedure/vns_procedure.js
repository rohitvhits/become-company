function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'procedure_name':$('#procedure_name').val(),
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
    $('#procedure_name').val("");
    $('#template_type').val('');
    loadAjaxList(1);
}

function resetAddProcedure(){
    $('#form_create_procedure_id')[0].reset();
    $('#procedure_name_error').html("");
    $('#template_type_error').html("");
    $('#add_template_type').val('');
}

function createProcedure(){
    var procedure_name = $('#add_procedure_name').val();
    $('#procedure_name_error').html('');
    $('#template_type_error').html('');
    $('#create-procedure').removeClass('d-none');
    $('#btn-save-text-procedure').text('Saving ...')
    var cnt = 0;

    if(procedure_name.trim() == ''){
        $('#procedure_name_error').html('Please enter Procedure Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#create-procedure').addClass('d-none');
        $('#btn-save-text-procedure').text('Save')
        return false;
    } else {
        var formData = new FormData($('#form_create_procedure_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _SAVE_PROCEDURE,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#create-procedure').addClass('d-none');
                $('#btn-save-text-procedure').text('Save')
            },
            error: function (jqXHR) {
                $('#create-procedure').addClass('d-none');
                $('#btn-save-text-procedure').text('Save')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function getDetails(id){
    $.ajax({
        type:"GET",
        url:_EDIT_PROCEDURE+'/'+id,

        success:function(res){
           $('#record_id').val(res.data.id);
           $('#edit_procedure_name').val(res.data.procedure_name);

           // Set the selected template in dropdown
        //    if(res.data.template_type) {
        //        $('#edit_template_type').val(res.data.template_type);
        //    } else {
        //        $('#edit_template_type').val('');
        //    }

           $('#exampleModal-edit-modal-procedure').modal('show')
        },
        error: function (jqXHR) {
            toastr.error(jqXHR.responseJSON.error_msg);
        },
    })
}

function updateProcedure(){
    var procedure_name = $('#edit_procedure_name').val();
    $('#edit_procedure_name_error').html('');
    $('#edit_template_type_error').html('');
    $('#update-procedure').removeClass('d-none');
    $('#btn-update-text-procedure').text('Updating ...')
    var cnt = 0;

    if(procedure_name.trim() == ''){
        $('#edit_procedure_name_error').html('Please enter Procedure Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#update-procedure').addClass('d-none');
        $('#btn-update-text-procedure').text('Update')
        return false;
    } else {
        var formData = new FormData($('#form_edit_procedure_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _UPDATE_PROCEDURE,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#update-procedure').addClass('d-none');
                $('#btn-update-text-procedure').text('Update')
            },
            error: function (jqXHR) {
                $('#update-procedure').addClass('d-none');
                $('#btn-update-text-procedure').text('Update')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function procedureDelete(id){
   
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
                        url:_DELETE_VNS_PROCEDURE+'/'+id,
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
        'procedure_name': $('#procedure_name').val(),
        'template_type': $('#template_type').val()
    });
    window.location.href = url + '?' + params.toString();
}
