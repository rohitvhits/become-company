$(document).ready(function () {

    function fetchTemplates() {
        $.ajax({
            headers: {
                "X-CSRF-Token": $("meta[name=_token]").attr("content"),
            },
            url: getTemplateData,
            type: "GET",
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    $('#template').empty();
                    $('#template').append('<option value="">Select Template</option>');
                    $.each(response, function (key, template) {
                        $('#template').append('<option value="' + template.id + '">' + template.template_name + '</option>');
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchTemplates();

    $("#addFormSetupModal").on("hidden.bs.modal", function () {
        $("#formSetupAdd").reset();
    });
    $("#agency").select2({
        placeholder: "Select Agency",
        allowClear: true,
    });

    function toggleAgencyDropdown() {
        var isDefault = $('input[name="is_default"]:checked').val();
        if (isDefault === '1') {
            $('#agency-dropdown').hide();
            $("#agency").val(null).trigger('change');
        } else {
            $('#agency-dropdown').show();
        }
    }

    $('input[name="is_default"]').change(function () {
        toggleAgencyDropdown();
    });

});

$(document).on("click", ".addFormSetupModal", function (e) {
    e.preventDefault();
    $("#title").val("");
    $("#agency-dropdown").hide();
    // $("#agency-dropdown input").val("");
    $(".charCls_new").val("");
    $(".form_setup_id").val("");
    $("#formSetupAdd").trigger('reset');
    $(".title_error, .is_default_error, .agency_error,.form_type_error").html("");
    $("#ModalLabel").text("Add Form Setup");
    $("#addFormSetup").text("Save");
    $("#addFormSetup").attr("data-uid", "");
    $("#addFormSetupModal").modal("show");
    $("#agency").val(null).trigger('change');

});

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", "#addFormSetup", function (e) {
    e.preventDefault();
    var temp = 0;
    var title = $("#title").val();
    var is_default = $("input[name='is_default']:checked").val();
    var agency = $("#agency").val();
    var form_type = $("input[name='form_type']:checked").val();

    $(this).prop("disabled", true);

    if (title.trim() === "") {
        $(".title_error").html("Please enter a Title");
        temp++;
    } else {
        $(".title_error").html("");
    }

    if (!is_default) {
        $(".is_default_error").html("Please select default or not");
        temp++;
    } else {
        $(".is_default_error").html("");
    }

    if (!form_type) {
        $(".form_type_error").html("Please select form Type");
        temp++;
    } else {
        $(".form_type_error").html("");
    }

    if (is_default == 0) {
        if (agency === "") {
            $(".agency_error").html("Please select an agency");
            temp++;
        } else {
            $(".agency_error").html("");
        }
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
        data: $("#formSetupAdd").serialize(),
        beforeSend: function () {
        },
        success: function (response) {
            if (response.status === false) {
                $.each(response.error, function (prefix, val) {
                    $("span." + prefix + "_error").text(val[0]);
                });
                $("#c").prop("disabled", false);

            } else {
                $("#formSetupAdd")[0].reset();
                $("#addFormSetupModal").modal("hide");
                $("#addFormSetup").prop("disabled", false);

                var totalRecord = "{{ $formSetup->total() }}";
                if (totalRecord == 0) {
                    $("#hidedis").addClass("hide");
                }
                var responseData = response.data;
                console.log(responseData);
                var isDefaultText = responseData.is_default == '1' ? 'Yes' : 'No';
                var formType = responseData.form_type == '1' ? 'Patient' : 'Cargiver';
                var agencyValue = responseData.is_default == '0' ? responseData.agency_value.agency_name : '-';
                if ($(".form_setup_id").val() != "") {
                    $("#title-" + responseData.id).html(ucfirst(responseData.title));
                    $("#is-default-" + responseData.id).html(isDefaultText);
                    $("#form-type-" + responseData.id).html(formType);
                    $("#agency-" + responseData.id).html(agencyValue);
                    $("#template-" + responseData.id).html();
                } else {
                    $("#hidedis").addClass("hide");
                    var idLength = $(".viewFormSetup").length;
                    if (formSetupShowPermission) {
                        var view = `<a href="javascript:void(0);" class="pull-left ml-1 viewFormSetup dropdown-item" data-eid="${responseData.id}" data-name="${responseData.title}" title="View">View</a>`;
                    }
                    if (formSetupEditPermission) {
                        var edit = `<a href="javascript:void(0);" class="pull-left ml-1 editFieldMaster dropdown-item" data-eid="${responseData.id}" data-name="${responseData.title}" title="Edit">Edit</a>`;
                    }

                    if (formSetupDeletePermission) {
                            var deleteForm = `<a href="javascript:void(0);" class="pull-left ml-1 deleteFormSetup dropdown-item" data-did="${responseData.id}" title="Delete">
                                Delete</a>`;
                    }
                    if (formSetupTemplatePermission) {
                        var linkTemplate = `<a href="javascript:void(0);" data-eid="${responseData.id}"
                    data-name="${responseData.title}"
                    class="pull-left ml-1 viewTemplate dropdown-item" title="Link Template">
                   Link Template</a>`;
                    }
                    if (formSetupAgencyShowPermission) {
                        var viewAgencyWiseField = ` <a href="${agencyMasterListUrl}?agency_id=${responseData.agency}&form_id=${responseData.id}" class="pull-left ml-1 dropdown-item" target="_blank" data-toggle="tooltip" title="View Agency Wise Field">View Agency Wise Field
                        </a>`;
                    }

                    if (formGroupListPermission) {
                        var viewFormGroup = `<a href="${formGroupUrl}?form_id=${responseData.id}" class="pull-left ml-1 dropdown-item" target="_blank" data-toggle="tooltip" title="View Form Group">
                                    View Form Group
                                    </a>`;
                    }

                    var actionButton = `<div class="btn-group pull-right status-dropdoown mr-2">
                    <button type="button" class="btn btn-warning"
                        title="Action">Action</button>
                    <button type="button"
                        class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                        id="dropdownMenuSplitButton6" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                        ${view + ' ' + edit + ' ' + linkTemplate + ' ' + deleteForm + ' ' + viewAgencyWiseField + ' ' + viewFormGroup}
                    </div>
                </div>`;

                    var action = actionButton;
                    var appendRow = `
                    <tr id="${responseData.id}">
                        <td>${idLength + 1}</td>
                       <td id="title-${responseData.id}">${ucfirst(responseData.title)}</td>
                        <td id="is-default-${responseData.id}">${isDefaultText}</td>
                        <td id="form-type-${responseData.id}">${formType}</td>
                       <td id="agency-${responseData.id}">${agencyValue}</td>
                       <td id="template-${responseData.id}">-</td>
                        <td>${action}</td>
                    </tr>`;
                    $("#refreshDivNew").append(appendRow);
                    toastr.success(response.msg);
                }
            }
        },

        error: function (error) {
            $("#addFormSetup").prop("disabled", false);
            toastr.error(error.responseJSON.errors);
        }
    });
});

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

                $("#title").val(responseData.title);
                $("#ModalLabel").text("Update Form Setup");
                $("#addFormSetup").text("Update");
                $(".charCls_new").val(responseData.title);
                $(".form_setup_id").val(responseData.id);
                $('input[name="is_default"]').filter('[value="' + responseData.is_default + '"]').prop('checked', true);
                $('input[name="form_type"]').filter('[value="' + responseData.form_type + '"]').prop('checked', true);
                if (responseData.is_default == '0') {
                    $(".agency-dropdown").show();
                } else {
                    $(".agency-dropdown").hide();
                }
                $("#agency").val(responseData.agency).trigger('change');

                $(".title_error, .is_default_error, .agency_error,.form_type_error").html("");
                $("#addFormSetupModal").modal("show");
            } else {
            }
        },
    });
});


