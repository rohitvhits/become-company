<div class="modal fade" id="exampleModal-link-task-health-patient" tabindex="-1" role="dialog" aria-labelledby="TaskHealthModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header py-2 px-3">
          <h6 class="modal-title" id="TaskHealthModalLabel">Link Task Health Patient</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeTaskHealthModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-2 px-3">
            <form id="form_task_health_search">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-2 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                                <strong class="small">Search Task Health Patient</strong>
                            </div>
                            <div class="card-body py-2 px-2">
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-1">
                                        <label class="small mb-1">First Name:</label>
                                        <input type="text" class="form-control form-control-sm" id="th_search_first_name" placeholder="First name">
                                    </div>
                                    <div class="form-group col-md-6 mb-1">
                                        <label class="small mb-1">Last Name:</label>
                                        <input type="text" class="form-control form-control-sm" id="th_search_last_name" placeholder="Last name">
                                    </div>
                                    <div class="form-group col-md-6 mb-1">
                                        <label class="small mb-1">Patient Code:</label>
                                        <input type="text" class="form-control form-control-sm" id="th_search_patient_code" placeholder="Code">
                                    </div>
                                    <div class="form-group col-md-6 mb-1">
                                        <label class="small mb-1">Phone:</label>
                                        <input type="text" class="form-control form-control-sm" id="th_search_phone" placeholder="Phone">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-1 px-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary" id="btn_th_search" onclick="searchTaskHealthPatient()">
                                    <i class="fa fa-search" id="btn_th_search_icon"></i>
                                    <span class="spinner-border spinner-border-sm d-none" id="btn_th_search_spinner" aria-hidden="true"></span>
                                    <span id="btn_th_search_text"> Search</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="th_search_results_section" style="display:none">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-1">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="small">#</th>
                                        <th class="small">ID</th>
                                        <th class="small">Patient Name</th>
                                        <th class="small">Code</th>
                                        <th class="small">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="th_search_results_loader" class="shimmer-loader" style="display:none">
                                    <tr><td></td><td></td><td></td><td></td><td></td></tr>
                                </tbody>
                                <tbody id="th_search_results">
                                </tbody>
                            </table>
                        </div>
                        <span class="text-danger" id="th_search_error"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer py-2 px-3">
          <button type="button" class="btn btn-success btn-sm" onclick="saveTaskHealthLink()">
              <span class="spinner-border spinner-border-sm d-none" id="btn_th_save_spinner" aria-hidden="true"></span>
              <span id="btn_th_save_text">Save</span>
          </button>
          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
