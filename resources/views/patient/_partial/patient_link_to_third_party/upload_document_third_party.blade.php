<style>
    #exampleModal-visiting-aid-document .modal-footer {
        padding: 4px 1px !important;
    }
    #exampleModal-visiting-aid-document .modal-header {
        padding: 8px 16px !important;
    }
    #exampleModal-visiting-aid-document .modal-title {
        font-size: 15px !important;
    }
</style>

<div class="modal fade" id="exampleModal-visiting-aid-document" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-file-send mr-2"></i>Send Visiting Medical
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeVistingThirdPartyModule()">
                    <span aria-hidden="true" style="color:#ffffff !important">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" >
                <div class="card mb-2 shadow-sm">
                    <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                        <strong class="small">Pending Medical </strong>
                        <small class="form-text text-muted ml-2"><strong>(This will update Pending medical)</strong></small>
                    </div>
                    <form id="visiting_third_party_medical_form">
                        <div class="card-body py-2 px-2">
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="visiting_third_party_medical_id" class="col-form-label">Medical Name<span style="color:red">*</span>:</label>
                                        <select name="visiting_third_party_medical_id[]" class="form-control" id="visiting_third_party_medical_id" multiple ></select>
                                        <span class="spinner-border spinner-border-sm d-none ml-2" id="visiting-medical-loader" role="status" aria-hidden="true"></span>
                                        <span id="visiting_third_party_medical_id_error" class="text-dander"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="visiting_notes" class="col-form-label">Notes:</label>
                                        <textarea class="form-control" colspan="5" rows="5" placeHolder="Enter Notes" style="
    margin-top: -1px;
" name="visiting_notes"></textarea>
                                    </div>
                                </div>
                                

                            </div>
                            <span class="row" id="multipleThirdPartyMedicalResultId" style="display:none">
                                
                            </span>
                        </div>
                    </form>
                    
                </div>
            </div>

            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="submitVistingThirdPartyDocument()">
                        <span class="spinner-border spinner-border-sm d-none" id="submit-third-party-doc-spinner" role="status" aria-hidden="true"></span>
                        <span id="btn-submit-third-party-text">Send</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="closeVistingThirdPartyModule()">
                        Cancel
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>