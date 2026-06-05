<!-- ===== Upload to AlayaCare Modal ===== -->
<style>
    #exampleModal-alayacare-upload .modal-footer {
        padding: 4px 1px !important;
    }
    #exampleModal-alayacare-upload .modal-header {
        padding: 8px 16px !important;
    }
</style>

<div class="modal fade" id="exampleModal-alayacare-upload" tabindex="-1" role="dialog" aria-labelledby="alayacareUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="alayacareUploadLabel">
                    <i class="mdi mdi-cloud-upload mr-2"></i>Send To AlayaCare
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="clearAlayacareUpload()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background-color:white">
                <input type="hidden" id="alayacare_upload_doc_id">
                <div class="form-group">
                    <label for="alayacare_skill_select_id1" class="font-weight-semibold">
                        Skill
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-control-lg select2" id="alayacare_skill_select_id" multiple="multiple">
                    </select>
                    <span id="alayacare_skill_select_error" class="error mt-2 text-danger d-block"></span>
                </div>
                <div id="alayacare_skill_expire_dates_container"  class="col-md-12"></div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="submitUploadToAlayaCare()">
                        <span class="spinner-border spinner-border-sm d-none" id="loaderAlayacareUploadSubmit" role="status" aria-hidden="true"></span>
                        <span id="btn-submit-alayacare">Submit</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="clearAlayacareUpload()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
