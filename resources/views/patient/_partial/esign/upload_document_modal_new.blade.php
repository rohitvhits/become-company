<style>
    #exampleModal-upload-document-new .modal-footer {
        padding: 4px 1px !important;
    }

    #exampleModal-upload-document-new .form-group label{
        line-height:normal !important;
        margin-bottom:0px !important
    }

    #exampleModal-upload-document-new .modal-header{
        padding:8px 16px !important;
       
    }

    #exampleModal-upload-document-new .modal-title{
        font-size:15px !important;
       
    }
</style>

<div class="modal fade " id="exampleModal-upload-document-new" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel" style="font-size:15px !impor">
                    <i class="mdi mdi-file-upload-outline mr-2"></i>Add Upload Document
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="closed_id_upload_document_new">
                    <span aria-hidden="true" style="color:white">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="edit_upload_document_modal_new">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="eid" id="temp1" value="<?php echo $record->id; ?>">
                <input type="hidden" name="eidc" id="temp1" value="<?php echo $record->patient_code; ?>">
                <input type="hidden" name="receipt_name" value="<?php echo $record->first_name . ' ' . $record->last_name; ?>">

                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label font-weight-semibold">
                            Document Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="document_name" class="form-control" id="documentName" placeholder="Enter Document Name">
                        <span id="documentName_error" class="error mt-2 text-danger d-block" for="document_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label font-weight-semibold">
                            Upload Document
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="fileUpload" name="file_upload">
                        <span class="error mt-2 text-danger d-block" id="fileUpload_error" for="file_name"></span>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="edit_upload_document_modal_submit_new">
                        <span class="spinner-border spinner-border-sm d-none" id="create-upload-doc-modal" role="status" aria-hidden="true"></span>
                        <span id="btn-save-upload-doc-modal">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
