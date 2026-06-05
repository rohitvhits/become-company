function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'type':$('#type').val(),
            'service_name':$('#service_name').val(),
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
    $('#service_name').val("");
    $('#type').val('');
    loadAjaxList(1);
}

function resetAddService(){
    $('#form_create_service_id')[0].reset();
    $('#service_name_error').html("");
    $('#service_type_error').html("");
}

function createServices(){
    var service_name = $('#add_service_name').val();
    var service_type = $('#service_type').val();
    $('#service_name_error').html('');
    $('#service_type_error').html('');
    var cnt =0;
    if(service_name.trim() ==''){
        $('#service_name_error').html('Please enter Service Name');
        cnt =1;
    }
    if(service_type ==''){
        $('#service_type_error').html('Please select Service Type');
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        var formData = new FormData($('#form_create_service_id')[0]);
        if($('#enabled_nubest_user:checked').val() !=undefined){
            formData.append('enabled_nybest_user',$('#enabled_nubest_user:checked').val());
        }
        
        formData.append('_token',_CSRF_TOKEN);
        $.ajax({
            type:"POST",
            url:_SAVE_SERVICES,
            data:formData,
            contentType: false,
            processData: false,
            success:function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
            },
            error: function (jqXHR) {
                $("#rateCardSave").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function getDetails(id){
    $.ajax({
        type:"GET",
        url:_EDIT_SERVICES+'/'+id,
        
        success:function(res){
           console.log(res);
           $('#record_id').val(res.data[0].id);
           $('#edit_service_name').val(res.data[0].name);
           $('#edit_service_type').val(res.data[0].types);

           $('#edit_enabled_nubest_user').prop('checked',false);
           if(res.data[0].enabled_nybest_user ==1){
            $('#edit_enabled_nubest_user').prop('checked',true);
           }
           $('#exampleModal-edit-modal-services').modal('show')
        },
        error: function (jqXHR) {
        
            toastr.error(jqXHR.responseJSON.error_msg);
        },
    })
}

function updateServices(){
    var service_name = $('#edit_service_name').val();
    var service_type = $('#edit_service_type').val();
    $('#edit_service_name_error').html('');
    $('#edit_service_type_error').html('');
    var cnt =0;
    if(service_name.trim() ==''){
        $('#edit_service_name_error').html('Please enter Service Name');
        cnt =1;
    }
    if(service_type ==''){
        $('#edit_service_type_error').html('Please select Service Type');
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        var formData = new FormData($('#form_edit_service_id')[0]);
        if($('#edit_enabled_nubest_user:checked').val() !=undefined){
            formData.append('enabled_nybest_user',$('#edit_enabled_nubest_user:checked').val());
        }
        
        formData.append('_token',_CSRF_TOKEN);
        $.ajax({
            type:"POST",
            url:_UPDATE_SERVICES,
            data:formData,
            contentType: false,
            processData: false,
            success:function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
            },
            error: function (jqXHR) {
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function serviceDelete(id){
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
                        type:"post",
                        url:_DELETE_SERVICES,
                        data:{
                            'id':id,
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

function changeStatus(id) {
    if ($('#is_disable_' + id).is(':checked')) {
        checked = false;
        status = 'enable';
    } else {
        checked = true;
        status = 'disable';
    }
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to ' + status + ' this record.',
        type: 'blue',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        global: false,
                        url: _ENABLED_SERVICE,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(response) {
                            toastr.success(response.error_msg);
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                $('#is_disable_' + id).prop("checked", checked);
            }
        }
    })

}

function enableNyBestUser(id){
    if ($('#enabled_nubest_user_' + id).is(':checked')) {
        checked = false;
        status = 'enable';
    } else {
        checked = true;
        status = 'disable';
    }
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to ' + status + ' NyBest User for record.',
        type: 'blue',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        global: false,
                        url: _ENABLE_NYBEST_USER,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(response) {
                            toastr.success(response.error_msg);
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                $('#enabled_nubest_user_' + id).prop("checked", checked);
            }
        }
    })      
}