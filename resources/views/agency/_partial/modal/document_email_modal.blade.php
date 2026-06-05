<div class="modal fade" id="addEditDocumentModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Document Email</h5>
                <button type="button" onclick="closeEditDocumentEmail()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="updateFormDocumentId">
                <div class="modal-body">
                
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                   

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Email (Commas Seperate)</b></label>
                        <textarea class="form-control" id="document_email_id" PlaceHolder="Email (Commas Seperate)" rows="4"></textarea>
                        <span id="document_email_id_error" class="text-danger"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="updateDocumentEmail" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeEditDocumentEmail()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>