<div class="modal fade" id="agency-tele-service-edit" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="servie_lable">Update Telehealth Agency Service</h5>
                <button type="button" class="close" onclick="resetService()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="edit_agency_service_form" method="post" id="edit_agency_service_form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <input type="hidden" id="id" name="id" value="">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Type<span style="color:red">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="edit_tele_type" class="form-check-input caregiver_type" id="edit_tele_caregiver_type" value="Caregiver" onclick="getTeleTypeWiseService('Caregiver')">
                                        Caregiver
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="edit_tele_type" class="form-check-input caregiver_type" id="edit_tele_patient_type" value="Patient" onclick="getTeleTypeWiseService('Patient')">
                                        Patient
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span style="color:red" id="edit_tele_type_error_service"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service<span style="color:red">*</span></label>
                        <select class="js-example-basic-multiple w-100" name="edit_agency_tele_service" id="edit_agency_tele_service">
                            <option value="">Select Service</option>
                        </select>

                        <span id="edit_agency_tele_service_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="editAgencyTele" onclick="updateTelehealth()" class="btn btn-success">Save</button>
                        <button type="button" onclick="resetService()" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>