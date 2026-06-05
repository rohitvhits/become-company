<div class="modal fade" id="view-exampleModal-document-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">View Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeReviewModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                
                <div class="row">
                    <div class="col-md-8">
                        <iframe id="show_over_review_document_id" src="" style="width:100%;height:600px"></iframe>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" name="document_id" id="review_document_id">
                        <p><strong>Document Name</strong><br>
                            <span id="review_over_document_name"></span>
                        </p>
                        <p><strong>Requested Id</strong><br>
                            <span id="review_over_requested_id"></span>
                        </p>
                        <p><strong>Attachment Service</strong><br>
                            <span id="review_over_attachment_service_id"></span>
                        </p>
                        <p><strong>Document Completion Date</strong><br>
                            <span id="review_over_document_completion_date"></span>
                        </p>

                        <p><strong>Created Date / Created By</strong><br>
                            <span id="review_over_document_created_date"></span>
                        </p>
                      
                        <p><strong>Status</strong><br>
                            <span id="review_over_document_status"></span>
                        </p>
                       
                        <p><strong>Assign User</strong><br>
                            <span id="review_over_document_assign_by"></span>
                        </p>
                        
                        <p><strong>Review Date / Review By</strong><br>
                            <span id="review_over_document_review_by"></span>
                        </p>
                       
                        <p><strong>Notes</strong><br>
                            <span id="review_over_document_review_notes" style="white-space:pre-line"></span>
                        </p>
                        
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>