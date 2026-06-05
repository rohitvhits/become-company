<div class="modal fade" id="exampleModal-emergency_phone" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Emergency Phone</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmergencyPhone()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Emergency Phone<span class="error">*</span>:</label>
                        <input type="text"  name="emergency_phone" class="form-control" id="emergency_phone" onkeypress="return isNumber(event)"    maxlength="15" value="<?php if ($record->emergency_phone != '') {
                                                                                                                                                                                                                    echo $record->emergency_phone;
                                                                                                                                                                                                                } ?>">
                        <span id="emergency_phone_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getEmergencyPhone()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="clearEmergencyPhone()">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>