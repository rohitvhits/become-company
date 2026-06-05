<div class="modal fade" id="exampleModal-upload-remote-document" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Upload Remote Document</h5>
                <button type="button" class="close" id="close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='' method="post" id="uploadDocumentFormID">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="remote_id" id="remote_id">
                    
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Document<span class="error mt-2">*</span></label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" id="upload_document" name="upload_document">
                            <span id="document_upload_error" class="error mt-2"><?php echo $errors->add_agency->first('upload_document'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Note</label>
                        <div class="col-sm-8">
                            <textarea name="note" class="form-control" placeholder="Enter Note"></textarea>

                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" id="uploadDocumentButton" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" id="close-upload-document-modal" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>