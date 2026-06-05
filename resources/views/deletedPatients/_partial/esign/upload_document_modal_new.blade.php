<div class="modal fade " id="exampleModal-upload-document-new" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id_upload_document_new">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="edit_upload_document_modal_new">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="eid" id="temp1" value="<?php echo $record->id; ?>">
                <input type="hidden" name="eidc" id="temp1" value="<?php echo $record->patient_code; ?>">
                <input type="hidden" name="receipt_name" value="<?php echo $record->first_name . ' ' . $record->last_name; ?>">

                <div class="modal-body">


                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Document Name<span
                                class="error">*</span>:</label>
                        <input type="text" name="document_name" class="form-control" id="documentName">
                        <span id="documentName_error" class="error mt-2" for="document_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Upload Document<span
                                class="error">*</span>:</label>
                        <input type="file" class="form-control" id="fileUpload" name="file_upload">
                        <span class="error mt-2" id="fileUpload_error" for="file_name"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_upload_document_modal_submit_new">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
