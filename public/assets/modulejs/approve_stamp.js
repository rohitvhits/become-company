$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadApproveStampList(1);
});


function loadApproveStampList(page) {
    $.ajax({
        url: _APPROV_STAMP_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

function getApproveStamp() {
    $('#approveStampModal').modal('show');
    $('#approveStampModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#image_error").html("");
    $("#approveStamp")[0].reset();
    $("#imageDiv").html('');
}

function getEditApproveStamp(id) {
    $('#id').val(id);
    $('#approveEditStampModal').modal('show');
    $('#approveEditStampModal').css({
        zIndex: '99999'
    })
    $("#title_error").html("");
    $("#image_error").html("");
    getModalData(id);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadApproveStampList(page);
});

function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: _APPROV_STAMP_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            if (json) {
                $('#title').val(json.title);
                var imageUrl = baseUrl + json.image;
                if (json.image != '') {
                    $('#imageDiv').html('<img id="agency-logo" src="' + imageUrl + '" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo"></div>');
                }
            }
        }

    })
}

$("#stampSave").click(function (e) {
    var title = $("#stamp_title").val();

    $("#title_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#title_error").html("Please enter Title");
        cnt = 1;
    }

    var files = $('input[name="image"]')[0].files;
    if (files.length == 0) {
        $("#image_error").html("Please upload an image");
        cnt = 1;
    } else {
        var fileExtensionType = ["jpg", "jpeg", "png"];
        var fileName = files[0].name;
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#image_error").html("Please select only jpg, jpeg, or png file");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#stampSave").prop("disabled", true);
        var formData = new FormData($("#approveStamp")[0]);
        formData.append('_token', _CSRF_TOKEN)
        $.ajax({
            type: "POST",
            url: _STAMP,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#approveStamp")[0].reset();
                $("#stampSave").prop("disabled", false);
                $("#approveStampModal").modal("hide");
                $("#id").val("").change();
                loadApproveStampList(1);
            },
            error: function (jqXHR) {
                $("#approveStamp").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
});

$("#stampUpdate").click(function (e) {
    var title = $("#title").val();
    var image = $("#image").val();
    var id = $("#id").val();
    $("#stamp_title_error").html("");
    var cnt = 0;

    if (title.trim() == "") {
        $("#stamp_title_error").html("Please enter Title");
        cnt = 1;
    }

    var files = $('input[name="stamp_image"]')[0].files;
    if (files.length > 0) {
        var fileExtensionType = ["jpg", "jpeg", "png"];
        var fileName = files[0].name;
        if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
            $("#stamp_image_error").html("Please select only jpg, jpeg, or png file");
            cnt = 1;
        }
    }

    if (cnt == 0) {
        $("#stampUpdate").prop("disabled", true);
        var formData = new FormData($("#approveEditStamp")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'PUT');
        $.ajax({
            url: `/stamp/${id}`,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#approveEditStamp")[0].reset();
                $("#stampUpdate").prop("disabled", false);
                $("#approveEditStampModal").modal("hide");
                $("#id").val("").change();
                loadApproveStampList(1);
            },
            error: function (jqXHR) {
                $("#approveEditStamp").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
});

function deleteApproveStamp(id) {
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
                            url: _STAMP + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadApproveStampList(1);
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

$(document).on("change", ".stampEnableDisabled", function () {
    var isDefault = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).attr("data-id");
    $.ajax({
        type: "GET",
        dataType: "json",
        url: _APPROVE_STAMP_STATUS,
        data: {
            'is_default': isDefault,
            'id': id
        },
        success: function (response) {
            if (response.status == true) {
                toastr.success(response.error_msg);
            } else {
                toastr.error(response.error_msg);
            }
            loadApproveStampList(1);
        },
        error: function (xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
});
