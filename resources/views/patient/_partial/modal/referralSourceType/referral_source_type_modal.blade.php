<div class="modal fade" id="exampleModal-edit-referral-source-modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
        <div class="modal-header py-2 px-3">
          <h6 class="modal-title" id="ModalLabel">Referral Source</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_referral_source_id">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-2 px-3">
            
            
                <div class="form-group">
                    <label for="referral_source_type" class="small mb-1">Referral Source Type: <span class="text-danger">*</span></label>
                    <select class="form-control" id="edit_referral_source_type">
                        <option value="">Select Referral Source Type</option>
                        @foreach($masterData as $referralSource)
                            @if($referralSource->master_type_fk ==31)
                                <option value="{{ $referralSource->name}}">{{ ucfirst( $referralSource->name)}}</option>
                            @endif
                        @endforeach
                    </select>
                    <span class="error referral_source_type_error"></span>
                </div>
        </div>
        <div class="modal-footer py-2 px-3">
          <button type="button" class="btn btn-success btn-sm" onclick="updateReferralSources()">Save</button>
          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
        </div>
      
    </div>
  </div>
</div>