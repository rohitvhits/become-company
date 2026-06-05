<style>
    #disableDateModal .modal-footer {
        padding: 4px 1px !important;
    }
    #disableDateModal .modal-header {
        padding: 8px 16px !important;
    }
    #disableDateModal .modal .modal-dialog .modal-content {
        border-radius:none !important
    }
    #disableDateModal .modal-body {
        overflow: visible !important;
        max-height: none !important;
    }
    #disableDateModal .modal-dialog {
        overflow: visible !important;
    }
    #disableDateModal .modal-content {
        overflow: visible !important;
    }
</style>

<div class="modal fade" id="disableDateModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" style="display: none; z-index:1050 !important" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-calendar-remove mr-2"></i>Add Disable Date
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo URL::to('/disable-date/store'); ?>" method="post" id="disable_date" name="disable_date" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    <div class="form-group">
                        <label for="disable_dates" class="font-weight-semibold">
                            Select Disable Date
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" autocomplete="off" name="disable_dates" class="form-control date" id="disable_dates" placeholder="Select Date">
                        <span class="error mt-1 text-danger d-block" id="disable_date_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="disable_dates_time" class="font-weight-semibold">
                            Disable Time
                        </label>
                        <input type="text" data-inputmask="'alias': 'datetime', 'inputFormat': 'HH:MM'" class="form-control form-control-sm" autocomplete="off" placeholder="Enter Time" id="time_id" name="time" im-insert="false">
                        <span class="text-muted"><b>Notes:</b>Please use 24-hour time format (HH:MM)</span>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-semibold">Type</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" value="Caregiver" class="form-check-input">
                                        Caregiver
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" value="Patient" class="form-check-input">
                                        Patient
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="save()" id="disableDateSave">
                            <span class="spinner-border spinner-border-sm d-none" id="loaderAddDisableDate" role="status" aria-hidden="true"></span>
                            <span id="btn-save-disable-date">Save</span>
                        </button>

                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>

                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
