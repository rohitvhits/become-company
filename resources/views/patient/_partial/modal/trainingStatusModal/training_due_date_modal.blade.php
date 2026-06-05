<div class="modal fade" id="exampleModal-traning_due_date" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>Training Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Training Due Date<span class="error">*</span>:</label>
                        <input type="text"  name="traning_due_date" class="form-control" id="traning_due_date_id" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php if ($record->traning_due_date != '') { echo date('m/d/Y', strtotime($record->traning_due_date)); } ?>">
                        <span id="traning_due_date_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getTrainingDueDate()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>