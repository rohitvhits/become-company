<div class="modal fade" id="review-exampleModal-document-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Review Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeReviewModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-8">
                        <iframe id="show_review_document_id" src="" style="width:100%;height:600px"></iframe>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" name="document_id" id="review_document_id">
                        <p><strong>Document Name</strong><br>
                            <span id="review_document_name"></span>
                        </p>
                        <p><strong>Requested Id</strong><br>
                            <span id="review_requested_id"></span>
                        </p>
                        <p><strong>Attachment Service</strong><br>
                            <span id="review_attachment_service_id"></span>
                        </p>
                        <p><strong>Document Completion Date</strong><br>
                            <span id="review_document_completion_date"></span>
                        </p>

                        <p><strong>Created Date / Created By</strong><br>
                            <span id="review_document_created_date"></span>
                        </p>

                        <p><strong>Status</strong><br>
                            <span id="review_document_status"></span>
                        </p>

                        <p><strong>Assign User</strong><br>
                            <span id="review_document_assign_by"></span>
                        </p>
                        <div id="remove_hide_review_show">
                            <p><strong>Review Date / Review By</strong><br>
                                <span id="review_over_document_review_by"></span>
                            </p>
                        
                            <p><strong>Notes</strong><br>
                                <span id="review_over_document_review_notes" style="white-space:pre-line"></span>
                            </p>
                        </div>
                        
                        <div id="remove_hide_show">
                            <p><strong>Action</strong><span class="error">*</span></p>
                            <div>
                                <label class="radio-label">
                                    <input type="radio" name="pdf_status" value="1" class="radio-review"> Approve
                                </label>
                                <label class="radio-label ml-2">
                                    <input type="radio" name="pdf_status" value="0" class="radio-review"> Reject
                                </label>

                            </div>
                            <span id="pdf_status_error" class="error"></span>
                            <p class="pdf_status_reason"><strong>Reason</strong><span id="rejected_notes_error" class="error hide">*</span></p>
                            <textarea class="pdf_status_reason" name="pdf_status_reason" id="pdf_status_reason" rows="4"
                                placeholder="Enter reason" style="width:350px"></textarea>
                            <span id="pdf_status_reason_error" class="error"></span>

                            <div class="actions">
                                <button type="button" class="btn btn-secondary btn-sm pull-right" onclick="closeReviewModal()">Cancel</button>
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="saveFormBtn()">Save</button>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>