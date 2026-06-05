<div class="modal fade" id="exampleModal-link-hha" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Link HHX Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" id="closedsNew" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="lnkhhx_pdf_id">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label for="recipient-name" class="col-form-label">Search Caregiver Code:</label><br>
                                            <input type="text" class="form-control" name="hha_caregiver_code_id"  id="hha_caregiver_code_id"><br/>
                        
                                            <span class="error hha_caregiver_code_id_error"></span>
                                        </div>
                                        <div class="col-md-2 mt-5">
                                            <a href="javascript:void(0)" onclick="searchCaregiver()"><i class="fa fa-search" style="font-size:20px;"></i></a>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label for="recipient-name" class="col-form-label">Search Caregiver: <span class="error">*</span></label><br>
                                            <input type="text" name="hha_profile_id"  id="hha_profile_id"><br/>
                                            <input type="hidden" name="dataType" id="dataTypeId">
                                            <span class="error hha_profile_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5"  id="hhas_caregiver_id" style="display:none">
                                <div class="form-group ">
                                    <div class="row">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th>#</th>
                                                <th nowrap>Caregiver ID</th>
                                                <th nowrap>Caregiver Name</th>
                                                <th nowrap>Status</th>
                                                <th nowrap>Action</th>
                                            </thead>
                                            <tbody id="hhaAppendCId">

                                            </tbody>
                                        </table>
                                    </div>
                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                
                            </div>
                        </div>
                        
                        

                        
                    </form> 

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getHhxProfile()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>