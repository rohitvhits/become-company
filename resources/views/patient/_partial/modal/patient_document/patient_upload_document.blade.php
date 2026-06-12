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
                       
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                            <input type="text" name="document_id" class="form-control" id="datenew_id">
                            <span id="document_id_error" class="error mt-2" for="document_type"></span>
                        </div>
                    
                        <div class="form-group">
                        @if(auth()->user()->agency_fk !="")
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            @else
                            <label for="recipient-name" class="col-form-label">Request Services:</label>
                            @endif
                            <select class="form-control select2 w-100 js-example-basic-multiple" name="request_service_id[]" id="request_service_id" onchange="requestSelectService()">
                                <option value="">Select Request Service</option>
                            </select>
                            <span id="request_service_id_error" class="error mt-2" for="request_service_id"></span>
                        </div>
                        
                        <div class="form-group">
                        @if(auth()->user()->agency_fk !="")
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            @else
                            <label for="recipient-name" class="col-form-label">Services:</label>
                            @endif
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="document_service_id[]" id="document_service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span id="document_service_id_error" class="error mt-2" for="document_type"></span>
                            <span id="document_branch_error" class="error mt-2" style="color:red;"></span>
                        </div>
                        <div class="form-group">
                        @if(auth()->user()->agency_fk !="")
                        <label for="message-text" class="col-form-label">Document Completed Date:</label>
                        @else
                        <label for="message-text" class="col-form-label">Document Completed Date:</label>
                        @endif
                            <input type="text" class="form-control document_completed_date" id="document_completed_date" name="document_completed_date">
                            <span id="document_completed_date_error" class="error mt-2" for="document_type"></span>
                        </div>
                    
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Attachment<span class="error">*</span>:</label>
                            <input type="file" class="form-control" id="timeidnew" name="images">
                            <span class="error mt-2" id="images_error" for="file_name"></span>
                        </div>

                        <div class="form-check form-check-primary mb-0">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="upload_for_info_only" name="upload_for_info_only" value="1">
                                <i class="input-helper"></i> Upload for Info Only
                            </label>
                        </div>
                        <span class="text-muted d-block mt-1 tx-12"><i class="mdi mdi-information-outline mr-1"></i>When checked, the document is for <b>information only</b>. When unchecked, <b>signatures or stamps</b> are allowed.</span>

                        @if($auth->agency_fk == '')
                        <div class="form-check form-check-primary mb-0">
                            <label class="form-check-label">
                                <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="internal_use" name="internal_use" value="1">
                                <i class="input-helper"></i>
                            <i class="input-helper"></i> Internal Use Only</label>
                        </div>
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block mt-0 tx-12">If this checkbox is selected, the agency will not receive any emails.</span>
                        @endif

                        @if($auth->agency_fk == '')
                            <div class="form-group">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="patient_document_review" name="document_review" value="1" @if($record->type == 'Patient') checked @endif>
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Choose for Document Approval</label>
                                </div>
                            </div>
                       
                            <div id="document_approval_id" class="hide">
                        
                                <div class="form-group">
                                <label  class="col-form-label">User @if($record->type == 'Caregiver')<span class="error">*</span>@endif:</label>
                                    <div>
                                    <input type="text" name="document_approval_user_id" class="form-control" id="document_approval_user_id">
                                    </div>
                                    
                                        <span class="error mt-2" id="document_approval_user_id_error" for="file_name"></span>
                                </div>
                        
                            </div>

                            <div class="form-group">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="medication_list" name="medication_list" value="1">
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Medication List</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check form-check-primary mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="insurance_elg" name="insurance_elg" value="1">
                                        <i class="input-helper"></i>
                                    <i class="input-helper"></i>Insurance Elg</label>
                                </div>
                            </div>
                            <span class="error mt-2" id="add_medication_insurance_err" for="medication_insurance"></span>

                            <div class="form-group row align-items-center mb-0">
                                <div class="col-4">
                                    <div class="form-check form-check-primary mb-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="mdo_tag" name="mdo_tag" value="1">
                                            <i class="input-helper"></i>
                                        <i class="input-helper"></i>MDO Tag</label>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 w-100 hide" name="mdo_source" id="mdo_source">
                                        <option value="">Select MDO Source</option>
                                        @foreach($masterData as $master)
                                            @if($master->master_type_fk == '35')
                                                <option value="{{ $master->id}}">{{$master->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <span class="error mt-2" id="add_mdo_tag_err" for="mdo_tag"></span>
                        @endif
                    </div>
                    <div class="modal-footer">
                    <img src="{{ asset('ajax-loader.gif')}}" alt="loader" id="loadertag_doc" style="display:none">
                     @can('ai-analyse-doc')
                        @if(auth()->user()->agency_fk == "")
                        <button type="button" class="btn-hmw" id="documentSaveAnalyse" style="padding:6px 16px !important;font-size:14px !important;border-radius:17px !important;height:40px !important;line-height:1.5 !important;"><svg width="15" height="15" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;flex-shrink:0;"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg> Save & Analyse</button>
                        @endif
                        @endcan
                        <button type="button" class="btn btn-success" id="documentSave">Save</button>
                       
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeDocumentSection()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

