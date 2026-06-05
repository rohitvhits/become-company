<div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeDocumentSection()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/document-send-patientId'); ?>" name="adduser" method="post" id="formnew">
                    <div class="modal-body">

                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <input type="hidden" name="did" id="document_ids" value="">
                        @if($auth->agency_fk == '')
                        <!-- <div class="form-check form-check-primary">
                            <label class="form-check-label">
                                <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="is_checked" name="is_checked" value="">
                                <i class="input-helper"></i>
                            <i class="input-helper"></i> Only for Nybest</label>
                        </div> -->
                        @endif
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                            <input type="text" name="document_id" class="form-control" id="datenew_id">
                            <span id="document_id_error" class="error mt-2" for="document_type"></span>
                        </div>
                    
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            <select class="form-control select2 w-100" name="request_service_id[]" id="request_service_id" onchange="requestSelectService()">
                                <option value="">Select Request Service</option>
                            </select>
                            <span id="request_service_id_error" class="error mt-2" for="request_service_id"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="document_service_id[]" id="document_service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span id="document_service_id_error" class="error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Document Completed Date:</label>
                            <input type="text" class="form-control document_completed_date" id="document_completed_date" name="document_completed_date">
                            <span id="document_completed_date_error" class="error mt-2" for="document_type"></span>
                        </div>
                    
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Attachment<span class="error">*</span>:</label>
                            <input type="file" class="form-control" id="timeidnew" name="images">
                            <span class="error mt-2" id="images_error" for="file_name"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="documentSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeDocumentSection()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>