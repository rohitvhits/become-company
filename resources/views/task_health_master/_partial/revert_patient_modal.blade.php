<style>
    #revertPatientModal .modal-body .search-result-row { cursor: pointer; }
    #revertPatientModal .modal-body .search-result-row:hover { background-color: #f0f4ff; }
    #revertPatientModal .modal-body .search-result-row.selected-patient { background-color: #d4edda; font-weight:600; }
    #revert_current_patient_info {
        background: linear-gradient(90deg,#f0f4ff 0%,#f8f9fc 100%);
        border: 1px solid #c8d6f8 !important;
        border-radius: 5px;
        padding: 5px 10px !important;
    }
    .rcp-chips { display:flex; flex-wrap:wrap; align-items:center; gap:6px; }
    .rcp-chip {
        display:inline-flex; align-items:center; gap:3px;
        background:#fff; border:1px solid #d1daf0;
        border-radius:20px; padding:1px 7px;
        font-size:11px; line-height:1.4; white-space:nowrap;
    }
    .rcp-chip .rcp-chip-label {
        font-size:9px; font-weight:700; color:#8a93a2;
        text-transform:uppercase; letter-spacing:0.4px; margin-right:1px;
    }
    .rcp-chip .rcp-chip-val { font-weight:600; color:#2d3748; }
    .rcp-chip .rcp-chip-val.highlight { color:#1a56db; }
    .rcp-chip-type { background:#e8f0fe; border-color:#b8caf8; }
    .rcp-chip-type .rcp-chip-val { color:#1a56db; }
</style>

<div class="modal fade" id="revertPatientModal" tabindex="-1" role="dialog" aria-labelledby="revertPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document" style="max-width:750px;">
        <div class="modal-content">
            <div class="modal-header py-2 d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="modal-title mb-0" id="revertPatientModalLabel" style="font-weight:700;">
                        <i class="fa fa-refresh mr-1 text-danger"></i> Resend
                    </h6>
                    <button type="button" class="close m-0 p-0" data-dismiss="modal" aria-label="Close" style="font-size:18px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="revert_current_patient_info" class="mt-2" style="display:none;">
                    <div class="rcp-chips">
                        <span style="font-size:10px;font-weight:700;color:#8a93a2;text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="fa fa-user-circle-o"></i> Current:
                        </span>
                        <span class="rcp-chip">
                            <span class="rcp-chip-label">Name</span>
                            <span class="rcp-chip-val highlight" id="rcp_name">-</span>
                        </span>
                        <span class="rcp-chip rcp-chip-type">
                            <span class="rcp-chip-label">Type</span>
                            <span class="rcp-chip-val" id="rcp_type">-</span>
                        </span>
                        <span class="rcp-chip">
                            <span class="rcp-chip-label"><i class="fa fa-hospital-o"></i> Agency</span>
                            <span class="rcp-chip-val" id="rcp_agency">-</span>
                        </span>
                        <span class="rcp-chip">
                            <span class="rcp-chip-label">DOB</span>
                            <span class="rcp-chip-val" id="rcp_dob">-</span>
                        </span>
                        <span class="rcp-chip">
                            <span class="rcp-chip-label"><i class="fa fa-mobile"></i> Mobile</span>
                            <span class="rcp-chip-val" id="rcp_mobile">-</span>
                        </span>
                        <span class="rcp-chip">
                            <span class="rcp-chip-label"><i class="fa fa-phone"></i> Phone</span>
                            <span class="rcp-chip-val" id="rcp_phone">-</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-body py-2">
                <input type="hidden" id="revert_task_health_id" value="">
                <input type="hidden" id="revert_selected_patient_id" value="">

                <div class="row col-md-12">
                    <div class="col-md-12">
                        <div class="form-group mb-2">
                            <label class="mb-1" style="font-size:12px;font-weight:600;"><i class="fa fa-hospital-o"></i> Agency</label>
                            <select class="form-control form-control-sm" id="revert_agency_id" onchange="resetRevertSearch()">
                                <option value="">-- Select Agency --</option>
                                @foreach($agencyList as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row col-md-12">
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label class="mb-1" style="font-size:12px;font-weight:600;">Portal ID</label>
                            <input type="text" class="form-control form-control-sm" id="revert_portal_id" placeholder="Enter Patient ID">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label class="mb-1" style="font-size:12px;font-weight:600;">Patient Name</label>
                            <input type="text" class="form-control form-control-sm" id="revert_patient_name" placeholder="First or Last Name">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-2 w-100">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchRevertPatient()">
                                Search
                            </button>
                        </div>
                    </div>
                </div>

                <div id="revert_search_error" class="text-danger mb-1" style="display:none;font-size:12px;"></div>

                <div id="revert_results_wrapper" style="display:none;">
                    <table class="table table-bordered table-sm mb-1" style="font-size:12px;">
                        <thead class="thead-light">
                            <tr>
                                <th>Portal ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>DOB</th>
                                <th>Status</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody id="revert_results_body"></tbody>
                    </table>
                </div>
                <div id="revert_no_results" class="text-muted text-center" style="display:none;font-size:12px;">
                    No patients found.
                </div>
                <div id="revert_loader" style="display:none; text-align:center;">
                    <img src="{{ asset('/ajax-loader.gif') }}" alt="loader">
                </div>
                <span id="revert_submit_error" class="text-danger" style="display:none;font-size:12px;"></span>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger btn-sm" id="revert_submit_btn" onclick="submitRevertPatient()" disabled>
                    <span class="text-white">&#9873;</span> Resend
                </button>
            </div>
        </div>
    </div>
</div>
