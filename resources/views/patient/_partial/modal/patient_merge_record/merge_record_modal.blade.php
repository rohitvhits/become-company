<div class="modal fade" id="exampleModal-merge-record" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Combine Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" id="closeds" aria-label="Close" onclick="hideCombineAppointment()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Chart Id<span class="error">*</span>:</label>
                    <input type="text" name="appointment_id" class="form-control" id="appointment_id">
                    <span id="appointment_id_error" class="error"></span>
                </div>

                
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="combineRecord()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>
