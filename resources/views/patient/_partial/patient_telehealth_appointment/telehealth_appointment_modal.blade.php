<div class="modal fade" id="exampleModal-44" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Telehealth appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" name="adduser"
                    method="post" id="telehealthform" onsubmit="return submitTelehealthForm();">
                    @csrf
                    <input type="hidden" name="id" value="{{ $record->id }}">
                    
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Date <span style="color:red">*</span>:</label>
                        <input type="text" name="date" class="form-control" autocomplete="off"
                            id="telehealth_id" placeholder="mm/dd/yyyy" readonly><i class="date-icon fa fa-calendar"
                            aria-hidden="true"></i>
                        <span id="telehealth_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Time <span
                                style="color:red">*</span>:</label>
                        <input type="time" name="time" class="form-control" autocomplete="off"
                            id="telehealth_time_id">
                        <span id="telehealth_time_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
