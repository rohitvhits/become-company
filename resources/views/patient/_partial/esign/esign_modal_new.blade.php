<style>
    #exampleModal-esign-new .modal-footer {
        padding: 4px 1px !important;
    }

    #exampleModal-esign-new .modal-header {
        padding: 8px 16px !important;
    }
</style>

<div class="modal fade" id="exampleModal-esign-new" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-file-document-edit-outline mr-2"></i>Add Esign Section
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="closed_id_esign_new">
                    <span aria-hidden="true"  style="color:#fff">&times;</span>
                </button>
            </div>
            <form action="{{ url('esign/template/docusign-sent-new')}}" method="POST" id="edit_template_modal_new">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="eid" id="temp1" value="<?php echo $record->id; ?>">
                <input type="hidden" name="eidc" id="temp1" value="<?php echo $record->patient_code; ?>">
                <input type="hidden" name="receipt_name" value="<?php echo $record->first_name . ' ' . $record->last_name; ?>">
                <input type="hidden" name="type" id="type" value="<?php echo $record->type; ?>">

                <div class="modal-body p-4" style="background-color:#fff">
                    @if($record->type == 'Caregiver')
                    @php
                        $userTemplateType = auth()->user()->template_type ?? 'All';
                    @endphp
                        @if(strtolower($userTemplateType) == 'all')
                        <div class="form-group">
                            <label class="font-weight-semibold">Template Type</label>
                            <div class="esign-template-type">
                                <label class="mr-3">
                                    <input type="radio" name="template_type" value="" checked> All
                                </label>
                                <label class="mr-3">
                                    <input type="radio" name="template_type" value="location"> Location
                                </label>
                                <label>
                                    <input type="radio" name="template_type" value="telehealth"> Telehealth
                                </label>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="template_type" value="{{ strtolower($userTemplateType) }}">
                        @endif
                    @endif

                    <div class="form-group">
                        <label for="template_idNew" class="font-weight-semibold">
                            Choose Template
                            <span class="text-danger">*</span>
                        </label>
                        <select name="template_id" class="form-control form-control-lg" id="template_idNew">
                            <option value="">Select Template</option>
                        </select>
                        <span id="template_idNew_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                    @if($record->type == 'Patient')
                    <div class="form-group">
                        <label for="doctor_idNew" class="font-weight-semibold">
                            Choose Doctor
                            <span class="text-danger">*</span>
                        </label>
                        <select name="doctor_id" class="form-control form-control-lg" id="doctor_idNew">
                            <option value="">Select Doctor</option>
                        </select>
                        <span id="doctor_idNew_error" class="error mt-2 text-danger d-block"></span>
                    </div>
                    @endif
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="edit_template_modal_submit_new">
                            <span id="btn-save-esign-new">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
