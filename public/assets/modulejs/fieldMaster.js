$(document).ready(function () {

    // getFormGroupData();

    $("#addFieldMasterModal").on("hidden.bs.modal", function () {
        $("#fieldMasterAdd")[0].reset();

    });
    $(".select-class2").select2({
        placeholder: "Select Size",
        allowClear: true,
    });
     $(".select-class").select2({
        placeholder: "Select Type",
        allowClear: true,
    });

    $("#type").change(function () {
        var selectedType = $(this).val();
        if (
            selectedType === "select" ||
            selectedType === "checkbox" ||
            selectedType === "radio"
        ) {
            $("#options-group").show();
            $("#options-group .optionDiv input").val("");
            $("#options-group .option_error").html("");
            $("#newInput").empty();
        } else {
            $("#options-group").hide();
            $("#options-group .optionDiv input").val("");
            $("#options-group .option_error").html("");
        }

        if (
            selectedType === "text" ||
            selectedType === "textarea" ||
            selectedType === "email" ||
            selectedType === "number"
        ) {
            $(".set-character-limit").show();
            $(".set_character_limit_error").html("");
        } else {
            $(".set-character-limit").hide();
            $("#set_character_limit").val("");
            $(".set_character_limit_error").html("");

        }
    });

    var optionHtml = `
      <div class="form-group row RemoveOption">
           <label class="col-sm-3"></label>
          <div class="optionDiv col-sm-9">
              <div class="row align-items-center">
                  <div class="col-md-10">
                      <input type="text" name="option[]" class="form-control option" value="" placeholder="Enter Option">
                  </div>
                  <div class="col-md-2">
                      <button type="button" class="btn btn-danger rowRemover"><i class="fa fa-minus"></i></button>
                  </div>
              </div>
              <span class="text-danger option_error"></span>
          </div></div>
      `;

    $(".options-group").on("click", ".rowAdder", function () {
        $("#newInput").append(optionHtml);
    });

    $("#newInput").on("click", ".rowRemover", function () {
        $(this).closest(".RemoveOption").remove();
    });
});

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", ".addFieldMasterModal", function (e) {
    e.preventDefault();
    $("#label").val("");
    $("#form_group").val("");
    $("#set_character_limit").val("");
    $("#options-group").hide();
    $("#options-group .optionDiv input").val("");
    $("#options-group .option_error").html("");
    $("#newInput").empty();
    $("#saveFieldMaster").attr("id", "addFieldMaster");
    $("#addFieldMaster").text("Save");
    $("#ModalLabel").text("Add Field Master");
    $("#addFieldMaster").attr("data-uid", "");
    $(".charCls_new").val("");
    $(".field_master_id").val("");
    $(".type-field").val(null).trigger("change");
    $(".select-form-group-field").val(null).trigger("change");
    $("#size").val(null).trigger("change");
    $("#show_in_portal").prop("checked", false);
    $(".label_error, .type_error,.size_error,.set_character_limit_error,.form_group_error").html("");
    $("#addFieldMasterModal").modal("show");
});

