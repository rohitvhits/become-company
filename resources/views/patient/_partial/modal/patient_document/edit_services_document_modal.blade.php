<div class="modal fade" id="edit-exampleModal-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Document Services</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeEditDocumentServices()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_document_service_form" method="post">
                    <input type="hidden" name="doc_id" id="edit_document_main_id">
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                            <input type="text" class="form-control" id="edit_doc_name" name="edit_doc_name">
                            <span id="edit_doc_name_error" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            <select class="form-control select2 w-100" name="request_service_id[]" id="edits_request_service_id" onchange="requestSelectService('edit')">
                                <option value="">Select Request Service</option>
                            </select>
                            <span id="edits_request_service_id_error" class="error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Choose Service
                            @if(strtolower($record->type) == 'caregiver')<span class="error">*</span>@endif:</label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="edit_document_service_id[]" id="edit_document_service_id">
                                    <option value="">Select Service</option>
                                </select>
                            <span id="edit_document_service_id_error" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Document Completed Date
                            @if(strtolower($record->type) == 'caregiver')<span class="error">*</span>@endif:</label>
                            <input type="text" class="form-control document_completed_date" id="edit_document_completed_date" name="edit_document_completed_date">
                            <span id="edit_document_completed_date_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-top:-10px">
                            <div class="form-check form-check-primary mb-0">
                                <label class="form-check-label">
                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="edit_internal_use" name="edit_internal_use" value="1">
                                    <i class="input-helper"></i>
                                <i class="input-helper"></i> Internal Use Only<i class="input-helper"></i></label>
                            </div>
                            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block mt-0 tx-12">If this checkbox is selected, the agency will not receive any emails.</span>

                        </div>
                        <div class="not-approved-div">
                            @if($auth->agency_fk == '')
                            <div class="form-group"  style="margin-top:-10px">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="edit_patient_document_review" name="edit_document_review" value="1">
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Choose for Document Approval</label>
                                </div>
                            </div>
                            @endif
                            <div id="edit_document_approval_id" class="hide"  style="margin-top:-10px"> 
                        
                                <div class="form-group">
                                    <label  class="col-form-label">User @if($record->type == 'Caregiver')<span class="error">*</span>@endif:</label>
                                    <div>
                                    <input type="text" name="edit_document_approval_user_id" class="form-control" id="edit_document_approval_user_id">
                                    </div>
                                    
                                        <span class="error mt-2" id="edit_document_approval_user_id_error" for="file_name"></span>
                                </div>
                        
                            </div>
                        </div>
                        @if($auth->agency_fk == '')
                            <div class="form-group">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="edit_medication_list" name="edit_medication_list" value="1">
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Medication List</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="edit_insurance_elg" name="edit_insurance_elg" value="1">
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Insurance Elg</label>
                                </div>
                            </div>
                            <span class="error mt-2" id="edit_medication_insurance_err" for="edit_medication_insurance"></span>

                            <div class="form-group row">
                                <div class="col-4">
                                    <div class="form-check form-check-primary mb-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="edit_mdo_tag" name="edit_mdo_tag" value="1">
                                            <i class="input-helper"></i>
                                        <i class="input-helper"></i>MDO Tag</label>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 w-100 hide" name="edit_mdo_source" id="edit_mdo_source">
                                        <option value="">Select MDO Source</option>
                                        @foreach($masterData as $master)
                                            @if($master->master_type_fk == '35')
                                                <option value="{{ $master->id}}">{{$master->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <span class="error mt-2" id="edit_mdo_tag_err" for="edit_mdo_tag"></span>
                        @endif
                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="editDocumentServices()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeEditDocumentServices()">Close</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>