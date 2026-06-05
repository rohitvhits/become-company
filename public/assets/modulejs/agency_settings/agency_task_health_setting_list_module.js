// State for the currently open configure modal
var _cfgAgencyId  = null;
var _cfgSha1Id    = null;
var _cfgDocIds    = {};
var _cfgPocNotes  = '';

function agencySettingsAjax(page) {
    $('.shimmer_id').removeClass('hide');
    $('#response_agency_settings_list').html('');
    $('.agency-settings-loader').show();

    $.ajax({
        url: _AGENCY_SETTINGS_AJAX + '?page=' + page,
        type: 'get',
        data: {
            'agency_name': $('#agency_name').val(),
            'email': $('#email').val(),
            'phone': $('#phone').val(),
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('#response_agency_settings_list').html(res);
            $('.agency-settings-loader').hide();
        },
        error: function() {
            $('.shimmer_id').addClass('hide');
            $('.agency-settings-loader').hide();
            toastr.error('An error occurred. Please try again.');
        }
    });

    return false;
}

function agencySettingsReset() {
    $('#agency_name').val('');
    $('#email').val('');
    $('#phone').val('');
    agencySettingsAjax(1);
}

function buildToggleRow(sf, agencyId, checked) {
    var html = '';
    html += '<div class="cfg-row' + (checked ? ' cfg-row--active' : '') + '">';
    html +=   '<div style="flex:1;min-width:0;">';
    html +=     '<div class="cfg-label">' + sf.label + '</div>';
    html +=     '<div class="cfg-desc">' + sf.description + '</div>';
    html +=   '</div>';
    html +=   '<label class="toggle-switch mb-0" style="flex-shrink:0;">';
    html +=     '<input type="checkbox" class="agency-toggle"'
                  + ' data-agency="' + agencyId + '"'
                  + ' data-field="'  + sf.field  + '"'
                  + (checked ? ' checked' : '') + '>';
    html +=     '<span class="toggle-slider"></span>';
    html +=   '</label>';
    html += '</div>';
    return html;
}

function buildNotesRow(notes) {
    var safeNotes   = notes ? $('<div>').text(notes).html() : '';
    var valClass    = notes ? 'cfg-val is-set' : 'cfg-val';
    var valText     = notes ? $('<div>').text(notes).html() : 'No notes added';
    var html = '';
    html += '<div class="cfg-sub-row cfg-sub-row--notes" id="cfg-poc-notes-row">';
    html +=   '<div class="cfg-sub-row-left">';
    html +=     '<div class="cfg-label">Notes</div>';
    html +=     '<div class="cfg-desc">Internal notes for this POC group configuration.</div>';
    html +=   '</div>';
    html +=   '<div class="cfg-sub-row-right" id="cfg-poc-notes-display-wrap">';
    html +=     '<span class="' + valClass + '" id="cfg-poc-notes-display" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + safeNotes + '">' + valText + '</span>';
    html +=     '<button type="button" class="cfg-edit-btn cfg-poc-notes-edit-btn"><i class="mdi mdi-pencil"></i> Edit</button>';
    html +=   '</div>';
    html +=   '<div class="cfg-sub-row-form" id="cfg-poc-notes-form" style="display:none;">';
    html +=     '<textarea id="cfg-poc-notes-input" class="form-control form-control-sm" rows="3" maxlength="5000" placeholder="Enter internal notes for POC group...">' + safeNotes + '</textarea>';
    html +=     '<div class="mt-2">';
    html +=       '<button type="button" class="btn btn-sm btn-primary cfg-poc-notes-save-btn">Save</button>';
    html +=       ' <button type="button" class="btn btn-sm btn-light cfg-poc-notes-cancel-btn">Cancel</button>';
    html +=     '</div>';
    html +=   '</div>';
    html += '</div>';
    return html;
}

