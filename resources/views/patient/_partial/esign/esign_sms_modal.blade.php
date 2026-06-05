<div class="modal fade" id="sendSMSEsign" aria-modal="true" role="dialog"
    style="padding-right: 17px; display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email Esign</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="sms_esign" ecntype="mulitpart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="groupId" id="main_caregiver_esign_id">
                    <input type="hidden" name="document_send_type" id="document_send_type_id">
                    <div class="form-group">
                        <label>Email</label><span class="error">*</span>
                        <input type="text" name="email" class="form-control" id="email"
                            value="<?php echo $record->email; ?>">
                        <span class="error" id="email_error"></span>
                    </div>

                    <div class="form-group">
                        <label>Mobile</label><span class="error">*</span>
                        <input type="text" name="mobile" class="form-control" id="mobile_no_id_caregiver"
                            value="<?php echo $record->mobile; ?>">
                        <span class="error" id="mobile_no_id_caregiver_error"></span>
                    </div>
                    <div class="form-group">
                        <label>Message</label><span class="error">*</span>
                        <input type="text" name="message" class="form-control" id="message"
                            value="">
                        <span class="error" id="message_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pull-right"
                        onclick="getSendSMSSubmit()">Send</button>
                </div>
        </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>