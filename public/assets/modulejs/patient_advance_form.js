$(document).ready(function () {
    var originalFormData = null;

    $('.edit-advance-form-btn').on('click', function () {
        // Store original values before editing
        var form = $('#dynamicAgencyForm')[0];
        originalFormData = {};
        $(form).find('input, select, textarea').each(function () {
            var el = $(this);
            var name = el.attr('name');
            if (!name) return;
            var id = el.attr('id') || name;

            if (el.is(':checkbox') || el.is(':radio')) {
                originalFormData[id] = el.prop('checked');
            } else {
                originalFormData[id] = el.val();
            }
        });

        $('.hidden-fields').show();
        $('.edit-advance-form-btn').hide();
        $('.dynamic-form-value').hide();
        $('.save-advance-form-btn, .cancel-advance-form-btn').show();
    });

    $('.cancel-advance-form-btn').on('click', function () {
        // Restore original values
        if (originalFormData) {
            var form = $('#dynamicAgencyForm')[0];
            $(form).find('input, select, textarea').each(function () {
                var el = $(this);
                var name = el.attr('name');
                if (!name) return;
                var id = el.attr('id') || name;

                if (el.is(':checkbox') || el.is(':radio')) {
                    el.prop('checked', originalFormData[id] || false);
                } else if (id in originalFormData) {
                    el.val(originalFormData[id]);
                }
            });
            originalFormData = null;
        }

        $('.hidden-fields').hide();
        $('.edit-advance-form-btn').show();
        $('.dynamic-form-value').show();
        $('.save-advance-form-btn, .cancel-advance-form-btn').hide();
    });

    $('.save-advance-form-btn').on('click', function (e) {
        e.preventDefault();

        var formAppend= $('#dynamicAgencyForm')[0];
        var formData = new FormData(formAppend);
        formData.append('_token',_CSRF_TOKEN)

        $.ajax({
            url: '/save-patient-custom-data',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                originalFormData = null;
                toastr.success('Data saved successfully');
                $.each(response.responses, function (fieldId, fieldResponse) {
                    $('#dynamic-field-' + fieldId).text(fieldResponse.data.value);
                });
                $('.hidden-fields').hide();
                $('.edit-advance-form-btn').show();
                $('.dynamic-form-value').show();
                $('.save-advance-form-btn, .cancel-advance-form-btn').hide();
            },
            error: function (xhr) {
                alert('An error occurred while saving the form.');
            }
        });
    });
});
