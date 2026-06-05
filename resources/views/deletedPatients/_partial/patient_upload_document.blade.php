<div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/document-send-patientId'); ?>" name="adduser" method="post" id="formnew">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <input type="hidden" name="did" id="document_ids" value="">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                            <input type="text" name="document_id" class="form-control" id="datenew_id">
                            <span id="document_id_error" class="error mt-2" for="document_type"></span>
                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="document_service_id[]" id="document_service_id">
                                <option value="">Select Service</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Attachment<span class="error">*</span>:</label>
                            <input type="file" class="form-control" id="timeidnew" name="images">
                            <span class="error mt-2" id="images_error" for="file_name"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="documentSave">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>