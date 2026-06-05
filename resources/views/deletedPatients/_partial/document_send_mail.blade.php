<div class="modal fade" id="exampleModal-send-mail" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Document Sent Mail</h5>
                    <button type="button" class="close" id="close_document_send_followup_date" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="document_upload_id">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Email<span class="error">*</span>:</label>
                        <input name="email" id="document_email"  class="form-control" />
                        
                        <span id="document_email_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="sendDocumentHHAMail()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>