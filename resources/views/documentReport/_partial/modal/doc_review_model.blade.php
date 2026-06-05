<div class="modal fade" id="review-document-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2 d-flex align-items-center justify-content-between">
                <div class="d-flex flex-column text-left col-md-8">
                    <h4 class="modal-title mb-1 d-flex align-items-center" style="font-size: 19px !important;">
                        <i class="fas fa-file-alt mr-1"></i>Review Document
                        <span class="badge badge-light ml-2" id="agency_name"></span>
                    </h4>
                    <div class="header-info small d-flex flex-wrap">
                        <span class="mr-3 mb-1"><strong>Agency:</strong> <span id="review_agency_name">-</span></span>
                        <span class="mr-3 mb-1"><strong>Portal ID:</strong> <span id="review_portal_id">-</span></span>
                        <span class="mr-3 mb-1"><strong>Patient:</strong> <span id="review_patient_name">-</span></span>
                        <span class="mr-3 mb-1"><strong>Gender:</strong> <span id="review_gender_name">-</span></span>
                        <span class="mb-1"><strong>Birth Date:</strong> <span id="review_birth_date">-</span></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="close text-white ml-3" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <a class="pull-right btn btn-info btn-sm d-none d-md-block mr-2 mt-2 ml-1" style="border-radius: 4px;" id="sign-url" onclick="openSignPopup();" target="_blank" title="Add Sign/Stamp"><i class="mdi mdi-plus"></i>Add Sign/Stamp</a>
                    <a data-toggle="modal" class="pull-right btn btn-primary btn-sm d-none d-md-block mr-2 mt-2 ml-3" style="border-radius: 4px;" onclick="refreshDoc();"><i class="mdi mdi-refresh"></i>Refresh</a>
                </div>
                
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-7 border-right">
                        <div class="p-2">
                            <iframe id="show_review_document_id" src="" class="w-100" style="height: 600px; border: 1px solid #dee2e6; border-radius: 4px;"></iframe>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="p-2">
                            <div class="card mb-2">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fa fa-list mr-2"></i>Document Details</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="document-details">
                                        <div class="detail-row">
                                            <span class="detail-label">Document Name:</span>
                                            <input type="hidden" id="review_document_id" value="">
                                            <input type="hidden" id="review_doc_name" value="">
                                            <input type="hidden" id="modal_patient_type" value="">
                                            <span class="detail-value" id="review_document_name" data-review-name="">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">SSN:</span>
                                            <span class="detail-value" id="review_ssn">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Requested ID:</span>
                                            <span class="detail-value" id="review_requested_id">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Attachment Service:</span>
                                            <span class="detail-value" id="review_attachment_service_id">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Document Completion Date:</span>
                                            <span class="detail-value" id="review_document_completion_date">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Status:</span>
                                            <span class="detail-value" id="review_document_status">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Assigned To:</span>
                                            <span class="detail-value" id="review_document_assign_by">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Review Date / Reviewer:</span>
                                            <span class="detail-value" id="review_over_document_review_by_model">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Created Date / Created By:</span>
                                            <span class="detail-value" id="review_document_created_date"></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label"> Notes: </span>
                                            <span class="detail-value" id="review_over_document_review_notes_model"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="remove_hide_show" class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fa fa-comment mr-2"></i> Review Action</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="form-group mb-2">
                                        <div class="form-group internal_use_div" style="margin-top:10px">
                                            <div class="form-check form-check-primary mb-0">
                                                <label class="form-check-label">
                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" id="internal_use" name="internal_use" value="1">
                                                    <i class="input-helper"></i>
                                                <i class="input-helper"></i> Internal Use Only<i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <label class="font-weight-bold small">Action <span class="text-danger">*</span></label>
                                        <div class="mt-1">
                                            <div class="custom-control custom-radio d-inline-block mr-3">
                                                <input type="radio" id="approve" name="pdf_status" value="1" class="custom-control-input radio-review">
                                                <label class="custom-control-label small" for="approve">Approve</label>
                                            </div>
                                            <div class="custom-control custom-radio d-inline-block">
                                                <input type="radio" id="reject" name="pdf_status" value="0" class="custom-control-input radio-review">
                                                <label class="custom-control-label small" for="reject">Reject</label>
                                            </div>
                                        </div>
                                        <span id="pdf_status_error" class="text-danger small"></span>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold small">Reason <span id="rejected_notes_error" class="text-danger hide">*</span></label>
                                        <textarea class="form-control form-control-sm" name="pdf_status_reason" id="pdf_status_reason" rows="3" placeholder="Enter reason for your decision"></textarea>
                                        <span id="pdf_status_reason_error" class="text-danger small"></span>
                                    </div>

                                    <div class="text-right">
                                        <button type="button" class="btn btn-light btn-sm mr-1" onclick="closeReviewModal()">Cancel</button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="saveFormBtn()">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>