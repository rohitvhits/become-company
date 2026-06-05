{{-- Common Model --}}
<div class="modal fade commons" id="" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>
                    Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                    <textarea name="document_id" class="form-control" id="notes_id"></textarea>

                    <span id="notes_status_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="commons_flag">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Notes Modal --}}
<div class="modal fade" id="exampleModal-cancel" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas"
                        style="text-transform:capitalize"></span>Cancel Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Reason<span class="error">*</span>:</label>
                    <select name="reason_id" class="form-control" id="reason_ids">
                        <option value="">Select Reason</option>
                        <?php
                        if (count($masterData) > 0) {
                            foreach ($masterData as $val) {
                                if ($val->master_type_fk == 12) {
                        ?>
                        <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                        <?php  }
                            }
                        } ?>
                    </select>
                    <span id="reason_id_status_error" class="error"></span>
                </div>

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                    <textarea name="document_id" class="form-control" id="notes_id_cancel"></textarea>
                    <span id="notes_status_cancel_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getStatusNew('cancel')">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- In Service Modal --}}
<div class="modal fade" id="exampleModal-inservice-record" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">In Service</h5>
                <button type="button" class="close" data-dismiss="modal" id="closeds" aria-label="Close"
                    onClick="hideInServiceAppointment()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">In Service Date<span
                            class="error">*</span>:</label>
                    <input type="datetime-local" my-date-format="MM/DD/YYYY, hh:mm:ss" name="inservice_id"
                        class="form-control" id="inservice_id">
                    <span id="inservice_id_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="inserviceRecord()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal"
                        onclick="hideInServiceAppointment()">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
