<div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="branchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color:#0f0f17 !important">
                <h5 class="modal-title" id="branchModalLabel">Add Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white !important;">&times;</span>
                </button>
            </div>
            <form id="branchForm">
                @csrf
                <input type="hidden" id="branch_id" name="branch_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="branch_name_input">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="branch_name_input" name="branch_name" placeholder="Enter branch name">
                                <div class="invalid-feedback" id="branch_name-error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="submit" class="btn btn-success btn-sm px-4 mr-2" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text submit-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
