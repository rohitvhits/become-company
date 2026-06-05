<style>
    #exampleModal-add-send_poc_modal .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="exampleModal-add-send_poc_modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-help-circle-outline mr-2"></i>POC Document
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_create_poc_question_id">
                <input type="hidden" name="hha_visit_task_health_id" id="hha_visit_task_health_id">
                <input type="hidden" name="hha_visit_portal_id" id="hha_visit_portal_id">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="poc_hha_patient_document_type" class="font-weight-semibold">
                                    HHA Patient Document Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="poc_hha_patient_document_type" id="poc_hha_patient_document_type" class="form-control form-control-lg">
                                    <option value="">Select HHA Patient Document Type</option>
                                
                                </select>
                                <span id="poc_hha_patient_document_type_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <iframe src="" title="" id="poc_document_iframe" style="width:100%;height:300px"></iframe>
                        </div>
                        <input type="hidden" id="poc_doc_file_name" name="poc_doc_file_name">
                        <input type="hidden" id="hha_poc_type_document_name" name="hha_poc_type_document_name">
                        <input type="hidden" id="hha_priview_type_document_name" name="hha_priview_type_document_name">
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="createHHAPOCQuestion()">
                            <span class="spinner-border spinner-border-sm d-none" id="create-hha-pocquestion" aria-hidden="true"></span>
                            <span id="btn-save-question">Save</span>
                        </button>
                        
                        <button type="button" class="btn btn-secondary btn-sm px-4 " data-dismiss="modal">
                            Cancel
                        </button>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
