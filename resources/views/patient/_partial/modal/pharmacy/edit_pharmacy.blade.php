{{-- Pharmacy Name Modal --}}
<div class="modal" id="edit-pharmacy-name-modal" tabindex="-1" aria-labelledby="editPharmacyNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPharmacyNameModalLabel">Edit Pharmacy Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-6 col-form-label">Pharmacy Name</label>
                    <div class="col-sm-12">
                        <input type="text" id="pharmacy_name_input" class="form-control" placeholder="Enter Pharmacy Name">
                        <span class="error mt-2 text-danger" id="pharmacy_name_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="savePharmacyName()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Pharmacy No Modal --}}
<div class="modal" id="edit-pharmacy-no-modal" tabindex="-1" aria-labelledby="editPharmacyNoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPharmacyNoModalLabel">Edit Pharmacy Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-6 col-form-label">Pharmacy Number</label>
                    <div class="col-sm-12">
                        <input type="text" id="pharmacy_no_input" class="form-control" placeholder="Enter Pharmacy Number">
                        <span class="error mt-2 text-danger" id="pharmacy_no_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="savePharmacyNo()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
