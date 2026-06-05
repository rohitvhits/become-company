<div class="row">
    <div class="col-sm-6 card-title">
        <h4 class="card-title">SMS Template</h4>
    </div>

</div>
<div class="">
    <div class="row">
        <div class="form-group">
            You can use below tags :start_date,start_time,end_time,url,link,patient_first_name,agency_name,namearray,
        </div>
    </div>
    <form class="forms-sample" enctype="multipart/form-data" action='' name="add-agency-wise-sms-form" method="post" id="add-agency-wise-sms-form">
        <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label"><b>App Appointment SMS English</b></label>
                <textarea class="form-control form-control-lg" name="send_sms_eng" id="send_sms_eng">{{$agencyDetails->send_sms_eng}}</textarea>
            </div>
            <div class="col-md-6">
                <label class="col-form-label"><b>App Appointment SMS Spanish</b></label>
                <textarea class="form-control form-control-lg" name="send_sms_spanish" id="send_sms_spanish">{{$agencyDetails->send_sms_spanish}}</textarea>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label"><b>Reminder SMS English</b></label>
                <textarea class="form-control form-control-lg" name="appointment_send_book_eng" id="appointment_send_book_eng">{{$agencyDetails->appointment_send_book_eng}}</textarea>
            </div>
            <div class="col-md-6">
                <label class="col-form-label"><b>Reminder SMS Spanish</b></label>
                <textarea class="form-control form-control-lg" name="appointment_send_book_spanish" id="appointment_send_book_spanish">{{$agencyDetails->appointment_send_book_spanish}}</textarea>
            </div>
        </div>
        <span id="agency_wise_sms_message_error" class="error mt-2" for="document_type"></span>

        <hr />

        <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label"><b>App Tele Appointment SMS English</b></label>
                <textarea class="form-control form-control-lg" name="tele_send_sms_eng" id="tele_send_sms_eng">{{$agencyDetails->tele_send_sms_eng}}</textarea>
            </div>
            <div class="col-md-6">
                <label class="col-form-label"><b>App Tele Appointment SMS Spanish</b></label>
                <textarea class="form-control form-control-lg" name="tele_send_sms_spanish" id="tele_send_sms_spanish">{{$agencyDetails->tele_send_sms_spanish}}</textarea>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label"><b>Reminder Tele SMS English </b></label>
                <textarea class="form-control form-control-lg" name="tele_remind_send_sms_eng" id="tele_remind_send_sms_eng">{{$agencyDetails->tele_remind_send_sms_eng}}</textarea>
            </div>
            <div class="col-md-6">
                <label class="col-form-label"><b>Reminder Tele SMS Spanish</b></label>
                <textarea class="form-control form-control-lg" name="tele_remind_send_sms_spanish" id="tele_remind_send_sms_spanish">{{$agencyDetails->tele_remind_send_sms_spanish}}</textarea>
            </div>
        </div>
        <span id="tele_agency_wise_sms_message_error" class="error mt-2" for="document_type"></span>
        <div class="modal-footer">
            <button type="button" id="agency-wise-sms-saveId" class="btn btn-success">Update</button>
        </div>
    </form>
</div>