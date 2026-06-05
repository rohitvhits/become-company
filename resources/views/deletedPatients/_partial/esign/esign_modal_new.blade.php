<div class="modal fade " id="exampleModal-esign-new" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Esign Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id_esign_new">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('esign/template/docusign-sent-new')}}" method="POST" id="edit_template_modal_new">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="eid" id="temp1" value="{{$record->id}}">
                <input type="hidden" name="eidc" id="temp12" value="{{ $record->patient_code }}">
                <input type="hidden" name="receipt_name" value="{{ $record->first_name }} {{ $record->last_name}}">
                <input type="hidden" name="type" id="type" value="{{ $record->type}}">
               
                <div class="modal-body">
                    <div class="form-group">
                        <label>Choose Template <span class="error" style="color:red">*</span></label>
                        <select name="template_id" class="form-control" id="template_idNew">
                            <option value="">Select Template</option>
                        </select>
                        <span class="error" id="template_idNew_error" style="color:red"></span>
                    </div>
                    @if($record->type == 'Patient')
                    <div class="form-group">
                        <label>Choose Doctor <span class="error" style="color:red">*</span></label>
                        <select name="doctor_id" class="form-control" id="doctor_idNew">
                            <option value="">Select Doctor</option>
                        </select>
                        <span class="error" id="doctor_idNew_error" style="color:red"></span>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_template_modal_submit_new">Save</button>
                </div>
            </form>
 
            </div> 
        </div>
    </div>