<div class="modal fade" id="exampleModal-link-visiting-id" tabindex="-1" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 px-3 text-white" style="background-color:#000000 !important;padding: 8px 16px !important;">
                <h6 class="modal-title" id="ModalLabel">Search Visiting </h6>
                <button type="button" class="close" id="close_visiting_aids" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2 px-3">

                <div class="row">
                    
                    <div class="col-md-6">
                        <form id="form_visiting_search_id">
                            <!-- Third Party Section -->
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                                    <strong class="small">Visiting Section</strong>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="third_party_first_name" class="small mb-1">First Name:</label>
                                            <input type="text" class="form-control form-control-sm" id="third_party_first_name" name="third_party_first_name" placeholder="First name" autofocus>
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="third_party_last_name" class="small mb-1">Last Name:</label>
                                            <input type="text" class="form-control form-control-sm" id="third_party_last_name" name="third_party_last_name" placeholder="Last name">
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="third_party_employee_code" class="small mb-1">Employee Code:</label>
                                            <input type="text" class="form-control form-control-sm" id="third_party_employee_code" name="third_party_employee_code" placeholder="Code">
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="third_party_dob" class="small mb-1">Date of Birth:</label>
                                            <input type="date" class="form-control form-control-sm" id="third_party_dob" name="third_party_dob">
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="third_party_phone" class="small mb-1">Phone Number:</label>
                                            <input type="text" class="form-control form-control-sm" id="third_party_phone" name="third_party_phone" placeholder="Phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer py-1 px-2">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" id="btn_search_visiting" onclick="searchThirdParty()">
                                        <i class="fa fa-search" id="btn_search_visiting_icon"></i>
                                        <span class="spinner-border spinner-border-sm d-none" id="btn_search_visiting_spinner" aria-hidden="true"></span>
                                        <span id="btn_search_visiting_text"> Search</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-6" id="visiting_third_party_results_section" style="display:none">
                        <!-- Search Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-1">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="small">#</th>
                                        <th scope="col" class="small">ID</th>
                                        <th scope="col" class="small">Name</th>
                                        <th scope="col" class="small">Employee Code</th>
                                        <th scope="col" class="small">Phone</th>
                                        <th scope="col" class="small">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="visitingThirdPartyResultsLoader" class="shimmer-loader" style="display:none">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tbody id="visitingThirdPartyResults">
                                    <!-- Results will be appended here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-success btn-sm" onclick="saveVisitingAid()">
                <span class="spinner-border spinner-border-sm d-none" id="btn_save_visiting_spinner" aria-hidden="true"></span>
                <span id="btn_submit_visiting_text"> Save</span></button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
