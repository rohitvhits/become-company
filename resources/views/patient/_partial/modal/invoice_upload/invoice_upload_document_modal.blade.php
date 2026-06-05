<div class="modal fade" id="exampleModal-invoice" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" enctype="multipart/form-data" action=">" name="adduser" method="post" id="formInvoice">
                    <div class="modal-body">
                    
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="patient_id" value="<?php echo $record->id; ?>">
                        <input type="hidden" name="agency_id" value="<?php echo $record->agency_id;?>">
                        <input type="hidden" name="type" id="type" value="">
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            <select class="form-control w-100 select_class" name="request_service_id" id="invoice_request_service_id" onchange="requestSelectService()">
                                <option value="">Select Request Service</option>
                            </select>
                            <span class="error mt-2" id="invoice_request_service_id_error" for=""></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            <select class="js-example-basic-multiple w-100 select_class" name="document_service_id" id="invoice_document_service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span class="error mt-2" id="invoice_document_service_id_error" for=""></span>
                        </div>
                    
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Attachment<span class="error">*</span>:</label>
                            <input type="file" class="form-control" id="attachmentImg" name="attachment">
                            <span class="error mt-2" id="imagess_error" for="file_name"></span>
                        </div>
            
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="invoiceDocumentSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>