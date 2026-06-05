$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadEventList(1);
});


function loadEventList(page) {
    $.ajax({
        url: _EVENT_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

function getEvent() {
    $('#eventMasterModal').modal('show');
    $('#eventMasterModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#content_error").html("");
    $("#start_date_error").html("");
    $("#end_date_error").html("");
    $("#status_error").html("");
    $("#image_error").html("");
    $("#eventMaster")[0].reset();
    $("#imageDiv").html('');
    window.editor.setData("")
}

function getEditEvent(id) {
    $('#id').val(id);
    $('#eventEditModal').modal('show');
    $('#eventEditModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#content_error").html("");
    $("#start_date_error").html("");
    $("#end_date_error").html("");
    $("#status_error").html("");
    $("#image_error").html("");
    $("#eventMaster")[0].reset();
    getModalData(id);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadEventList(page);
});

function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: _EVENT_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            if (json) {
                $('#event_edit_title').val(json.title);
                $('#event_edit_content').html(json.content);
                window.editorEdit.setData(json.content)
                $('#event_edit_start_date').val(moment(json.start_date).format('MM/DD/YYYY'));
                $('#event_edit_end_date').val(moment(json.end_date).format('MM/DD/YYYY'));
                $("input[name=status][value='"+json.status+"']").prop("checked",true);
                if(ISAWS == 1){
                    var imageUrl = _EVENT_AWS+"/"+id+"?type=event"
                }else{
                    var imageUrl = BASEURL + json.image;
                }
                if (json.image != '') {
                    $('#imageDiv').html('<img id="agency-logo" src="' + imageUrl + '" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo"></div>');
                }
            }
        }
    })
}
function save(){
  
    var title = $("#event_title").val();
    var content = window.editor.getData();
    var start_date = $("#start_date").val();
    var end_date =$("#end_date").val();

    $("#title_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    if (content.trim() == "") {
        $("#content_error").html("Please enter Content");
        cnt = 1;
    }
    if (start_date.trim() == "") {
        $("#start_date_error").html("Please enter Start Date");
        cnt = 1;
    }
    if (end_date.trim() == "") {
        $("#end_date_error").html("Please enter End Date");
        cnt = 1;
    }

    var files = $('input[name="image"]')[0].files;
    if (files.length == 0) {
      
    } else {
        var fileExtensionType = ["jpg", "jpeg", "png"];
        var fileName = files[0].name;
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#image_error").html("Please select only jpg, jpeg, or png file");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#loaderAddEvent").attr('style',"display:block");
        $("#eventSave").prop("disabled", true);
        var formData = new FormData($("#eventMaster")[0]);
        formData.append('_token', _CSRF_TOKEN);
        formData.append('content', content);
        $.ajax({
            type: "POST",
            url: _ANNOUNCEMENTS_ADD,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#eventMaster")[0].reset();
                $("#eventSave").prop("disabled", false);
                $("#eventMasterModal").modal("hide");
                $("#id").val("").change();
                $("#loaderAddEvent").attr('style',"display:none");
                loadEventList(1);
            },
            error: function (jqXHR) {
                $("#eventSave").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function update(){
    var title = $("#event_edit_title").val();
    var content = window.editorEdit.getData();
    var start_date = $("#event_edit_start_date").val();
    var end_date =$("#event_edit_end_date").val();
   

    $("#title_error").html("");
    $("#content_error").html("");
    $("#start_date_error").html("");
    $("#end_date_error").html("");
    
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    if (content.trim() == "") {
        $("#content_error").html("Please enter Content");
        cnt = 1;
    }
    if (start_date.trim() == "") {
        $("#start_date_error").html("Please enter Start Date");
        cnt = 1;
    }
    if (end_date.trim() == "") {
        $("#end_date_error").html("Please enter End Date");
        cnt = 1;
    }
   
    var id = $("#id").val();

    var files = $('input[name="image"]')[0].files;
    if (files.length > 0) {
        var fileExtensionType = ["jpg", "jpeg", "png"];
        var fileName = files[0].name;
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#image_error").html("Please select only jpg, jpeg, or png file");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#loaderEditEvent").attr('style',"display:block");
        $("#eventUpdate").prop("disabled", true);
        var formData = new FormData($("#eventEdit")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'POST');
        formData.append('content', content);
        formData.append('id', id);
        $.ajax({
            url: _ANNOUNCEMENTS_UPDATE,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#eventEdit")[0].reset();
                $("#eventUpdate").prop("disabled", false);
                $("#eventEditModal").modal("hide");
                $("#id").val("").change();
                $("#loaderEditEvent").attr('style',"display:none");
                loadEventList(1);
            },
            error: function (jqXHR) {
                $("#eventUpdate").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function deleteEvent(id) {
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
                            url: _ANNOUNCEMENTS_DELETE,
                            type: "get",
                            data: {
                               
                                'id':id
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadEventList(1);
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

function changeStatus(id,start_date,end_date){
    if($('#is_disabled_'+id).is(':checked')) {
        setData(id,start_date,end_date);
    }else{
        updateStatus(id);
    }
}

function updateStatus(id){
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to change status.',
        type: 'blue',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        global: false,
                        url: _CHANGE_STATUS,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token' : $('input[name=_token]').val(),
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                           
                            var status ="";
                            if(response.data.status =="1"){
                                status ="<span class='badge badge-success'>Active</span>";
                            }else{
                                status ="<span class='badge badge-danger'>Deactive</span>"; 
                            }
                            $('#row_'+id).html(status)
                           
                        },
                        error: function (xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function () {
                $('#is_disabled_'+id).prop("checked", checked);
            }
        }
    })
}
function setData(id,start_date,end_date) {
    $('#id').val(id);
    $('#eventEditDateModal').modal('show');
    $('#eventEditDateModal').css({
        zIndex: '99999'
    })
    $("#start_date_error").html("");
    $("#end_date_error").html("");
    $('#event_edit_start_date').val(moment(start_date).format('MM/DD/YYYY'));
    $('#event_edit_end_date').val(moment(end_date).format('MM/DD/YYYY'));
}
function cancelDatePopup(){
    var id = $("#id").val();
    if($('#is_disabled_'+id).is(':checked')) {
        checked = false;
    }else{
        checked = true;
    }
    $('#is_disabled_'+id).prop("checked", checked);
}
function updateDate(){
    var start_date = $("#popup_edit_start_date").val();
    var end_date =$("#popup_edit_end_date").val();   
    var cnt = 0;

    if (start_date.trim() == "") {
        $("#popup_start_date_error").html("Please enter Start Date");
        cnt = 1;
    }
    if (end_date.trim() == "") {
        $("#popup_end_date_error").html("Please enter End Date");
        cnt = 1;
    }
   
    var id = $("#id").val();

    if (cnt == 0) {
        $("#loaderEditDateEvent").attr('style',"display:block");
        $("#popupBtnUpdate").prop("disabled", true);
        $.ajax({
            global: false,
            url: _CHANGE_STATUS,
            type: "POST",
            data: {
                'id': id,
                '_token' : $('input[name=_token]').val(),
                'start_date': start_date,
                'end_date': end_date,
            },
            success: function (response) {
                toastr.success(response.error_msg);
                $('#eventEditDateModal').modal('hide');
                $("#loaderEditDateEvent").attr('style',"display:none");
                $("#id").val("").change();
                $("#eventPopupEdit")[0].reset();
                loadEventList(1);
            },
            error: function (xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    } else {
        return false;
    }
}

function commonMessage(id){
    var content =$('#'+id).html();
    $.confirm({
        title: 'Message',
        content: '<p style="white-space:pre-line">'+content+'</p>',
        columnClass: 'col-md-9',
        type: 'blue',
        buttons: {
            
            cancel: function () {
               
            }
        }
    })
}

function sendMail(id){
   
    $.confirm({
        title: 'Are you sure?',
        content: "You are about to send an announcement to all users.",
        columnClass: 'col-md-9',
        type: 'blue',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action:function(){
                    $.ajax({
                    
                        global: false,
                        url: _ANNOUNCEMENTS_MAIL,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token' : _CSRF_TOKEN,
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                           
                           
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