function buildDocTypeRow(type, label, desc, currentId, currentName) {
    var rowId    = 'cfg-' + type + '-doctype-row';
    var labelId  = 'cfg-' + type + '-doctype-label';
    var formId   = 'cfg-' + type + '-doctype-form';
    var selectId = 'cfg-' + type + '-doctype-select';
    var valClass = currentId ? 'cfg-val is-set' : 'cfg-val';

    var html = '';
    html += '<div class="cfg-sub-row" id="' + rowId + '">';
    html +=   '<div class="cfg-sub-row-left">';
    html +=     '<div class="cfg-label">' + label + '</div>';
    html +=     '<div class="cfg-desc">' + desc + '</div>';
    html +=   '</div>';
    html +=   '<div class="cfg-sub-row-right" id="' + labelId + '-wrap">';
    html +=     '<span class="' + valClass + '" id="' + labelId + '" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + (currentName || '') + '">' + (currentName || 'Not Set') + '</span>';
    html +=     '<button type="button" class="cfg-edit-btn cfg-doctype-edit-btn" data-type="' + type + '"><i class="mdi mdi-pencil"></i> Edit</button>';
    html +=   '</div>';
    html +=   '<div class="cfg-sub-row-form" id="' + formId + '" style="display:none;">';
    html +=     '<select id="' + selectId + '" class="form-control form-control-sm">';
    html +=       '<option value="">-- Select Type --</option>';
    html +=     '</select>';
    html +=     '<div class="mt-2">';
    html +=       '<button type="button" class="btn btn-sm btn-primary cfg-doctype-save-btn" data-type="' + type + '">Save</button>';
    html +=       ' <button type="button" class="btn btn-sm btn-light cfg-doctype-cancel-btn" data-type="' + type + '">Cancel</button>';
    html +=     '</div>';
    html +=   '</div>';
    html += '</div>';
    return html;
}

