<style>
/* ── Listing: read-only flag indicators ── */
.thf-ro-wrap { display:flex; gap:6px; align-items:center; margin-bottom:5px; flex-wrap:wrap; }
.thf-ro-label {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; font-weight:600; color:#9ca3af;
    cursor:default; user-select:none;
}
.thf-ro-label.is-checked { color:#155724; }
.thf-ro-cb {
    width:14px; height:14px; flex-shrink:0;
    border:2px solid #ced4da; border-radius:3px;
    background:#fff; position:relative; display:inline-block;
}
.thf-ro-label.is-checked .thf-ro-cb { background:#28a745; border-color:#28a745; }
.thf-ro-label.is-checked .thf-ro-cb::after {
    content:''; position:absolute;
    left:2px; top:0; width:5px; height:8px;
    border:2px solid #fff; border-top:none; border-left:none;
    transform:rotate(45deg);
}

/* ── Manage Flags button ── */
.thf-manage-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:6px; border:none;
    font-size:11px; font-weight:600; cursor:pointer; transition:opacity .15s;
}
.thf-manage-btn:hover   { opacity:.85; }
.thf-manage-btn.flag-on  { background:#3263d1; color:#fff; }
.thf-manage-btn.flag-off { background:#3263d1; color:#fff; }

/* ── Modal: actionable flag rows ── */
.thf-flag-row {
    display:flex; align-items:center; gap:14px;
    padding:12px 15px; border:2px solid #e9ecef; border-radius:10px;
    margin-bottom:8px; cursor:pointer;
    transition:border-color .15s, background .15s; user-select:none;
}
.thf-flag-row:hover          { border-color:#28a745; background:#f8fffe; }
.thf-flag-row.thf-row-active { border-color:#28a745; background:#f0fff4; }
.thf-cb {
    width:20px; height:20px; flex-shrink:0;
    border:2px solid #ced4da; border-radius:4px;
    background:#fff; position:relative; pointer-events:none;
    transition:background .15s, border-color .15s;
}
.thf-row-active .thf-cb { background:#28a745; border-color:#28a745; }
.thf-row-active .thf-cb::after {
    content:''; position:absolute;
    left:5px; top:1px; width:7px; height:11px;
    border:2px solid #fff; border-top:none; border-left:none;
    transform:rotate(45deg);
}
.thf-flag-text-main { font-size:13px; font-weight:700; color:#1f2937; line-height:1.2; }
.thf-flag-text-sub  { font-size:11px; color:#9ca3af; margin-top:2px; }
.thf-row-active .thf-flag-text-main { color:#155724; }

/* ── Ensure flag modal appears above the visit detail drawer (z-index:1055) ── */
#thFlagModal { z-index: 1200 !important; }

/* ── Modal: history section ── */
.thf-hist-row {
    display:flex; align-items:flex-start; gap:10px;
    padding:9px 12px; border-radius:7px;
    background:#f8f9fa; margin-bottom:7px;
}
.thf-hist-badge {
    min-width:46px; text-align:center;
    font-size:10px; font-weight:700; letter-spacing:.4px;
    padding:2px 7px; border-radius:20px; flex-shrink:0; margin-top:2px;
}
.thf-hist-badge.on  { background:#d4edda; color:#155724; }
.thf-hist-badge.off { background:#e9ecef; color:#9ca3af; }
.thf-hist-detail    { font-size:11.5px; color:#495057; line-height:1.7; }
.thf-hist-detail b  { color:#1f2937; }
.thf-hist-updated {
    margin-top:10px; padding:9px 12px; border-radius:7px;
    background:#fff3cd; border:1px solid #ffc107;
    font-size:11.5px; color:#856404;
}
</style>

{{-- ── Manage Flags Modal (shared) ── --}}
<div class="modal fade" id="thFlagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:420px;">
        <div class="modal-content" style="border:none;border-radius:10px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a7a4a,#28a745);border-bottom:none;padding:14px 20px;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-flag-checkered"></i> Action
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">&times;</button>
            </div>

            <div class="modal-body" style="padding:20px 20px 10px;">
                <p id="thf-modal-name" style="font-size:12px;font-weight:600;color:#6c757d;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                    <i class="mdi mdi-account-outline"></i> <span></span>
                </p>

                <input type="hidden" id="thf-master-id">
                <input type="hidden" id="thf-th-patient-id">
                <input type="hidden" id="thf-patient-id">
                <input type="hidden" id="thf-task-id">

                {{-- Actionable checkboxes --}}
                <div class="thf-flag-row" id="thf-row-poc" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">POC</div><div class="thf-flag-text-sub">Plan of Care</div></div>
                </div>
                <div class="thf-flag-row" id="thf-row-mdo" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">MDO</div><div class="thf-flag-text-sub">MDO</div></div>
                </div>
                <div class="thf-flag-row" id="thf-row-alert" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">Alert</div><div class="thf-flag-text-sub">Critical Alert Flag</div></div>
                </div>
                <div class="thf-flag-row" id="thf-row-supervision" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">Supervision</div><div class="thf-flag-text-sub">Supervision Check</div></div>
                </div>
                <div class="thf-flag-row" id="thf-row-assessment" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">Assessment</div><div class="thf-flag-text-sub">NEW - Patient Assessment (Doc 80752)</div></div>
                </div>
                <div class="thf-flag-row" id="thf-row-kardex" onclick="thfToggleRow(this)">
                    <span class="thf-cb"></span>
                    <div><div class="thf-flag-text-main">Kardex</div><div class="thf-flag-text-sub">NEW - Emergency Kardex (Doc 81049)</div></div>
                </div>

                <div style="text-align:right;margin-bottom:16px;">
                    <button type="button" id="thf-save-btn" class="btn btn-success btn-sm"> Save
                    </button>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="hide_show_send_poc" onclick="sendToPOCCreate()">
                    <span class="spinner-border spinner-border-sm d-none" id="send-hha-pocquestion" aria-hidden="true"></span>
                    <span id="send-poc-btn-text">Send To POC</span></button>
                </div>

                {{-- History --}}
                <div id="thf-history-wrap">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <div style="flex:1;height:1px;background:#e9ecef;"></div>
                        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;">History</span>
                        <div style="flex:1;height:1px;background:#e9ecef;"></div>
                    </div>
                    <div id="thf-history-body"></div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('_partial.task_health_flags.send_hha_poc_modal')

{{-- ── Send To POC – Plan of Care Items Modal ── --}}
<div class="modal fade" id="pocItemsModal" tabindex="-1" aria-hidden="true" style="z-index:1300;">
    <div class="modal-dialog modal-xl" style="max-width:860px;">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">

            <div class="modal-header" style="background:linear-gradient(135deg,#0e7490,#0891b2);border-bottom:none;padding:14px 20px;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-clipboard-list-outline"></i> Plan of Care Items
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;font-size:20px;">&times;</button>
            </div>

            <div class="modal-body" style="padding:20px 20px 10px;max-height:70vh;overflow-y:auto;">
                <div id="poc-items-loading" class="text-center py-4">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="ml-2" style="font-size:13px;color:#6c757d;">Loading...</span>
                </div>
                <div id="poc-items-error" class="alert alert-danger d-none" style="font-size:13px;"></div>
                <div id="poc-items-table-wrap" class="d-none">
                    <div style="overflow-x:auto;">
                        <table class="table table-bordered table-hover table-sm mb-0" style="font-size:13px;min-width:600px;">
                            <thead>
                                <tr style="background:#0e7490;color:#fff;">
                                    <th style="width:90px;white-space:nowrap;color:#fff;">Visit Code</th>
                                    <th style="color:#fff;">Visit Task Name</th>
                                    <th style="width:100px;color:#fff;">HHA Task ID</th>
                                    <th style="width:180px;color:#fff;">HHA Task Name</th>
                                    <th style="width:120px;color:#fff;">HHA Task Code</th>
                                </tr>
                            </thead>
                            <tbody id="poc-items-tbody"></tbody>
                        </table>
                    </div>
                    <p id="poc-items-empty" class="text-center text-muted d-none mt-3" style="font-size:13px;">No Plan of Care items found.</p>
                </div>
            </div>

            <div class="modal-footer" style="padding:10px 20px;background:#f8f9fa;border-top:1px solid #e9ecef;">
                <button type="button" onclick="sendToPOCCreateNew()" class="btn btn-primary btn-sm" data-dismiss="modal">Confirm</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    var _THF_SAVE_URL = '{{ url("/task-health-flags-save") }}';

    function thfCsrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    window.thfToggleRow = function (el) { $(el).toggleClass('thf-row-active'); };

    function thfHistRow(label, checked, by, date) {
        return '<div class="thf-hist-row">' +
            '<span class="thf-hist-badge ' + (checked ? 'on' : 'off') + '">' + label + '</span>' +
            '<div class="thf-hist-detail">' +
            (checked
                ? '<b>Checked by:</b> ' + by + '<br><b>Date:</b> ' + date
                : '<span style="color:#adb5bd;">Not checked</span>') +
            '</div></div>';
    }

    function thfBuildHistory(info) {
        var upd = (info.upd_by && info.upd_by !== '—')
            ? '<div class="thf-hist-updated"><i class="mdi mdi-pencil-outline"></i> <b>Last updated by:</b> ' + info.upd_by + ' &nbsp;·&nbsp; ' + info.upd_at + '</div>'
            : '';
        return thfHistRow('POC',        info.poc,        info.poc_by,        info.poc_date) +
               thfHistRow('MDO',        info.mdo,        info.mdo_by,        info.mdo_date) +
               thfHistRow('Alert',      info.alert,      info.alert_by,      info.alert_date) +
               thfHistRow('Supervision',info.supervision, info.supervision_by, info.supervision_date) +
               thfHistRow('Assessment', info.assessment,  info.assessment_by,  info.assessment_date) +
               thfHistRow('Kardex',     info.kardex,      info.kardex_by,      info.kardex_date) +
               upd;
    }

    $(document).on('click', '.thf-open-flag', function () {
        var $btn = $(this);
        var info = $btn.data('info') || {};

        $('#thf-master-id').val($btn.data('master-id') || '');
        $('#thf-th-patient-id').val($btn.data('th-patient-id') || '');
        $('#thf-task-id').val($btn.data('task-id') || '');
        $('#thf-modal-name span').text($btn.data('name') || '');

        $btn.data('poc')        ? $('#thf-row-poc').addClass('thf-row-active')        : $('#thf-row-poc').removeClass('thf-row-active');
        $btn.data('mdo')        ? $('#thf-row-mdo').addClass('thf-row-active')        : $('#thf-row-mdo').removeClass('thf-row-active');
        $btn.data('alert')      ? $('#thf-row-alert').addClass('thf-row-active')      : $('#thf-row-alert').removeClass('thf-row-active');
        $btn.data('supervision') ? $('#thf-row-supervision').addClass('thf-row-active') : $('#thf-row-supervision').removeClass('thf-row-active');
        $btn.data('assessment')  ? $('#thf-row-assessment').addClass('thf-row-active')  : $('#thf-row-assessment').removeClass('thf-row-active');
        $btn.data('kardex')      ? $('#thf-row-kardex').addClass('thf-row-active')      : $('#thf-row-kardex').removeClass('thf-row-active');

        $('#thf-history-body').html(thfBuildHistory(info));

        // Hide Send POC button if poc is already completed
        $('#hide_show_send_poc').toggle($btn.data('poc') != 1);

        $('#thFlagModal').modal('show');
    });

    $('#thf-save-btn').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving…');

        var postData = {
            _token:      thfCsrf(),
            poc:         $('#thf-row-poc').hasClass('thf-row-active')         ? 1 : 0,
            mdo:         $('#thf-row-mdo').hasClass('thf-row-active')         ? 1 : 0,
            alert:       $('#thf-row-alert').hasClass('thf-row-active')       ? 1 : 0,
            supervision: $('#thf-row-supervision').hasClass('thf-row-active') ? 1 : 0,
            assessment:  $('#thf-row-assessment').hasClass('thf-row-active')  ? 1 : 0,
            kardex:      $('#thf-row-kardex').hasClass('thf-row-active')      ? 1 : 0,
        };

        var masterId   = $('#thf-master-id').val();
        var thPatId    = $('#thf-th-patient-id').val();
        var patientId  = $('#thf-patient-id').val();
        var taskId     = $('#thf-task-id').val();

        if (masterId)  postData.task_health_master_id  = masterId;
        if (thPatId)   postData.task_health_patient_id = thPatId;
        if (patientId) postData.patient_id             = patientId;
        if (taskId)    postData.task_id                = taskId;

        $.ajax({
            url: _THF_SAVE_URL, type: 'POST', data: postData,
            success: function (res) {
                $('#thFlagModal').modal('hide');

                var $manageBtn = $('.thf-open-flag').filter(function () {
                    var mid  = $(this).data('master-id')     || '';
                    var tid  = $(this).data('th-patient-id') || '';
                    var tkid = $(this).data('task-id')       || '';
                    return (masterId && mid  == masterId)
                        || (thPatId  && tid  == thPatId)
                        || (taskId   && tkid == taskId);
                });

                $manageBtn.each(function () {
                    var $b    = $(this);
                    var $cell = $b.closest('td, .thf-cell-wrap');

                    $b.data('poc', res.poc).data('mdo', res.mdo).data('alert', res.alert)
                      .data('supervision', res.supervision)
                      .data('assessment', res.assessment)
                      .data('kardex', res.kardex)
                      .removeClass('flag-on flag-off')
                      .addClass(res.any_flag ? 'flag-on' : 'flag-off');

                    var newInfo = {
                        name:             $b.data('name'),
                        poc:              res.poc,         poc_by:          res.poc         ? res.user_name : '—', poc_date:          res.poc         ? res.saved_at : '—',
                        mdo:              res.mdo,         mdo_by:          res.mdo         ? res.user_name : '—', mdo_date:          res.mdo         ? res.saved_at : '—',
                        alert:            res.alert,       alert_by:        res.alert       ? res.user_name : '—', alert_date:        res.alert       ? res.saved_at : '—',
                        supervision:      res.supervision,  supervision_by:  res.supervision  ? res.user_name : '—', supervision_date:  res.supervision  ? res.saved_at : '—',
                        assessment:       res.assessment,   assessment_by:   res.assessment   ? res.user_name : '—', assessment_date:   res.assessment   ? res.saved_at : '—',
                        kardex:           res.kardex,       kardex_by:       res.kardex       ? res.user_name : '—', kardex_date:       res.kardex       ? res.saved_at : '—',
                        upd_by:           res.user_name,
                        upd_at:           res.saved_at,
                    };
                    $b.data('info', newInfo);

                    thfUpdateRoCell($cell, 'poc',         res.poc,         res.poc         ? res.user_name : null, res.poc         ? res.saved_at : null);
                    thfUpdateRoCell($cell, 'mdo',         res.mdo,         res.mdo         ? res.user_name : null, res.mdo         ? res.saved_at : null);
                    thfUpdateRoCell($cell, 'alert',       res.alert,       res.alert       ? res.user_name : null, res.alert       ? res.saved_at : null);
                    thfUpdateRoCell($cell, 'supervision',  res.supervision,  res.supervision  ? res.user_name : null, res.supervision  ? res.saved_at : null);
                    thfUpdateRoCell($cell, 'assessment',   res.assessment,   res.assessment   ? res.user_name : null, res.assessment   ? res.saved_at : null);
                    thfUpdateRoCell($cell, 'kardex',       res.kardex,       res.kardex       ? res.user_name : null, res.kardex       ? res.saved_at : null);
                });
            },
            error: function () { toastr.error('Something went to wrong'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> Save'); loadTaskHealthSection(); }
        });
    });

    window.thfUpdateRoCell = function ($cell, flag, checked, byName, byDate) {
        var $label = $cell.find('.thf-ro-label[data-flag="' + flag + '"]');
        var label  = flag.toUpperCase();
        if (checked) {
            $label.addClass('is-checked').attr('title', label + ': Checked by ' + byName + ' · ' + byDate);
        } else {
            $label.removeClass('is-checked').attr('title', label + ': Not checked');
        }
    };

    // Boost backdrop z-index only while #thFlagModal is open
    // so it appears above the visit detail drawer without affecting other modals
    $('#thFlagModal').on('show.bs.modal', function () {
        setTimeout(function () {
            $('.modal-backdrop').last().css('z-index', 1190);
        }, 0);
    }).on('hidden.bs.modal', function () {
        $('.modal-backdrop').css('z-index', '');
    });

    $('#pocItemsModal').on('show.bs.modal', function () {
        setTimeout(function () {
            $('.modal-backdrop').last().css('z-index', 1290);
        }, 0);
    }).on('hidden.bs.modal', function () {
        $('.modal-backdrop').css('z-index', '');
    });
}());

// function sendToPOCCreate1(){
//     $('#send-hha-pocquestion').removeClass('d-none');
//     $.ajax({
//         async: false,
//         global: false,
//         url: "{{ url('hha/hha-patient/show-hha-poc-document')}}",
//         type: "post",
//         data: {
//             visit_task_health_id: $('#thf-task-id').val(),
//             'portal_id': '{{ isset($record->id) ? $record->id : "" }}',
//             '_token':"{{ csrf_token()}}"
//         },
//         success: function(resp) {
//             $('#send-hha-pocquestion').addClass('d-none');
//             toastr.success(resp.error_msg);
//             let hhaDocPOCObject = '';
//             if (resp.data.documentType && resp.data.documentType.length > 0) {
//                 hhaDocPOCObject = '<option value="">HHA Patient Document Type</option>';
//                 $.each(resp.data.documentType,function(i,v){
//                     hhaDocPOCObject +='<option value="'+v.document_id+'">'+v.document_name+'</option>'
//                 })
//             }
//             $('#poc_hha_patient_document_type').html("");
//             $('#poc_hha_patient_document_type').html(hhaDocPOCObject);
//             $('#poc_document_iframe').attr('src',resp.data.url);
//             $('#poc_doc_file_name').val(resp.data.task_url)
//             $('#hha_visit_portal_id').val('{{ isset($record->id) ? $record->id : "" }}')
//             $('#hha_visit_task_health_id').val($('#thf-task-id').val())
//             $('#hha_poc_type_document_name').val(resp.data.title)
//             $('#hha_priview_type_document_name').val(resp.data.url)
//             $('#exampleModal-add-send_poc_modal').modal('show');
//         },
//         error:function(jqr){
//             $('#send-hha-pocquestion').addClass('d-none');
//             showErrorAndLoginRedirection(jqr);
//         }
//     })
// }

function sendToPOCCreate(){
    var taskId = $('#thf-task-id').val();
    if(!taskId){
        toastr.error('Task ID not found.');
        return;
    }
    var $btn = $('#hide_show_send_poc');

    // Show loader on button
    $btn.prop('disabled', true);
    $('#send-hha-pocquestion').removeClass('d-none');
    $('#send-poc-btn-text').text('Loading...');

    // Reset modal state
    $('#poc-items-loading').removeClass('d-none');
    $('#poc-items-error').addClass('d-none').text('');
    $('#poc-items-table-wrap').addClass('d-none');
    $('#poc-items-tbody').html('');
    $('#poc-items-empty').addClass('d-none');
    $('#pocItemsModal').modal('show');

    $.ajax({
        global: false,
        url: "{{ url('task-health/visit-detail-json-poc') }}/"+taskId,
        type: "get",
        success: function(resp) {
            $('#poc-items-loading').addClass('d-none');
            var items = (resp.data && resp.data.planOfCareItems) ? resp.data.planOfCareItems : [];
            var pocRows = '';
            if(items.length > 0){
                $.each(items,function(i,v){
                    pocRows += '<tr>';
                    pocRows += '<td>'+(v.code || '')+'</td>';
                    pocRows += '<td>'+(v.name || '')+'</td>';
                    pocRows += '<td>'+(v.matched_task_id || '')+'</td>';
                    pocRows += '<td>'+(v.matched_task_name || '')+'</td>';
                    pocRows += '<td>'+(v.matched_task_code || '')+'</td>';
                    pocRows += '</tr>';
                })
                $('#poc-items-tbody').html(pocRows);
                $('#poc-items-table-wrap').removeClass('d-none');
            } else {
                $('#poc-items-table-wrap').removeClass('d-none');
                $('#poc-items-empty').removeClass('d-none');
            }
        },
        error:function(jqr){
            $('#poc-items-loading').addClass('d-none');
            showErrorAndLoginRedirection(jqr);
        },
        complete: function(){
            $btn.prop('disabled', false);
            $('#send-hha-pocquestion').addClass('d-none');
            $('#send-poc-btn-text').text('Send To POC');
        }
    })
}

function sendToPOCCreateNew(){

    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: 'You want to create a new HHA POC.',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-success',
                action: function() {
                    var formData = new FormData($('#form_create_poc_question_id')[0]);
                    formData.append('_token','{{ csrf_token()}}')
                    formData.append('portal_id', $('#thf-patient-id').val());
                    formData.append('visit_task_health_id',$('#thf-task-id').val());
                    $.ajax({
                        async: false,
                        global: false,
                        url: "{{ url('hha/hha-patient/send-hha-poc')}}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(resp) {
                            $('#create-hha-pocquestion').addClass('d-none')
                           toastr.success(resp.error_msg);
                           $('#form_create_poc_question_id')[0].reset();
                           $('#poc_document_iframe').attr('src','')
                           $('#exampleModal-add-send_poc_modal').modal('hide')
                           loadTaskHealthSection();
                        },
                        error:function(jqr){
                            showErrorAndLoginRedirection(jqr);
                        }

                    })
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}
</script>
