<div class="modal fade" id="exampleModal-edit-modal-services" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="" style="text-transform:capitalize"></span>Edit Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_edit_service_id">
                <input type="hidden" id="record_id" name="record_id" >
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service Name<span class="error">*</span>:</label>
                        <input type="text" class="form-control" placeholder="Enter Name" id="edit_service_name" name="service_name" value="">
                        <span id="edit_service_name_error" class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service Type<span class="error">*</span>:</label>
                        <select name="service_type" class="form-control" id="edit_service_type">
                            <option value="">Select Service Type</option>
                            <option value="Patient">Patient</option>
                            <option value="Caregiver">Caregiver</option>

                        </select>
                        <span id="edit_service_type_error" class="error"></span>

                    </div>

                    <div class="form-group">
                        <label for="is_disabled1" class="col-form-label">Enable For Nybest User:</label>
                        <label  class="toggle-switch toggle-switch-success">
                            <input type="checkbox" name="enabled_nubest_user" value="1" id="edit_enabled_nubest_user">
                            <span class="toggle-slider round"></span>
                        </label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="updateServices()">Update</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>