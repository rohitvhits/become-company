<div class="modal fade" id="exampleModal-inservice-record" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">In Service</h5>
                <button type="button" class="close" data-dismiss="modal" id="closeds" aria-label="Close" onClick="hideInServiceAppointment()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">In Service Date<span class="error">*</span>:</label>


                    <input type="datetime-local" my-date-format="MM/DD/YYYY, hh:mm:ss" name="inservice_id" class="form-control" id="inservice_id">
                    <span id="inservice_id_error" class="error"></span>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="inserviceRecord()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="hideInServiceAppointment()">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>