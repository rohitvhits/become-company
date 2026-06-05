
<!-- upload document -->
<div class="modal fade" id="exampleModal-invoice-upload-doc" tabindex="-1" role="dialog"
    aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data"
                    action="{{ URL::to('/invoice/document-upload') }}" name="adduser" method="post"
                    id="formnewdocupload">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="upload_invoice_document_id" id="upload_invoice_document_id" value="">

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Attachment<span
                                style="color:red">*</span>:</label>
                        <input type="file" class="form-control" id="doc_image" name="images">
                        <span class="error mt-2 text-danger" id="doc_images_error" for="file_name"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>