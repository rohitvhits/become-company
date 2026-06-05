<div class="modal fade cron-log-modal" id="cronLogModal" tabindex="-1" role="dialog" aria-labelledby="cronLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cronLogModalLabel">Cron Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <strong>Send Response (Request Response)</strong>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <pre id="modal_request_response" style="white-space: pre-wrap; word-wrap: break-word; font-size: 12px; margin: 0;"></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <strong>Return Response</strong>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <pre id="modal_return_response" style="white-space: pre-wrap; word-wrap: break-word; font-size: 12px; margin: 0;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