$(document).on("click", "#addFieldMaster", function (e) {
    e.preventDefault();
    var temp = 0;
    var label = $("#label").val();
    var set_character_limit = $("#set_character_limit").val();
    var type = $("#type").val();
    var size = $("#size").val();
    var agency_id = $("#agency_id").val();
    var form_id = $("#form_id").val();
    var form_group = $("#form_group").val();

    $(this).prop("disabled", true);

    if (label.trim() == "") {
        $(".label_error").html("Please enter Label");
        temp++;
    } else {
        $(".label_error").html("");
    }

    if (form_id) {
        if (typeof form_group === 'undefined' || form_group === null || form_group.trim() == "") {
            $(".form_group_error").html("Please select Form Group");
            temp++;
        } else {
            $(".form_group_error").html("");
        }
    }

    if (["text", "textarea", "email", 'number'].includes(type)) {
        if (set_character_limit.trim() == "") {
            $(".set_character_limit_error").html("Please enter Character Limit");
            temp++;
        } else {
            $(".set_character_limit_error").html("");
        }
    }

    if (type.trim() == "") {
        $(".type_error").html("Please enter Type");
        temp++;
    } else {
        $(".type_error").html("");
    }
    if (size.trim() == "") {
        $(".size_error").html("Please enter Size");
        temp++;
    } else {
        $(".size_error").html("");
    }

    if (["checkbox", "radio", "select"].includes(type)) {
        $(".optionDiv").each(function () {
            if ($(this).find("input").val().trim() == "") {
                temp++;
                $(this).find(".option_error").html("Please enter Options");
            } else {
                $(this).find(".option_error").html("");
            }
        });
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
        data: $("#fieldMasterAdd").serialize(),
        beforeSend: function () { },
        success: function (response) {
            if (response.status === false) {
                $.each(response.error, function (prefix, val) {
                    $("span." + prefix + "_error").text(val[0]);
                });
                $("#addFieldMaster").prop("disabled", false);

            } else {
                $("#addFieldMasterModal").modal("hide");
                $("#fieldMasterAdd")[0].reset();
                $("#addFieldMaster").prop("disabled", false);

                var totalRecord = "{{ $formFields->total() }}";
                if (totalRecord == 0) {
                    $("#hidedis").addClass("hide");
                }
                var responseData = response.data;

                toastr.success(response.msg);
                $(".hide-no-record").hide();
                if ($(".field_master_id").val() != "") {
                    $("#label-" + responseData.id).html(ucfirst(responseData.label));
                    $("#type-" + responseData.id).html(ucfirst(responseData.type));
                    $("#size-" + responseData.id).html(ucfirst(responseData.size));
                    $("#set-character-limit-" + responseData.id).html(responseData.set_character_limit ? responseData.set_character_limit : "-");
                    updateSort('#sortableTable');
                    saveOrderToDatabase();
                } else {
                    $("#hidedis").addClass("hide");
                    // var idLength = $(".field-master-class").length
                    var idLength = $(".viewFieldMaster").length;

                    var appendRow = `
                    <tr class="form-list-classs" id="${responseData.id}">
                        <td><span id="rowIndex">${idLength + 1}</span></td>
                       <td id="label-${responseData.id}">${ucfirst(responseData.label)}</td>
                        <td id="type-${responseData.id}">${ucfirst(responseData.type)}</td>
                        <td id="size-${responseData.id}">${ucfirst(responseData.size)}</td>
                        <td id="set-character-limit-${responseData.id}">${(responseData.set_character_limit ? responseData.set_character_limit : '-')}</td>
                        <td>
                            <a href="javascript:void(0);" class="pull-left ml-1 viewFieldMaster" data-eid="${responseData.id}" data-name="${responseData.label}" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 editFieldMaster" data-eid="${responseData.id}" data-name="${responseData.label}" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 deleteFieldMaster" data-did="${responseData.id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </a>
                            <input class="sortID" type="hidden" name="sortID[]" value="${responseData.sort_id}" />
                            <input class="formFieldsID" type="hidden" name="formFieldsID[]" value="${responseData.id}" />
                            <input class="formID" type="hidden" name="formID[]" value="${responseData.form_id}" />
                            <input class="agencyId" type="hidden" name="agencyId[]" value="${responseData.agency_id}" />
                        </td>
                    </tr>`;
                    $("#refreshDivNew").append(appendRow);
                    updateSort('#sortableTable');
                    saveOrderToDatabase();
                }
                if (agency_id != null) {
                    var idLength = $(".viewAgencyMaster").length;
                    var formGroup = '';
                    
                    if(responseData.form_group){
                        var formGroup = `<td>${(responseData.form_group && responseData.form_group.title ? responseData.form_group.title : '-')}</td>`;
                    }
                    var appendRow = `
                    <tr class="form-list-classs" id="${responseData.id}">
                        <td><span id="rowIndex">${idLength + 1}</span></td>
                        ${formGroup}
                        <td>${ucfirst(responseData.label)}</td>
                        <td>${ucfirst(responseData.type)}</td>
                        <td>${ucfirst(responseData.size)}</td>
                        <td>${(responseData.set_character_limit ? responseData.set_character_limit : '-')}</td>
                        <td>
                        <a href="javascript:void(0);" class="pull-left ml-1 viewAgencyMaster" data-eid="${responseData.id}"  data-aid="${agency_id}" data-name="${responseData.label}" data-fid="${form_id}" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 deleteAgencyMaster" data-did="${responseData.id}" data-aid="${agency_id}" data-fid="${form_id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </a>
                            <input class="sortID" type="hidden" name="sortID[]" value="${responseData.sort_id}" />
                            <input class="formFieldsID" type="hidden" name="formFieldsID[]" value="${responseData.id}" />
                            <input class="agencyId" type="hidden" name="agencyId[]" value="${responseData.agency_id}" />
                            <input class="formID" type="hidden" name="formID[]" value="${responseData.form_id}" />
                        </td>
                    </tr>`;
                    $("#refreshDiv").append(appendRow);
                    updateSort('#sortableTable');
                    saveOrderToDatabase();
                }

            }
        },
        error: function (error) {
            $("#addFieldMaster").prop("disabled", false);
            toastr.error(error.responseJSON.errors);
        }
    });
});

