<style>
    #exampleModal-edit-modal-procedure-result .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="exampleModal-edit-modal-procedure-result" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-pencil-outline mr-2"></i>Edit VNS Procedure Result
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_edit_procedure_result_id">
                <input type="hidden" id="record_id" name="record_id">
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="edit_vns_procedure_id" class="font-weight-semibold">
                            VNS Procedure
                            <span class="text-danger">*</span>
                        </label>
                        <select name="vns_procedure_id" id="edit_vns_procedure_id" class="form-control form-control-lg">
                            <option value="">Select VNS Procedure</option>
                            @if(isset($procedures) && count($procedures) > 0)
                                @foreach($procedures as $procedure)
                                    <option value="{{ $procedure->id }}">{{ $procedure->procedure_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span id="edit_vns_procedure_id_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_result_names_container" class="font-weight-semibold">
                            Result Name
                            <span class="text-danger">*</span>
                        </label>
                        <div id="edit_result_names_container">
                            <div class="result-name-row mb-2">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text"
                                               class="form-control result-name-input"
                                               placeholder="Enter Result Name"
                                               name="names[]"
                                               value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span id="edit_names_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="updateProcedureResult()">
                            <span class="spinner-border spinner-border-sm d-none" id="update-procedure-result" role="status" aria-hidden="true"></span>
                            <span id="btn-update-procedure-result">Update</span>
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
