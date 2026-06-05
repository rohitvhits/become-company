<style>
    #exampleModal-4 .modal-footer {
        padding: 4px 1px !important;
    }

</style>

<div class="modal fade" id="exampleModal-add-remote-appointment" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-calendar-clock mr-2"></i>Add Appointment
                </h5>
                <button type="button" class="close text-white" id="close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:white">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='' name="add-appointment" method="post" id="submitId">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group row">
                        <label for="hha_dicipline_id" class="col-sm-4 col-form-label font-weight-semibold">Remote Discipline </label>
                        <div class="col-sm-8    ">
                            <span id="hha_dicipline_id"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="diciplin_id" class="col-sm-3 col-form-label font-weight-semibold">Discipline</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="diciplin" id="diciplin_id">
                                <option value="">Select Discipline</option>
                                <option value="HHA">HHA</option>
                                <option value="CDPAP">CDPAP</option>
                                <option value="RN">RN</option>
                                <option value="LPN">LPN</option>
                                <option value="Pre-HHA">Pre-HHA</option>
                                <option value="Pre-CDPAP">Pre-CDPAP</option>
                                <option value="OTHER">Other</option>
                            </select>
                            <span id="displine_error" class="error mt-2 text-danger d-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="service_id" class="col-sm-3 col-form-label font-weight-semibold">Services<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-example-basic-multiple w-100 form-control" multiple="multiple" name="service_id[]" id="service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span id="service_id_error" class="error text-danger d-block">{{ $errors->add_agency->first('service_id') }}</span>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" id="saveId" class="btn btn-success btn-sm px-4 mr-2">
                        <span class="spinner-border spinner-border-sm d-none" id="create-add-remote" role="status" aria-hidden="true"></span>
                        <span id="btn-save-text-remote">Save</span></button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" id="close-modal-popup" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>