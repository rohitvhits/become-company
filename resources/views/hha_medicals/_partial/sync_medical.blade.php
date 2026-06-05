<style>
    #syncMedicalModal .modal-footer {
        padding: 4px 1px !important;
    }
</style>

<div class="modal fade" id="syncMedicalModal" tabindex="-1" role="dialog" aria-labelledby="syncMedicalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white"  style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="syncMedicalModalLabel">
                    <i class="mdi mdi-sync mr-2"></i>Sync Medical
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="sync-medical-form">
                @csrf
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="sync_agency_fk" class="font-weight-semibold">
                            Agency
                            <span class="text-danger">*</span>
                        </label>
                        <select name="sync_agency_fk" id="sync_agency_fk" class="form-control form-control-lg">
                            <option value="">Select Agency</option>
                            @foreach($agency_list as $agency)
                                <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                            @endforeach
                        </select>
                        <span id="sync_agency_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="sync_office_fk" class="font-weight-semibold">
                            Office
                            <span class="text-danger">*</span>
                        </label>
                        <select name="sync_office_fk" id="sync_office_fk" class="form-control form-control-lg">
                            <option value="">Select Office</option>
                        </select>
                        <span id="sync_office_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="sync_medicals" class="font-weight-semibold">
                            Medicals
                            <span class="text-danger">*</span>
                        </label>
                        <select name="sync_medicals" id="sync_medicals" class="form-control form-control-lg">
                            <option value="">Select Medicals</option>
                        </select>
                       
                        <span id="sync_medicals_error" class="error mt-2 text-danger d-block"></span>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="sync-medical-submit">
                            <span class="spinner-border spinner-border-sm d-none" id="syncLoader" role="status" aria-hidden="true"></span>
                            <span id="btn-sync-medical">Sync Medical</span>
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
