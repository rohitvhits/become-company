<div class="modal fade" id="csvMappingModal" tabindex="-1" aria-labelledby="csvMappingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
            <div class="modal-header text-white" style="background-color:#1f202f !important">
                <h5 class="modal-title font-weight-bold" id="csvMappingModalLabel">
                    <i class="mdi mdi-table-edit mr-2"></i>CSV Import Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:white !important">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0"  style="background-color:#fff !important">
                <!-- Summary Info -->
                <div class="p-3 border-bottom">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Template</small>
                            <p class="mb-0 font-weight-bold" id="modal_template_name"></p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">File Name</small>
                            <p class="mb-0 font-weight-bold" id="modal_file_name"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <small class="text-muted">Total Records</small>
                            <p class="mb-0"><span class="badge badge-info" id="modal_total_records"></span></p>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Matched</small>
                            <p class="mb-0"><span class="badge badge-success" id="modal_matched_count"></span></p>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Not Found</small>
                            <p class="mb-0"><span class="badge badge-danger" id="modal_not_found_count"></span></p>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Agency Not Match</small>
                            <p class="mb-0"><span class="badge badge-warning" id="modal_agency_not_match_count"></span></p>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Type Not Match</small>
                            <p class="mb-0"><span class="badge badge-info" id="modal_type_not_match_count"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Per Page + Search -->
                <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <label for="" class="mb-0 mr-2 small text-muted">Show</label>
                        <select id="modal_per_page" class="form-control form-control-sm" style="width:80px;">
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                        <span class="ml-2 small text-muted">entries per page</span>
                    </div>
                    <div id="modal_page_info" class="small text-muted"></div>
                </div>
                <div class="p-3 border-bottom">
                    <div class="row">
<!-- Mapping Table -->
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;" id="mapping_table_scroll">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light" style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                                    <tr>
                                        <th style="font-size:12px; width:80px;">#</th>
                                        <th style="font-size:12px;">Agency</th>
                                        <th style="font-size:12px;">Portal ID</th>
                                        <th style="font-size:12px;">Name</th>
                                        <th style="font-size:12px;">Mobile No</th>
                                        <th style="font-size:12px; width:130px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="mapping_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="p-2 border-top d-flex justify-content-between align-items-center">
                    <div id="modal_pagination_info" class="small text-muted"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="modal_pagination_controls"></ul>
                    </nav>
                </div>

                <!-- Info -->
                <div class="p-2 border-top">
                    <span class="text-muted">
                        <i class="mdi mdi-information-outline mr-1"></i>
                        <small><strong>Records with "Matched" status have been found in the system. "Not Found" and "Agency Not Match" and "Type Not Match" records will be skipped during processing.</strong></small>
                    </span>
                </div>
            </div>

            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <span class="text-muted" id="modal_summary_text"></span>
                    </div>
                    <div>
                        <form id="confirmImportForm" action="{{ url('esign/esign-import-confirm') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="import_id" id="confirm_import_id" value="">
                            <button type="submit" class="btn btn-primary btn-sm px-4 mr-2" id="confirmImportBtn">
                                <span class="spinner-border spinner-border-sm d-none mr-1" id="confirm_import_loader"></span>
                                Confirm & Import
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>