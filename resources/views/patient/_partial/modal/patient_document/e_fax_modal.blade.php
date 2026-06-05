<div class="modal fade" id="efax-exampleModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">E-Fax</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeReviewModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                
                <div class="row">
                    <div class="col-md-8">
                        <iframe id="fax_over_review_document_id" src="" style="width:100%;height:600px"></iframe>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="hidden" name="doc_efax_id" id="doc_efax_id">
                                <input type="hidden" name="doc_patient_fax_id" id="doc_patient_fax_id">
                                <label for="exampleInputUsername1">E Fax Number<span class="text-danger">*</span>:</label>
                                <input type="text" class="form-control" id="e_fax_no" placeholder="E Fax Number" value="@if(isset($agencyDetails) && !empty($agencyDetails)){{ $agencyDetails->efax_no }}@endif">
                                <span class="text-danger" id="e_fax_no_error"></span>
                            </div>
                            <button type="button" onclick="submitEFax()" class="btn btn-primary mr-2">Submit</button>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>