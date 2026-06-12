<div class="modal fade" id="bulkSendEsignReportModal" tabindex="-1" aria-labelledby="bulkSendEsignReportLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h5 class="modal-title" id="bulkSendEsignReportLabel">Bulk Send E-Sign</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="bulkReportSendFormSection">
                    <form id="bulkEsignReportForm">
                        <div class="form-group">
                            <label for="bulkReportMessage">Message </label>
                            <textarea name="message" class="form-control" id="bulkReportMessage" rows="4" placeholder="Enter message"></textarea>
                            <span class="text-muted"><b>Note</b>:If the message is blank, the system will send a default message; otherwise, it will send the message you entered.</span>
                            <span class="error" id="bulkReportMessage_error"></span>
                        </div>
                    </form>
                </div>

                <!-- Results Section (hidden initially) -->
                <div id="bulkReportSendResultsSection" style="display:none;">
                    <h5>Results</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Template Name</th>
                                    <th>Portal ID</th>
                                    <th>Portal Name</th>
                                    <th>Mobile No</th>
                                    <th>SMS Status</th>
                                </tr>
                            </thead>
                            <tbody id="bulkReportSendResultsBody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span id="bulkReportResultInfo" class="text-muted"></span>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="bulkReportResultPagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-primary btn-sm px-4 mr-2" id="bulkReportSendSubmitBtn" onclick="submitBulkSendEsignReport()">Send</button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
