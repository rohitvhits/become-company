<div class="modal fade" id="assignsms_notfication-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add SMS Notification</h5>
                <button type="button" onclick="closeSMSNotification()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-sms-notification")}}' name="adduser" method="post" id="smsFormSubmitNotification">
                <div class="modal-body">
                
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <input type="hidden" name="id" value="" id="mid">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Caregiver</b><span class="text-danger">*</span></label>
                        <br>
                        <div class="row">
                            @php $count = 0; 
                                $smsNotification = ['create','booked'];
                            @endphp
                            @if(!empty($smsNotification[0]))
                            @foreach($smsNotification as $item)
                            
                            @if($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif
                            
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" id="sms_notification_caregiver{{ $item }}" name="sms_notification_caregiver[]" value="{{ $item }}" data-id="{{ $item}}" class="sms_notification_checkbox sms_notification_caregiver_checkbox">
                                    {{ ucfirst($item)}}
                                </label>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif

                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Patient</b><span class="text-danger">*</span></label>
                        <br>
                        <div class="row">
                            @php $count = 0; 
                                $smsNotification = ['create','booked'];
                            @endphp
                            @if(!empty($smsNotification[0]))
                            @foreach($smsNotification as $item)
                            
                            @if($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif
                            
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" id="sms_notification_patient{{ $item }}" name="sms_notification_patient[]" value="{{ $item }}" data-id="{{ $item}}" class="sms_notification_checkbox sms_notification_patient_checkbox">
                                    {{ ucfirst($item)}}
                                </label>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif

                        </div>
                    </div>
                    <span id="agency_sms_notification_error" class="text-danger"></span>

                   
                </div>
                <div class="modal-footer">
                    <button type="button" id="smsSaveNotificationId" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeWithoutNotification()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>