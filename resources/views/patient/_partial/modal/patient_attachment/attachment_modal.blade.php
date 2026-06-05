<div class="modal fade" id="exampleModal-attachment" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Attachment</h5>
                <button type="button" class="close" data-dismiss="modal" id="closeds" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="attachment_pdf_id">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Attachment: <span class="error">*</span></label><br>
                        <input type="file" name="attchment_pdf" id="attchment_pdf">
                        <span class="error attchment_pdf_error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="getuploadAttachment()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
