<style>
    #exampleModal-signed-mdorder-hha .modal-footer {
        padding: 4px 1px !important;
    }

    #exampleModal-signed-mdorder-hha .modal-header {
        padding: 8px 16px !important;
    }
</style>

<div class="modal fade" id="exampleModal-signed-mdorder-hha" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-medical-bag mr-2"></i>Add Signed
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_mdo_signed">
                <input type="hidden" id="upload_doc_mdo_id">
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="mdo_signed_date" class="font-weight-semibold">
                           Completed Date
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="mdo_signed_date"
                               class="form-control mdo_signed_date"
                               placeholder="Enter Completed Date"
                               name="mdo_signed_date"
                               value="" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" min="1000-01-01" max="9999-12-31">
                        <span id="mdo_signed_date_error" class="error mt-2 text-danger d-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="mdo_signed_upload_document" class="font-weight-semibold">
                            Upload Document
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file"
                               id="mdo_signed_upload_document"
                               class="form-control mdo_signed_upload_document"
                               placeholder="Enter Procedure Name"
                               name="mdo_signed_upload_document"
                               value="">
                        <span id="mdo_signed_upload_document_error" class="error mt-2 text-danger d-block"></span>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="sendHHAMDODocument()">
                            <span class="spinner-border spinner-border-sm d-none" id="create-hha-mdo-order" role="status" aria-hidden="true"></span>
                            <span id="btn-save-text-hha-mdo-order">Save</span>
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
