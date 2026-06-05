<div class="modal fade" id="exampleModal-67" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas"
                        style="text-transform:capitalize"></span>Medical Due Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Medical Due Date<span
                            class="error">*</span>:</label>
                    <input type="text" readonly name="due_date" class="form-control" id="due_date_id"
                        data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false"
                        value="<?php if ($record->due_date != '') {
                            echo date('m/d/Y', strtotime($record->due_date));
                        } ?>">
                    <span id="due_date_id_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getDueDate()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
