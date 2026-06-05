<div class="modal fade" id="exampleModal-alayacare-emp" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title" id="ModalLabel">Add Appointment</h5>
                <button type="button" class="close" id="close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="add-appointment" method="post" id="submitId">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="emp_id" id="emp_id" value="">
                    <input type="hidden" name="alaycare-emp-id" value="" id="alaycare-emp-id">

                    <div class="form-group">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Discipline <span class="error mt-2">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="diciplin" id="diciplin_id">
                                    <option value="">Select Discipline</option>
                                    @foreach($masterData as $dis)
                                        <option value="{{ $dis->name}}">{{ $dis->name}}</option>
                                    @endforeach
                                </select>
                                <span id="displine_error" class="error mt-2"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Services<span class="error mt-2">*</span></label>
                            <div class="col-sm-9">
                                <select class="js-example-basic-multiple w-100 form-control" multiple="multiple" name="service_id[]" id="service_id">
                                    <option value="">Select Service</option>
                                </select>
                                <span id="service_id_error" class="error mt-2"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" id="saveId" class="btn btn-success btn-sm px-4 mr-2">
                    <span class="spinner-border spinner-border-sm d-none" id="create-alayacare-emp" aria-hidden="true"></span>
                    <span id="btn-save-text">Save</span></button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" id="close-modal-popup" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>