<div class="modal fade" id="exampleModal-phone" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Phone</h5>
                <button type="button" class="close" id="close_phone" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="phone_form_submit_id">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Phone<span style="color:red">*</span>:</label>
                        <input type="text" class="form-control" placeholder="Enter Phone" id="record_phn_id" onkeypress="return isNumber(event)" name="record_phn" value="" maxlength="15">
                        <span id="record_phn_error" class="error mt-2" for="document_type"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="updatePatientPhone()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            
        </div>
    </div>
</div>