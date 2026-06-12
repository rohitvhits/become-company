<style>
    .form-check-input{
        margin-left:4px;
    }

    #sendSMSEsignNew .modal-footer {
        padding: 4px 1px !important;
    }

    #sendSMSEsignNew .form-group label{
        line-height:normal !important;
        margin-bottom:0px !important
    }

    #sendSMSEsignNew .modal-header{
        padding:8px 16px !important;
    }

    #sendSMSEsignNew .modal-title{
        font-size:15px !important;
    }
</style>
<div class="modal fade" id="sendSMSEsignNew" aria-modal="true" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h4 class="modal-title">Sign Document</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" id="sms_esign_new" enctype="multipart/form-data">
                <div class="modal-body" style="background-color:#ffffff !important">
                    <input type="hidden" name="groupId" id="main_caregiver_esign_id_new">
                    <input type="hidden" name="document_send_type" id="document_send_type_id_new">

                    <!-- Email Row -->
                    <div class="row"> 
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input sendType" type="checkbox" name="sendType[]" id="sendEmailNew" value="email" checked>
                                <label class="form-check-label" for="sendEmailNew"><strong>Email</strong></label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="email" class="form-control" id="email_new" placeholder="Enter Email">
                            <span class="text-danger" id="email_new_error"></span>
                        </div>
                    </div>

                    <!-- Mobile Row -->
                    <div class="form-group row align-items-center">
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input sendType" type="checkbox" name="sendType[]" id="sendMobileNew" value="mobile">
                                <label class="form-check-label" for="sendMobileNew"><strong>Mobile</strong></label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="mobile" class="form-control" id="mobile_no_id_caregiver_new" placeholder="Enter Mobile">
                            <span class="text-danger" id="mobile_no_id_caregiver_new_error"></span>
                        </div>
                    </div>

                    <!-- Message Field -->
                    <div class="form-group">
                        <label for="message_new"><strong>Message</strong></label>
                        <input type="text" name="message" class="form-control" id="message_new" placeholder="Enter Message" value="">
                        <span class="text-danger" id="message_new_error"></span>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="getSendSMSSubmitNew()">Send Document to Sign</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>
