<div class="modal" id="edit-branch-modal" tabindex="-1" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Location/Branch</h5>
                <button type="button" class="close" id="close_branch" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit_branch">
                <div class="modal-body">
                    <div class="form-group" id="branch_dropdown_wrapper">
                        <label for="Branch" class="col-sm-6 col-form-label">Branch<span style="color:red">*</span></label>
                        <div class="col-sm-12">
                            <select class="form-control form-control-sm" name="branch_id" id="patient_branch_id">
                                <option value="">Select Branch</option>
                            </select>
                            <span class="error mt-2 text-danger location_branch_error" for="document_type"></span>
                        </div>
                    </div>

                    <div class="form-group" id="location_branch_wrapper">
                        <label for="location-branch" class="col-sm-6 col-form-label">Location / Branch</label>
                        <div class="col-sm-12">
                             <input type="text" id="location_branch" name="location_branch" class="form-control" placeholder="Enter Location / Branch" value="{{ $record->location_branch }}">
                             <span class="error mt-2 text-danger" for="document_type location_branch_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="editBranch()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
