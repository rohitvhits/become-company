<div class="modal fade" id="exampleModal-email" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Email Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmail()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Email<span class="error">*</span>:</label>
                        <input type="text"  name="email" class="form-control email_value" id="email"  value="<?php if ($record->email != '') {
                                                                                                                                                                                                                    echo $record->email;
                                                                                                                                                                                                                } ?>">
                        <span id="emergency_email_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getEmail()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="clearEmail()">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>