<style>
    #editAgencyUserRepModal .modal-footer {
        padding: 4px 1px !important;
    }
    #editAgencyUserRepModal .modal-content .modal-header {
        padding: 8px 16px !important;
    }
</style>

<!-- Edit Agency User Rep Modal -->
<div class="modal fade" id="editAgencyUserRepModal" tabindex="-1" aria-labelledby="editAgencyUserRepLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background:transparent !important">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="editAgencyUserRepLabel">
                    <i class="mdi mdi-account-edit mr-2"></i>Edit Agency Rep
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background:white">
                <div class="form-group">
                    <label for="edit_agency_user_repnew" class="font-weight-semibold">
                        Agency Rep
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="edit_agency_user_rep"
                           class="form-control"
                           placeholder="Enter Agency Rep"
                           name="edit_agency_user_rep"
                           value="">
                           <span id="edit_agency_user_rep_error" class="error"></span>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="saveAgencyUserRep()">
                        <span class="spinner-border spinner-border-sm d-none" id="save-agency-rep-spinner" role="status" aria-hidden="true"></span>
                        <span id="btn-save-agency-rep-text">Save</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
