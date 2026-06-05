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
                    <input type="hidden" name="doc_id" id="edit_document_main_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                            <input type="text" class="form-control" id="edit_doc_name" name="edit_doc_name">
                            <span id="edit_doc_name_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name:</label>
                            <input type="text" class="form-control" id="edit_doc_name" name="edit_doc_name">
                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            <select class="form-control select2 w-100" name="request_service_id[]" id="edits_request_service_id" onchange="requestSelectService('edit')">
                                <option value="">Select Request Service</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Choose Service<span class="error">*</span>:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="edit_document_service_id[]" id="edit_document_service_id">
                                    <option value="">Select Service</option>
                                </select>
                            <span id="edit_document_service_id_error" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Document Completed Date:</label>
                            <input type="text" class="form-control document_completed_date" id="edit_document_completed_date" name="edit_document_completed_date">

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