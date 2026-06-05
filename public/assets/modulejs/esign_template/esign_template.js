function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'agency_fk':$('#agency_fk').val(),
            'template_name':$('#template_name').val(),
            'lookup_fields':$('#lookup_fields').val(),
            'created_date':$('#created_date').val(),
            'updated_date':$('#updated_date').val(),
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

$('#submitid').submit(function(e) {
    var name = $('#name_id').val();
    var cnt = 0;
    $('#name_error').html(" ");
    if (name == '') {
        $('#name_error').html("Name is required");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        return true;
    }
});

$(document).on("change", ".statusActiveDeactive", function () {
    var $toggle = $(this);
    var previousStatus = $toggle.data('previous');
    var id = $(this).attr("data-id");
   
    $.confirm({
        title: 'Change Status',
        columnClass: "col-md-6",
        content: 'Are you sure you want to change the status?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                 
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: _STATUS_ACTIVE_DEACTIVE,
                        data: {
                            'id': id
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            loadAjaxList(1)
                        },
                        error: function (xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-default',
                action: function () {
                    let checked = false;
                    if(previousStatus =='Active'){
                        checked = true;
                    }
                    $toggle.prop('checked', checked);
                }
            }
        }
    });
    
   
});

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

function refresh(){
    $('#template_name').val("");
    $('#agency_fk').val(null).trigger("change");
    $('#lookup_fields').val("");
    $('#created_date').val("");
    $('#updated_date').val("");
    loadAjaxList(1);
}

function openSignerNotificationModal(templateId){
    $('#signer_notification_template_id').val(templateId);
    $('#signer_checkbox_container').html('');
    $('#no_signer_msg').hide();
    $.ajax({
        url: _SIGNER_NOTIFICATION_GET,
        data: { 'id': templateId },
        type: "GET",
        dataType: "json",
        success: function(response){
            if(response.status && response.allocated_signers.length > 0){
                var html = '';
                $.each(response.allocated_signers, function(i, signerValue){
                    var label = response.signer_labels[signerValue] || signerValue;
                    var checked = (response.signer_types.indexOf(label) !== -1) ? 'checked' : '';
                    html += '<div class="col-md-6 mb-3">';
                    html += '<div class="form-check custom-check table-check">';
                    html += '<label class="form-check-label">';
                    html += '<input type="checkbox" class="form-check-input signer-type-checkbox" value="'+label+'" '+checked+'>';
                    html += '<i class="input-helper"></i> '+ucFirst(label);
                    html += '</label>';
                    html += '</div>';
                    html += '</div>';
                });
                $('#signer_checkbox_container').html(html);
            } else {
                $('#no_signer_msg').show();
            }
            $('#signerNotificationModal').modal('show');
        },
        error: function(){
            $('#no_signer_msg').show();
            $('#signerNotificationModal').modal('show');
        }
    });
}

function saveSignerNotification(){
    var templateId = $('#signer_notification_template_id').val();
    var signerTypes = [];
    $('.signer-type-checkbox:checked').each(function(){
        signerTypes.push($(this).val());
    });
    $.ajax({
        url: _SIGNER_NOTIFICATION_SAVE,
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': templateId,
            'signer_types': signerTypes
        },
        type: "POST",
        dataType: "json",
        success: function(response){
            if(response.status){
                toastr.success(response.error_msg);
                $('#signerNotificationModal').modal('hide');
                loadAjaxList(1);
            } else {
                toastr.error(response.error_msg);
            }
        },
        error: function(xhr){
            toastr.error('Something went wrong');
        }
    });
}

function ucFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}