<div class="modal fade" id="exampleModal-update-other-compliance" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Update Other Complience to HHX Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="update_other_compliance_form_id">
                <input type="hidden" name="update_other_compliance_id" id="update_other_compliance_id">
                <input type="hidden" name="update_caregiver_other_compliance_id" id="update_caregiver_other_compliance_id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Due Date<span style="color:red">*</span>:</label>
                            <input type="text" name="update_other_complience_due_date" class="form-control perforrm-datepicker" id="update_other_complience_due_date">
                            <span id="update_other_complience_due_date_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"> Date Performed<span style="color:red">*</span>:</label>
                            <input type="text" name="update_date_perform" class="form-control perforrm-datepicker" id="update_date_perform">
                            <span id="update_date_perform_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Result<span style="color:red">*</span>:</label>
                            <select name="update_other_compliance_result" class="form-control" id="update_other_compliance_result">
                                <option value="">Select Result</option>
                            </select>
                            
                            <span id="update_other_compliance_result_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Score:</label>
                            <input type="text" name="update_other_compliance_score" class="form-control" id="update_other_compliance_score">
                            <span id="update_other_compliance_score_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes:</label>
                            <input type="text" name="update_other_compliance_notes" class="form-control" id="update_other_compliance_notes">
                            <span id="update_other_compliance_notes_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">File Name:</label>
                            <input type="text" name="update_other_document_name" class="form-control" id="update_other_document_name">
                            <span id="update_other_document_name_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Upload Document:</label>
                            <input type="file" name="update_other_compliance_document" class="form-control" id="update_other_compliance_document">
                            <span id="update_other_compliance_document_error" style="color:red" class="error"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="update-hha-complience-id-new">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
            </form>
            
            
        </div>
    </div>
</div>