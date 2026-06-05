$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadGroupNotificationList(1);
});

function loadGroupNotificationList(page) {
    $.ajax({
        url: _GROUP_NOTIFICATION_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

$('#groupNotificatrionForm').submit(function(e){
    $('#groupNotificationSave').prop('disabled',true);
    var title = $("#name").val();
    var patient_flag = $('input[name="patient_flag"]:checked').val();
    var caregiver_flag = $('input[name="caregiver_flag"]:checked').val();
    $("#title_error").html("");
    $("#type_error").html("");
    $("#notification_error").html("");
    $("#user_error").html("");
    $("#agency_error").html("");
    $("#patients_notification_error").html("");
    $("#caregiver_notification_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    // if ($('#agency_fk').val().length == 0) {
    //     $("#agency_error").html("Please select Agency");
    //     cnt = 1;
    // }
    if ($('#user_id').val().length == 0) {
        $("#user_error").html("Please select User");
        cnt = 1;
    }

    if(patient_flag ==undefined && caregiver_flag ==undefined){
        $('#type_error').html("Please select Notification");
        cnt =1;
    }

    if(caregiver_flag){
        var isCareChecked = $('.caregiver_checkbox:checked').length > 0;
        if(!isCareChecked ){
            $("#caregiver_notification_error").html("Please select Caregiver Notification");
            cnt = 1;
        }
    }
    if(patient_flag){
        var isPatientChecked = $('.patient_checkbox:checked').length > 0;
        if(!isPatientChecked ){
            $("#patients_notification_error").html("Please select Patient Notification");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        return true;
    }else{
        $('#groupNotificationSave').prop('disabled',false);
        return false;
    }
})

$('#edit_form_group_notification').submit(function(e){
    $('#groupNotificationSave').prop('disabled',true);
    var title = $("#name").val();
    var patient_flag = $('input[name="patient_flag"]:checked').val();
    var caregiver_flag = $('input[name="caregiver_flag"]:checked').val();
    $("#title_error").html("");
    $("#type_error").html("");
    $("#notification_error").html("");
    $("#user_error").html("");
    $("#agency_error").html("");
    $("#patients_notification_error").html("");
    $("#caregiver_notification_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    // if ($('#agency_fk').val().length == 0) {
    //     $("#agency_error").html("Please select Agency");
    //     cnt = 1;
    // }
    if ($('#user_id').val().length == 0) {
        $("#user_error").html("Please select User");
        cnt = 1;
    }

    if(patient_flag ==undefined && caregiver_flag ==undefined){
        $('#type_error').html("Please select Notification");
        cnt =1;
    }

    if(caregiver_flag){
        var isCareChecked = $('.caregiver_checkbox:checked').length > 0;
        if(!isCareChecked ){
            $("#caregiver_notification_error").html("Please select Caregiver Notification");
            cnt = 1;
        }
    }
    if(patient_flag){
        var isPatientChecked = $('.patient_checkbox:checked').length > 0;
        if(!isPatientChecked ){
            $("#patients_notification_error").html("Please select Patient Notification");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        return true;
    }else{
        $('#groupNotificationSave').prop('disabled',false);
        return false;
    }
})

function deleteGroupNotification(id) {
    if (id != '') {
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to delete this record.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            global: false,
                            url: _GROUP_NOTIFICATION + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadGroupNotificationList(1);
                            },
                            error: function (xhr, status, error) {
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        });
                    }
                },
                cancel: function () {

                }
            }
        })

    }
    return false;
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadGroupNotificationList(page);
});


$('#caregiver_flag').on('click', function() {
    getServiceData();
});

$('#patient_flag').on('click',function(){
    getServiceData();
});

$('#both_flag').on('click',function(){
    getServiceData();
});
function getServiceData(){
 
    var patient_flag = $('input[name="patient_flag"]:checked').val(); 
    var caregiver_flag = $('input[name="caregiver_flag"]:checked').val();
    $.ajax({
        url: SERVICE_DATA,
        type: "get",
        data: {
            'patient_flag' : patient_flag,
            'caregiver_flag' : caregiver_flag
        },
        success: function (response) {
            var id = $('#id').val(); 
            var selectedData = [];
            if(id != ''){
                selectedData = $('#service_id').val();
            }
            $('#service_id').html('');
            var responseData = response.data;
            $('#service_id').append(new Option('Select Service'));
            responseData.forEach(function(responseData) {
                $('#service_id').append(new Option(responseData.name, responseData.id));
            });
            $('#service_id').val(selectedData);
        }
    });
}

function showCheckBox(type){
    var notification = $('.notification_checkbox_'+type).is(":checked");
    
    if(notification){
     
        if(type =='both'){
            $('.notification_checkbox_patient').prop("checked",true);
            $('.notification_checkbox_caregiver').prop("checked",true);
            $('#caregiver_notification_id').removeClass('hide')
            $('#patient_notification_id').removeClass('hide')
        }else{
            if(type =='caregiver'){
                $('#caregiver_notification_id').removeClass('hide')
            }
            if(type =='patient'){
                $('#patient_notification_id').removeClass('hide')
            }

            
        }
    }else{
        if(type =='both'){
            $('.notification_checkbox_patient').prop("checked",false);
            $('.notification_checkbox_caregiver').prop("checked",false);
            $('#caregiver_notification_id').addClass('hide')
            $('#patient_notification_id').addClass('hide')
            $('.caregiver_checkbox').prop("checked",false);
            $('.patient_checkbox').prop("checked",false);
        }else{
            if(type =='caregiver'){
                $('#caregiver_notification_id').addClass('hide')
                $('.caregiver_checkbox').prop("checked",false);
            }
            if(type =='patient'){
                $('#patient_notification_id').addClass('hide')
                $('.patient_checkbox').prop("checked",false);
            }
            $('.notification_checkbox_both').prop("checked",false);
        } 
    }
}

function showCaregiverAll(){
    var select_caregiver_checkbox = $('.select_caregiver_checkbox').is(":checked");
    if(select_caregiver_checkbox){
        $('.caregiver_checkbox').prop("checked",true);
    }else{
        $('.caregiver_checkbox').prop("checked",false);
    }
}

$('.caregiver_checkbox').click(function(e){
    var caregiver_checkbox_length = $('.caregiver_checkbox').length;
    var cnt =0;
    $('.caregiver_checkbox').each(function(i,v){
        if($(this).is(":checked")){
            cnt++;
        }
    })

    if(caregiver_checkbox_length === cnt){
        $('#select_caregiver_checkbox').prop("checked",true);
    }else{
        $('#select_caregiver_checkbox').prop("checked",false); 
    }

})

function showPatientAll(){
    var select_caregiver_checkbox = $('.select_patient_checkbox').is(":checked");
    if(select_caregiver_checkbox){
        $('.patient_checkbox').prop("checked",true);
    }else{
        $('.patient_checkbox').prop("checked",false);
    }
}

$('.patient_checkbox').click(function(e){
    var patient_checkbox_length = $('.patient_checkbox').length;
    var cnt =0;
    $('.patient_checkbox').each(function(i,v){
        if($(this).is(":checked")){
            cnt++;
        }
    })

    if(patient_checkbox_length === cnt){
        $('#select_patient_checkbox').prop("checked",true);
    }else{
        $('#select_patient_checkbox').prop("checked",false); 
    }

})