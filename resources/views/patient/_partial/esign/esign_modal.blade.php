<div class="modal fade " id="exampleModal-esign" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Esign Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id_esign">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/esign/template/docusign-sent'); ?>" method="POST" id="edit_template_modal">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="eid" id="temp1" value="<?php echo $record->id; ?>">
                <input type="hidden" name="eidc" id="temp1" value="<?php echo $record->patient_code; ?>">
                <input type="hidden" name="receipt_name" value="<?php echo $record->first_name . ' ' . $record->last_name; ?>">
                <input type="hidden" name="type" value="caregiver">
               
                <div class="modal-body">


                    <div class="form-group">
                        <label>Choose Template <span class="error" style="color:red">*</span></label>
                        <select name="template_id" class="form-control" id="template_idold">
                            <option value="">Select Template</option>
                        </select>
                        <span class="error" id="change_templateold_error" style="color:red"></span>
                    </div>

                    @if($record->type == 'Patient')
                    <div class="form-group">
                        <label>Choose Doctor <span class="error" style="color:red">*</span></label>
                        <select name="doctor_id" class="form-control" id="doctor_id">
                            <option value="">Select Doctor</option>
                        </select>
                        <span class="error" id="doctor_id_error" style="color:red"></span>
                    </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_template_modal_submit">Save</button>
                </div>
            </form>
 
            </div> 
        </div>
    </div>