$(document).ready(function () {
    $("#addRatingMasterModal").on("hidden.bs.modal", function () {
        $("#ratingMasterAdd")[0].reset();

    });
    $(".select-class").select2({
        placeholder: "Select Type",
        allowClear: true,
    });
});

function ucfirst(string) {
    if (typeof string !== 'string' || string.length === 0) {
        return '';
    }
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", ".addRatingMasterModal", function (e) {
    e.preventDefault();
    $("#title").val("");
    $(".type-field").val(null).trigger("change");
    $("#saveRatingMaster").attr("id", "addRatingMaster");
    $("#addRatingMaster").text("Save");
    $("#ModalLabel").text("Add Rating Master");
    $("#addRatingMaster").attr("data-uid", "");
    $(".charCls_new").val("");
    $(".rating_master_id").val("");
    $(".title_error, .type_error").html("");
    $("#addRatingMasterModal").modal("show");
});

$(document).on("click", "#addRatingMaster", function (e) {
    e.preventDefault();
    var temp = 0;
    var title = $("#title").val();
    var type = $("#type").val();

    $(this).prop("disabled", true);

    if (title.trim() == "") {
        $(".title_error").html("Please enter Title");
        temp++;
    } else {
        $(".title_error").html("");
    }

    if (type.trim() == "") {
        $(".type_error").html("Please enter Type");
        temp++;
    } else {
        $(".type_error").html("");
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
        data: $("#ratingMasterAdd").serialize(),
        beforeSend: function () { },
        success: function (response) {
            if (response.status === false) {
                $.each(response.error, function (prefix, val) {
                    $("span." + prefix + "_error").text(val[0]);
                });
                $("#addRatingMaster").prop("disabled", false);

            } else {
                $("#addRatingMasterModal").modal("hide");
                $("#ratingMasterAdd")[0].reset();
                $("#addRatingMaster").prop("disabled", false);

                var totalRecord = "{{ $ratingMaster->total() }}";
                if (totalRecord == 0) {
                    $("#hidedis").addClass("hide");
                }
                var responseData = response.data;
                toastr.success(response.msg);
                $(".hide-no-record").hide();
                if ($(".rating_master_id").val() != "") {
                    $("#title-" + responseData.id).html(ucfirst(responseData.title));
                    $("#type-" + responseData.id).html(ucfirst(responseData.type));
                } else {
                    $("#hidedis").addClass("hide");
                    var idLength = $(".viewRatingMaster").length;

                    var appendRow = `
                    <tr class="form-list-classs" id="${responseData.id}">
                        <td><span id="rowIndex">${idLength + 1}</span></td>
                       <td id="title-${responseData.id}">${ucfirst(responseData.title)}</td>
                        <td id="type-${responseData.id}">${ucfirst(responseData.type)}</td>
                        <td>
                            <a href="javascript:void(0);" class="pull-left ml-1 viewRatingMaster" data-eid="${responseData.id}" data-name="${responseData.title}" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 editRatingMaster" data-eid="${responseData.id}" data-name="${responseData.title}" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 deleteRatingMaster" data-did="${responseData.id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>`;
                    $("#refreshDivNew").append(appendRow);
                }
            }
        },
        error: function (error) {
            $("#addRatingMaster").prop("disabled", false);
            toastr.error(error.responseJSON.errors);
        }
    });
});

$(document).on("click", ".editRatingMaster", function () {
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

                if (responseData.is_text == 1) {
                    $(".is-text-field").prop("checked", true);
                } else {
                    $(".is-text-field").prop("checked", false);
                }
                $("#title").val($(this).attr("data-label"));
                $("#saveRatingMaster").attr("id", "addRatingMaster");
                $("#addRatingMaster").text("Update");
                $("#ModalLabel").text("Update Rating Master");
                $(".charCls_new").val(responseData.title);
                $(".rating_master_id").val(responseData.id);
                $(".type-field").val([responseData.type]).trigger("change");
                $("#addRatingMaster").attr("data-uid", $(this).data("eid"));
                $(".title_error, .type_error").html("");
                $("#addRatingMasterModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".viewRatingMaster", function () {
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
                if (responseData.is_text == 1) {
                    $(".is-text-html").html('YES');
                } else {
                    $(".is-text-html").html('NO');
                }
                $(".title-html").html(ucfirst(responseData.title));
                $(".type-html").html(ucfirst(responseData.type));
                $("#viewRatingMasterModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".deleteRatingMaster", function() {
    var id = $(this).attr('data-did');
    deleteRatingMaster(id);
});

function deleteRatingMaster(id) {
    $.confirm({
        title: 'Are you sure?',
        content: 'You want to delete this Rating Master?',
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
                                $("#" + id).remove();                                 
                                toastr.success(response.msg);
                                if (response.data == 0) {
                                    $('#hidedis').removeClass('hide');
                                }
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


