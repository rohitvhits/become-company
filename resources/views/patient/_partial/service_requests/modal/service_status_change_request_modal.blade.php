<div class="modal fade" id="serviceChangeStatusModal" aria-modal="true" role="dialog"
    style="padding-right: 17px; display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Status</h4>
                <button type="button" onclick="clearChangeStatusFormData()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="save_status_form_service" ecntype="mulitpart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" id="service_patient_id_status">
                        
                    <div class="form-group">
                        <label class="col-form-label">Status<span class="error">*</span></label>
                        <select class="js-example-basic-multiple w-100 service_id_by_patient_type" name="status"
                            id="service_status">
                            <option value="">Select Status</option>
                            <option value="Scheduled">Scheduled</option>                            
                            <option value="Pending">Pending</option>                            
                            <option value="Completed">Completed</option>                            
                            <option value="Cancelled">Cancelled</option>                            
                            <option value="Refused">Refused</option>                            
                            <option value="InService">In Service</option>                            
                            <option value="OnHold">On Hold</option>                            
                            <option value="OnLeave">On Leave</option>                            
                            <option value="Terminated">Terminated</option>                            
                            <option value="MarkAsCheckIn">Mark as CheckIn</option>                                                        
                            <option value="MarkAsProcessing">Mark as Processing</option>                                                        
                            <option value="MarkAsCompleted">Mark as Completed</option>                                                        
                            <option value="MarkAsCancel">Mark as Cancel</option>                                                        
                            <option value="MarkAsNoShow">Mark as NoShow</option>                                                        
                            <option value="MarkAsRefused">Mark as Refused</option>                                                        
                            <option value="Undo">Undo</option>                                                        
                            <option value="MarkAsHospitalized/Rehab">Mark as Hospitalized/Rehab</option>                                                        
                            <option value="UnableToContact">Unable To Contact</option>                                                        
                            <option value="PendingTermination">Pending Termination</option>                                                                              
                        </select>
                        <span class="error mt-2 text-danger" id="service_status_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-right"
                onclick="savePatientStatusTypeWiseServiceRequest()">Save</button>
                    <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>