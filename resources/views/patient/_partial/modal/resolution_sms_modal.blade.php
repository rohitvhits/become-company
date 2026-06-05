<!-- Resolution SMS Modal -->
<div class="modal fade" id="resolutionSmsModal" tabindex="-1" role="dialog" aria-labelledby="resolutionSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resolutionSmsModalLabel">Resolution SMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="resolutionStatus">Select Resolution SMS</label>
                    <select class="form-control" id="resolutionStatus">
                        <option value="">Select Resolution SMS</option>
                        <option value="Require Medication List">Require Medication List</option>
                        <option value="Additional Documentation">Additional Documentation</option>
                    </select>
                </div>
                <div class="form-group d-none" id="previewArea">
                    <label class="font-weight-bold">Message Preview</label>
                    <div id="resolutionSmsPreview" class="border p-2 bg-light text-muted" style="min-height: 50px; border-radius: 4px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSendResolutionSms">Send SMS</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#resolutionStatus').on('change', function() {
        var status = $(this).val();
        var $previewArea = $('#previewArea');
        var $preview = $('#resolutionSmsPreview');

        if (!status) {
            $previewArea.addClass('d-none');
            return;
        }

        $preview.html('<i class="fa fa-spinner fa-spin"></i> Fetching preview...');
        $previewArea.removeClass('d-none');

        $.ajax({
            type: "POST",
            url: _RESOLVE_RESOLUTION_SMS_MSG,
            data: {
                'patient_id': '{{ $record->id }}',
                'status': status,
                '_token': "{{ csrf_token() }}"
            },
            success: function(res) {
                if(res.success) {
                    $preview.text(res.error_msg);
                } else {
                    $preview.html('<span class="text-danger">Failed to load preview</span>');
                }
            },
            error: function(xhr) {
                showErrorAndLoginRedirection(xhr);
            }
        });
    });

    $('#btnSendResolutionSms').off('click').on('click', function() {
        var status = $('#resolutionStatus').val();
        var patientId = '{{ $record->id }}';

        if (!status) {
            toastr.error('Please select a resolution status first');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            type: "POST",
            url: _SEND_RESOLUTION_SMS,
            data: {
                'patient_id': patientId,
                'status': status,
                '_token': "{{ csrf_token() }}"
            },
            success: function(res) {
                $btn.prop('disabled', false).text('Send SMS');
                if(res.success) {
                    toastr.success(res.error_msg);
                    $('#resolutionSmsModal').modal('hide');
                    $('#resolutionStatus').val('').trigger('change');
                }
            },
            error: function(xhr) {
                showErrorAndLoginRedirection(xhr);
                $btn.prop('disabled', false).text('Send SMS');
            }
        });
    });
});
</script>
