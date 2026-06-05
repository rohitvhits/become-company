<div class="modal fade" id="serviceByPatientTypeModal" aria-modal="true" role="dialog"
    style="padding-right: 17px; display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Services</h4>
                <button type="button" onclick="clearFormData()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="save_form_service" ecntype="mulitpart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" id="service_patient_id" >
                    <input type="hidden" name="patient_type_wise_service_id" id="patient_type_wise_service_id" value="">
                    <input type="hidden" name="portal_type" id="portal_type" value="">
                                     
                        
                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error">*</span></label>
                        <select class="js-example-basic-multiple w-100 service_id_by_patient_type" multiple="multiple" name="service_id[]"
                            id="service_id_by_patient_type">
                            <option value="">Select Service</option>                            
                        </select>
                        <span class="error mt-2 text-danger" id="service_id_by_patient_type_error"></span>
                    </div>

                    <div class="form-group" id="service_branch_div" style="display:none;">
                        <label class="col-form-label">Branch <span class="error" id="service_branch_mandatory_star" style="display:none;">*</span></label>
                        <select class="form-control" name="branch_id" id="service_branch_id">
                            <option value="">Select Branch</option>
                        </select>
                        <span class="error mt-2 text-danger" id="service_branch_id_error"></span>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Due Date</label>
                        <input type="text" class="form-control document_completed_date" id="service_due_date" name="service_due_date">
                        <span class="error mt-2 text-danger" id="service_due_date_error"></span>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Follow Date</label>
                        <input type="text" class="form-control service_follow_date" id="service_follow_date" name="service_follow_date">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Close</button>
                    <button type="button" id="add_services_request_id_new" class="btn btn-primary pull-right"
                        onclick="savePatientTypeWiseServiceRequest()">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>