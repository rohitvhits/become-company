<!-- Other Compliance Modal -->
<div class="modal fade" id="otherComplianceModal" tabindex="-1" aria-labelledby="otherComplianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="otherComplianceModalLabel">
                    <i class="mdi mdi-file-check"></i> Create Other Compliance
                </h5>
                <button type="button" class="close text-white" id="close_other_cmp_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="otherComplianceForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <!-- Document Type -->
                        <div class="col-md-4 mb-3">
                            <label for="create_view_document_type" class="form-label">
                                Document Type <span style="color:red">*</span>
                            </label>
                            <select class="form-control" id="create_view_document_type" name="create_view_document_type">
                                <option value="">Select Document Type</option>
                                
                            </select>
                            <small class="text-danger d-none" id="create_view_document_type_error"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="date_performed" class="form-label">
                                Date Performed
                            </label>
                            <input type="text" class="form-control " data-inputmask="'alias': 'datetime'"
                            data-inputmask-inputformat="mm/dd/yyyy" id="date_performed" name="date_performed" placeholder="Select date" autocomplete="off" >
                            <small class="text-danger d-none" id="date_performed_error"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">
                                Due Date <span style="color:red">*</span>
                            </label>
                            <input type="text" class="form-control " data-inputmask="'alias': 'datetime'"
                            data-inputmask-inputformat="mm/dd/yyyy" id="due_date" name="due_date" placeholder="Select date" autocomplete="off">
                            <small class="text-danger d-none" id="due_date_error"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="document_upload" class="form-label">
                                Document Upload
                            </label>
                            <input type="file" class="form-control" id="document_upload" name="document_upload" accept=".pdf">
                          
                            <small class="text-danger d-none" id="document_upload_error"></small>
                        </div>
                        <div class="col-md-4 mb-3 ml-4">
                            <div class="mt-4">
                                <input type="checkbox"
                                    class="form-check-input"
                                    id="auto_update_next_due_date"
                                    name="auto_update_next_due_date"
                                    value="1">
                                <label class="form-check-label" for="auto_update_next_due_date">
                                    Update Next Due Date Based on Performed Date
                                </label>
                            </div>
                        </div>
                        <!-- Medical Dropdown (Multiple Selection) -->
                        <div class="col-md-12 mb-3">
                            <label for="created_medical_id" class="form-label">
                                Medical <span style="color:red">*</span>
                            </label>
                            <select class="form-control created_medical_id" id="created_medical_id" name="created_medical_id[]" multiple style="height: 120px;" >
                                <option value="">Loading...</option>
                            </select>
                            <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple medical items</small>
                            <small class="text-danger d-none" id="created_medical_id_error"></small>
                        </div>

                        <!-- Dynamic Medical Result Dropdowns -->
                        <div class="col-md-12 mb-3">
                            <div class="row" id="medical_results_container">
                                <!-- Medical result dropdowns will be dynamically added here -->
                            </div>
                        </div>

                    </div>
                </div>
                
            </form>
            <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="saveComplianceBtn">
                            <i class="mdi mdi-content-save"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Cancel
                        </button>
                        
                    </div>
                    
                </div>
        </div>
    </div>
</div>