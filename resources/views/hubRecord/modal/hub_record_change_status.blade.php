<div class="modal fade" id="change-status" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Phone No</h5>
                <button type="button" class="close" id="close_hub_phone" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="hub_status_update">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Status<span style="color:red">*</span>:</label>
                        <select name="status" id="hub_status">
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Deactivate">Deactivate</option>                        
                        </select>
                        <span id="hub_phone_error" class="error mt-2" for="document_type"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="updateHubPhone()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            
        </div>
    </div>
</div>