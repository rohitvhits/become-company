<div class="modal fade" id="edit-exampleModal-invoice-document" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Invoice Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_document_service_form" method="post">
                    <input type="hidden" name="doc_id" id="edit_document_main_id">
                    <input type="hidden" name="type" id="type" value="">
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services</label>
                            <select class="form-control select_class w-100" name="request_service_id" id="edits_request_service_id" onchange="requestSelectService('edit')">
                                <option value="">Select Request Service</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Service<span class="error">*</span>:</label>
                            <select class="form-control select_class w-100" name="edit_document_service_id" id="edit_document_service_id">
                                    <option value="">Select Service</option>
                                </select>
                            <span id="edit_document_service_id_error" class="error"></span>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="editInvoiceDocument()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>