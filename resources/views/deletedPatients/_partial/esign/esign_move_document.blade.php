<div class="modal fade" id="esignMoveDocumentModal-1" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post" id="esignMoveDocumentForm">
                    <div class="modal-body">
                    
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <input type="hidden" name="esign_doc_id" id="esign_doc_id" value="">
                        <input type="hidden" name="did" id="document_ids" value="">
                        <input type="hidden" name="type" id="type" value="">
                        <input type="hidden" name="template_id" id="template_id" value="">
                        <input type="hidden" name="group_id" id="group_id" value="">
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            <select class="form-control select2 w-100" name="request_service_id[]" id="esign_request_service_id" onchange="requestSelectService()">
                                <option value="">Select Request Service</option>
                            </select>
                            <span id="esign_request_service_id_error" class="error mt-2" for=""></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="document_service_id[]" id="esign_document_service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span id="esign_document_service_id_error" class="error mt-2" for=""></span>
                        </div>
                           
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="esignMoveDocumentSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>