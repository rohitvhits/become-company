<div class="modal fade" id="editOtherComplianceModal" tabindex="-1" aria-labelledby="editOtherComplianceModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content border-0 shadow-lg">
                 <div class="modal-header text-white" style="background-color:#000000 !important">
                     <h5  id="editOtherComplianceModalLabel" class="modal-title font-weight-bold" >Edit Other Compliance Details</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="edit_close_other_compliance_modal">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form id="editOtherComplianceForm" enctype="multipart/form-data">
                         <input type="hidden" id="edit_caregiver_medical_id" name="edit_caregiver_medical_id">
                         <input type="hidden" id="edit_agency_id" name="agency_id">
                         <input type="hidden" id="edit_caregiver_id" name="caregiver_id">
                         <div class="row">
                             <div class="col-md-3">
                                 <div class="form-group">
                                     <label for="edit_medical_id" class="font-weight-bold">Medical ID:</label>
                                     <input type="text" class="form-control" id="edit_medical_id" readonly name="medical_id">
                                     <span id="edit_hha_other_medical_id_error" class="error"></span> 
                                 </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                     <label for="edit_medical_name" class="font-weight-bold">Medical Name:</label>
                                     <input type="text" class="form-control" id="edit_medical_name" readonly>
                                     <span id="edit_hha_other_medical_name_error" class="error"></span> 
                                 </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                    <label for="edit_due_date" class="font-weight-bold">Due Date:</label>
                                    <input type="text" class="form-control" id="edit_due_date" name="due_date" placeholder="MM/DD/YYYY" autocomplete="off" readonly>
                                    <span id="edit_hha_other_due_date_error" class="error"></span> 
                                </div>
                             </div>

                             <div class="col-md-3">
                                 <div class="form-group">
                                     <label for="edit_date_perform" class="font-weight-bold">Date Performed: <span class="text-danger">*</span></label>
                                     <input type="text" class="form-control" data-inputmask="'alias': 'datetime'"
                                     data-inputmask-inputformat="mm/dd/yyyy" id="edit_date_perform" name="date_perform" placeholder="MM/DD/YYYY" autocomplete="off">
                                     <span id="edit_hha_other_date_perform_error" class="error"></span>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-md-4">
                                 <div class="form-group">
                                     <label for="edit_document_type" class="font-weight-bold">Document Type: <span class="text-danger">*</span></label>
                                     <select class="form-control" id="edit_document_type" name="edit_document_type">
                                        
                                     </select>
                                     <span id="edit_hha_other_document_type_error" class="error"></span>
                                 </div>
                             </div>
                             <div class="col-md-2">
                                 <div class="form-group">
                                     <label for="edit_result" class="font-weight-bold">Result: <span class="text-danger">*</span></label>
                                     <select class="form-control" id="edit_result" name="edit_result" ></select>
                                     <span id="edit_hha_other_result_error" class="error"></span>
                                 </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                     <label for="edit_document_upload" class="font-weight-bold">Document Upload:</label>
                                     <input type="file" name="edit_document_upload" id="edit_document_upload" class="form-control">
                                     <span id="edit_hha_other_document_upload_error" class="error"></span>
                                 </div>
                             </div>
                             <div class="col-md-3 mt-3">
                                 <div class="form-group ml-3">
                                    <input type="checkbox"
                                        class="form-check-input"
                                        id="edit_auto_update_next_due_date"
                                        name="edit_auto_update_next_due_date"
                                        value="1">
                                    <label class="form-check-label" for="auto_update_next_due_date">
                                        Update Next Due Date Based on Performed Date
                                    </label>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-md-12">
                                 <div class="form-group">
                                     <label for="notes" class="font-weight-bold">Notes:</label>
                                     <textarea class="form-control" id="edit_notes" name="notes" rows="4" placeholder="Enter notes"></textarea>
                                 </div>
                             </div>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="saveOtherComplianceBtn"><i class="mdi mdi-content-save"></i> Update</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="editCloseOtherComplianceModal()"><i class="mdi mdi-close"></i> Cancel</button>
                        
                    </div>
                 </div>
             </div>
         </div>
     </div>