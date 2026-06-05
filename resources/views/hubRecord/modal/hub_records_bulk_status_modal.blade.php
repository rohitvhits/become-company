<div class="modal fade" id="statusmodal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Status</h5>
                <button type="button" class="close" id="close_status" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post"
                id="hub_status_submit_id">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="hub_record_ids" id="hub_record_ids" value="">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Status<span
                                style="color:red">*</span>:</label>
                        <select name="status_id" id="status_id"
                            style="max-width: 200px;float:right;margin-right:10px;border-radius:4px;height:36px;"
                            class="form-control">

                            <option value="active">Active</option>
                            <option value="deactivated">Deactive</option>
                        </select>
                        <span id="hub_status_error" class="error mt-2" for="document_type"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="updateHubStatus()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>