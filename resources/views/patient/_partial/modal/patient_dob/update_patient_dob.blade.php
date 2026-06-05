<div class="modal fade" id="exampleModal-dob" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Update Date Of Birth Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearDob()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Date Of Birth<span class="error">*</span>:</label>
                        <input type="text" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" name="dob" class="form-control" id="dob" value="{{date('m/d/Y' ,strtotime($record->dob))}}" min="1000-01-01" max="9999-12-31">
                        <span id="dob_error" class="error"></span>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="updateDob()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="clearEmail()">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>