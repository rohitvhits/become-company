$(document).ready(function () {
    var currentPortalFieldId = null;
    var currentPortalAgencyId = null;
    var currentPortalFieldType = null;

    function formatPortalDisplayValue(value, fieldType) {
        if (!value || value === '') return '-';
        if (fieldType === 'date') {
            var parts = value.split('-');
            if (parts.length === 3) {
                return parts[1] + '/' + parts[2] + '/' + parts[0];
            }
        } else if (fieldType === 'time') {
            var timeParts = value.split(':');
            if (timeParts.length >= 2) {
                var hours = parseInt(timeParts[0]);
                var minutes = timeParts[1];
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                if (hours === 0) hours = 12;
                return (hours < 10 ? '0' + hours : hours) + ':' + minutes + ' ' + ampm;
            }
        }
        return value;
    }

    $(document).on('click', '.edit-portal-field-btn', function () {
        var fieldId = $(this).data('field-id');
        var fieldLabel = $(this).data('field-label');
        var fieldType = $(this).data('field-type');
        var fieldOptions = $(this).data('field-options');
        var fieldLimit = $(this).data('field-limit');
        var fieldValue = $(this).data('field-value');
        var agencyId = $(this).data('agency-id');

        currentPortalFieldId = fieldId;
        currentPortalAgencyId = agencyId;
        currentPortalFieldType = fieldType;

        $('#portalFieldModalLabel').text(fieldLabel);
        $('#portalFieldInputLabel').html(fieldLabel + '<span class="error">*</span>:');
        $('#portal_field_error').text('');

        var container = $('#portalFieldInputContainer');
        container.empty();

        if (fieldType === 'select') {
            var options = typeof fieldOptions === 'string' ? JSON.parse(fieldOptions) : fieldOptions;
            var selectHtml = '<select class="form-control" id="portalFieldInput"><option value="">Select an option</option>';
            if (Array.isArray(options)) {
                options.forEach(function (opt) {
                    selectHtml += '<option value="' + opt + '"' + (fieldValue == opt ? ' selected' : '') + '>' + opt + '</option>';
                });
            }
            selectHtml += '</select>';
            container.html(selectHtml);
        } else if (fieldType === 'radio') {
            var options = typeof fieldOptions === 'string' ? JSON.parse(fieldOptions) : fieldOptions;
            var radioHtml = '';
            if (Array.isArray(options)) {
                options.forEach(function (opt, idx) {
                    radioHtml += '<div class="form-check"><input type="radio" name="portalFieldInput" class="form-check-input" value="' + opt + '"' + (fieldValue == opt ? ' checked' : '') + ' id="portalRadio' + idx + '"><label class="form-check-label" for="portalRadio' + idx + '">' + opt + '</label></div>';
                });
            }
            container.html(radioHtml);
        } else if (fieldType === 'textarea') {
            container.html('<textarea class="form-control" id="portalFieldInput" style="height:100px;" maxlength="' + (fieldLimit || '') + '">' + (fieldValue || '') + '</textarea>');
        } else if (fieldType === 'information') {
            container.html('<p>' + (fieldValue || '-') + '</p>');
        } else {
            var inputHtml = '<input type="' + fieldType + '" class="form-control" id="portalFieldInput" value="' + (fieldValue || '') + '" maxlength="' + (fieldLimit || '') + '"';
            if (fieldType === 'number' && fieldLimit) {
                inputHtml += ' oninput="if(this.value.length > ' + fieldLimit + ') this.value = this.value.slice(0, ' + fieldLimit + ');"';
            }
            inputHtml += '>';
            container.html(inputHtml);
        }

        $('#portalFieldEditModal').modal('show');
    });

    $('#savePortalFieldBtn').on('click', function () {
        var fieldValue;
        var fieldType = $('#portalFieldInputContainer').find('input[type="radio"]').length > 0 ? 'radio' : 'other';

        if (fieldType === 'radio') {
            fieldValue = $('input[name="portalFieldInput"]:checked').val();
        } else {
            fieldValue = $('#portalFieldInput').val();
        }

        if (fieldValue === undefined || fieldValue === null || fieldValue === '') {
            $('#portal_field_error').text('This field is required.');
            return;
        }

        var formData = new FormData();
        formData.append('_token', _CSRF_TOKEN);
        formData.append('patient_id', _RECORD_ID);
        formData.append('agency_id', currentPortalAgencyId);
        formData.append('fields[' + currentPortalFieldId + ']', fieldValue);

        $.ajax({
            url: '/save-patient-custom-data',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success('Data saved successfully');
                var displayValue = formatPortalDisplayValue(fieldValue, currentPortalFieldType);
                $('#portal-field-' + currentPortalFieldId).text(displayValue);
                $('.edit-portal-field-btn[data-field-id="' + currentPortalFieldId + '"]').data('field-value', fieldValue);
                $('#portalFieldEditModal').modal('hide');
            },
            error: function (xhr) {
                toastr.error('An error occurred while saving.');
            }
        });
    });
});
