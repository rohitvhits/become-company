function loadUserDateWiseAgencyView(page=1){
    $('#date_wise_agency_ajax_id').html("");
    $('.date-wise-agency-view-loader').attr('style','');
    $.ajax({
        type:"get",
        url:_LOAD_DATE_WISE_AGENCY_ACCESS_LIST,
        data:{
            'agency_id':AGENCY_ID,
            'page':page
        },
        success:function(res){
            $('#date_wise_agency_ajax_id').html(res);
            $('.date-wise-agency-view-loader').attr('style','display:none');

        }
    })
}

function refreshDateWiseAgencyAccess(){
    $('#formAgencyViewSubmit')[0].reset();
 
    $('.error_date_html').html("");
    $('#agency_view_permission').val('').trigger("change");
}

$('#submit_view_agency_access').click(function(e){
    var permission = $('#agency_view_permission').val();
    var start_date = $('#permission_start_date').val();
    var end_date = $('#permission_end_date').val();
    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;
    var cnt =0;
    $('#agency_view_permission_error').html("");
    $('#start_date_error').html("")
    $('#end_date_error').html("")
    if(permission.length ==0){
        $('#agency_view_permission_error').html("Please select Permission");
        cnt =1;
    }

    if(start_date.trim() ==''){
        $('#start_date_error').html("Please select Start Date");
        cnt =1;
    }

    if(start_date.trim() !=""){
        if (!regex.test(start_date.trim())) {
            $('#start_date_error').html("Please select Valid Start Date");
            cnt =1;
        }
    }

    if(end_date.trim() ==''){
        $('#end_date_error').html("Please select End Date");
        cnt =1;
    }

    if(end_date.trim() !=""){
        if (!regex.test(end_date.trim())) {
            $('#end_date_error').html("Please select Valid End Date");
            cnt =1;
        }
    }

    if(cnt ==1){
        return false;
    }else{
        var formData = new FormData($('#formAgencyViewSubmit')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('agency_id',_AGENCY_ID);

        $.ajax({
            type:"POST",
            url:_SAVE_AGENCY_WISE_DATE_PERMISSION,
            data:formData,
            processData: false,
            contentType: false,
            success:function(res){
                toastr.success(res.error_msg);
                loadUserDateWiseAgencyView(1)
                $('#add-date-wise-agency-view-use').modal('hide');
            },
            error:function(jqr){

            }

        });
    }
})

function editDetailsDateWiseAgencyAccess(id){
    $.ajax({
        type:"get",
        url:_EDIT_DATE_WISE_AGENCY_ACCESS,
        data:{
            'agency_id':AGENCY_ID,
            'id':id
        },
        success:function(res){
          $('#edit-date-wise-agency-view-use').modal('show');
          $('#edit_permission_start_date').val(res.data.start_date);
          $('#edit_permission_end_date').val(res.data.end_date);
          $('#edit_agency_view_permission').val(res.data.permission).trigger('change');
          $('#date_agency_view_access_id').val(res.data.id);
        }
    })
}

$('#update_submit_view_agency_access').click(function(e){
    var permission = $('#edit_agency_view_permission').val();
    var start_date = $('#edit_permission_start_date').val();
    var end_date = $('#edit_permission_end_date').val();
    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;
    var cnt =0;
    $('#edit_agency_view_permission_error').html("");
    $('#edit_start_date_error').html("")
    $('#edit_end_date_error').html("")
    if(permission.length ==0){
        $('#edit_agency_view_permission_error').html("Please select Permission");
        cnt =1;
    }

    if(start_date.trim() ==''){
        $('#edit_start_date_error').html("Please select Start Date");
        cnt =1;
    }

    if(start_date.trim() !=""){
        if (!regex.test(start_date.trim())) {
            $('#edit_start_date_error').html("Please select Valid Start Date");
            cnt =1;
        }
    }

    if(end_date.trim() ==''){
        $('#edit_end_date_error').html("Please select End Date");
        cnt =1;
    }

    if(end_date.trim() !=""){
        if (!regex.test(end_date.trim())) {
            $('#edit_end_date_error').html("Please select Valid End Date");
            cnt =1;
        }
    }

    if(cnt ==1){
        return false;
    }else{
       
        var formData = new FormData($('#editFormAgencyViewSubmit')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('agency_id',_AGENCY_ID);
       
        formData.append('start_date',$('#edit_permission_start_date').val());
        formData.append('end_date',$('#edit_permission_end_date').val());
        $.ajax({
            type:"POST",
            url:_UPDATE_AGENCY_WISE_DATE_PERMISSION,
            data:formData,
            processData: false,
            contentType: false,
            success:function(res){
                toastr.success(res.error_msg);
                loadUserDateWiseAgencyView(1)
                $('#edit-date-wise-agency-view-use').modal('hide');
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }

        });
    }
})

function deleteDetailsDateWiseAgencyAccess(id){
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: "You want to delete date-wise agency permission access.",

        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url:_DELETE_AGENCY_WISE_DATE_PERMISSION,
                        type: "post",
                        data: {
                            'id': id,
                            "_token":_CSRF_TOKEN,
                            'agency_id':_AGENCY_ID
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            loadUserDateWiseAgencyView(1);
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg)
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

$('body').on('click', '.user-date-wise-access-permission .pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    var linkExplode = $(this).attr('href').split('date-wise-agency-access/')[1].split('?');
    
    if (linkExplode && linkExplode.length > 0) {
        if (linkExplode[0] === 'load-date-wise-user-access-list') {
            loadUserDateWiseUserView(page);
        } else if (linkExplode[0] === 'load-date-wise-agency-access-list') {
            loadUserDateWiseAgencyView(page);
        }
    }
});

/********************************User Side */
function loadUserDateWiseUserView(page=1){
    $('#date_wise_user_ajax_id').html("");
    $('.date-wise-user-view-loader').attr('style','');
    $.ajax({
        type:"get",
        url:_LOAD_DATE_WISE_USER_ACCESS_LIST,
        data:{
            'user_id':_USER_ID,
            'page':page
        },
        success:function(res){
            $('#date_wise_user_ajax_id').html(res);
            $('.date-wise-user-view-loader').attr('style','display:none');

            // Check if permanent restriction exists and update buttons
            updateUserAccessButtons(res);
        }
    })
}

function updateUserAccessButtons(htmlContent) {
    // Check if the response contains permanent restriction badge
    var hasPermanent = $(htmlContent).find('.badge-danger:contains("PERMANENT")').length > 0;

    if (hasPermanent) {
        // Show remove button, hide set button
        $('#set-permanent-restriction-btn').hide();
        $('#remove-permanent-restriction-btn').show();

        // Disable add button
        $('#add-user-access-btn')
            .removeClass('btn-success')
            .addClass('btn-secondary')
            .attr('data-toggle', '')
            .attr('data-target', '')
            .attr('onclick', '')
            .css({'pointer-events': 'none', 'opacity': '0.6'})
            .attr('title', 'Cannot add new access - Permanent restriction already set');
    } else {
        // Show set button, hide remove button
        $('#set-permanent-restriction-btn').show();
        $('#remove-permanent-restriction-btn').hide();

        // Enable add button
        $('#add-user-access-btn')
            .removeClass('btn-secondary')
            .addClass('btn-success')
            .attr('data-toggle', 'modal')
            .attr('data-target', '#add-date-wise-user-view-use')
            .attr('onclick', 'refreshDateWiseUserAccess()')
            .css({'pointer-events': '', 'opacity': ''})
            .removeAttr('title');
    }
}


function refreshDateWiseUserAccess(){
    $('#formUserViewSubmit')[0].reset();
    $('.error_date_html').html("");
    $('#user_view_permission').val('').trigger("change");
    $('#user_permanent_access').prop('checked', false);
    $('#user_start_date_group').show();
    $('#user_end_date_group').show();
    setTimeout(function(){
        $('#user_view_permission').select2({
            placeholder: 'Select Permission',
            allowClear: true
        });
    },200)

    $('.datepicker').datepicker();
}

// Handle permanent restriction toggle for USER ADD modal
$('#user_permanent_access').on('change', function() {
    if ($(this).is(':checked')) {
        $('#user_start_date_group').hide();
        $('#user_end_date_group').hide();
        $('#permission_start_date').val('');
        $('#permission_end_date').val('');
        $('#start_date_error').html('');
        $('#end_date_error').html('');
    } else {
        $('#user_start_date_group').show();
        $('#user_end_date_group').show();
    }
});

// Handle permanent restriction toggle for USER EDIT modal
$('#edit_user_permanent_access').on('change', function() {
    if ($(this).is(':checked')) {
        $('#edit_user_start_date_group').hide();
        $('#edit_user_end_date_group').hide();
        $('#edit_permission_start_date').val('');
        $('#edit_permission_end_date').val('');
        $('#edit_start_date_error').html('');
        $('#edit_end_date_error').html('');
    } else {
        $('#edit_user_start_date_group').show();
        $('#edit_user_end_date_group').show();
    }
});

$('#submit_view_user_access').click(function(e){
    $('#create-date-wise-loader').removeClass('d-none');
    $('#btn-save-text-date-wise').text('Saving ...')
    var permission = $('#user_view_permission').val();
    var start_date = $('#permission_start_date').val();
    var end_date = $('#permission_end_date').val();
    var is_permanent = $('#user_permanent_access').is(':checked');
    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;
    var cnt =0;
    $('#agency_view_permission_error').html("");
    $('#start_date_error').html("")
    $('#end_date_error').html("")
    if(permission.length ==0){
        $('#agency_view_permission_error').html("Please select Permission");
        cnt =1;
    }

    // Only validate dates if permanent restriction is NOT checked
    if(!is_permanent){
        if(start_date.trim() ==''){
            $('#start_date_error').html("Please select Start Date");
            cnt =1;
        }

        if(start_date.trim() !=""){
            if (!regex.test(start_date.trim())) {
                $('#start_date_error').html("Please select Valid Start Date");
                cnt =1;
            }
        }

        if(end_date.trim() ==''){
            $('#end_date_error').html("Please select End Date");
            cnt =1;
        }

        if(end_date.trim() !=""){
            if (!regex.test(end_date.trim())) {
                $('#end_date_error').html("Please select Valid End Date");
                cnt =1;
            }
        }

        if(start_date.trim() !="" && end_date.trim() !=""){
            if (new Date(end_date.trim()) < new Date(start_date.trim())) {
                $('#end_date_error').html("End Date cannot be earlier than Start Date");
                cnt =1;
            }
        }
    }

    if(cnt ==1){
        $('#create-date-wise-loader').addClass('d-none');
        $('#btn-save-text-date-wise').text('Save');
        return false;
    }else{
        var formData = new FormData($('#formUserViewSubmit')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('user_id',_USER_ID);
        formData.append('permanent_access', is_permanent ? 1 : 0);

        $.ajax({
            type:"POST",
            url:_SAVE_USER_WISE_DATE_PERMISSION,
            data:formData,
            processData: false,
            contentType: false,
            success:function(res){
                toastr.success(res.error_msg);
                loadUserDateWiseUserView(1)
                $('#create-date-wise-loader').addClass('d-none');
                $('#btn-save-text-date-wise').text('Save');
                $('#add-date-wise-user-view-use').modal('hide');
            },
            error:function(jqr){
                $('#create-date-wise-loader').addClass('d-none');
                $('#btn-save-text-date-wise').text('Save');
                toastr.error(jqr.responseJSON.error_msg);
            }

        });
    }
})


function editDetailsDateWiseUserAccess(id){
    $.ajax({
        type:"get",
        url:_EDIT_DATE_WISE_USER_ACCESS,
        data:{
            'id':id
        },
        success:function(res){
            $('#edit-date-wise-user-view-use').modal('show');
            $('#edit_permission_start_date').val(res.data.start_date);
            $('#edit_permission_end_date').val(res.data.end_date);
            $('#edit_user_view_permission').val(res.data.permission).trigger('change');
            $('#date_view_access_id').val(res.data.id);
            $('#edit_view_user_id').val(res.data.user_id);
            $('.error_date_html').html("");

            // Handle permanent restriction checkbox
            if(res.data.permanent_access == 1){
                $('#edit_user_permanent_access').prop('checked', true);
                $('#edit_user_start_date_group').hide();
                $('#edit_user_end_date_group').hide();
            } else {
                $('#edit_user_permanent_access').prop('checked', false);
                $('#edit_user_start_date_group').show();
                $('#edit_user_end_date_group').show();
            }

            setTimeout(function(){
                $('#edit_user_view_permission').select2({
                    placeholder: 'Select Permission',
                    allowClear: true
                });
            },200)
            $('.datepicker').datepicker();
        }
    })
}

$('#update_submit_view_user_access').click(function(e){
    $('#edit-date-wise-loader').removeClass('d-none');
    $('#btn-update-text-date-wise').text('Updating...');
    var permission = $('#edit_user_view_permission').val();
    var start_date = $('#edit_permission_start_date').val();
    var end_date = $('#edit_permission_end_date').val();
    var is_permanent = $('#edit_user_permanent_access').is(':checked');
    var regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/;
    var cnt =0;
    $('#edit_agency_view_permission_error').html("");
    $('#edit_start_date_error').html("")
    $('#edit_end_date_error').html("")
    if(permission.length ==0){
        $('#edit_agency_view_permission_error').html("Please select Permission");
        cnt =1;
    }

    // Only validate dates if permanent restriction is NOT checked
    if(!is_permanent){
        if(start_date.trim() ==''){
            $('#edit_start_date_error').html("Please select Start Date");
            cnt =1;
        }

        if(start_date.trim() !=""){
            if (!regex.test(start_date.trim())) {
                $('#edit_start_date_error').html("Please select Valid Start Date");
                cnt =1;
            }
        }

        if(end_date.trim() ==''){
            $('#edit_end_date_error').html("Please select End Date");
            cnt =1;
        }

        if(end_date.trim() !=""){
            if (!regex.test(end_date.trim())) {
                $('#edit_end_date_error').html("Please select Valid End Date");
                cnt =1;
            }
        }

        if(start_date.trim() !="" && end_date.trim() !=""){
            if (new Date(end_date.trim()) < new Date(start_date.trim())) {
                $('#edit_end_date_error').html("End Date cannot be earlier than Start Date");
                cnt =1;
            }
        }
    }

    if(cnt ==1){
        $('#edit-date-wise-loader').addClass('d-none');
        $('#btn-update-text-date-wise').text('Update');
        return false;
    }else{

        var formData = new FormData($('#editFormUserViewSubmit')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('user_id',_USER_ID);
        formData.append('permanent_access', is_permanent ? 1 : 0);

        formData.append('start_date',$('#edit_permission_start_date').val());
        formData.append('end_date',$('#edit_permission_end_date').val());
        $.ajax({
            type:"POST",
            url:_UPDATE_USER_WISE_DATE_PERMISSION,
            data:formData,
            processData: false,
            contentType: false,
            success:function(res){
                toastr.success(res.error_msg);
                loadUserDateWiseUserView(1)
                $('#edit-date-wise-loader').addClass('d-none');
                $('#btn-update-text-date-wise').text('Update');
                $('#edit-date-wise-user-view-use').modal('hide');
            },
            error:function(jqr){
                $('#edit-date-wise-loader').addClass('d-none');
                $('#btn-update-text-date-wise').text('Update');
                toastr.error(jqr.responseJSON.error_msg);
            }

        });
    }
})

function deleteDetailsDateWiseUserAccess(id){
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: "You want to delete date-wise user permission access.",
        type:"blue",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url:_DELETE_USER_WISE_DATE_PERMISSION,
                        type: "post",
                        data: {
                            'id': id,
                            "_token":_CSRF_TOKEN,
                            'user_id':_USER_ID
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            loadUserDateWiseUserView(1);
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg)
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

function setPermanentRestriction(){
    // First, check if user has existing entries
    $.ajax({
        url: _CHECK_EXISTING_ENTRIES_USER,
        type: "post",
        data: {
            "_token": _CSRF_TOKEN,
            'user_id': _USER_ID
        },
        success: function(checkRes) {
            if (checkRes.has_permanent) {
                // User already has permanent restriction
                toastr.error(checkRes.message);
                return;
            }

            // Build warning message if there are existing entries
            let warningMessage = "";
            if (checkRes.has_entries) {
                warningMessage = "<div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 15px; margin-bottom: 15px; border-radius: 4px;'>" +
                                "<div style='display: flex; align-items: center;'>" +
                                "<i class='fa fa-exclamation-triangle' style='color: #856404; font-size: 20px; margin-right: 10px;'></i>" +
                                "<div style='flex: 1;'>" +
                                "<strong style='color: #856404; font-size: 14px;'>Warning</strong>" +
                                "<p style='margin: 5px 0 0 0; color: #856404; font-size: 13px;'>" +
                                "This action will <strong>delete " + checkRes.count + " existing restriction(s)</strong> and replace them with a permanent restriction." +
                                "</p>" +
                                "</div></div></div>";
            }

            // Build permissions list with badges
            var permissionsList = "";
            if (ALL_PERMISSION && Object.keys(ALL_PERMISSION).length > 0) {
                permissionsList = "<div style='background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; border-radius: 4px; margin-top: 10px;'>" +
                                 "<strong style='color: #495057; font-size: 13px; display: block; margin-bottom: 10px;'><i class='fa fa-ban' style='margin-right: 5px;'></i>Permissions to be restricted:</strong>" +
                                 "<div style='line-height: 2;'>";

                var permissions = Object.values(ALL_PERMISSION);
                permissions.forEach(function(item, index){
                    permissionsList += "<span style='display: inline-block; background-color: transparent; color: #495057; padding: 4px 10px; border: 1px solid #ced4da; border-radius: 3px; font-size: 12px; margin-right: 5px; margin-bottom: 5px; font-weight: 500;'>" +
                                      "<i class='fa fa-lock' style='font-size: 10px; margin-right: 4px; color: #6c757d;'></i>" + item +
                                      "</span>";
                    if (index < permissions.length - 1) {
                        permissionsList += "";
                    }
                });

                permissionsList += "</div></div>";
            }

            // Main content message
            var mainContent = "<div style='margin-bottom: 15px;'>" +
                             "<p style='font-size: 15px; color: #495057; line-height: 1.6; margin: 0;'>" +
                             "You are about to set a <strong>permanent restriction</strong> that will block all the following permissions indefinitely." +
                             "</p>" +
                             "</div>";
            // Combine all content
            var fullContent = warningMessage + mainContent + permissionsList;

            $.confirm({
                title: '<i class="fa fa-lock"></i> Set Permanent Restriction',
                columnClass: "col-md-7",
                content: fullContent,
                type: "red",
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '<i class="fa fa-check"></i> Confirm',
                        btnClass: 'btn-danger',
                        keys: ['enter'],
                        action: function() {
                            // Show loading state
                            var jc = this;
                            jc.buttons.confirm.setText('<i class="fa fa-spinner fa-spin"></i> Processing...');
                            jc.buttons.confirm.disable();
                            jc.buttons.cancel.disable();

                            $.ajax({
                                url: _SET_PERMANENT_RESTRICTION_USER,
                                type: "post",
                                data: {
                                    "_token": _CSRF_TOKEN,
                                    'user_id': _USER_ID
                                },
                                success: function(res) {
                                    toastr.success(res.error_msg);
                                    loadUserDateWiseUserView(1);
                                    jc.close();
                                },
                                error: function(jqr) {
                                    jc.buttons.confirm.setText('<i class="fa fa-check"></i> Yes, Set Permanent Restriction');
                                    jc.buttons.confirm.enable();
                                    jc.buttons.cancel.enable();
                                    toastr.error(jqr.responseJSON.error_msg || "An error occurred");
                                }
                            });
                            return false; // Prevent dialog from closing immediately
                        }
                    },
                    cancel: {
                        text: '<i class="fa fa-times"></i> Cancel',
                        btnClass: 'btn-secondary',
                        keys: ['esc'],
                        action: function() {
                            // Just close the dialog
                        }
                    }
                }
            });
        },
        error: function(jqr) {
            toastr.error(jqr.responseJSON?.error_msg || "Error checking existing entries");
        }
    });
}

function removePermanentRestriction(){
    $.confirm({
        title: 'Remove Permanent Restriction',
        columnClass: "col-md-6",
        content: "<div style='font-size:15px;line-height:1.6;'>" +
                 "Are you sure?<br>" +
                 "<small style='color:#666;'>You can set date-based restrictions again.</small>" +
                 "</div>",
        type: "orange",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-warning btn-sm',
                action: function() {
                    $.ajax({
                        url: _REMOVE_PERMANENT_RESTRICTION_USER,
                        type: "post",
                        data: {
                            "_token": _CSRF_TOKEN,
                            'user_id': _USER_ID
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            loadUserDateWiseUserView(1);
                        },
                        error: function(jqr) {
                            toastr.error(jqr.responseJSON.error_msg)
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-sm',
                action: function() {
                    //close
                }
            },
        },
    });
}