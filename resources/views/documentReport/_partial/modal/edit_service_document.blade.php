<div class="modal fade" id="edit-exampleModal-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Document Services</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeEditDocumentServicesNew()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_document_service_form" method="post">
                    <input type="hidden" name="doc_id" id="edit_document_main_id">
                    <input type="hidden" name="updated_module_flag" id="updated_module_flag">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <iframe id="show_document_id" src="" style="width:100%;height:600px"></iframe>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Request Services:</label>
                                    <select class="form-control select2 w-100 js-example-basic-multiple" name="request_service_id[]" id="edits_request_service_id" onchange="requestSelectService('edit')">
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
                                    <label for="recipient-name" class="col-form-label">Document Completed Date<span class="error">*</span>:</label>
                                    <input type="text" class="form-control document_completed_date" id="edit_document_completed_date" name="edit_document_completed_date">
                                    <span id="edit_document_completed_date_error" class="error"></span>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="editDocumentServicesNew()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeEditDocumentServicesNew()">Close</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>