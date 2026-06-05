<style>
    #visiting_detail_edit_modal .modal-footer {
        padding: 4px 1px !important;
    }
    #visiting_detail_edit_modal .modal-content {
        border-radius:1.3rem
    }
</style>

<div class="modal fade" id="visiting_detail_edit_modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important;padding: 8px 16px !important;">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="fa fa-hospital-o mr-2"></i>Visiting Detail
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:#ffffff !important">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency/app-visiting-add")}}' id="editVistingAppDetail" name="editVistingAppDetail" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" val="">

                    <div class="form-group">
                        <label for="app_user_key" class="font-weight-semibold">
                            App User Key
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="app_user_key"
                               class="form-control"
                               id="app_user_key"
                               placeholder="Enter App Name">
                        <span id="app_user_key_error" class="error mt-2 text-danger d-block" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label for="app_user_password" class="font-weight-semibold">
                            App User Password
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="app_user_password"
                               class="form-control"
                               id="app_user_password"
                               placeholder="Enter App Secret">
                        <span id="app_user_password_error" class="error mt-2 text-danger d-block" for="document_type"></span>
                    </div>
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" id="update-agency-visting-detail" class="btn btn-success btn-sm px-4 mr-2" onclick="saveEditVisitingDeatil();">
                            <span class="spinner-border spinner-border-sm d-none"  aria-hidden="true"></span>
                            <span id="btn-visting-update-text">Update</span>
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