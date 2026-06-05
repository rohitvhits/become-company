<div class="modal fade" id="exampleModal-link-remote-id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Remote Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="CloseRemoteEmployeePopup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="lnkhhx_remote_id">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Employee: <span class="error">*</span></label>
                            <input type="text" name="hha_remote_id" class="form-control" value=""  id="hha_remote_id" style="width:100% !important">
                            <input type="hidden" name="hha_remote_name" class="form-control" value=""  id="hha_remote_name">
                            
          
                            <span class="error hha_remote_id_error"></span>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-remote-id" >Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseRemoteEmployeePopup()">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>