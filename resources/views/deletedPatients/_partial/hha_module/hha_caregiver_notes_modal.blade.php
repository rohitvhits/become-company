<div class="modal fade" id="exampleModal-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Caregiver Notes </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="hha_caregivers_notes">
                    <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Subject<span class="error">*</span>:</label>
                            <select class="form-control" id="subjectId" name="subjectId">

                            </select>
                            <span id="hha_subject_id_error" class="error mt-2"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                            <textarea type="text"    rows="4" cols="50"  class="form-control" id="hha_caregivers_notes_id"></textarea>
                            <span id="hha_caregivers_notes_id_error" class="error mt-2" for="hha_caregivers_notes_type"></span>
                        </div>
                       
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="hhaCaregiverSave">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>