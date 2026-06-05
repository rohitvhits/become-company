<style>
.status-section-wrapper {
    background: #ffffff;
    border: 1px solid #d1d3e2;
    border-radius: 8px;
    padding: 0;
    margin-bottom: 15px;
    overflow: hidden;
}

.status-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-title {
    font-weight: 600;
    font-size: 14px;
    color: #ffffff;
    margin: 0;
}

.status-body {
    padding: 15px;
}

.select-all-wrapper {
    background: #f8f9fc;
    border: 1px dashed #4e73df;
    border-radius: 6px;
    padding: 8px 12px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.select-all-wrapper .custom-control {
    margin: 0;
}

.select-all-wrapper .custom-control-label {
    font-weight: 600;
    font-size: 13px;
    color: #4e73df;
    cursor: pointer;
    margin: 0;
}

.status-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 8px 15px;
    align-items: start;
}

.custom-checkbox-inline {
    margin: 0 !important;
    display: flex;
    align-items: flex-start;
}

.custom-checkbox-inline .custom-control-label {
    font-size: 13px;
    color: #5a5c69;
    cursor: pointer;
    user-select: none;
    line-height: 1.4;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.custom-checkbox-inline .custom-control-label::before,
.custom-checkbox-inline .custom-control-label::after {
    top: 1px;
}
</style>

<div class="modal fade" id="add-notification-email-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title notification-emails" id="ModalLabel">Add Notification Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetNotificationEmail()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="addnotificationemail" method="post" id="addnotificationemail">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <input type="hidden" id="notificationId" name="id" value="">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Email</b></label>
                        <br>
                        <input type="type" name="email" id="notificationEmail" value="" class="form-control email">
                        <span id="notifications_email_error" class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Patient</b></label>
                        <br>
                        <div class="row">
                            @php $count = 0; @endphp
                            @if(!empty($agencyWiseNotificationEmail[0]))
                            @foreach($agencyWiseNotificationEmail as $item)

                            @if($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif

                            <div class="col-md-4">
                                @if(strtolower($item->name) =='status update')
                                <label>
                                    <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" value="{{ $item->name }}" data-id="{{ $item->id}}" class="notification_checkbox patient_checkbox" onclick="showPatientStatus()">
                                    {{ $item->name }}
                                </label>
                                @else
                                <label>
                                    <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" value="{{ $item->name }}" data-id="{{ $item->id}}" class="notification_checkbox patient_checkbox">
                                    {{ $item->name }}
                                </label>
                                @endif

                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif

                        </div>
                    </div>
                    @php 
                        $statusUpdate = ['noshow'=>'No Show','checkin'=>'CheckIn','completed'=>'Completed'];
                    @endphp
                    <?php 

                    ?>
                    <div class="form-group hide" id="patient_status_show">
                        <div class="status-section-wrapper">
                            <div class="status-header">
                                <span class="status-title">Patient Status Update</span>
                            </div>
                            <div class="status-body">
                                <div class="select-all-wrapper">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="patient_status_select_all">
                                        <label class="custom-control-label" for="patient_status_select_all">
                                            ✓ Select All
                                        </label>
                                    </div>
                                </div>
                                <div class="status-checkboxes">
                                    @php $counts = 0; @endphp
                                    @if(count($statusData) >0)
                                    @foreach($statusData as $key=>$items)
                                    <div class="custom-control custom-checkbox custom-checkbox-inline">
                                        <input type="checkbox" class="custom-control-input patient_checkbox_status" id="patient_notification_status_email{{ $counts }}" name="patient_status[]" value="{{ $key }}" data-id="{{ $key}}">
                                        <label class="custom-control-label" for="patient_notification_status_email{{ $counts }}">
                                            {{ $items }}
                                        </label>
                                    </div>
                                    @php $counts++; @endphp
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Caregiver</b><span style="color:red">*</span></label>
                        <br>
                        <div class="row">
                            @php $count = 0; @endphp
                            @if(!empty($agencyWiseNotificationEmail[0]))
                            @foreach($agencyWiseNotificationEmail as $item)
                            @if($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif
                            <div class="col-md-4">
                                @if(strtolower($item->name) =='status update')
                                <label>
                                    <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox" onclick="showCaregiverStatus()">
                                    {{ $item->name }}
                                </label>
                                @else
                                <label>
                                    <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox">
                                    {{ $item->name }}
                                </label>
                                @endif
                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="form-group hide" id="caregiver_status_show">
                        <div class="status-section-wrapper">
                            <div class="status-header">
                                <span class="status-title">Caregiver Status Update</span>
                            </div>
                            <div class="status-body">
                                <div class="select-all-wrapper">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="caregiver_status_select_all">
                                        <label class="custom-control-label" for="caregiver_status_select_all">
                                            ✓ Select All
                                        </label>
                                    </div>
                                </div>
                                <div class="status-checkboxes">
                                    @php $countss = 0; @endphp
                                    @if(count($statusUpdate) >0)
                                    @foreach($statusUpdate as $key=>$items)
                                    <div class="custom-control custom-checkbox custom-checkbox-inline">
                                        <input type="checkbox" class="custom-control-input caregiver_checkbox_status" id="caregiver_notification_status_email{{ $countss }}" name="caregiver_status[]" value="{{ $key }}" data-id="{{ $key}}">
                                        <label class="custom-control-label" for="caregiver_notification_status_email{{ $countss }}">
                                            {{ $items }}
                                        </label>
                                    </div>
                                    @php $countss++; @endphp
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Services</b></label>

                        <div class="">
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                <option value="">Select Service</option>
                            </select>
                            <span id="service_id_error" class="error mt-2"></span>
                        </div>
                    </div>
                    <span id="notification_email_error" class="error"></span>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Discipline</b></label>

                        <div class="">
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="discipline_id[]" id="discipline_id">
                                <option value="">Select Discipline</option>
                            </select>
                            <span id="discipline_id_error" class="error mt-2"></span>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="notification-email-saveId" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal" onclick="resetNotificationEmail()">Close</button>
            </div>
        </div>
    </div>
</div>