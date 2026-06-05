<div class="modal fade" id="exampleModal-availibility-followup_date" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Avaibility Followup Date</h5>
                    <button type="button" class="close" id="close_availibility_followup_date" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Avaibility Followup Date<span class="error">*</span>:</label>
                        <input type="text"  name="availibility_followup_date" class="form-control" id="availibility_followup_date"  value="<?php if ($record->avaibility_followup_date != '') {
                            echo date('m/d/Y', strtotime($record->avaibility_followup_date));
                        } ?>">
                        <span id="avaibility_followup_date_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getAvaibilityFollowupDate()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>