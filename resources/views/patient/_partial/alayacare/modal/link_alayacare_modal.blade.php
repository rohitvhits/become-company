<div class="modal fade" id="exampleModal-link-alaycare-id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Alaycare Employee</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="CloseEmployeePopup()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="lnkhhx_alaycare_id">
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name" class="mb-0">Search Employee</label>
                                        <input type="text" class="form-control" name="search_alaya_employee" id="search_alaya_employee" data-gtm-form-interact-field-id="0">
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <a href="javascript:void(0)" onclick="searchAlayaEmployee()"><i class="fa fa-search" style="font-size:20px;"></i></a>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        
                                        <label for="recipient-name" class="mb-0">Employee:</label>
                                        <input type="text" name="hha_alaycare_id" class="form-control" value="" id="hha_alaycare_id">
                                        <input type="hidden" name="hha_alaycare_name" class="form-control" value="" id="hha_alaycare_name">
                                        <span class="error hha_alaycare_id_error"></span>
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-7 hide" id="alayacare_employee_search_response">
                            <div class="form-group ">
                                <div class="row">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr><th>#</th>
                                            <th nowrap="">Employee ID</th>
                                            <th nowrap="">Employee Name</th>
                                            <th nowrap="">Status</th>
                                            <th nowrap="">Action</th>
                                        </tr></thead>
                                        <tbody id="alaayaemployee_id">

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="update-alaycare-id">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseEmployeePopup()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>