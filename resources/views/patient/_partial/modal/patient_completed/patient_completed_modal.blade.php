<div class="modal fade" id="exampleModal-complete" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Completed Date</h5>
                    <button type="button" class="close" data-dismiss="modal" id="closeds" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Completed Date<span class="error">*</span>:</label>
                        <input type="text" name="due_date" class="form-control" id="completed_date_id" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php if ($record->completed_date != '') {
                                                                                                                                                                                                                    echo date('m/d/Y', strtotime($record->completed_date));
                                                                                                                                                                                                                } ?>">
                        <span id="completed_date_id_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getCompletedDate()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>