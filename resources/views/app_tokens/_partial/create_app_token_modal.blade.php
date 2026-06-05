<style>
    #exampleModal-add-app-token .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="exampleModal-add-app-token" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-key-plus mr-2"></i>Add App Token
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_create_app_token_id">
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="add_app_name" class="font-weight-semibold">
                            App Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="add_app_name"
                               class="form-control app-name-input"
                               placeholder="Enter App Name"
                               name="app_name"
                               value="">
                        <span id="app_name_error" class="error text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="add_description" class="font-weight-semibold">
                            Description
                        </label>
                        <textarea id="add_description"
                                  class="form-control"
                                  placeholder="Enter Description (Optional)"
                                  name="description"
                                  rows="3"></textarea>
                        <span id="description_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="mdi mdi-information-outline mr-2"></i>
                        <small>Token will be auto-generated upon creation (40 characters).</small>
                    </div>

                    <div class="form-group">
                        <label for="add_referral_type" class="font-weight-semibold">
                            Referral Type <span class="text-danger">*</span>
                        </label>
                        <select name="referral_type" class="form-control" id="referral_type">
                            <option value="">Select Referral Type</option>
                            @foreach($master_list as $val)
                                <option value="{{ $val->id}}">{{$val->name }}</option>
                            @endforeach

                        </select>
                        <span id="referral_type_error" class="error mt-2 text-danger d-block"></span>
                    </div>


                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="createAppToken()">
                            <span class="spinner-border spinner-border-sm d-none" id="create-app-token" aria-hidden="true"></span>
                            <span id="btn-save-text-app-token">Save</span>
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