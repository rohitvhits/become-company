function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'name':$('#name').val(),
            'procedure_name':$('#procedure_name').val(),
            'vns_procedure_id':$('#vns_procedure_id').val(),
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
    $('#procedure_name').val("");
    $('#vns_procedure_id').val('');
    loadAjaxList(1);
}

function resetAddProcedureResult(){
    $('#form_create_procedure_result_id')[0].reset();
    $('#vns_procedure_id_error').html("");
    $('#names_error').html("");
    $('#add_vns_procedure_id').val('');

    
    // Reset result names container to single input
    $('#result_names_container').html(`
        <div class="result-name-row mb-2">
            <div class="row">
                <div class="col-md-10">
                    <input type="text" class="form-control result-name-input" placeholder="Enter Result Name" name="names[]" value="">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeResultNameRow(this)" style="display:none;">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            </div>
        </div>
    `);
}

// Add more result name fields
function addMoreResultName(){
    var newRow = `
        <div class="result-name-row mb-2">
            <div class="row">
                <div class="col-md-10">
                    <input type="text" class="form-control result-name-input" placeholder="Enter Result Name" name="names[]" value="">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeResultNameRow(this)"  title="Remove">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    $('#result_names_container').append(newRow);

    // Show remove button for all rows when there's more than one
    updateRemoveButtons();
}

// Remove result name row
function removeResultNameRow(button){
    $(button).closest('.result-name-row').remove();
    // Update remove button visibility
    updateRemoveButtons();
}

// Update remove button visibility - show only when multiple rows exist
function updateRemoveButtons(){
    var rowCount = $('#result_names_container .result-name-row').length;
    if(rowCount > 1){
        $('#result_names_container .result-name-row button').show();
    } else {
        $('#result_names_container .result-name-row button').hide();
    }
}

function createProcedureResult(){
    var vns_procedure_id = $('#add_vns_procedure_id').val();
    $('#vns_procedure_id_error').html('');
    $('#names_error').html('');
    $('#create-procedure-result').removeClass('d-none');
    $('#btn-save-procedure-result').text('Saving ...')

    var cnt = 0;

    if(vns_procedure_id.trim() == ''){
        $('#vns_procedure_id_error').html('Please select VNS Procedure');
        cnt = 1;
    }

    // Validate at least one result name is entered
    var hasValidName = false;
    $('.result-name-input').each(function(){
        if($(this).val().trim() != ''){
            hasValidName = true;
            return false; // break loop
        }
    });

    if(!hasValidName){
        $('#names_error').html('Please enter at least one Result Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#create-procedure-result').addClass('d-none');
        $('#btn-save-procedure-result').text('Save')
        return false;
    } else {
        var formData = new FormData($('#form_create_procedure_result_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _SAVE_PROCEDURE_RESULT,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#create-procedure-result').addClass('d-none');
                $('#btn-save-procedure-result').text('Save')
            },
            error: function (jqXHR) {
                $('#create-procedure-result').addClass('d-none');
                ('#btn-save-procedure-result').text('Save')
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function getDetails(id){
    $.ajax({
        type:"GET",
        url:_EDIT_PROCEDURE_RESULT+'/'+id,

        success:function(res){
           $('#record_id').val(res.data.id);

           // Set the selected VNS Procedure in dropdown
           if(res.data.vns_procedure_id) {
               $('#edit_vns_procedure_id').val(res.data.vns_procedure_id);
           } else {
               $('#edit_vns_procedure_id').val('');
           }

           // Reset and populate with single existing name
           $('#edit_result_names_container').html(`
               <div class="result-name-row mb-2">
                   <div class="row">
                       <div class="col-md-11">
                           <input type="text" class="form-control result-name-input" placeholder="Enter Result Name" name="names[]" value="${res.data.name || ''}">
                       </div>
                       <div class="col-md-1">
                           <button type="button" class="btn btn-danger btn-sm" onclick="removeEditResultNameRow(this)" style="display:none;" result_names_container>
                               <i class="mdi mdi-minus"></i>
                           </button>
                       </div>
                   </div>
               </div>
           `);

           $('#exampleModal-edit-modal-procedure-result').modal('show')
        },
        error: function (jqXHR) {
            toastr.error(jqXHR.responseJSON.error_msg);
        },
    })
}

// Add more result name fields in edit modal
function addMoreEditResultName(){
    var newRow = `
        <div class="result-name-row mb-2">
            <div class="row">
                <div class="col-md-11">
                    <input type="text" class="form-control result-name-input" placeholder="Enter Result Name" name="names[]" value="">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeEditResultNameRow(this)">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    $('#edit_result_names_container').append(newRow);

    // Show remove button for all rows when there's more than one
    updateEditRemoveButtons();
}

// Remove result name row in edit modal
function removeEditResultNameRow(button){
    $(button).closest('.result-name-row').remove();
    // Update remove button visibility
    updateEditRemoveButtons();
}

// Update remove button visibility in edit modal - show only when multiple rows exist
function updateEditRemoveButtons(){
    var rowCount = $('#edit_result_names_container .result-name-row').length;
    if(rowCount > 1){
        $('#edit_result_names_container .result-name-row button').show();
    } else {
        $('#edit_result_names_container .result-name-row button').hide();
    }
}

function updateProcedureResult(){
    var vns_procedure_id = $('#edit_vns_procedure_id').val();
    $('#edit_vns_procedure_id_error').html('');
    $('#edit_names_error').html('');
    $('#update-procedure-result').removeClass('d-none');
    $('#btn-update-procedure-result').text('Updating ...')
    var cnt = 0;

    if(vns_procedure_id.trim() == ''){
        $('#edit_vns_procedure_id_error').html('Please select VNS Procedure');
        cnt = 1;
    }

    // Validate at least one result name is entered
    var hasValidName = false;
    $('#edit_result_names_container .result-name-input').each(function(){
        if($(this).val().trim() != ''){
            hasValidName = true;
            return false; // break loop
        }
    });

    if(!hasValidName){
        $('#edit_names_error').html('Please enter at least one Result Name');
        cnt = 1;
    }

    if(cnt == 1){
        $('#update-procedure-result').addClass('d-none');
        $('#btn-update-procedure-result').text('Update');
        return false;
    } else {
        var formData = new FormData($('#form_edit_procedure_result_id')[0]);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            type: "POST",
            url: _UPDATE_PROCEDURE_RESULT,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                toastr.success(res.error_msg)
                loadAjaxList(1);
                $('.close').click();
                $('#update-procedure-result').addClass('d-none');
                $('#btn-update-procedure-result').text('Update');
            },
            error: function (jqXHR) {
                $('#update-procedure-result').addClass('d-none');
                $('#btn-update-procedure-result').text('Update');
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        })
    }
}

function procedureResultDelete(id){
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
                        url:_DELETE_VNS_PROCEDURE_RESULT+'/'+id,
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
        'procedure_name': $('#procedure_name').val(),
        'vns_procedure_id': $('#vns_procedure_id').val()
    });
    window.location.href = url + '?' + params.toString();
}