var optionHtml = `
              <div class="form-group row RemoveOption">
                  <label class="col-sm-3"></label>
                  <div class="optionDiv col-sm-9">
                      <div class="row align-items-center">
                          <div class="col-md-10">
                              <input type="text" name="option[]" class="form-control option" value="" placeholder="Enter Option">
                          </div>
                          <div class="col-md-2">
                              <button type="button" class="btn btn-danger rowRemover"><i class="fa fa-minus"></i></button>
                          </div>
                      </div>
                      <span class="text-danger option_error"></span>
                  </div>
              </div>
          `;

$(document).on("click", ".editFieldMaster", function () {
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

                $("#label").val($(this).attr("data-label"));
                $("#saveFieldMaster").attr("id", "addFieldMaster");
                $("#addFieldMaster").text("Update");
                $("#ModalLabel").text("Update Field Master");
                $(".charCls_new").val(responseData.label);
                $("#set_character_limit").val(responseData.set_character_limit);
                $(".field_master_id").val(responseData.id);
                $(".type-field").val([responseData.type]).trigger("change");
                $("#size").val([responseData.size]).trigger("change");
                $("#show_in_portal").prop("checked", responseData.show_in_portal == 1);
                $("#addFieldMaster").attr("data-uid", $(this).data("eid"));
                if (responseData.option_new.length == 0) {
                    $("#newInput").html("");
                }

                responseData.option_new.forEach(function (option, key) {
                    if (key == 0) {
                        $("#options-group").show();
                        $("#options-group .optionDiv input").val(option);
                        $("#options-group .option_error").html("");
                    } else {
                        var newOptionHtml = $(optionHtml);
                        newOptionHtml.find("input.option").val(option);
                        $("#newInput").append(newOptionHtml);
                    }
                });
                $(".label_error, .type_error,.size_error,.set_character_limit_error").html("");
                $("#addFieldMasterModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".viewFieldMaster", function () {
    var id = $(this).data("eid");
    $(".option-html-view").addClass('d-none')
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
                $(".label-html").html(ucfirst(responseData.label));
                $(".type-html").html(ucfirst(responseData.type));
                $(".size-html").html(ucfirst(responseData.size));
                $(".set-character-limit-html").html(responseData.set_character_limit ? responseData.set_character_limit : '-');

                $(".show-in-portal-html").html(responseData.show_in_portal == 1 ? 'Yes' : 'No');
                $(".option-html").html('');
                responseData.option_new.forEach(function (option, key) {
                    $(".option-html-view").removeClass('d-none');
                    $(".option-html").append('<li>' + option + '</li>');
                });

                $("#viewFieldMasterModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

$(document).on("click", ".viewAgencyMaster", function () {
    var id = $(this).data("eid");
    $(".option-html-view").addClass('d-none')
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
                $(".label-html").html(ucfirst(responseData.label));
                $(".type-html").html(ucfirst(responseData.type));
                $(".size-html").html(ucfirst(responseData.size));
                $(".set-character-limit-html").html(responseData.set_character_limit ? responseData.set_character_limit : '-');
                $(".show-in-portal-html").html(responseData.show_in_portal == 1 ? 'Yes' : 'No');
                $(".option-html").html('');
                responseData.option_new.forEach(function (option, key) {
                    $(".option-html-view").removeClass('d-none');
                    $(".option-html").append('<li>' + option + '</li>');
                });

                $("#viewAgencyMasterModal").modal("show");
            } else {
                // Error
            }
        },
    });
});

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
        var agencyId = $(this).find('.agencyId').val();

        $(this).find('span').text(row_index);
        $(this).find('.sortID').val(row_index);

        sortArray.push({
            id: formFieldsID,
            order: row_index,
            formID: formID,
            agencyId: agencyId
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
        url: "/update-agencymaster-order",
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

function getFormGroupData() {
    var form_id = $('#form_id').val();
    
    $.ajax({
        url: _FORM_GROUP_URL,
        type: 'GET',
        dataType: 'json',
        data: {
            form_id: form_id,
        },
        success: function (data) {
            let formGroupSelect = $('#form_group');
            formGroupSelect.empty();
            formGroupSelect.append('<option value="">Select Type</option>');

            $.each(data, function (key, formGroup) {
                formGroupSelect.append('<option value="' + formGroup.id + '">' + formGroup.title + '</option>');
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching form groups: ", error);
        }
    });
}

