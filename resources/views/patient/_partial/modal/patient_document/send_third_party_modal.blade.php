<style>
    #send-third-party-document-modal .modal-footer {
        padding: 4px 1px !important;
    }
    #send-third-party-document-modal .modal-header {
        padding: 8px 16px !important;
    }
    #send-third-party-document-modal .modal-title {
        font-size: 15px !important;
    }
</style>

<div class="modal fade" id="send-third-party-document-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-file-send mr-2"></i>Send To Third Party
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeThirdPartyModule()">
                    <span aria-hidden="true" style="color:#ffffff !important">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <div id="show_third_party_data"></div>
            </div>

            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="submitThirdPartyDocument()">
                        <span class="spinner-border spinner-border-sm d-none" id="submit-third-party-doc-spinner" role="status" aria-hidden="true"></span>
                        <span id="btn-submit-third-party-text">Send</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="closeThirdPartyModule()">
                        Cancel
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>