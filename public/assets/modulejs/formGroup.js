$(function() {
    $(".wmd-view-topscroll").scroll(function() {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function() {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadFormGroupList(1);
});

function loadFormGroupList(page) {
    var form_id = $('#form_id').val();
    
    $.ajax({
        url: _FORM_GROUP_LIST+"?page=" + page,
        type: "get",
        data: {
            "form_id":form_id,
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}


$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadFormGroupList(page);
});

$(document).ready(function () {
    $("#addModal").on("hidden.bs.modal", function () {
        $("#formDataAdd")[0].reset();

    });
});

function ucfirst(string) {
    if (typeof string !== 'string' || string.length === 0) {
        return '';
    }
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", ".addModal", function (e) {
    e.preventDefault();
    $("#title").val("");
    $("#addFormData").text("Save");
    $("#ModalLabel").text("Add Form Group");
    $("#addFormData").attr("data-uid", "");
    $(".charCls_new").val("");
    $(".form_group_id").val("");
    $(".title_error").html("");
    $("#addModal").modal("show");
});

$(document).on("click", "#addFormData", function (e) {
    e.preventDefault();
    var temp = 0;
    var title = $("#title").val();

    $(this).prop("disabled", true);

    if (title.trim() == "") {
        $(".title_error").html("Please enter Title");
        temp++;
    } else {
        $(".title_error").html("");
    }

    if (temp > 0) {
        $(this).prop("disabled", false);
        return false;
    }

    $.ajax({
        headers: {
            "X-CSRF-Token": $("meta[name=_token]").attr("content"),
        },
        url: storeData,
        type: "POST",
        cache: false,
        data: $("#formDataAdd").serialize(),
        beforeSend: function () { },
        success: function (response) {
            if (response.status === false) {
                $.each(response.error, function (prefix, val) {
                    $("span." + prefix + "_error").text(val[0]);
                });
                $("#addFormData").prop("disabled", false);

            } else {
                $("#addModal").modal("hide");
                $("#formDataAdd")[0].reset();
                $("#addFormData").prop("disabled", false);
                toastr.success(response.msg);
                // updateSort('#sortableTable');
                // saveOrderToDatabase();
                loadFormGroupList(1);
            }
        },
        error: function (error) {
            $("#addFormData").prop("disabled", false);
            toastr.error(error.responseJSON.errors);
        }
    });
});

$(document).on("click", ".editData", function () {
    var id = $(this).data("eid");
    var fnUrl = editData.replace("id", id);
    $.ajax({
        async: false,
        global: false,
        url: fnUrl,
        type: "get",
        data: {
            id: id,
            _token: _CSRF_TOKEN,
        },
        success: function (response) {
            if (response.status) {
                var responseData = response.data;

                $("#title").val($(this).attr("data-label"));
                $("#addFormData").text("Update");
                $("#ModalLabel").text("Update Form Group");
                $(".charCls_new").val(responseData.title);
                $(".form_group_id").val(responseData.id);
                $("#addFormData").attr("data-uid", $(this).data("eid"));
                $(".title_error").html("");
                $("#addModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".viewData", function () {
    var id = $(this).data("eid");
    var fnUrl = editData.replace("id", id);
    $.ajax({
        async: false,
        global: false,
        url: fnUrl,
        type: "get",
        data: {
            id: id,
            _token: _CSRF_TOKEN,
        },
        success: function (response) {
            if (response.status) {
                var responseData = response.data;
                $(".title-html").html(ucfirst(responseData.title));
                $("#viewModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".deleteData", function() {
    var id = $(this).attr('data-did');
    deleteData(id);
});

function deleteData(id) {
    $.confirm({
        title: 'Are you sure?',
        content: 'You want to delete this Form Group?',
        buttons: {
            confirm: {
                text: 'DELETE',
                btnClass: 'btn-danger',
                action: function () {
                    var url = _DELETE_URL;
                    url = url.replace('id', id);

                    $.ajax({
                        async: false,
                        url: url,
                        type: "DELETE",
                        data: {
                            id: id,
                            _token: _CSRF_TOKEN
                        },
                        success: function(response) {
                            if (response.status) {
                                // updateSort('#sortableTable');
                                // saveOrderToDatabase();
                                loadFormGroupList(1);                                 
                                toastr.success(response.msg);
                               
                            } else {
                                toastr.error(response.msg);
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred while deleting the record.');
                        }
                    });
                }
            },
            cancel: {
                text: 'CANCEl',
                btnClass: 'btn-secondary',
                action: function () {
                }
            }
        }
    });
}

// Sortable rows, helps maintain column widths a little better
var fixHelperModified = function (e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function (index) {
        $(this).width($originals.eq(index).width());
    });
    return $helper;
};

var sortArray = [];
function updateSort(table) {
    sortArray = [];

    $(table + ' tbody tr').each(function () {
        var row_index = $(this).index() + 1;
        var formFieldsID = $(this).find('.formFieldsID').val();
        var formID = $(this).find('.formID').val();

        $(this).find('span').text(row_index);
        $(this).find('.sortID').val(row_index);

        sortArray.push({
            id: formFieldsID,
            order: row_index,
            formID: formID,
        });
    });
    return sortArray;
}

$(function () {
    $("#sortableTable tbody").sortable({
        helper: fixHelperModified,
        update: function (event, ui) {
            updateSort('#sortableTable');
            saveOrderToDatabase();
        }
    })
        .disableSelection();
});

function saveOrderToDatabase() {
    $.ajax({
        url: updateFormGroupUrl,
        method: "POST",
        data: {
            _token: _CSRF_TOKEN,
            sortOrder: sortArray
        },
        success: function (response) {
            $(".successfully-saved").css("display", "block").delay(2000).fadeOut(400);
        },
        error: function (xhr) {
            console.error("Error updating sort order:", xhr.responseText);
        }
    });
}


