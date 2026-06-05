$(document).ready(function () {
    let availableFields = [];
    let availableFilterFields = [];
    let filterDropdownOptions = {};
    let selectedFields = [];
    let filters = [];
    let sorting = [];
    let grouping = [];
    let modulePreviouslyLoaded = false;

    function getCsrfToken() {
        return _CSRF_TOKEN;
    }

    $('.report-tab').on('click', function () {
        const targetTab = $(this).data('tab');
        switchToTab(targetTab);
    });

    $('.next-tab').on('click', function () {
        const nextTab = $(this).data('next');
        if (validateCurrentTab()) {
            switchToTab(nextTab);
        }
    });

    $('.prev-tab').on('click', function () {
        const prevTab = $(this).data('prev');
        switchToTab(prevTab);
    });

    function switchToTab(tabName) {
        $('.report-tab').removeClass('active');
        $(`.report-tab[data-tab="${tabName}"]`).addClass('active');

        $('.tab-pane').removeClass('active');
        $(`#tab-${tabName}`).addClass('active');

        $('.tab-content-wrapper').animate({ scrollTop: 0 }, 300);

        let currentTabIndex = getTabIndex(tabName);
        $('.report-tab').each(function (index) {
            if (index < currentTabIndex) {
                $(this).addClass('completed');
            } else if (index > currentTabIndex) {
                $(this).removeClass('completed');
            }
        });
    }

    function getTabIndex(tabName) {
        const tabs = ['basic-info', 'fields', 'filters', 'sorting', 'preview'];
        return tabs.indexOf(tabName);
    }

    function validateCurrentTab() {

        clearValidationErrors();
        const currentTab = $('.tab-pane.active').attr('id');
        if (currentTab === 'tab-basic-info' || currentTab === 'todo-section') {

            let isValid = true;
            const reportName = $('#report_name').val().trim();
            const moduleName = $('#module_name').val();
            const subModuleVisible = $('#sub-module-group').is(':visible');
            const subModuleName = $('#sub_module_name').val();

            if (!reportName) {
                showFieldError('#report_name', 'Report name is required');
                isValid = false;
            }

            if (!moduleName) {
                showFieldError('#module_name', 'Module is required');
                isValid = false;
            }

            if (subModuleVisible && !subModuleName) {
                showFieldError('#sub_module_name', 'Sub module is required');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire('Validation Error', 'Please complete Basic Info section', 'warning');
                return false;
            }
        }

        if (currentTab === 'tab-fields') {
            updateSelectedFields();
            if (selectedFields.length === 0) {
                Swal.fire('Validation Error', 'Please select at least one field', 'warning');
                return false;
            }
        }

        if (currentTab === 'tab-filters') {
            if ($('.filter-row').length === 0) {
                Swal.fire('Validation Error', 'Please add at least one filter before continuing.', 'warning');
                return false;
            }

            let hasFilterValue = false;
            $('.filter-row').each(function () {
                const $row = $(this);
                const operator = $row.find('.filter-operator').val();

                // Operators that don't require a value input
                if (operator === 'is_null' || operator === 'is_not_null') {
                    hasFilterValue = true;
                    return false; // break
                }

                // Range inputs (between / not_between)
                const fromVal = $row.find('.filter-value-from').val();
                const toVal = $row.find('.filter-value-to').val();
                if ((fromVal && fromVal.trim() !== '') || (toVal && toVal.trim() !== '')) {
                    hasFilterValue = true;
                    return false; // break
                }

                // Multi-select
                const $multi = $row.find('.filter-multiselect');
                if ($multi.length && $multi.val() && $multi.val().length > 0) {
                    hasFilterValue = true;
                    return false; // break
                }

                // Regular text / single-select (exclude hidden inputs used for null operators)
                const $valueInput = $row.find('.filter-value:not([type="hidden"])');
                if ($valueInput.length && $valueInput.val() && $valueInput.val().toString().trim() !== '') {
                    hasFilterValue = true;
                    return false; // break
                }
            });

            if (!hasFilterValue) {
                Swal.fire('Validation Error', 'Please add at least one filter before continuing.', 'warning');
                return false;
            }
        }

        return true;
    }

    function showFieldError(selector, message) {
        const $field = $(selector);
        $field.addClass('is-invalid');

        if ($field.closest('.form-group').find('.validation-error').length === 0) {
            $field.closest('.form-group').append(
                `<div class="validation-error" style="color:#e74a3b;font-size:12px;margin-top:5px;">
                    <i class="mdi mdi-alert-circle-outline"></i> ${message}
                </div>`
            );
        }
    }

    /**
     * Enable/disable the Patient-only fields based on the selected module.
     * Fields: Status Wise Count, Enable Header Image & Row Colors.
     */
    function togglePatientOnlyFields(moduleName) {
        var isPatient = (moduleName === 'Patient');
        var $fields = $('#status_wise_count, #enable_header_and_color');

        if (isPatient) {
            $fields.prop('disabled', false)
                   .closest('.form-group').removeClass('patient-field-disabled');
        } else {
            $fields.prop('disabled', true)
                   .val('no')
                   .closest('.form-group').addClass('patient-field-disabled');
        }
    }

    $('#module_name').on('change', function () {
        let moduleName = $(this).val();

        if (moduleName) {
            const reportName = $('#report_name').val();

            loadSubModules(moduleName);
            $('#sub-module-group').show();
        } else {
            $('#sub-module-group').hide();
        }

        togglePatientOnlyFields(moduleName);
    });

    $('#sub_module_name').on('change', function () {
        let moduleName = $('#module_name').val();
        let subModuleName = $(this).val();

        loadFields(subModuleName || moduleName);
    });

    function loadSubModules(moduleName) {
    var url = SUBMODULES_URL_TEMPLATE.split('__MODULE__').join(moduleName);

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',   // ⭐ ADD THIS LINE
        success: function (response) {

            let $select = $('#sub_module_name');
            $select.html('<option value="">Select Sub-Module</option>');

            $.each(response, function (name, modelClass) {
                var valueName = name;
                if (name == 'Patient') {
                    name = 'Portal Records';
                } else if (name == 'DocumentPatient') {
                    name = 'Documents';
                } else if (name == 'HhaOtherComplience') {
                    name = 'Other Compliance';
                }
                $select.append(`<option value="${valueName}">${name}</option>`);
            });

            if (typeof existingReport !== 'undefined' && existingReport.sub_module_name) {
                $select.val(existingReport.sub_module_name);
                loadFields(existingReport.sub_module_name);
            }
        }
    });
}

    function showLoading(message = 'Loading...') {
        if ($('#loading-overlay').length === 0) {
            $('body').append(`
                <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                     background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center;
                     justify-content: center;">
                    <div style="background: #fff; padding: 30px; border-radius: 10px; text-align: center;">
                        <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #1e9ff2;
                             border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;
                             margin: 0 auto 15px;"></div>
                        <p style="margin: 0; font-weight: 600; color: #2d3748;">${message}</p>
                    </div>
                </div>
            `);
        }
    }

    function hideLoading() {
        $('#loading-overlay').fadeOut(300, function () {
            $(this).remove();
        });
    }

    function loadFields(moduleName) {
        showLoading('Loading fields...');

        const postData = {
            _token:          getCsrfToken(),
            module_name:     $('#module_name').val(),
            sub_module_name: moduleName
        };

        const dropdownDeferred = $.Deferred();
        $.ajax({
            url: FILTER_DROPDOWN_OPTIONS_URL,
            type: 'POST',
            data: postData,
            'dataType':'json',
            success: function (data) { dropdownDeferred.resolve(data || {}); },
            error:   function ()     { dropdownDeferred.resolve({});         }
        });

        $.when(
            $.ajax({ url: FIELDS_URL,       type: 'POST', data: postData,dataType: 'json'}),
            $.ajax({ url: FILTER_FIELDS_URL, type: 'POST', data: postData,dataType: 'json' }),
            dropdownDeferred.promise()
        ).done(function (displayFieldsResponse, filterFieldsResponse, dropdownData) {

            if (modulePreviouslyLoaded) {
                filters = [];
                sorting = [];
                selectedFields = [];
                $('#filters-list').empty();
                $('#sorting-list').empty();
            }

            availableFields       = displayFieldsResponse[0];
            availableFilterFields = filterFieldsResponse[0];
            filterDropdownOptions = dropdownData || {};

            renderFieldsCheckboxes();

            if (!modulePreviouslyLoaded) {
                if (typeof existingReport !== 'undefined' && existingReport.fields) {
                    preSelectFields(existingReport.fields);
                }

                if (typeof existingReport !== 'undefined' && existingReport.filters) {
                    loadExistingFilters(existingReport.filters);
                }

                if (typeof existingReport !== 'undefined' && existingReport.sorting) {
                    loadExistingSorting(existingReport.sorting);
                }

                modulePreviouslyLoaded = true;
            }

            hideLoading();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Fields Loaded',
                    text: `${availableFields.length} fields available`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }).fail(function (xhr) {
            hideLoading();
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error!', 'Failed to load fields', 'error');
            } else {
                alert('Failed to load fields');
            }
        });
    }


    var GROUP_META = {
        computed:             { label: 'Computed Fields',            icon: 'mdi-function-variant' },
        users:                { label: 'Created User Info',          icon: 'mdi-account-outline' },
        assign_user:          { label: 'Assign NyBest User',         icon: 'mdi-account-tie-outline' },
        agency:               { label: 'Agency',                     icon: 'mdi-office-building-outline' },
        patient_master:       { label: 'Patient',                    icon: 'mdi-account-heart-outline' },
        patient_demographic:  { label: 'Patient Demographic Details', icon: 'mdi-card-account-details-outline' },
        document_patient:     { label: 'Document Patient',           icon: 'mdi-file-document-outline' },
    };

    function tableKeyToLabel(key) {
        if (GROUP_META[key]) return GROUP_META[key].label;
        // Fallback: snake_case → Title Case
        return key.split('_').map(function (w) {
            return w.charAt(0).toUpperCase() + w.slice(1);
        }).join(' ');
    }

    // Build filter field <select> options as a flat list.
    function buildFilterFieldOptions(fieldsForFilter) {
        var moduleName    = $('#module_name').val() || '';
        var subModuleName = $('#sub_module_name').val() || '';
        var isPatient     = (moduleName === 'Patient' || subModuleName === 'Patient');

        var html = '<option value="">Select Field</option>';

        // Sort fields alphabetically by label
        var sortedFields = fieldsForFilter.slice().sort(function(a, b) {
            return a.label.localeCompare(b.label);
        });

        sortedFields.forEach(function (f) {
            var label = f.label;
            // For Patient module: full_name doubles as the record identifier
            if (isPatient && (f.name === 'full_name' || f.name === 'patient_master.full_name')) {
                label = 'Record Name (Patient Name)';
            }
            html += '<option value="' + f.name + '" data-type="' + f.type + '">' + label + '</option>';
        });

        return html;
    }

    function tableKeyToIcon(key) {
        if (GROUP_META[key]) return GROUP_META[key].icon;
        return 'mdi-table';
    }

    function renderFieldsCheckboxes() {
        // ----- Group fields by their `table` property -----
        var groups = {};
        availableFields.forEach(function (field) {
            var key = field.table || 'other';
            if (!groups[key]) { groups[key] = []; }
            groups[key].push(field);
        });

        // ----- Sort fields within each group alphabetically by label -----
        Object.keys(groups).forEach(function (key) {
            groups[key].sort(function (a, b) {
                return a.label.toLowerCase().localeCompare(b.label.toLowerCase());
            });
        });

        // ----- Sort group keys alphabetically; always put 'computed' last -----
        var sortedKeys = Object.keys(groups).sort(function (a, b) {
            if (a === 'computed') return 1;
            if (b === 'computed') return -1;
            return a.localeCompare(b);
        });

        // ----- Build HTML -----
        var html = `
            <div class="field-selection-controls">
                <div class="fsc-left">
                    <button type="button" class="btn btn-sm btn-primary" id="select-all-fields">
                        <i class="mdi mdi-check-all"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="deselect-all-fields">
                        <i class="mdi mdi-close"></i> Clear All
                    </button>
                </div>
                <div class="fsc-center">
                    <input type="text" id="field-search" class="form-control form-control-sm"
                           placeholder="&#128269; Search fields...">
                </div>
                <div class="fsc-right">
                    <span id="field-count">
                        <i class="mdi mdi-checkbox-marked-circle-outline"></i> 0 fields selected
                    </span>
                </div>
            </div>
        `;

        var fieldIndex = 0;
        sortedKeys.forEach(function (key) {
            var groupFields = groups[key];
            var groupLabel  = tableKeyToLabel(key);
            var icon        = tableKeyToIcon(key);
            var count       = groupFields.length;

            html += `
            <div class="field-group" data-group="${key}">
                <div class="field-group-header">
                    <span class="field-group-title">
                        <i class="mdi ${icon}"></i> ${groupLabel}
                    </span>
                    <small class="field-group-count">${count} field${count !== 1 ? 's' : ''}</small>
                </div>
                <div class="fields-grid">
            `;

            groupFields.forEach(function (field) {
                html += `
                    <div class="field-item">
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox"
                                   value="${field.name}" id="field_${fieldIndex}"
                                   data-label="${field.label}" data-type="${field.type}">
                            <label class="form-check-label" for="field_${fieldIndex}">
                                ${field.label}
                            </label>
                        </div>
                    </div>
                `;
                fieldIndex++;
            });

            html += `</div></div>`;
        });

        $('#fields-container').html(html);

        // ----- Live search: filter items and collapse empty groups -----
        $('#field-search').on('input', function () {
            var term = $(this).val().toLowerCase().trim();
            if (term === '') {
                $('.field-item').show();
                $('.field-group').show();
                return;
            }
            $('.field-group').each(function () {
                var visible = 0;
                $(this).find('.field-item').each(function () {
                    var label = $(this).find('label').text().toLowerCase();
                    if (label.includes(term)) { $(this).show(); visible++; }
                    else { $(this).hide(); }
                });
                $(this).toggle(visible > 0);
            });
        });

        // ----- Field checkbox change -----
        $('.field-checkbox').on('change', function () {
            updateSelectedFields();
            updateGroupingOptions();
            updateFieldCount();
        });

        // ----- Select All (respects active search filter) -----
        $('#select-all-fields').on('click', function () {
            $('.field-item:visible .field-checkbox').prop('checked', true);
            updateSelectedFields();
            updateGroupingOptions();
            updateFieldCount();
        });

        // ----- Deselect All -----
        $('#deselect-all-fields').on('click', function () {
            $('.field-checkbox').prop('checked', false);
            updateSelectedFields();
            updateGroupingOptions();
            updateFieldCount();
        });
    }

    function updateFieldCount() {
        const count = $('.field-checkbox:checked').length;
        const total = $('.field-checkbox').length;
        $('#field-count').html(`<i class="mdi mdi-checkbox-marked-circle-outline"></i> ${count} of ${total} fields selected`);
    }

    function updateSelectedFields() {
        selectedFields = [];
        $('.field-checkbox:checked').each(function () {
            selectedFields.push({
                name: $(this).val(),
                label: $(this).data('label'),
                type: $(this).data('type')
            });
        });
    }

    function updateGroupingOptions() {
        let $select = $('#grouping_field');
        $select.html('<option value="">Select field to group by</option>');

        selectedFields.forEach(function (field) {
            $select.append(`<option value="${field.name}">${field.label}</option>`);
        });
    }

    function preSelectFields(fields) {
        fields.forEach(function (field) {
            let fieldName = typeof field === 'string' ? field : field.name;
            $(`.field-checkbox[value="${fieldName}"]`).prop('checked', true);
        });
        updateSelectedFields();
        updateGroupingOptions();
        updateFieldCount();
    }

    $('#add-filter').on('click', function () {
        addFilterRow();
    });

    function getOperatorsByFieldType(fieldType) {
        const operators = {
            text: [
                { value: 'equals', label: '= (Equals)' },
                { value: 'not_equals', label: '!= (Not Equals)' },

                { value: 'contains', label: 'Contains' },
                { value: 'starts_with', label: 'Starts With' },
                { value: 'ends_with', label: 'Ends With' },

                { value: 'greater_than', label: '> (Greater Than)' },
                { value: 'greater_than_equal', label: '>= (Greater Than or Equal)' },

                { value: 'less_than', label: '< (Less Than)' },
                { value: 'less_than_equal', label: '<= (Less Than or Equal)' },

                { value: 'between', label: 'Between' },
                { value: 'not_between', label: 'Not Between' },

                { value: 'in', label: 'In (Multiple)' },
                { value: 'not_in', label: 'Not In ()' },

                { value: 'today', label: 'Today' },
                { value: 'yesterday', label: 'Yesterday' },
                { value: 'tomorrow', label: 'Tomorrow' },

                { value: 'this_week', label: 'This Week' },
                { value: 'last_week', label: 'Last Week' },

                { value: 'this_month', label: 'This Month' },
                { value: 'last_month', label: 'Last Month' },

                { value: 'this_year', label: 'This Year' },
                { value: 'last_year', label: 'Last Year' },

                { value: 'is_null', label: 'Is Null' },
                { value: 'is_not_null', label: 'Is Not Null' }
            ],
            numeric: [
                { value: 'equals', label: '= (Equals)' },
                { value: 'not_equals', label: '!= (Not Equals)' },

                { value: 'greater_than', label: '> (Greater Than)' },
                { value: 'greater_than_equal', label: '>= (Greater Than or Equal)' },

                { value: 'less_than', label: '< (Less Than)' },
                { value: 'less_than_equal', label: '<= (Less Than or Equal)' },

                { value: 'between', label: 'Between' },
                { value: 'not_between', label: 'Not Between' },

                { value: 'in', label: 'In (Multiple)' },
                { value: 'not_in', label: 'Not In ()' },

                { value: 'today', label: 'Today' },
                { value: 'yesterday', label: 'Yesterday' },
                { value: 'tomorrow', label: 'Tomorrow' },

                { value: 'this_week', label: 'This Week' },
                { value: 'last_week', label: 'Last Week' },

                { value: 'this_month', label: 'This Month' },
                { value: 'last_month', label: 'Last Month' },

                { value: 'this_year', label: 'This Year' },
                { value: 'last_year', label: 'Last Year' },

                { value: 'is_null', label: 'Is Null' },
                { value: 'is_not_null', label: 'Is Not Null' }
            ],
            date: [
                { value: 'equals', label: '= (Equals)' },
                { value: 'not_equals', label: '!= (Not Equals)' },

                { value: 'greater_than', label: '> (Greater Than)' },
                { value: 'greater_than_equal', label: '>= (Greater Than or Equal)' },

                { value: 'less_than', label: '< (Less Than)' },
                { value: 'less_than_equal', label: '<= (Less Than or Equal)' },

                { value: 'between', label: 'Between' },
                { value: 'not_between', label: 'Not Between' },

                { value: 'in', label: 'In (Multiple)' },
                { value: 'not_in', label: 'Not In ()' },

                { value: 'today', label: 'Today' },
                { value: 'yesterday', label: 'Yesterday' },
                { value: 'tomorrow', label: 'Tomorrow' },

                { value: 'this_week', label: 'This Week' },
                { value: 'last_week', label: 'Last Week' },

                { value: 'this_month', label: 'This Month' },
                { value: 'last_month', label: 'Last Month' },

                { value: 'this_year', label: 'This Year' },
                { value: 'last_year', label: 'Last Year' },

                { value: 'is_null', label: 'Is Null' },
                { value: 'is_not_null', label: 'Is Not Null' }
            ]
        };

        return operators[fieldType] || operators.text;
    }

    function updateOperatorOptions($filterRow, fieldType, selectedOperator = null) {
        let operators = getOperatorsByFieldType(fieldType);
        const fieldName = $filterRow.find('.filter-field').val() || '';

        const $operatorSelect = $filterRow.find('.filter-operator');

        $operatorSelect.html('');
        operators.forEach(op => {
            const selected = selectedOperator === op.value ? 'selected' : '';
            $operatorSelect.append(`<option value="${op.value}" ${selected}>${op.label}</option>`);
        });

        const activeOperator = selectedOperator || $operatorSelect.val();
        if (activeOperator) {
            updateValueInput($filterRow, fieldType, activeOperator, fieldName);
        }
    }

    function updateValueInput($filterRow, fieldType, operator, fieldName) {

        const $valueContainer = $filterRow.find('.filter-value-container');

        const noValueOperators = [
            'today','yesterday','tomorrow',
            'this_week','last_week',
            'this_month','last_month',
            'this_year','last_year',
            'is_null','is_not_null'
        ];

        if (noValueOperators.includes(operator)) {

            let today = new Date();
            let from = null;
            let to = null;

            switch (operator) {

                case 'today':
                    from = to = today;
                    break;

                case 'yesterday':
                    from = to = new Date(today.setDate(today.getDate() - 1));
                    break;

                case 'tomorrow':
                    from = to = new Date(today.setDate(today.getDate() + 1));
                    break;

                case 'this_week':
                    // Monday as start of week (matches Carbon's default)
                    let dayOfWeek = today.getDay() || 7; // Convert Sunday=0 to 7
                    let startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - dayOfWeek + 1);
                    let endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(startOfWeek.getDate() + 6);
                    from = startOfWeek;
                    to = endOfWeek;
                    break;

                case 'last_week':
                    let dow = today.getDay() || 7;
                    let lastWeekStart = new Date(today);
                    lastWeekStart.setDate(today.getDate() - dow - 6);
                    let lastWeekEnd = new Date(lastWeekStart);
                    lastWeekEnd.setDate(lastWeekStart.getDate() + 6);
                    from = lastWeekStart;
                    to = lastWeekEnd;
                    break;

                case 'this_month':
                    from = new Date(today.getFullYear(), today.getMonth(), 1);
                    to = new Date();
                    break;

                case 'last_month':
                    from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    to = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;

                case 'this_year':
                    from = new Date(today.getFullYear(), 0, 1);
                    to = new Date();
                    break;

                case 'last_year':
                    from = new Date(today.getFullYear() - 1, 0, 1);
                    to = new Date(today.getFullYear() - 1, 11, 31);
                    break;

                case 'is_null':
                case 'is_not_null':
                    $valueContainer.html('<input type="hidden" class="filter-value" value="">');
                    return;
            }

            function format(d) {
                let yyyy = d.getFullYear();
                let mm = String(d.getMonth() + 1).padStart(2, '0');
                let dd = String(d.getDate()).padStart(2, '0');
                return yyyy + '-' + mm + '-' + dd;
            }

            $valueContainer.html(`
                <input type="hidden" class="filter-value-from" value="${format(from)}">
                <input type="hidden" class="filter-value-to" value="${format(to)}">
            `);

            return;
        }

        // DROPDOWN fields (e.g. agency.agency_name) — check filterDropdownOptions
        var dropdownOpts = fieldName ? (filterDropdownOptions[fieldName] || null) : null;

        var useDropdown = dropdownOpts && dropdownOpts.length > 0
            && ['equals', 'not_equals', 'in', 'not_in'].indexOf(operator) !== -1;

        if (useDropdown && (operator === 'equals' || operator === 'not_equals')) {
            var optionsHtml = '<option value="">-- Select --</option>';
            dropdownOpts.forEach(function (opt) {
                optionsHtml += '<option value="' + opt.value + '">' + opt.label + '</option>';
            });
            $valueContainer.html('<select class="form-control filter-value">' + optionsHtml + '</select>');
            return;
        }

        if (useDropdown && (operator === 'in' || operator === 'not_in')) {
            var optionsHtml = '';
            dropdownOpts.forEach(function (opt) {
                optionsHtml += '<option value="' + opt.value + '">' + opt.label + '</option>';
            });
            $valueContainer.html('<select class="form-control filter-value filter-multiselect" multiple>' + optionsHtml + '</select>');
            $valueContainer.find('.filter-multiselect').select2({
                placeholder: 'Select...',
                allowClear: true,
                width: '100%'
            });
            return;
        }

        // BETWEEN / NOT BETWEEN
        if (operator === 'between' || operator === 'not_between') {

            if (fieldType === 'date') {
                $valueContainer.html(`
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control filter-value-from" placeholder="MM/DD/YYYY">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control filter-value-to" placeholder="MM/DD/YYYY">
                        </div>
                    </div>
                `);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr($valueContainer.find('.filter-value-from')[0], {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'm/d/Y',
                        allowInput: true
                    });
                    flatpickr($valueContainer.find('.filter-value-to')[0], {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'm/d/Y',
                        allowInput: true
                    });
                }
            }
            else if (fieldType === 'numeric') {
                $valueContainer.html(`
                    <div class="row">
                        <div class="col-md-6">
                            <input type="number" class="form-control filter-value-from">
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control filter-value-to">
                        </div>
                    </div>
                `);
            }
            else {
                $valueContainer.html(`
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control filter-value-from">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control filter-value-to">
                        </div>
                    </div>
                `);
            }

            return;
        }

        // INPUT TYPE SELECT
        if (fieldType === 'date') {
            $valueContainer.html(`
                <input type="text" class="form-control filter-value" placeholder="MM/DD/YYYY">
            `);
            if (typeof flatpickr !== 'undefined') {
                flatpickr($valueContainer.find('.filter-value')[0], {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'm/d/Y',
                    allowInput: true
                });
            }
        } else {
            let inputType = fieldType === 'numeric' ? 'number' : 'text';
            $valueContainer.html(`
                <input type="${inputType}" class="form-control filter-value" placeholder="Enter value">
            `);
        }
    }

    function addFilterRow(filter = null) {
        let filterIndex = filters.length;

        const fieldsForFilter = availableFilterFields.length > 0 ? availableFilterFields : availableFields;

        let html = `
            <div class="filter-row card mb-2 p-3" data-index="${filterIndex}">
                <div class="row">
                    <div class="col-md-3">
                        <label>Field</label>
                        <select class="form-control filter-field" name="filters[${filterIndex}][field]">
                            ${buildFilterFieldOptions(fieldsForFilter)}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Operator</label>
                        <select class="form-control filter-operator" name="filters[${filterIndex}][operator]">
                            <option value="">Select Operator</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Value</label>
                        <div class="filter-value-container">
                            <input type="text" class="form-control filter-value" name="filters[${filterIndex}][value]" placeholder="Enter value">
                        </div>
                    </div>
                    <div class="col-md-1" style="display:flex;align-items:flex-end;justify-content:center;padding-bottom:1px;">
                        <button type="button" class="cr-btn-remove remove-filter" title="Remove filter">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#filters-list').append(html);
        refreshFilterDropdowns();

        const $filterRow = $(`.filter-row[data-index="${filterIndex}"]`);

        // Add change event for field selection
        $filterRow.find('.filter-field').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            const fieldType = selectedOption.data('type') || 'text';
            updateOperatorOptions($filterRow, fieldType);
            refreshFilterDropdowns();
        });

        $filterRow.find('.filter-operator').on('change', function () {
            const operator = $(this).val();
            const selectedFieldOption = $filterRow.find('.filter-field option:selected');
            const fieldType = selectedFieldOption.data('type') || 'text';
            const fieldName = $filterRow.find('.filter-field').val();
            updateValueInput($filterRow, fieldType, operator, fieldName);
        });

        if (filter) {
            $filterRow.find('.filter-field').val(filter.field);

            const selectedOption = $filterRow.find('.filter-field option:selected');
            const fieldType = selectedOption.data('type') || 'text';
            updateOperatorOptions($filterRow, fieldType, filter.operator);

            // Pre-populate the saved value after the DOM has updated
            setTimeout(() => {
                const op = filter.operator;
                const val = filter.value;

                // Operators that use from/to hidden or visible inputs
                const fromToOperators = [
                    'between', 'not_between',
                    'today', 'yesterday', 'tomorrow',
                    'this_week', 'last_week',
                    'this_month', 'last_month',
                    'this_year', 'last_year'
                ];

                if (fromToOperators.indexOf(op) !== -1 && Array.isArray(val)) {
                    let $fromEl = $filterRow.find('.filter-value-from');
                    $fromEl.val(val[0] || '');
                    if ($fromEl[0] && $fromEl[0]._flatpickr) {
                        $fromEl[0]._flatpickr.setDate(val[0] || '', false);
                    }
                    let $toEl = $filterRow.find('.filter-value-to');
                    $toEl.val(val[1] || '');
                    if ($toEl[0] && $toEl[0]._flatpickr) {
                        $toEl[0]._flatpickr.setDate(val[1] || '', false);
                    }
                } else {
                    let $valEl = $filterRow.find('.filter-value');
                    if ($valEl.is('select')) {
                        $valEl.val(Array.isArray(val) ? val.map(String) : val);

                        // Backward compat: old reports stored agency name as value.
                        // If nothing matched by value, try matching by option label text.
                        if (!$valEl.val() && val && !Array.isArray(val)) {
                            $valEl.find('option').each(function () {
                                if ($(this).text().trim().toLowerCase() === String(val).toLowerCase()) {
                                    $valEl.val($(this).val());
                                    return false;
                                }
                            });
                        }

                        if ($valEl.hasClass('filter-multiselect')) {
                            $valEl.trigger('change');
                        }
                    } else {
                        let strVal = Array.isArray(val) ? val.join(', ') : (val || '');
                        $valEl.val(strVal);
                        // If flatpickr is attached, sync its display too
                        if ($valEl[0] && $valEl[0]._flatpickr) {
                            $valEl[0]._flatpickr.setDate(strVal, false);
                        }
                    }
                }
            }, 150);
        }

        filters.push(filter || {});
    }

    $(document).on('click', '.remove-filter', function () {
        $(this).closest('.filter-row').remove();
        refreshFilterDropdowns();
    });

    // Returns a map of { rowIndex: selectedFieldValue } for all rows that have a field chosen.
    function getSelectedFilterFields() {
        let selected = {};
        $('.filter-row').each(function () {
            let idx  = $(this).data('index');
            let val  = $(this).find('.filter-field').val();
            if (val) selected[idx] = val;
        });
        return selected;
    }

    // Disables options that are already selected in a different row so the same
    // field cannot be picked twice. The row's own current selection stays enabled.
    function refreshFilterDropdowns() {
        let selected = getSelectedFilterFields();

        $('.filter-row').each(function () {
            let $row    = $(this);
            let rowIdx  = String($row.data('index'));

            $row.find('.filter-field option').each(function () {
                let optVal = $(this).val();
                if (!optVal) return; // leave the blank "Select Field" option alone

                let usedElsewhere = Object.entries(selected).some(
                    ([idx, field]) => idx !== rowIdx && field === optVal
                );

                $(this).prop('disabled', usedElsewhere);

                // Keep the option visible but greyed — hiding it from a <select>
                // is not cross-browser safe, so we rely on the disabled state.
            });
        });
    }

    function loadExistingFilters(existingFilters) {
        existingFilters.forEach(function (filter) {
            if (typeof IS_AGENCY_USER !== 'undefined' && IS_AGENCY_USER) {
                if (filter.field === 'agency_id' || filter.field === 'agency.agency_name') {
                    return; // Skip rendering the hidden agency filter for agency users
                }
            }
            addFilterRow(filter);
        });
    }

    $('#add-sorting').on('click', function () {
        addSortingRow();
    });

    function addSortingRow(sort = null) {
        let sortIndex = sorting.length;

        let html = `
            <div class="sorting-row card mb-2 p-3" data-index="${sortIndex}">
                <div class="row">
                    <div class="col-md-6">
                        <label>Field</label>
                        <select class="form-control sorting-field" name="sorting[${sortIndex}][field]">
                            <option value="">Select Field</option>
                            ${availableFields.map(f => `<option value="${f.name}">${f.label}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Direction</label>
                        <select class="form-control sorting-direction" name="sorting[${sortIndex}][direction]">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                    <div class="col-md-1" style="display:flex;align-items:flex-end;justify-content:center;padding-bottom:1px;">
                        <button type="button" class="cr-btn-remove remove-sorting" title="Remove rule">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#sorting-list').append(html);
        if (sort) {
            $(`.sorting-row[data-index="${sortIndex}"] .sorting-field`).val(sort.field);
            $(`.sorting-row[data-index="${sortIndex}"] .sorting-direction`).val(sort.direction);
        }

        sorting.push(sort || {});
    }

    $(document).on('click', '.remove-sorting', function () {
        $(this).closest('.sorting-row').remove();
    });

    function loadExistingSorting(existingSorting) {
        existingSorting.forEach(function (sort) {
            addSortingRow(sort);
        });
    }

    $('#preview-report').on('click', function () {
        previewReport();
    });

    function previewReport() {
        let reportData = collectReportData();

        // Validate
        if (!reportData.fields || reportData.fields.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Warning!', 'Please select at least one field', 'warning');
            } else {
                alert('Please select at least one field');
            }
            return;
        }

        showLoading('Generating preview...');

        $.ajax({
            url: PREVIEW_URL,
            type: 'POST',
            'dataType':'json',
            data: {
                _token: getCsrfToken(),
                ...reportData
            },
            success: function (response) {
                hideLoading();
                if (response.status) {
                    renderPreview(response.data);
                    $('#preview-section').fadeIn(300);
                    $('html, body').animate({
                        scrollTop: $('#preview-section').offset().top - 100
                    }, 500);
                }
            },
            error: function (xhr) {
                hideLoading();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', 'Error generating preview', 'error');
                } else {
                    alert('Error generating preview');
                }
            }
        });
    }

    function renderPreview(data) {
        let totalRecords = data.total_count || data.total || 0;
        let currentPage = data.current_page || 1;
        let from = data.from || 0;
        let to = data.to || 0;

        let showingText = (from && to)
            ? `Showing ${from} to ${to} of ${totalRecords} records`
            : 'No records found';

        let html = `
            <div class="preview-header mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0" style="color: #2d3748; font-weight: 600;">
                            <i class="mdi mdi-table-large"></i> Preview Results
                        </h6>
                        <small class="text-muted">${showingText}</small>
                    </div>
                    <div>
                        <span class="badge badge-info" style="padding: 8px 12px; font-size: 13px;">
                            <i class="mdi mdi-database"></i> ${totalRecords} Records
                        </span>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 600px; overflow-y: auto; border: 2px solid #e2e8f0; border-radius: 8px;">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <tr>
        `;

        selectedFields.forEach(function (field, index) {
            html += `
                                <th style="padding: 15px 12px; border: none; font-weight: 600; white-space: nowrap; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="mdi mdi-table-column"></i> ${field.label}
                                </th>
                            `;
        });

        html += '</tr></thead><tbody>';

        if (data.data && data.data.length > 0) {
            data.data.forEach(function (row, rowIndex) {
                const rowClass = rowIndex % 2 === 0 ? 'even-row' : 'odd-row';
                html += `<tr class="${rowClass}" style="transition: all 0.2s ease;">`;
                selectedFields.forEach(function (field) {

                    // Joined-table columns use table__column alias (matches buildSelectColumns)
                    let key = field.name.includes('.')
                        ? field.name.replace('.', '__')
                        : field.name;

                    let value = row[key];

                    // Format value
                    if (value === null || value === undefined || value === '') {
                        value = '<span style="color:#cbd5e0;font-style:italic;">N/A</span>';
                    }
                    else if (field.type === 'date' && value) {

                        // Support YYYY-MM-DD or datetime
                        const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);

                        if (match) {
                            const year = match[1];
                            const month = match[2];
                            const day = match[3];

                            // MM/DD/YYYY format
                            value = `${month}/${day}/${year}`;
                        } else {
                            // fallback if JS Date needed
                            const d = new Date(value);
                            if (!isNaN(d.getTime())) {
                                value = (d.getMonth() + 1).toString().padStart(2, '0')
                                    + '/' +
                                    d.getDate().toString().padStart(2, '0')
                                    + '/' +
                                    d.getFullYear();
                            }
                        }
                    }
                    else if (
                        field.type === 'boolean' ||
                        typeof value === 'boolean' ||
                        ['medication_list', 'insurance_elg', 'mdo_tag', 'is_archive', 'patient_master__is_archive'].includes(key)
                    ) {
                        const boolVal = (value === true || value == 1);
                        value = boolVal
                            ? '<span class="">Yes</span>'
                            : '<span class="">No</span>';
                    }

                    html += `
                        <td style="padding:12px;border-top:1px solid #e2e8f0;">
                            ${value}
                        </td>
                    `;
                });

                html += '</tr>';
            });
        } else {
            html += `
                <tr>
                    <td colspan="${selectedFields.length}" class="text-center" style="padding: 60px 20px; color: #a0aec0;">
                        <div>
                            <i class="mdi mdi-database-off" style="font-size: 48px; color: #cbd5e0;"></i>
                            <p style="margin-top: 15px; font-size: 16px; font-weight: 600; color: #718096;">No Data Found</p>
                            <p style="font-size: 13px; color: #a0aec0;">Try adjusting your filters or selecting different fields</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        html += `
                    </tbody>
                </table>
            </div>
        `;

        if (data.next_page_url) {
            html += `
                <div class="preview-footer mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Page ${currentPage}</small>
                        <small class="text-muted"><i class="mdi mdi-information-outline"></i> This is a preview with limited records</small>
                    </div>
                </div>
            `;
        }

        $('#preview-container').html(html);

        const style = `
            <style>
                .preview-section .even-row {
                    background-color: #f7fafc;
                }
                .preview-section .odd-row {
                    background-color: #ffffff;
                }
                .preview-section tbody tr:hover {
                    background-color: #e6f7ff !important;
                    transform: scale(1.01);
                    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
                }
            </style>
        `;

        if ($('#preview-styles').length === 0) {
            $('head').append(`<div id="preview-styles">${style}</div>`);
        }
    }

    function collectReportData() {
        updateSelectedFields();

        let filtersData = [];
        $('.filter-row').each(function () {
            let field = $(this).find('.filter-field').val();
            let operator = $(this).find('.filter-operator').val();
            let value = null;

            if (field && operator) {
                // Preset date operators that use hidden from/to inputs
                const presetDateOperators = [
                    'today','yesterday','tomorrow',
                    'this_week','last_week',
                    'this_month','last_month',
                    'this_year','last_year'
                ];

                if (operator === 'is_null' || operator === 'is_not_null') {
                    // No value needed — push directly and skip the value check below
                    filtersData.push({ field: field, operator: operator, value: '' });
                    return; // continue to next .filter-row
                } else if (presetDateOperators.includes(operator)) {
                    // These operators store computed dates in hidden from/to inputs
                    let valueFrom = $(this).find('.filter-value-from').val();
                    let valueTo = $(this).find('.filter-value-to').val();
                    if (valueFrom && valueTo) {
                        value = [valueFrom, valueTo];
                    }
                } else if (operator === 'between' || operator === 'not_between') {
                    let valueFrom = $(this).find('.filter-value-from').val();
                    let valueTo = $(this).find('.filter-value-to').val();
                    if (valueFrom && valueTo) {
                        value = [valueFrom, valueTo];
                    } else {
                        return;
                    }
                } else if (operator === 'in' || operator === 'not_in') {
                    let $valEl = $(this).find('.filter-value');
                    let inputValue = $valEl.val();
                    if (Array.isArray(inputValue) && inputValue.length > 0) {
                        value = inputValue;
                    } else if (inputValue && !Array.isArray(inputValue)) {
                        value = inputValue.split(',').map(v => v.trim()).filter(v => v !== '');
                        if (value.length === 0) value = null;
                    }
                } else {
                    value = $(this).find('.filter-value').val();
                    // Guard against undefined (element not found) or empty
                    if (value === undefined) value = null;
                }

                if (value !== null && value !== '' && value !== undefined) {
                    filtersData.push({
                        field: field,
                        operator: operator,
                        value: value
                    });
                }
            }
        });

        // Agency users: auto-inject their assigned agency as a hidden filter so it
        // is stored in the report config (server-side already enforces the scope).
        if (typeof IS_AGENCY_USER !== 'undefined' && IS_AGENCY_USER &&
            typeof AGENCY_USER_AGENCY_ID !== 'undefined' && AGENCY_USER_AGENCY_ID) {
            var alreadyHasAgency = filtersData.some(function (f) {
                return f.field && f.field.indexOf('agency') !== -1;
            });
            if (!alreadyHasAgency) {
                filtersData.push({
                    field: 'agency_id',
                    operator: 'equals',
                    value: String(AGENCY_USER_AGENCY_ID)
                });
            }
        }

        let sortingData = [];
        $('.sorting-row').each(function () {
            let field = $(this).find('.sorting-field').val();
            let direction = $(this).find('.sorting-direction').val();

            if (field) {
                sortingData.push({
                    field: field,
                    direction: direction
                });
            }
        });

        // Collect grouping
        let groupingData = [];
        let groupingField = $('#grouping_field').val();
        if (groupingField) {
            groupingData.push(groupingField);
        }

        return {
            report_name: $('#report_name').val(),
            module_name: $('#module_name').val(),
            sub_module_name: $('#sub_module_name').val(),
            fields: selectedFields.map(f => f.name),
            filters: filtersData,
            sorting: sortingData,
            grouping: groupingData,
            status_wise_count: $('#status_wise_count').val() || 'no',
            enable_header_and_color: $('#enable_header_and_color').val() || 'no'
        };
    }

    function clearValidationErrors() {
        $('.validation-error').remove();
        $('.form-control.is-invalid').removeClass('is-invalid');
    }

    function displayValidationErrors(errors) {
        var fieldMap = {
            'report_name': '#report_name',
            'module_name': '#module_name',
            'sub_module_name': '#sub_module_name',
            'fields': '#fields-container',
            'status_wise_count': '#status_wise_count',
            'enable_header_and_color': '#enable_header_and_color'
        };

        $.each(errors, function (key, messages) {
            var baseKey = key.split('.')[0];
            var selector = fieldMap[baseKey];

            if (selector && $(selector).length) {
                var $field = $(selector);
                $field.addClass('is-invalid');
                var errorHtml = '<div class="validation-error" style="color: #e74a3b; font-size: 12px; margin-top: 5px; font-weight: 500;">';
                errorHtml += '<i class="mdi mdi-alert-circle-outline"></i> ' + messages[0];
                errorHtml += '</div>';

                if (baseKey === 'fields') {
                    $field.after(errorHtml);
                } else {
                    $field.closest('.form-group').append(errorHtml);
                }
            }
        });

        var firstErrorKey = Object.keys(errors)[0];
        if (firstErrorKey) {
            var baseKey = firstErrorKey.split('.')[0];
            if (['report_name', 'module_name', 'sub_module_name', 'status_wise_count', 'enable_header_and_color'].indexOf(baseKey) !== -1) {
                switchToTab('basic-info');
            } else if (baseKey === 'fields') {
                switchToTab('fields');
            } else if (baseKey === 'filters') {
                switchToTab('filters');
            } else if (baseKey === 'sorting' || baseKey === 'grouping') {
                switchToTab('sorting');
            }
        }
    }

    $('#report-form').on('submit', function (e) {
        e.preventDefault();

        let reportData = collectReportData();

        clearValidationErrors();

        let isValid = true;

        if (!reportData.report_name) {
            showFieldError('#report_name', 'Report name is required');
            isValid = false;
        }

        if (!reportData.module_name) {
            showFieldError('#module_name', 'Module is required');
            isValid = false;
        }

        if (!isValid) {
            Swal.fire('Validation Error', 'Please complete Basic Info section', 'warning');
            switchToTab('basic-info');
            return;
        }

        if (!reportData.fields || reportData.fields.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Warning!', 'Please select at least one field', 'warning');
            } else {
                alert('Please select at least one field');
            }
            return;
        }

        let reportId = $(this).data('report-id');

        if (reportId && (!reportData.filters || reportData.filters.length === 0)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Validation Error', 'At least one filter is required to update the report.', 'warning');
            } else {
                alert('At least one filter is required to update the report.');
            }
            return;
        }

        let url = reportId ? UPDATE_REPORT_URL + '/' + reportId : STORE_REPORT_URL;
        let method = reportId ? 'PUT' : 'POST';

        showLoading('Saving report...');

        $.ajax({
            url: url,
            type: method,
            'dataType':'json',
            data: {
                _token: getCsrfToken(),
                ...reportData
            },
            success: function (response) {
                hideLoading();

                if (response.status) {
                    var savedReportId = response.report.id;
                    var listUrl = response.redirect || INDEX_REPORT_URL;

                    if (reportId) {
                        // Update mode — redirect to index (flash message will show there)
                        window.location.href = listUrl;
                    } else {
                        // Create mode — show export prompt, then export settings modal
                        $('#exportCsvBtn').data('report-id', savedReportId).data('format', 'csv');
                        $('#exportExcelBtn').data('report-id', savedReportId).data('format', 'excel');
                        $('#cancelExportBtn').data('url', listUrl);

                        $('#exportModal').modal('show');
                    }
                }
            },
            error: function (xhr) {
                hideLoading();
                clearValidationErrors();

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayValidationErrors(xhr.responseJSON.errors);
                    let errorMsg = xhr.responseJSON.error_msg || 'Please fix the validation errors.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Validation Error!', errorMsg, 'warning');
                    } else {
                        alert(errorMsg);
                    }
                } else {
                    let errorMsg = 'Error saving report';
                    if (xhr.status === 419) {
                        errorMsg = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                        errorMsg = xhr.responseJSON.error_msg;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                }
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('input change', '#report_name, #module_name, #sub_module_name, #status_wise_count, #enable_header_and_color', function () {
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group').find('.validation-error').remove();
    });

    togglePatientOnlyFields($('#module_name').val());

    if ($('#module_name').val()) {
        $('#module_name').trigger('change');
    }

    $('#exportCsvBtn').on('click', function () {
        var reportId = $(this).data('report-id');
        var format   = $(this).data('format');
        var listUrl  = $('#cancelExportBtn').data('url');
        $('#exportModal').modal('hide');
        window.open('/report/custom-reports/' + reportId + '/export/' + format, '_blank');
        window.location.href = listUrl;
    });

    $('#exportExcelBtn').on('click', function () {
        var reportId = $(this).data('report-id');
        var format   = $(this).data('format');
        var listUrl  = $('#cancelExportBtn').data('url');
        $('#exportModal').modal('hide');
        window.open('/report/custom-reports/' + reportId + '/export/' + format, '_blank');
        window.location.href = listUrl;
    });

    $('#cancelExportBtn').on('click', function () {
        var url = $(this).data('url');
        window.location.href = url;
    });
});