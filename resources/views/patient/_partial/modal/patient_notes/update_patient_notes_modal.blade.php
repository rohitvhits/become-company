<div class="modal fade" id="exampleModal-patient-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Note:</label>
                    <textarea name="patient_basic_note" id="patient_basic_note_id" rows="5" class="form-control"></textarea> 
                    <span id="patient_basic_note_id_error" class="error"></span>
                </div>


                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="updatePatientNotes()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>