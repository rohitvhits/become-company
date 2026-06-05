<div class="modal fade" id="exampleModal-refuse_modal_show" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas"
                        style="text-transform:capitalize"></span>Refused Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="mb-0">Reason<span class="error">*</span>:</label>
                    <select name="refuse_reason_id" class="form-control js-example-basic-multiple" id="refuse_reason_ids">


                    </select>
                    <span id="refuse_reason_id_status_error" class="error"></span>
                </div>

                <div class="form-group hide" id="other_refuse_notes">
                    <label for="recipient-name" class="mb-0">Notes<span class="error">*</span>:</label>
                    <textarea name="refuse_document_id" class="form-control" id="refuse_notes_id"></textarea>
                    <span id="refuse_notes_status_error" class="error"></span>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="getStatusRefuse()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>