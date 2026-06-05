<style>
    #send-rnpad-document-modal .modal-footer {
        padding: 4px 1px !important;
    }
    #send-rnpad-document-modal .modal-header {
        padding: 8px 16px !important;
    }
    #send-rnpad-document-modal .modal-title {
        font-size: 15px !important;
    }
</style>

<div class="modal fade" id="send-rnpad-document-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-file-send mr-2"></i>Send RNPad Document
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeSendRnPadModal()">
                    <span aria-hidden="true" style="color:#ffffff !important">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <div class="form-group">
                    
                    <input type="hidden"  id="rnpad_document_id">
                    <label for="e_fax_no" class="font-weight-semibold">
                        Service
                        <span class="text-danger">*</span>
                    </label>
                    <select name="" class="form-control" id="rnpad_choose_services">
                        <option value="">Select Service</option>
                    </select>
                    <span id="rnpad_choose_services_error" class="error mt-2 text-danger d-block"></span>
                </div>
                
            </div>

            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="submitRndPadDocument()">
                        <span class="spinner-border spinner-border-sm d-none" id="submit-rnpad-doc-spinner" role="status" aria-hidden="true"></span>
                        <span id="btn-submit-rnpad-text">Send</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="closeSendRnPadModal()">
                        Cancel
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>