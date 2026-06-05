<style>

#exampleModal-add-hha-medical .modal-header .closeMedical {
    padding: 1rem 1rem;
    margin: -20px -25px -20px auto;
}
#exampleModal-add-hha-medical .closeMedical {
    float: right;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: .5;
}

#exampleModal-add-hha-medical button.closeMedical {
    padding: 0;
    background-color: transparent;
    border: 0;
}
</style>
<div class="modal fade" id="exampleModal-add-hha-medical" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsass" style="text-transform:capitalize"></span>Create Caregiver Medical</h5>
                <button type="button" class="closeMedical" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="save-hha-add-medical-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Medical ID<span class="text-danger">*</span>:</label>
                                <select onchange="getHHAMedicalResults()" name="hha_medical_document_medical_id" id="hha_medical_document_medical_id" class="">
                                    <option value="">Select Medical ID</option>
                                </select>
                                <span id="hha_medical_document_medical_error" class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Due Date:</label>
                                <input type="text" name="hha_medical_document_due_date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" id="hha_medical_document_due_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Date Performed:</label>
                                <input type="text" name="hha_medical_document_date_perform"  placeHolder="Date Performed" id="hha_medical_document_date_perform" class="form-control"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Result:</label>
                                <select name="hha_medical_document_result" id="hha_medical_document_result" class="">
                                    <option value="">Select Result</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">File Name:</label>
                                <input type="text" name="hha_medical_document_name" class="form-control" placeHolder="File Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Upload Document:</label>
                                <input type="file" name="hha_medical_document_file"  class="form-control" >
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Comments:</label>
                            <textarea name="hha_medical_document_comment"  rows="5" class="form-control"></textarea>
                        </div>
                       
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="saveHHAMedical()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            
        </div>
    </div>
</div>