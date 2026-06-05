<div class="modal fade" id="exampleModal-mobile" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Mobile No</h5>
                <button type="button" class="close" id="close_mobile" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="mobile_form_submit_id">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Mobile<span style="color:red">*</span>:</label>
                        <input type="text" class="form-control" placeholder="Enter Mobile" id="record_mob_id" onkeypress="return isNumber(event)" name="mobile" value="" maxlength="15">
                        <span id="record_mob_error" class="error mt-2" for="document_type"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="updatePatientMobile()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            
        </div>
    </div>
</div>