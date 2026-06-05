<style>
    #exampleModal-add-vns-procedure .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="exampleModal-add-vns-procedure" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-medical-bag mr-2"></i>Add VNS Procedure
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_create_procedure_id">
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="add_procedure_name" class="font-weight-semibold">
                            Procedure Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="add_procedure_name"
                               class="form-control procedure-name-input"
                               placeholder="Enter Procedure Name"
                               name="procedure_name"
                               value="">
                        <span id="procedure_name_error" class="error mt-2 text-danger d-block"></span>
                    </div>
                    <!-- <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Template Type:</label>
                        <select name="template_type" id="add_template_type" class="form-control">
                            <option value="">Select Template Type</option>
                            @if(isset($templates) && count($templates) > 0)
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->template_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span id="template_type_error" class="error"></span>
                    </div> -->

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="createProcedure()">
                            <span class="spinner-border spinner-border-sm d-none" id="create-procedure" role="status" aria-hidden="true"></span>
                            <span id="btn-save-text-procedure">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
