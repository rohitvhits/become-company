
<div class="modal fade" id="show-patient-demo-modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Patient Demographic SMS</h4>
                <button onclick="clearSMSMobileModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_new_patient">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <input type="hidden" name="sms_patient_id" id="sms_patient_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Mobile No <span class="text-danger">*</span></label>
                            <div class="col-sm-8">

                                <input type="text" id="sms_mobile_no" name="sms_mobile_no"  data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" class="form-control">
                                <span id="sms_mobile_no_error" class="text-danger"></span>
                            </div>
                        </div>

                    </div>
      
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="clearSMSMobileModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePatientSMSMobile">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>