$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadEbookList(1);
});

$("#ebook_type").select2({
    placeholder: "Select Service"
});

$("#ebook_edit_type").select2({
    placeholder: "Select Service"
});


function loadEbookList(page) {
    $.ajax({
        url: _EBOOK_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

function getEbook() {
    $('#ebookModal').modal('show');
    $('#ebookModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#content_error").html("");
    $("#video_error").html("");
    $("#type_error").html("");
    $("#ebook")[0].reset();
    $("#imageDiv").html('');
}

function getEditEbook(id) {
    $('#id').val(id);
    $('#ebookEditModal').modal('show');
    $('#ebookEditModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#content_error").html("");
    $("#video_error").html("");
    $("#type_error").html("");
    $("#ebook")[0].reset();
    getModalData(id);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadEbookList(page);
});

function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: _EBOOK_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            if (json) {
                $('#ebook_edit_title').val(json.title);
                editor.setData(json.content);
                $("#ebook_edit_type").val(json.type);
                valuesToSelect = json.type.split(',');
                $('#ebook_edit_type').select2();
                $('#ebook_edit_type').val(valuesToSelect).trigger('change'); // Set values and trigger change
                var video = _EBOOK_AWS+"/"+id+"?type=ebook"
                if (json.video != '') {
                    $('#show-video').html('<video id="agency-logo" src="' + video + '" style="height: 110px;width: 103%;border-radius: 5px;" alt="Logo"/>');
                }
            }
        }
    })
}
function save(){
    var title = $("#ebook_title").val();
    var content = window.editorAdd.getData();
    var type = $("#ebook_type").val();
    $("#title_error").html("");
    $("#content_error").html("");
    $("#type_error").html("");
    $("#video_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    if (content.trim() == "") {
        $("#content_error").html("Please enter Content");
        cnt = 1;
    }

    if (type == "") {
        $("#type_error").html("Please select Type");
        cnt = 1;
    }

    var files = $('input[name="video"]')[0].files;
    if (files.length == 0) {
        $("#video_error").html("Please upload an video");
        cnt = 1;
    } else {
        var fileExtensionType = ["mp4", "avi", "mkv","mov","wmv","flv","webm","3gp"];
        var fileName = files[0].name;
        console.log(fileName.split(".").pop().toLowerCase());
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#video_error").html("Please select only mp4, avi, mkv, mov, wmv, flv, webm or 3gp file");
            cnt = 1;
        }
    }
    console.log(cnt);

    if (cnt == 0) {
        $("#loaderAddEbook").attr('style',"display:block");
        $("#ebookSave").prop("disabled", true);
        var formData = new FormData($("#ebook")[0]);
        formData.append('_token', _CSRF_TOKEN)
        formData.append('content', content);
        $.ajax({
            type: "POST",
            url: _EBOOK,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#ebook")[0].reset();
                $("#ebookSave").prop("disabled", false);
                $("#ebookModal").modal("hide");
                $("#id").val("").change();
                $("#loaderAddEbook").attr('style',"display:none");
                loadEbookList(1);
            },
            error: function (jqXHR) {
                $("#ebookSave").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function update(){
    var title = $("#ebook_edit_title").val();
    var content = window.editor.getData();
    var type = $('input[name="type"]').is(":selected");
    var type = $("#ebook_edit_type").val();


    $("#title_error").html("");
    $("#content_error").html("");
    $("#type_error").html("");
    $("#video_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }
    if (content.trim() == "") {
        $("#content_error").html("Please enter Content");
        cnt = 1;
    }

    if (type == "") {
        $("#type_error").html("Please select Type");
        cnt = 1;
    }

    var id = $("#id").val();

    var files = $('input[name="video"]')[0].files;
    if (files.length > 0) {
        var fileExtensionType = ["mp4", "avi", "mkv","mov","wmv","flv","webm","3gp"];
        var fileName = files[0].name;
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#video_error").html("Please select only mp4, avi, mkv, mov, wmv, flv, webm or 3gp file");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#loaderEditEbook").attr('style',"display:block");
        $("#ebookUpdate").prop("disabled", true);
        var formData = new FormData($("#ebookEdit")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'PUT');
        formData.append('content', content);
        $.ajax({
            url: _EBOOK + '/' + id,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#ebookEdit")[0].reset();
                $("#ebookUpdate").prop("disabled", false);
                $("#ebookEditModal").modal("hide");
                $("#id").val("").change();
                $("#loaderEditEbook").attr('style',"display:none");
                loadEbookList(1);

            },
            error: function (jqXHR) {
                $("#ebookUpdate").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function deleteEbook(id) {
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
                            url: _EBOOK + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadEbookList(1);
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

function viewEbook(id){
    $.ajax({
        async: false,
        global: false,
        url: _EBOOK_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            $('#videoModal').modal('show');
            var json = response.data;
            if (json) {
                if(ISAWS == 1){
                    var video = _EBOOK_AWS+"/"+id+"?type=ebook"
                }else{
                    var video = BASEURL + json.video;
                }
                if (video != '') {
                    const videoFrame = document.getElementById('videoFrame');
                    videoFrame.src = video;
                }
            }
        }
    })
}
