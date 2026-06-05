<style>
    #exampleModal-edit-app-token .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="exampleModal-edit-app-token" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-pencil-outline mr-2"></i>Edit App Token
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_edit_app_token_id">
                <input type="hidden" id="edit_token_id" name="token_id">
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="edit_app_name" class="font-weight-semibold">
                            App Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="edit_app_name"
                               class="form-control edit-app-name-input"
                               placeholder="Enter App Name"
                               name="app_name"
                               value="">
                        <span id="edit_app_name_error" class="error text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_description" class="font-weight-semibold">
                            Description
                        </label>
                        <textarea id="edit_description"
                                  class="form-control"
                                  placeholder="Enter Description (Optional)"
                                  name="description"
                                  rows="3"></textarea>
                        <span id="edit_description_error" class="error text-danger d-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit_referral_type" class="font-weight-semibold">
                            Referral Type <span class="text-danger">*</span>
                        </label>
                        <select name="referral_type" id="edit_referral_type" class="form-control">
                            <option value="">Select Referral Type</option>
                            @foreach($master_list as $val)
                                <option value="{{ $val->id}}">{{$val->name }}</option>
                            @endforeach

                        </select>
                        <span id="edit_referral_type_error" class="error mt-2 text-danger d-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="current_token" class="font-weight-semibold">Current Token</label>
                        <div class="token-display" id="current_token"></div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="mdi mdi-alert-outline mr-2"></i>
                        <small>Token cannot be changed during update.</small>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="updateAppToken()">
                            <span class="spinner-border spinner-border-sm d-none" id="update-app-token"aria-hidden="true"></span>
                            <span id="btn-update-text-app-token">Update</span>
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