<div class="modal fade" id="createDocApprovalModal" tabindex="-1" role="dialog" aria-labelledby="createDocApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1e1e2f !important;">
                <h5 class="modal-title font-weight-bold" id="createDocApprovalModalLabel">
                    <i class="mdi mdi-account-check mr-2"></i>Add User Doc Approval
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="docApprovalCreateForm">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-semibold">User <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="create_user_id" name="user_id">
                            <option value="">-- Select User --</option>
                        </select>
                        <span class="error text-danger" id="create_user_id_error"></span>
                    </div>

                    <div class="form-group">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <label class="font-weight-semibold mb-0" style="white-space: nowrap;">Key <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary btn-sm px-3" id="create_key_mdo_label">
                                    <input type="radio" name="key" id="create_key_mdo" value="181"> With MDO
                                </label>
                                <label class="btn btn-outline-primary btn-sm px-3" id="create_key_all_label">
                                    <input type="radio" name="key" id="create_key_all" value="without_service"> All Service
                                </label>
                            </div>
                        </div>
                        <span class="error text-danger d-block mt-1" id="create_key_error"></span>
                    </div>

                    <div class="modal-footer px-0 pb-0 border-top-0">
                        <button type="button" class="btn btn-success btn-sm px-4" id="saveDocApproval">
                            <span id="btn-save-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
