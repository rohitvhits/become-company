<div class="modal fade" id="bulkSendSMSModal" tabindex="-1" aria-labelledby="bulkSendSMSLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h5 class="modal-title" id="bulkSendSMSLabel">Bulk Send SMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkSmsForm">
                    <div class="form-group">
                        <label for="bulk_sms_message">Message </label>
                        <textarea name="message" class="form-control" id="bulk_sms_message" rows="4" placeholder="Enter SMS message"></textarea>
                        <span class="text-muted"><b>Note</b>:If the message is blank, the system will send a default message; otherwise, it will send the message you entered.</span>
                        <span class="error" id="bulk_sms_message_error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-primary btn-sm px-4 mr-2" id="bulkSendSMSBtn" onclick="submitBulkSMS()">Send</button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    
                </div>
                
            </div>
        </div>
    </div>
</div>