$(document).on("click", ".viewFormSetup", function () {
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
                var isDefaultText = responseData.is_default == '1' ? 'Yes' : 'No';
                var formType = responseData.form_type == '1' ? 'Patient' : 'Cargiver';
                var agencyValue = responseData.is_default == '0' ? responseData.agency_value.agency_name : '-';

                $(".title-html").html(ucfirst(responseData.title));
                $(".is-default-html").html(isDefaultText);
                $(".form-type-html").html(formType);
                $(".agency-html").html(agencyValue)
                $("#viewFormSetupModal").modal("show");
            } else {
            }
        },
    });
});

$(document).on("click", ".viewTemplate", function (e) {
    var eid = $(this).data('eid');
    var newUrl = addTemplateUrl + '?custom_form_id=' + eid;
    var csrfToken = $("meta[name='csrf-token']").attr("content");

    var additionalContent = '';
    if (createTemplatePermission) {
        additionalContent =
            '<div class="form-group" style="margin-top: 2px; text-align: right;">' +
            '<a href="' + newUrl + '" target="_blank" class="btn btn-link" style="color: #1e9ff2;">Create New Template</a>' +
            '</div>';
    }

    $.confirm({
        title: 'Add Template',
        content: '' +
            '<div class="form-container" style="display: flex; flex-direction: column; height: 100%;">' +
            '<form id="templateAddForm" class="formName" style="position: relative; z-index: 99999999999; flex: 1;">' +
            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
            '<input type="hidden" name="custom_form_id" class="custom_form_id" value="' + eid + '">' +
            '<div class="form-group" style="margin-bottom: 5px;">' +
            '<label for="template">Template<span class="error mt-2">*</span></label>' +
            '<select class="form-control form-control-user template-field col-sm-11 select2" id="template" name="template">' +
            '<option value="">Select Template</option>' +
            '</select>' +
            '<span class="template_error" style="color:red; font-size: 0.875rem;"></span>' +
            '</div>' +
            additionalContent +
            '</form>' +
            '</div>',
        buttons: {
            save: {
                text: 'Save',
                btnClass: 'btn-success',
                action: function () {
                    var template = this.$content.find('#template').val();
                    var errors = 0;

                    if (template === "") {
                        this.$content.find('.template_error').html("Please select a Template");
                        errors++;
                    } else {
                        this.$content.find('.template_error').html("");
                    }

                    if (errors > 0) {
                        return false;
                    }

                    var formData = new FormData($('#templateAddForm')[0]);
                    formData.append('_token', _CSRF_TOKEN);
                    $.ajax({

                        url: storeTemplateData,
                        type: "POST",
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === false) {
                                $.each(response.error, function (prefix, val) {
                                    $("span." + prefix + "_error").text(val[0]);
                                });
                                toastr.error(response.msg);
                            } else {
                                $("#template-" + response.data.custom_form_id).html(response.data.template_name);
                                toastr.success(response.msg);
                                $.alert(response.msg);
                                fetchTemplates();
                            }
                        },

                        error: function (error) {
                            $.alert(error.responseJSON.msg);
                        }
                    });
                }
            },
            cancel: {
                text: 'Close',
                btnClass: 'btn-light'
            },
        },
        onContentReady: function () {
            var jc = this;

            this.$content.find('.jconfirm-box').css({
                'max-width': '100%',
                'width': 'auto',
                'height': '600px',
                'overflow-y': 'auto'
            });
            var templateDropdown = this.$content.find('#template');

            $.ajax({
                headers: {
                    "X-CSRF-Token": $("meta[name=_token]").attr("content")
                },
                url: getTemplateData,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.length > 0) {
                        templateDropdown.empty();
                        templateDropdown.append('<option value="">Select Template</option>');
                        $.each(response, function (key, template) {
                            var selected = '';
                            if (template.custom_form_id == eid) {
                                selected = 'selected';
                            }

                            templateDropdown.append('<option value="' + template.id + '" ' + selected + '>' + template.template_name + '</option>');

                        });
                        templateDropdown.select2();
                        $("#template").select2({
                            placeholder: "Select Template",
                            allowClear: true,
                            dropdownParent: $('#templateAddForm')
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }
    });
});




