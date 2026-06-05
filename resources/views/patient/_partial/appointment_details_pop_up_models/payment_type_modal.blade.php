<div class="modal fade" id="exampleModal-payment-type" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Payment Type</h5>
                <button type="button" class="close close_p" data-dismiss="modal" id="closeds" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="payment_method_id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $record->id }}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Payment Type <span
                                class="error">*</span>:</label>
                        <select name="payment_type" id="payments_id" class="form-control">
                            <option value="">Select Payment Type</option>
                            <?php
                            if (count($masterData) > 0) {
                                foreach ($masterData as $val) {
                                    if ($val->master_type_fk == 17) {
                            ?>
                            <option value="<?php echo $val->id; ?>" @if ($val->id == $record->payment_type) selected @endif>
                                <?php echo $val->name; ?></option>
                            <?php  }
                                }
                            } ?>
                        </select>
                        <span class="error payments_id_error"></span>
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getPaymentNewStatus()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
