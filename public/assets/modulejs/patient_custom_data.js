document.addEventListener('DOMContentLoaded', function () {
    $("#addAgencyFormModal").on("hidden.bs.modal", function () {
        $("#agencyFormAdd")[0].reset();
    });

    $('#addAgencyFormModal').on('shown.bs.modal', function () {
        $('.select_class').select2({
            placeholder: "Select Values",
            allowClear: true
        });
    });

    $(document).on("click", ".addFormModal", function (e) {
        e.preventDefault();
        $("#form_id").val("");
        $("#doctor_id").val("");
        $("#addAgencyForm").text("Save");
        $("#ModalLabel").text("Add Form");
        $("#addAgencyForm").attr("data-uid", "");
        $(".form_id_error, .doctor_id_error").html("");
        $("#addAgencyFormModal").modal("show");
    });

    $(document).on("click", "#addAgencyForm", function (e) {
        e.preventDefault();
        var temp = 0;
        var form_id = $("#f_id").val();
        var doctor_id = $("#d_id").val();

        $(this).prop("disabled", true);

        if (form_id.trim() == "") {
            $(".form_id_error").html("Please enter Form Name");
            temp++;
        } else {
            $(".form_id_error").html("");
        }

        if (doctor_id.trim() == "") {
            $(".doctor_id_error").html("Please enter Doctor Name");
            temp++;
        } else {
            $(".doctor_id_error").html("");
        }

        if (temp > 0) {
            $(this).prop("disabled", false);
            return false;
        }
        var formAppend = $('#agencyFormAdd')[0];
        var formData = new FormData(formAppend);
        formData.append('_token', _CSRF_TOKEN)

        $.ajax({

            url: storeData,
            type: "POST",
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () { },
            success: function (response) {
                console.log(response);
                if (response.status === false) {
                    if (response.error) {
                        $.each(response.error, function (prefix, val) {
                            $("span." + prefix + "_error").text(val[0]);
                        });
                        toastr.error(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                    $("#addAgencyForm").prop("disabled", false);

                } else {
                    var doctorOptions = `<option class="field_doctor_name" value=""></option>`;
                    $.each(response.doctorList, function (index, doctor) {
                        var selected = String(doctor.id) === String(response.data.doctor_id) ? ' selected' : '';
                        doctorOptions += `<option class="field_doctor_name" value="${doctor.id}"${selected}>${doctor.full_name}</option>`;
                    });

                    var htmlRes = ``;
                    $.each(response.data.agency_master, function (index, res) {
                        let fieldHtml = '';

                        if (res.fields && res.fields.type) {
                            switch (res.fields.type) {
                                case 'select':
                                    fieldHtml = generateSelectField(res.fields, response.patientSubmitData, response.data.form_id, response.data.agency_id, response.data.patient_id);
                                    break;
                                case 'radio':
                                    fieldHtml = generateRadioField(res.fields, response.patientSubmitData, response.data.form_id, response.data.agency_id, response.data.patient_id);
                                    break;
                                case 'checkbox':
                                    fieldHtml = generateCheckboxField(res.fields, response.patientSubmitData, response.data.form_id, response.data.agency_id, response.data.patient_id);
                                    break;
                                case 'textarea':
                                    fieldHtml = generateTextareaField(res.fields, response.patientSubmitData, response.data.form_id, response.data.agency_id, response.data.patient_id);
                                    break;
                                default:
                                    fieldHtml = generateTextField(res.fields, response.patientSubmitData, response.data.form_id, response.data.agency_id, response.data.patient_id);
                                    break;
                            }
                        } else {
                            console.error('Fields are not available in the response.');
                        }

                        htmlRes += `
                            <div class="col-md-${res.fields && res.fields.size === 'half' ? '6' : '12'}">
                                <div class="field-container">
                                    <div class="label-edit-container">
                                        <dt>${res.fields ? res.fields.label : ''}</dt>
                                    </div>
                                    <dd>
                                        <span id="dynamic-field-${response.data.form_id}-${res.fields ? res.fields.id : ''}"
                                            class="flex-grow-1 dynamic-form-value${response.data.form_id}">
                                            ${res.fields && res.fields.type === 'checkbox'
                                ? (() => {
                                    const checkboxData = response.patientSubmitData?.[response.data.form_id]?.[response.data.agency_id]?.[response.data.patient_id]?.[res.fields.id];

                                    const checkboxValues = parseSerializedData(checkboxData);
                                    return Array.isArray(checkboxValues)
                                        ? checkboxValues
                                            .filter(item => item !== null && item !== '' && item !== 'null')
                                            .map(item => `<li>${item}</li>`)
                                            .join('')
                                        : '';
                                })()
                                : response.patientSubmitData?.[response.data.form_id]?.[response.data.agency_id]?.[response.data.patient_id]?.[res.fields ? res.fields.id : ''] || ''
                            }
                                        </span>
                                        ${fieldHtml}
                                    </dd>
                                </div>
                            </div>`;
                    });

                    const hasTemplateId = response.data.template_by_id && response.data.template_by_id.id;

                    const cardFooterHtml = hasTemplateId ? `
                        <div class="card-footer">
                            <i class="fa fa-download download-icon disabled-icon formdownloadbtn${response.data.form_id}"
                                data-form-id="${response.data.form_id}"
                                data-patient-id="${response.data.patient_id}"
                                data-agency-id="${response.data.agency_id}"
                                data-template-id="${response.data.template_by_id.id ?? ''}"
                                title="All fields are required" id="downloadIcon"></i>
                        </div>` : '';

                    var newFormHtml = `
                    <div class="card border-bottom agencyAllFormList" data-id="${response.data.id}" data-f-id="${response.data.form_id}">
                        <div class="card-header" role="tab" id="heading-${response.data.id}">
                            <h6 class="mb-0">
                                <a data-toggle="collapse" href="#collapse-${response.data.id}" aria-expanded="false" aria-controls="collapse-${response.data.id}" class="">${response.data.forms.title}</a>
                            </h6>
                        </div>
                        <form id="dynamicAgencyForm_${response.data.id}" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="${_CSRF_TOKEN}">
                                    <input type="hidden" id="form_id_${response.data.form_id}" name="form_id" value="${response.data.form_id}">
                                    <input type="hidden" id="patient_i_${response.data.form_id}" name="patient_id" value="${response.data.patient_id}">
                                    <input type="hidden" id="agency_id_${response.data.form_id}" name="agency_id"
                                        value="${response.data.agency_id}">
                        <div id="collapse-${response.data.id}" class="collapse" role="tabpanel" aria-labelledby="heading-${response.data.id}" data-parent="#accordion-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <a class="btn btn-info btn-fw btn-sm pull-right edit-form-btn"
                                            href="javascript:void(0)" data-fid="${response.data.form_id}" data-aid="${response.data.agency_id}" data-pid="${response.data.patient_id}">Edit
                                        </a>
                                        <button type="submit"
                                                        class="btn btn-info btn-fw btn-sm pull-right save-form-btn ml-2"
                                                        data-id="${response.data.id}" data-fid="${response.data.form_id}"
                                                        style="display:none;">Save</button>
                                                    <a class="btn btn-secondary btn-fw btn-sm pull-right cancel-form-btn"
                                                        href="javascript:void(0)" style="display:none;">Cancel</a>
                                    </div>
                                     <div class="col-md-12">
                                    <div class="field-container">
                                            <div class="label-edit-container">
                                                <dt>Doctor Name</dt>
                                            </div>
                                            <dd>
                                                <span id="dynamic-field-${response.data.form_id}-doctor_name"
                                                    class="flex-grow-1 dynamic-form-value${response.data.form_id}">
                                                    ${response.data.doctors.full_name}
                                                </span>
                                                <input type="hidden" name="formId" id="formId" value="${response.data.id}">
                                                <select name="doctor_id" id="input-field-${response.data.form_id}-doctor_name" class="form-control" style="display:none;">
                                                ${doctorOptions}
                                                </select>
                                            </dd>
                                        </div>
                                    </div>
                                        ${htmlRes}
                                </div>
                            </div>
                            ${cardFooterHtml}
                        </div>
                         </form>
                    </div>`;
                    $("#accordion-4").append(newFormHtml);

                    // checkFieldsAndToggleIcon(response.data.form_id);
                    $(".no-data-div").hide();
                    $("#addAgencyFormModal").modal("hide");
                    $("#agencyFormAdd")[0].reset();
                    $("#addAgencyForm").prop("disabled", false);

                    toastr.success(response.msg);

                }
            },
            error: function (error) {
                $("#addAgencyForm").prop("disabled", false);
                toastr.error(error.responseJSON.errors);
            }
        });
    });
    $("body").delegate(".edit-form-btn", "click", function () {
        var formId = $(this).data('fid');
        $(this).hide();
        $(this).siblings('.save-form-btn').show();
        $(this).siblings('.cancel-form-btn').show();
        console.log(formId);
        $('.dynamic-form-value' + formId).each(function () {
            var spanId = $(this).attr('id');
            var inputId = spanId.replace('dynamic-field', 'input-field');

            $(this).hide();
            $('#' + inputId).show();
            $('.checkInput' + formId).show();
        });
    });

    $("body").delegate(".cancel-form-btn", "click", function () {
        var formId = $(this).siblings('.edit-form-btn').data('fid');

        $(this).hide();
        $(this).siblings('.save-form-btn').hide();
        $(this).siblings('.edit-form-btn').show();

        $('.dynamic-form-value' + formId).each(function () {
            var spanId = $(this).attr('id');
            var inputId = spanId.replace('dynamic-field', 'input-field');
            $(this).show();
            $('#' + inputId).hide();
            $('.checkInput' + formId).hide();

        });
    });

    $(document).on('click', '.save-form-btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var fid = $(this).data('fid');
        var formAppend = $('#dynamicAgencyForm_' + id)[0];
        var formData = new FormData(formAppend);
        formData.append('_token', _CSRF_TOKEN)

        $.ajax({
            url: '/store-patient-custom-data',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var formId = response.form_id;
                toastr.success('Data saved successfully');
                $('.save-form-btn, .cancel-form-btn').hide();
                $('.edit-form-btn').show();
                $('#form-fields-container').empty();
                $('.dynamic-form-value' + response.form_id).each(function () {
                    var spanId = $(this).attr('id');
                    var inputId = spanId.replace('dynamic-field', 'input-field');
                    $(this).show();
                    $('.checkInput' + fid).hide();
                    $('#' + inputId).hide();
                    $('#dynamic-field-' + formId + '-doctor_name').text(response.responses.doctor_id.doctor_name);
                });

                $.each(response.responses, function (fieldId, fieldResponse) {
                    if (fieldResponse.type === 'checkbox') {
                        var value = (fieldResponse && fieldResponse.data && fieldResponse.data.value) || '';
                        const checkboxValues = parseSerializedData(value);

                        const bulletPoints = Array.isArray(checkboxValues)
                            ? checkboxValues
                                .filter(item => item !== null && item !== '' && item !== 'null')
                                .map(item => `<li>${item}</li>`)
                                .join('')
                            : '';
                        $('#dynamic-field-' + formId + '-' + fieldId).html(bulletPoints);
                    } else {
                        var value = (fieldResponse && fieldResponse.data && fieldResponse.data.value) || '';
                        $('#dynamic-field-' + formId + '-' + fieldId).text(value);
                    }

                });
                // checkFieldsAndToggleIcon(response.form_id);

            },

            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $('.agencyAllFormList').each(function () {
        var formId = $(this).data('f-id');
        checkFieldsAndToggleIcon(formId);
    });

});

function checkFieldsAndToggleIcon(formId) {
    var allFieldsFilled = 0;
    $(`.dynamic-form-value${formId}`).each(function () {
        var value = $(this).html();
        if ($.trim(value) == "") {
            allFieldsFilled++;
        }
    });

    if (allFieldsFilled > 0) {
        $(`.formdownloadbtn${formId}`).addClass('disabled-icon');
    } else {
        $(`.formdownloadbtn${formId}`).removeClass('disabled-icon');
    }
}

$(document).on("click", "#downloadIcon", function () {
    var form_id = $(this).data("form-id");
    var patient_id = $(this).data("patient-id");
    var template_id = $(this).data("template-id");
    var agency_id = $(this).data("agency-id");

    $.ajax({
        url: getTemplateData,
        type: "get",
        data: {
            template_id: template_id,
            form_id: form_id,
            patient_id: patient_id,
            agency_id: agency_id,
        },
        xhrFields: {
            responseType: 'blob'  // Ensures the response is treated as a Blob
        },
        success: function (response) {
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var formName = $('#formName' + form_id).val();

            link.download = formName + ".pdf";
            link.click();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});

function generateSelectField(field, patientSubmitData, form_id, agency_id, patient_id) {
    let options = JSON.parse(field.options);
    let selectedValue = patientSubmitData[form_id]?.[agency_id]?.[patient_id]?.[field.id] || '';
    let optionsHtml = options.map(option => `
        <option value="${option}" ${selectedValue === option ? 'selected' : ''}>
            ${option}
        </option>
    `).join('');
    return `
        <select style="display:none;"
                name="fields[${field.id}]"
                id="input-field-${form_id}-${field.id}"
                class="form-control">
            <option value="">Select an option</option>
            ${optionsHtml}
        </select>
    `;
}

function generateRadioField(field, patientSubmitData, form_id, agency_id, patient_id) {
    let options = JSON.parse(field.options);
    let checkedValue = patientSubmitData?.[form_id]?.[agency_id]?.[patient_id]?.[field.id];

    return options.map((option, index) => `
        <div class="form-check">
            <input type="radio" style="display:none;"
                   name="fields[${field.id}]"
                   id="input-field-${form_id}-${field.id}-${index}"
                   class="form-check-input checkInput${form_id} ml-1"
                   value="${option}"
                   ${checkedValue === option ? 'checked' : ''}>
            <label class="form-check-label checkInput${form_id}"
                   style="display:none;"
                   for="input-field-${form_id}-${field.id}-${index}">
                ${option}
            </label>
        </div>
    `).join('');
}

function generateCheckboxField(field, patientSubmitData, form_id, agency_id, patient_id) {
    let options = JSON.parse(field.options);
    let existingValues = parseSerializedData(
        patientSubmitData?.[form_id]?.[agency_id]?.[patient_id]?.[field.id] || ''
    );
    return options.map((option, index) => `
        <div class="form-check">
        <input type="hidden"
        name="fields[${field.id}][]"
        value="null">
            <input type="checkbox" style="display:none;"
                   name="fields[${field.id}][]"
                   id="input-field-${form_id}-${field.id}-${index}"
                   class="form-check-input checkInput${form_id} ml-1"
                   value="${option}"
                   ${existingValues.includes(option) ? 'checked' : ''}>
            <label class="form-check-label checkInput${form_id}"
                   style="display:none;"
                   for="input-field-${form_id}-${field.id}-${index}">
                ${option}
            </label>
        </div>
    `).join('');
}

function generateTextField(field, patientSubmitData, form_id, agency_id, patient_id) {
    // let value = patientSubmitData[form_id][agency_id][patient_id][field.id] || '';
    let value = patientSubmitData?.[form_id]?.[agency_id]?.[patient_id]?.[field.id] || '';
    return `
        <input type="${field.type}"
               name="fields[${field.id}]"
               id="input-field-${form_id}-${field.id}"
               class="form-control"
               style="display:none;"
               maxlength="${field.set_character_limit ? field.set_character_limit : ''}"
               value="${value}">
    `;
}
function generateTextareaField(field, patientSubmitData, form_id, agency_id, patient_id) {
    let value = patientSubmitData?.[form_id]?.[agency_id]?.[patient_id]?.[field.id] || '';

    return `
        <textarea name="fields[${field.id}]"
            id="input-field-${form_id}-${field.id}"
            class="form-control"
            maxlength="${field.set_character_limit ? field.set_character_limit : ''}"
            style="display:none; height: 100px;">${value}</textarea>
    `;
}

function parseSerializedData(serializedString) {
    if(!serializedString){
        return [];
    }
    const matches = serializedString.match(/s:\d+:"([^"]+)"/g);
    if (!matches) {
        return [];
    }
    const items = matches.map(item => item.match(/"([^"]+)"/)[1]);
    return items;
}