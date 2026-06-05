<style>
    #app_detail_edit_modal .modal-footer {
        padding: 4px 1px !important;
    }
    #app_detail_edit_modal .modal-content {
        border-radius:1.3rem
    }
</style>

<div class="modal fade" id="app_detail_edit_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important;padding: 8px 16px !important;">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="fa fa-hospital-o mr-2"></i>HHA Detail
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:#ffffff !important">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency/app-details-add")}}' id="editAppDetail" name="appDetail" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" val="">

                    <div class="form-group">
                        <label for="agency_edit_app_name" class="font-weight-semibold">
                            App Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="agency_edit_app_name"
                               class="form-control"
                               id="agency_edit_app_name"
                               placeholder="Enter App Name">
                        <span id="agency_edit_app_name_error" class="error mt-2 text-danger d-block" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label for="agency_edit_app_key" class="font-weight-semibold">
                            App Secret
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="agency_edit_app_key"
                               class="form-control"
                               id="agency_edit_app_key"
                               placeholder="Enter App Secret">
                        <span id="agency_edit_app_key_error" class="error mt-2 text-danger d-block" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label for="agency_edit_app_token" class="font-weight-semibold">
                            App Key
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="agency_edit_app_token"
                               class="form-control"
                               id="agency_edit_app_token"
                               placeholder="Enter App Key">
                        <span id="agency_edit_app_token_error" class="error mt-2 text-danger d-block" for="document_type"></span>
                    </div>
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" id="update-agency-detail" class="btn btn-success btn-sm px-4 mr-2" onclick="saveEditDeatil();">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="btn-update-text">Update</span>
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