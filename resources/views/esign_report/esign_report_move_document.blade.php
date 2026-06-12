<style>
    #esignMoveDocumentModal-1 .modal-footer {
        padding: 4px 1px !important;
    }

    #esignMoveDocumentModal-1 .modal-header {
        padding: 8px 16px !important;
    }

    #esignMoveDocumentModal-1 #esign_document_approval_id .token-input-input-token {
        width: 500px !important;
    }
    #esignMoveDocumentModal-1 .modal-content{
        background-color:transparent;
    }
</style>

<div class="modal fade" id="esignMoveDocumentModal-1" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold documens" id="ModalLabel">
                    <i class="mdi mdi-file-document mr-2"></i>Add Document
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:white">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post" id="esignMoveDocumentForm">
                <div class="modal-body p-4" style="background-color:white">

                    <input type="hidden" name="id" id="move_doc_record_id" value="@if(isset($record->id)) {{ $record->id}} @endif">
                    <input type="hidden" name="esign_doc_id" id="esign_doc_id" value="">
                    <input type="hidden" name="did" id="document_ids" value="">
                    <input type="hidden" name="type" id="type_new" value="">
                    <input type="hidden" name="template_id" id="template_new_id" value="">
                    <input type="hidden" name="group_id" id="group_id" value="">
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label for="document_name" class="font-weight-semibold">
                                    Document Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="esign_document_name" class="form-control" placeholder="Enter Document Name" name="esign_document_name" value="">
                                <span id="document_name_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                            
                            <div class="form-group row">
                                <label for="esign_report_request_service_id" class="font-weight-semibold">
                                    Request Services
                                    
                                </label>
                                <select class="form-control select2 w-100" name="esign_request_service_id[]" id="esign_report_request_service_id">
                                    <option value="">Select Request Service</option>
                                </select>
                                <span id="esign_report_request_service_id_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                            <div class="form-group row">
                                <label for="esign_report_document_service_id" class="font-weight-semibold">
                                    Services
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="js-example-basic-multiple w-100" multiple="multiple" name="esign_document_service_id[]" id="esign_report_document_service_id">
                                    <option value="">Select Service</option>
                                </select>
                                <span id="esign_report_document_service_id_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                            <div class="form-group row">
                                <label for="document_completed_date" class="font-weight-semibold">
                                    Document Completed Date
                                </label>
                                <input type="text" id="esign_document_completed_date" class="form-control" data-inputmask="'alias': 'datetime'" placeholder="MM/DD/YYYY" name="esign_document_completed_date" data-inputmask-inputformat="mm/dd/yyyy" min="1000-01-01" max="9999-12-31" autocomplete="off"  value="">
                                <span id="esign_document_completed_date_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                            <div class="form-group mb-2 row">
                                <div class="form-check form-check-primary mb-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_upload_info_only" name="esign_upload_info_only" value="1">
                                        <i class="input-helper"></i> Upload for Info Only
                                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block mt-0 tx-12">When checked, the document is for information only. When unchecked, signatures or stamps are allowed.</span>
                                        
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row @if($auth->agency_fk == '') @else hide @endif">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_internal_use_esign" name="esign_internal_use_esign" value="1">
                                        <i class="input-helper"></i> Internal Use Only<br>
                                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block mt-0 tx-12">If this checkbox is selected, the agency will not receive any emails.</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row @if($auth->agency_fk == '') @else hide @endif">
                                <div class="form-check form-check-primary mb-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_document_approval" name="esign_document_approval" value="1">
                                        <i class="input-helper"></i> Choose for Document Approval
                                    </label>
                                </div>
                            </div>
                           
                            <div class="form-group row  @if(isset($record->type) && strtolower($record->type) == 'patient' && $auth->agency_fk == '') @else hide @endif" id="esign_document_approval_id">
                                <label for="esign_document_approval_user_id" class="font-weight-semibold">
                                    User <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="esign_document_approval_user_id" class="form-control" id="esign_document_approval_user_id" >
                               
                            </div>
                             <span class="error mt-2" id="esign_document_approval_user_id_error" for="file_name"></span>
                            <div class="form-group mb-2 row @if($auth->agency_fk == '') @else hide @endif">
                                <div class="form-check form-check-primary mb-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_medication_list" name="esign_medication_list" value="1">
                                        <i class="input-helper"></i> Medication List
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row @if($auth->agency_fk == '') @else hide @endif">
                                <div class="form-check form-check-primary mb-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_insurance_eligibility" name="esign_insurance_eligibility" value="1">
                                        <i class="input-helper"></i> Insurance Elg<br>
                                        <span class="error mt-2" id="esign_insurance_eligibility_error" for="file_name"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row @if($auth->agency_fk == '') @else hide @endif">
                                <div class="form-check form-check-primary mb-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="esign_mdo_tag" name="esign_mdo_tag" value="1">
                                        <i class="input-helper"></i> MDO Tag
                                    </label>
                                </div>
                            </div>
                            <div class="form-group hide row" id="esign_mdo_source">
                                <label for="esign_mdo_source_id" class="font-weight-semibold">
                                    MDO Source
                                </label>
                                <select class="form-control select2 w-100" name="esign_mdo_source" id="esign_mdo_source_id" style="width:100%">
                                    <option value="">Select MDO Source</option>
                                    @foreach($masterData as $master)
                                            @if($master->master_type_fk == '35')
                                                <option value="{{ $master->id}}">{{$master->name}}</option>
                                            @endif
                                        @endforeach
                                </select>
                                <span class="error mt-2" id="esign_mdo_source_error" for="file_name"></span>
                            </div>

                        </div>
                        <div class="col-md-7">
                            <iframe id="esign-attachment-iframe" title="" src="" style="width:100%; height:700px; border:1px solid #dee2e6; border-radius:4px;"></iframe>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="esignMoveDocumentSaveReport">
                            <span class="spinner-border spinner-border-sm d-none" id="esign-move-doc-loader" aria-hidden="true"></span>
                            <span id="btn-save-text-esign">Save</span>
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