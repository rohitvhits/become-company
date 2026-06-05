<div class="modal fade" id="editServiceModal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Services</h4>
                <button type="button" onclick="clearEditServiceData()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="edit_service" ecntype="mulitpart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="hidden_service_request_id" id="hidden_service_request_id" value="">
                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error">*</span></label>
                        <select class="js-example-basic-multiple edit_services" multiple="multiple" name="edit_service_id[]"
                            id="edit_services">
                            <option value="">Select Service</option>
                        </select>
                        <span class="error mt-2 text-danger" id="edit_services_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Close</button>
                    <button type="button" id="edit_services_request_id" class="btn btn-primary pull-right" onclick="editServices()">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>