<div class="modal fade" id="edit-exampleModal-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Document Services</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeEditDocumentServices()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_document_service_form" method="post">
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Choose Service<span class="error">*</span>:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="edit_document_service_id[]" id="edit_document_service_id">
                                    <option value="">Select Service</option>
                                </select>
                            <span id="edit_document_service_id_error" class="error"></span>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="editDocumentServices()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeEditDocumentServices()">Close</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>