<div class="modal fade" id="exampleModal-link-third-party-id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Link Third Party</h5>
                    <button type="button" class="close" id="close_link_third_party" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="thirs_lnkhhx_pdf_id">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Search Third Party<span class="error">*</span>:</label>
                        <input type="text"  name="third_party_id" class="form-control" id="third_party_id" >
                        <span id="third_party_id_error" class="error"></span>

                        <input type="hidden" id="third_party_ids" value="{{ $record->link_third_party}}">
                        <input type="hidden" id="third_party_ids_names" value="{{ $record->link_third_party_name}}">
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="saveLinkThirdParty()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
                </form>
            </div>
        </div>
    </div>