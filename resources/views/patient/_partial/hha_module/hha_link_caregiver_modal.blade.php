<div class="modal fade" id="exampleModal-link-hha" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
        <div class="modal-header py-2 px-3">
          <h6 class="modal-title" id="ModalLabel">Link HHX Profile</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closedsNew">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-2 px-3">
            <form id="lnkhhx_pdf_id">
                <div class="row">
                    <div class="col-md-7">
                    <!-- HHA Section -->
                        <div class="card mb-2 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                            <strong class="small">HHA Section</strong>
                            </div>
                            <div class="card-body py-2 px-2">
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-1">
                                    <label for="first_name" class="small mb-1">First Name:</label>
                                    <input type="text" class="form-control form-control-sm" id="hha_caregiver_first_name" name="hha_caregiver_first_name" placeholder="First name" autofocus>
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <label for="last_name" class="small mb-1">Last Name:</label>
                                    <input type="text" class="form-control form-control-sm" id="hha_caregiver_last_name" name="hha_caregiver_last_name" placeholder="Last name">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <label for="hha_caregiver_code_id" class="small mb-1">Caregiver Code:</label>
                                    <input type="text" class="form-control form-control-sm" id="hha_caregiver_code_id" name="hha_caregiver_code_id" placeholder="Code">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <label for="phone_no" class="small mb-1">Phone No:</label>
                                    <input type="text" class="form-control form-control-sm" id="hha_caregiver_phone_no" name="hha_caregiver_phone_no" placeholder="Phone">
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <label for="ssn" class="small mb-1">SSN:</label>
                                    <input type="text" class="form-control form-control-sm" id="hha_caregiver_ssn" name="hha_caregiver_ssn" placeholder="SSN">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-2">
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="searchCaregiver()">
                                <i class="fa fa-search"></i> Search
                            </a>
                        </div>
                    </div>
                  
                    <div class="card mb-2 shadow-sm">
                        <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                        <strong class="small">Search Caregiver</strong>
                        </div>
                        <div class="card-body py-2 px-2">
                            <label for="hha_profile_id" class="small mb-1">Search Caregiver: <span class="text-danger">*</span></label>
                            <div >
                                <input type="text" class="form-control form-control-sm" name="hha_profile_id" id="hha_profile_id" placeholder="Name or Code" aria-describedby="searchHelp" onkeydown="if(event.key==='Enter'){searchCaregiver();}">
                                
                            </div>
                        <input type="hidden" name="dataType" id="dataTypeId">
                        <input type="hidden" name="hhaSearchType" id="hha_search_flag">
                        <small id="searchHelp" class="form-text text-muted small">
                            <strong>Note:</strong> Search by Caregiver Name or Caregiver Code
                        </small>
                        <span class="error hha_profile_error"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5" id="hhas_caregiver_id" style="display:none">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-1">
                            <thead class="thead-light">
                                <tr>
                                <th scope="col" class="small">#</th>
                                <th scope="col" class="small">Caregiver ID</th>
                                <th scope="col" class="small">Caregiver Name</th>
                                <th scope="col" class="small">Gender</th>
                                <th scope="col" class="small">Employee Type</th>
                                <th scope="col" class="small">Status</th>
                                <th scope="col" class="small">Action</th>
                                </tr>
                            </thead>
                            <tbody id="hhaAppendCIdLoader" class="shimmer-loader">
                                <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                </tr>
                            </tbody>
                            <tbody id="hhaAppendCId">
                                <!-- Caregiver rows go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </form>
        </div>
        <div class="modal-footer py-2 px-3">
          <button type="button" class="btn btn-success btn-sm" onclick="getHhxProfile()">Save</button>
          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
        </div>
      
    </div>
  </div>
</div>