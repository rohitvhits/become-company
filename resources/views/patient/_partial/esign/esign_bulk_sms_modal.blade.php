<style>
    #bulkSendEsignModal .modal-footer {
        padding: 4px 1px !important;
    }

    #bulkSendEsignModal .form-group label{
        line-height:normal !important;
        margin-bottom:0px !important
    }

    #bulkSendEsignModal .modal-header{
        padding:8px 16px !important;
    }

    #bulkSendEsignModal .modal-title{
        font-size:15px !important;
    }
</style>
<div class="modal fade" id="bulkSendEsignModal" aria-modal="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h4 class="modal-title">Bulk Send E-Sign</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color:#ffffff">
                <div id="bulkSendFormSection">
                    <p><strong>Selected Documents: <span id="bulkSelectedCount">0</span></strong></p>

                    <!-- Email Row -->
                    <div class="row">
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input bulkSendType" type="checkbox" name="bulkSendType[]" id="bulkSendEmail" value="email" checked>
                                <label class="form-check-label" for="bulkSendEmail"><strong>Email</strong></label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="bulkEmail" placeholder="Enter Email" value="<?php echo $record->email ?? ''; ?>">
                            <span class="text-danger" id="bulkEmail_error"></span>
                        </div>
                    </div>

                    <!-- Mobile Row -->
                    <div class="form-group row align-items-center mt-2">
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input bulkSendType" type="checkbox" name="bulkSendType[]" id="bulkSendMobile" value="mobile">
                                <label class="form-check-label" for="bulkSendMobile"><strong>Mobile</strong></label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="bulkMobile" placeholder="Enter Mobile" value="<?php echo $record->mobile ?? ''; ?>">
                            <span class="text-danger" id="bulkMobile_error"></span>
                        </div>
                    </div>

                    <!-- Message Field -->
                    <div class="form-group mt-2">
                        <label for="bulkMessage"><strong>Message</strong></label>
                        <input type="text" class="form-control" id="bulkMessage" placeholder="Enter Message" value="">
                        <span class="text-danger" id="bulkMessage_error"></span>
                    </div>
                </div>

                <!-- Results Section (hidden initially) -->
                <div id="bulkSendResultsSection" style="display:none;">
                    <h5>Results</h5>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Template</th>
                                <th>Status</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody id="bulkSendResultsBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-primary btn-sm px-4 mr-2" id="bulkSendSubmitBtn" onclick="submitBulkSendEsign()">Send All</button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    
                </div>
            </div>
        </div>
    </div>
</div>
