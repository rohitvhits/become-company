<div class="modal fade" id="send_reply_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Reply Enquiry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" method="post" id="insuranceAdd">
                <div class="modal-body">
                    
                        <input type="hidden" name="_token" value="{{ csrf_token()}}"> 
                        <input type="hidden" id="enquiry_id">  
                        <input type="hidden" id="enquiry_email">                       
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Subject<span class="error">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                placeholder="Enter Subject" maxlength="50">
                            <span class="error-text subject_error error"></span>

                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Message<span class="error">*</span></label>
                            <textarea class="form-control" id="message_id" rows="10"></textarea>
                            <span class="error-text message_id_error error"></span>

                        </div>

                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="sendReplaySubmit"
                            data-uid="">Save</button>
                        <button type="button" class="btn btn-light" onclick="clearSendReplay()" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>