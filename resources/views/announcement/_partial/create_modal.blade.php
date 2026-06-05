<div class="modal fade" id="InsuranceModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Insurance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" method="post" id="insuranceAdd">
                        @csrf
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Name<span class="error">*</span></label>
                            <input type="text" class="form-control" id="insurance_name" name="insurance_name"
                                placeholder="Enter Name" maxlength="50">
                            <span class="error-text name_error error"></span>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="addInsurance"
                                data-uid="">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>