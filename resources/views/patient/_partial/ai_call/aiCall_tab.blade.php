<div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0"><i class="mdi mdi-robot text-primary mr-1"></i> AI Call Logs</p>
                                                <button class="btn btn-info btn-sm" onclick="openAddCallModal()">
                                                    <i class="mdi mdi-phone-plus mr-1"></i> Add Call
                                                </button>
                                            </div>
                                            <div id="aiCallLogsContainer">
                                                <div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                                            </div>

<!-- Add Call Modal -->
<div class="modal fade" id="addAiCallModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#e8f4fd;border-bottom:2px solid #007bff;">
                <h6 class="modal-title" style="font-weight:700;color:#007bff;">
                    <i class="mdi mdi-phone-plus mr-1"></i> Add AI Call
                </h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size:13px;">
                    <i class="mdi mdi-information-outline mr-1 text-info"></i>
                    If an active call log already exists for this portal, the call will be <strong>re-fired</strong> on that log.
                    Otherwise a <strong>new call log</strong> will be created and scheduled.
                </p>
                <div id="addCallFeedback" class="mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <img src="{{ asset('ajax-loader.gif') }}" id="addCallLoader" style="display:none;width:24px;height:24px;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info btn-sm" id="addCallSubmitBtn" onclick="submitAddCall()">
                    <i class="mdi mdi-phone mr-1"></i> Fire Call
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openAddCallModal() {
    $('#addCallFeedback').hide().html('');
    $('#addCallSubmitBtn').prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call');
    $('#addAiCallModal').modal('show');
}

function submitAddCall() {
    var btn    = $('#addCallSubmitBtn');
    var mobile = '';

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Processing...');
    $('#addCallLoader').show();
    $('#addCallFeedback').hide();

    $.ajax({
        type: 'POST',
        url:  '{{ url("patient/".$record->id."/ai-call-logs/add-call") }}',
        data: { _token: _CSRF_TOKEN, mobile: mobile },
        success: function(res) {
            $('#addCallLoader').hide();
            var cls  = res.status ? 'alert-success' : 'alert-danger';
            var icon = res.status ? 'fa-check-circle' : 'fa-times-circle';
            var extra = res.is_existing
                ? ' <small class="d-block mt-1 text-muted">Re-fired on existing log #' + res.log_id + '</small>'
                : ' <small class="d-block mt-1 text-muted">New log #' + res.log_id + ' created</small>';
            $('#addCallFeedback')
                .attr('class', 'alert ' + cls + ' py-2 px-3 mt-3')
                .html('<i class="fa ' + icon + ' mr-1"></i>' + res.message + extra)
                .show();
            if (res.status) {
                btn.prop('disabled', true).html('<i class="fa fa-check mr-1"></i> Done');
                // Reload AI call logs list
                _aiCallLogsLoaded = false;
                setTimeout(function() {
                    loadPatientAiCallLogs();
                }, 1200);
            } else {
                btn.prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call');
            }
        },
        error: function(jqXHR) {
            $('#addCallLoader').hide();
            var msg = 'Something went wrong.';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
            $('#addCallFeedback')
                .attr('class', 'alert alert-danger py-2 px-3 mt-3')
                .html('<i class="fa fa-times-circle mr-1"></i>' + msg)
                .show();
            btn.prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call');
        }
    });
}
</script>