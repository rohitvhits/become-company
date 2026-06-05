<div class="modal fade" id="serviceEmailRequestModal" aria-modal="true" role="dialog"
    style="padding-right: 17px; display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Request Services</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="save_service" ecntype="mulitpart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $record->id }}">
                    <input type="hidden" name="patient_wise_service_id" id="patient_wise_service_id" value="">
                    <input type="hidden" name="email" id="email" value="{{ $record->email }}">                    

                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error">*</span></label>
                        <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]"
                            id="service_id_email">
                            <option value="">Select Service</option>                            
                        </select>
                    </div>

                    <span class="error mt-2 text-danger" id="service_eid_error"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary pull-right"
                        onclick="saveEmailServiceRequest()">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>