<div class="modal fade" id="sendSMSEsignReport" aria-modal="true" role="dialog"
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
                    <input type="hidden" name="user_new_id" id="user_new_id">
                    <input type="hidden" name="document_send_type" id="document_send_type_id">
                    <div class="form-group">
                        <label>Email</label><span class="error">*</span>
                        <input type="text" name="email" class="form-control" id="esign_report_email"
                            value="">
                        <span class="error" id="email_error"></span>
                    </div>

                    <div class="form-group">
                        <label>Mobile</label><span class="error">*</span>
                        <input type="text" name="mobile" class="form-control" id="esign_report_mobile"
                            value="">
                        <span class="error" id="mobile_no_id_caregiver_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pull-right"
                        onclick="getSendSMSSubmitEsignReport()">Send</button>
                </div>
        </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>