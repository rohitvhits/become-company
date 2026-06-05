<div class="modal fade" id="exampleModal-link-hha-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Link HHX Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" id="closedsNewPatient" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="lnkhhx_patient_pdf_id">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label for="recipient-name" class="col-form-label">Search Patient Code:</label><br>
                                            <input type="text" class="form-control" name="hha_patient_code_id"  id="hha_patient_code_id"><br/>
                        
                                            <span class="error hha_patient_code_id_error"></span>
                                        </div>
                                        <div class="col-md-2 mt-5">
                                            <a href="javascript:void(0)" onclick="searchPatient()"><i class="fa fa-search" style="font-size:20px;"></i></a>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label for="recipient-name" class="col-form-label">Search Patient: <span class="error">*</span></label><br>
                                            <input type="text" name="hha_profile_patient_id"  id="hha_profile_patient_id">
                                            <input type="hidden" name="dataTypePatient" id="dataTypePatient">
                                            <span class="error hha_profile_patient_error"></span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-5"  id="hhas_patient_id" style="display:none">
                                <div class="row">
                                    <h6>Search By <span id="view_admission_id"></span></h6>
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>#</th>
                                            <th nowrap>Patient ID</th>
                                            <th nowrap>Patient Name</th>
                                            <th nowrap>Status</th>
                                            <th nowrap>Action</th>
                                        </thead>
                                        <tbody id="hhaPatientAppID">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        
                    </form> 

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getHhxProfilePatient()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>