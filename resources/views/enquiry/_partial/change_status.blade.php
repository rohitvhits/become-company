<div class="modal fade" id="change_status_form_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" method="post" id="change_enquiry_form">
                <div class="modal-body">
                    
                        <input type="hidden" name="_token" value="{{ csrf_token()}}"> 
                        <input type="hidden" id="change_enquiry_id">  
       
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Status<span class="error">*</span></label>
                            <select class="form-control" id="status_id">
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <span class="error-text status_id_error error"></span>

                        </div>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="changeStatusId"
                            data-uid="">Save</button>
                        <button type="button" class="btn btn-light"  data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>