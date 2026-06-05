<div class="modal fade" id="exampleModal-bulk-assign-user" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Bulk Assign User</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeBulkAssignUserModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('save-bulk-assign-user')}}" method="post" enctype="multipart/form-data" id="form_bulk_assign_user_id">
                @csrf
                <input type="hidden" id="bulk_appointments_id" name="bulk_appointments_id">
                <div class="modal-body">
                <label for="user_id">Assign User <span class="text-danger">*</span></label>
                    <div class="form-group">
                        
                        <input type="text" name="bulk_user_id" id="bulk_user_id" class="form-control">
                        <span id="bulk_user_id_error" class="text-danger bulk_user_id_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" name="submit" value="Submit" class="btn btn-primary" >
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>


                </div>

            </form>
        </div>
    </div>
</div>