$(document).ready(function() {
    agencySettingsAjax(1);

    $('#filter-btn').on('click', function() {
        $('#search-filter-btn').toggle();
    });

    $('#search-data').on('click', function() {
        agencySettingsAjax(1);
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        agencySettingsAjax(page);
    });

    // ── Open Configure modal ──────────────────────────────────────────────────
    $(document).on('click', '.agency-configure-btn', function() {
        var $btn      = $(this);
        var agencyId  = $btn.data('agency');
        var agencyName= $btn.data('name');
        var settings  = $btn.data('settings');
        var sha1Id    = $btn.data('sha1');
        var pocDocId          = $btn.data('poc-doc-id')          || '';
        var pocDocName        = $btn.data('poc-doc-name')        || 'Not Set';
        var supDocId          = $btn.data('sup-doc-id')          || '';
        var supDocName        = $btn.data('sup-doc-name')        || 'Not Set';
        var assessmentDocId   = $btn.data('assessment-doc-id')   || '';
        var assessmentDocName = $btn.data('assessment-doc-name') || 'Not Set';
        var packageDocId      = $btn.data('package-doc-id')      || '';
        var packageDocName    = $btn.data('package-doc-name')    || 'Not Set';
        var cms485DocId       = $btn.data('cms485-doc-id')       || '';
        var cms485DocName     = $btn.data('cms485-doc-name')     || 'Not Set';
        var kardexDocId       = $btn.data('kardex-doc-id')       || '';
        var kardexDocName     = $btn.data('kardex-doc-name')     || 'Not Set';
        var pocNotes          = $btn.data('poc-notes')           || '';

        _cfgAgencyId  = agencyId;
        _cfgPocNotes  = pocNotes;
        _cfgSha1Id   = sha1Id;
        _cfgDocIds   = {
            poc:        pocDocId,
            sup:        supDocId,
            assessment: assessmentDocId,
            package:    packageDocId,
            cms485:     cms485DocId,
            kardex:     kardexDocId,
        };

        $('#cfg-modal-agency-name').text(agencyName);

        // Group fields by their group key
        var groups = {};
        var groupOrder = [];
        (_AGENCY_SETTING_FIELDS || []).forEach(function(sf) {
            if (!groups[sf.group]) {
                groups[sf.group] = [];
                groupOrder.push(sf.group);
            }
            groups[sf.group].push(sf);
        });

        var html = '';
        groupOrder.forEach(function(groupName) {
            html += '<div class="cfg-group-section">';
            html += '<div class="cfg-group-title">' + groupName + '</div>';
            html += '<div class="cfg-toggle-grid">';
            groups[groupName].forEach(function(sf) {
                var checked = (settings[sf.field] == 1);
                html += buildToggleRow(sf, agencyId, checked);
            });
            html += '</div>';

            if (groupName === 'POC') {
                html += buildDocTypeRow('poc', 'POC Document Type', 'Select the HHA document type used for Plan of Care (POC).', pocDocId, pocDocName);
                html += buildNotesRow(pocNotes);
            }
            if (groupName === 'Supervision') {
                html += buildDocTypeRow('sup', 'Supervision Document Type', 'Select the HHA document type used for Supervision visits.', supDocId, supDocName);
            }
            if (groupName === 'Assessment') {
                html += buildDocTypeRow('assessment', 'Patient Assessment Document Type', 'Select the HHA document type for Patient Assessment documents.', assessmentDocId, assessmentDocName);
            }
            if (groupName === 'Kardex') {
                html += buildDocTypeRow('kardex', 'Emergency Kardex Document Type', 'Select the HHA document type for Emergency Kardex documents.', kardexDocId, kardexDocName);
            }
            if (groupName === 'CMS MDO') {
                html += buildDocTypeRow('cms485', 'CMS 485 Document Type', 'Select the HHA document type for CMS-485 documents.', cms485DocId, cms485DocName);
            }
            if (groupName === 'Patient Package') {
                html += buildDocTypeRow('package', 'Patient Package Document Type', 'Select the HHA document type for Patient Package documents.', packageDocId, packageDocName);
            }
            html += '</div>';
        });

        $('#cfg-modal-body').html(html);
        $('#agencyConfigModal').modal('show');
    });

    // ── Toggle update ─────────────────────────────────────────────────────────
    $(document).on('change', '.agency-toggle', function() {
        var checkbox  = $(this);
        var row       = checkbox.closest('.cfg-row');
        var agencyId  = checkbox.data('agency');
        var field     = checkbox.data('field');
        var isChecked = checkbox.is(':checked');

        // Optimistic UI
        row.toggleClass('cfg-row--active', isChecked);

        $.ajax({
            url: _AGENCY_SETTINGS_TOGGLE,
            type: 'POST',
            data: {
                '_token'   : _CSRF_TOKEN,
                'agency_id': agencyId,
                'field'    : field,
                'value'    : isChecked ? 1 : 0,
            },
            success: function(res) {
                if (res.status) {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                    checkbox.prop('checked', !isChecked);
                    row.toggleClass('cfg-row--active', !isChecked);
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
                checkbox.prop('checked', !isChecked);
                row.toggleClass('cfg-row--active', !isChecked);
            }
        });
    });

    // ── Doc type: Edit button — load available types into dropdown ────────────
    $(document).on('click', '.cfg-doctype-edit-btn', function() {
        var $btn        = $(this);
        var type        = $btn.data('type');
        var formId      = '#cfg-' + type + '-doctype-form';
        var selectId    = '#cfg-' + type + '-doctype-select';
        var labelWrapId = '#cfg-' + type + '-doctype-label-wrap';

        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

        var isSupervision = (type === 'sup');
        var url  = isSupervision ? _CFG_SUP_TYPES_URL : _CFG_POC_SYNC_URL;
        var data = isSupervision ? { agencyId: _cfgAgencyId } : { id: _cfgSha1Id };

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function(res) {
                var select    = $(selectId);
                var currentId = _cfgDocIds[type] || '';
                select.empty().append('<option value="">-- Select --</option>');
                $.each(res.data || [], function(i, doc) {
                    var id   = isSupervision ? doc.id            : doc.document_id;
                    var name = isSupervision ? doc.name          : doc.document_name;
                    var sel  = isSupervision ? (doc.id == res.selected) : (id == currentId);
                    select.append('<option value="' + id + '" data-name="' + name + '"' + (sel ? ' selected' : '') + '>' + name + '</option>');
                });
                $(formId).show();
                $(labelWrapId).hide();
            },
            error: function() {
                toastr.error('Failed to load document types.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="mdi mdi-pencil"></i> Edit');
            }
        });
    });

    // ── Doc type: Cancel ──────────────────────────────────────────────────────
    $(document).on('click', '.cfg-doctype-cancel-btn', function() {
        var type = $(this).data('type');
        $('#cfg-' + type + '-doctype-form').hide();
        $('#cfg-' + type + '-doctype-label-wrap').show();
    });

    // ── Doc type: Save ────────────────────────────────────────────────────────
    var _CFG_SAVE_URL_MAP = null;

    function getCfgSaveUrlMap() {
        if (!_CFG_SAVE_URL_MAP) {
            _CFG_SAVE_URL_MAP = {
                poc:        _CFG_POC_SAVE_URL,
                sup:        _CFG_SUP_SAVE_URL,
                assessment: _CFG_ASSESSMENT_SAVE_URL,
                package:    _CFG_PACKAGE_SAVE_URL,
                cms485:     _CFG_CMS485_SAVE_URL,
                kardex:     _CFG_KARDEX_SAVE_URL,
            };
        }
        return _CFG_SAVE_URL_MAP;
    }

    $(document).on('click', '.cfg-doctype-save-btn', function() {
        var $btn    = $(this);
        var type    = $btn.data('type');
        var select  = $('#cfg-' + type + '-doctype-select');
        var selId   = select.val();
        var selName = select.find('option:selected').text();

        if (!selId) {
            toastr.warning('Please select a document type.');
            return;
        }

        $btn.prop('disabled', true);

        var postData = { '_token': _CSRF_TOKEN, 'id': _cfgSha1Id };
        if (type === 'poc') {
            postData.poc_document_type_id   = selId;
            postData.poc_document_type_name = selName;
        } else if (type === 'sup') {
            postData.supervision_document_type_id   = selId;
            postData.supervision_document_type_name = selName;
        } else {
            postData.document_type_id   = selId;
            postData.document_type_name = selName;
        }

        $.ajax({
            url: getCfgSaveUrlMap()[type],
            type: 'POST',
            data: postData,
            success: function(res) {
                if (res.status) {
                    var $lbl = $('#cfg-' + type + '-doctype-label');
                    $lbl.text(selName).attr('title', selName).removeClass('cfg-val').addClass('cfg-val is-set');
                    $('#cfg-' + type + '-doctype-form').hide();
                    $('#cfg-' + type + '-doctype-label-wrap').show();
                    toastr.success(res.error_msg || 'Saved successfully.');
                } else {
                    toastr.error(res.error_msg || 'Failed to save.');
                }
            },
            error: function() { toastr.error('An error occurred. Please try again.'); },
            complete: function() { $btn.prop('disabled', false); }
        });
    });

    // ── POC Notes: Edit ───────────────────────────────────────────────────────
    $(document).on('click', '.cfg-poc-notes-edit-btn', function() {
        $('#cfg-poc-notes-display-wrap').hide();
        $('#cfg-poc-notes-form').show();
        $('#cfg-poc-notes-input').focus();
    });

    // ── POC Notes: Cancel ─────────────────────────────────────────────────────
    $(document).on('click', '.cfg-poc-notes-cancel-btn', function() {
        $('#cfg-poc-notes-form').hide();
        $('#cfg-poc-notes-display-wrap').show();
    });

    // ── POC Notes: Save ───────────────────────────────────────────────────────
    $(document).on('click', '.cfg-poc-notes-save-btn', function() {
        var $btn  = $(this);
        var notes = $('#cfg-poc-notes-input').val();

        $btn.prop('disabled', true);

        $.ajax({
            url: _CFG_POC_NOTES_SAVE_URL,
            type: 'POST',
            data: { '_token': _CSRF_TOKEN, 'agency_id': _cfgAgencyId, 'notes': notes },
            success: function(res) {
                if (res.status) {
                    _cfgPocNotes = notes;
                    var $disp = $('#cfg-poc-notes-display');
                    if (notes) {
                        $disp.text(notes).attr('title', notes).removeClass('cfg-val').addClass('cfg-val is-set');
                    } else {
                        $disp.text('No notes added').attr('title', '').removeClass('is-set');
                    }
                    $('#cfg-poc-notes-form').hide();
                    $('#cfg-poc-notes-display-wrap').show();
                    toastr.success(res.message || 'Notes saved successfully.');
                } else {
                    toastr.error(res.message || 'Failed to save notes.');
                }
            },
            error: function(jqr) { toastr.error('An error occurred. Please try again.'); showErrorAndLoginRedirection(jqr);},
            complete: function() { $btn.prop('disabled', false); }
        });
    });
});
