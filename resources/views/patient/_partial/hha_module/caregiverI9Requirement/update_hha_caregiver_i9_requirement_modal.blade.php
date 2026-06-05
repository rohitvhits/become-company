<style>
#exampleModal-caregiver-i9-requirement .modal-header .closeCaregiverI9Requirement {
    padding: 1rem 1rem;
    margin: -20px -25px -20px auto;
}

#exampleModal-caregiver-i9-requirement button.closeCaregiverI9Requirement {
    padding: 0;
    background-color: transparent;
    border: 0;
}

#exampleModal-caregiver-i9-requirement .closeCaregiverI9Requirement {
    float: right;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: .5;
}
</style>
<div class="modal fade" id="exampleModal-caregiver-i9-requirement" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="ModalLabel">Update Caregiver I-9 Requirements</h5>
        <button type="button" class="closeCaregiverI9Requirement" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      
      <!-- Form -->
      <form id="save-caregiver-i9-form">
        <div class="modal-body" style="height:calc(100vh - 250px)">
          
          <!-- Section: Hire Info -->
          <div class="p-2 border bg-light mb-3"><strong>Employment Timeline</strong></div>
            <div class="row mb-3">
                <div class="col-md-6">
                <label for="hha_caregiver_i9_requirement_hire_date" class="form-label">Hire Date</label>
                    <input type="text" class="form-control" name="hha_caregiver_i9_requirement_hire_date" id="hha_caregiver_i9_requirement_hire_date" placeholder="mm/dd/yyyy" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy">
                </div>
                <div class="col-md-6">
                    <label for="hha_caregiver_i9_requirement_doc_exp_date" class="form-label">I-9 Document Expiration Date</label>
                    <input type="text" class="form-control" name="hha_caregiver_i9_requirement_doc_exp_date" id="hha_caregiver_i9_requirement_doc_exp_date" placeholder="mm/dd/yyyy" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy">
                </div>
            </div>

          <!-- Section: Documents -->
          <div class="p-2 border bg-light mb-3"><strong>Document Verification</strong></div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hha_caregiver_i9_requirement_ab_document" class="form-label">Column A+B Documents</label>
                    <select class="form-control" name="hha_caregiver_i9_requirement_ab_document" id="hha_caregiver_i9_requirement_ab_document">
                        <option value="">Select Column A+B Documents</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hha_caregiver_i9_requirement_cdocument" class="form-label">Column C Documents</label>
                    <select class="form-control" name="hha_caregiver_i9_requirement_cdocument" id="hha_caregiver_i9_requirement_cdocument">
                        <option value="">Select Column C Documents</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="hha_caregiver_i9_requirement_verified" class="form-label">I-9 Verified</label>
                    <select class="form-control" name="hha_caregiver_i9_requirement_verified" id="hha_caregiver_i9_requirement_verified">
                        <option value="">Select</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
          </div>

          <!-- Section: Verification -->
          <div class="p-2 border bg-light mb-3"><strong>E-Verify Information</strong></div>
            <div class="row mb-3">
                
                <div class="col-md-6">
                <label for="hha_caregiver_i9_requirement_verify_number" class="form-label">E-Verify Number</label>
                <input type="text"  class="form-control" name="hha_caregiver_i9_requirement_verify_number" id="hha_caregiver_i9_requirement_verify_number" placeholder="E-Verify Number">
                </div>
            
            </div>

          <!-- Section: Notes -->
            <div class="p-2 border bg-light mb-3"><strong>I-9 Note</strong></div>
            <div class="mb-3">
                <textarea class="form-control" id="hha_caregiver_i9_requirement_note" name="hha_caregiver_i9_requirement_note" rows="3" placeholder="I-9 Note"></textarea>
            </div>

        </div>
        
        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="updateCaregiverI9Requirements()">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
      
    </div>
  </div>
</div>
