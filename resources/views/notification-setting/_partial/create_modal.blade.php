<div class="modal fade" id="add-notification-email-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title notification-emails" id="ModalLabel">Notification
                    Setting
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="addnotificationemail" method="post" id="addnotificationemail" novalidate>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="0">
                    <input type="hidden" id="notificationId" name="id" value="">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Email</b></label>
                        <br>
                        <input type="email" name="email" id="notificationEmail" value="" placeholder="Enter Email" class="form-control email" required>
                        <span id="notifications_email_error" class="error"></span>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Patient</b></label>
                        <br>
                        <div class="row">
                            @php $count = 0; @endphp
                            @if (!empty($agencyWiseNotificationEmail[0]))
                            @foreach ($agencyWiseNotificationEmail as $item)
                            @if ($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" value="{{ $item->name }}" data-id="{{ $item->id }}" class="notification_checkbox patient_checkbox">
                                    {{ $item->name }}
                                </label>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif
                            <span id="patientCheckboxes_error" class="error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Caregiver</b><span style="color:red">*</span></label>
                        <br>
                        <div class="row">
                            @php $count = 0; @endphp
                            @if (!empty($agencyWiseNotificationEmail[0]))
                            @foreach ($agencyWiseNotificationEmail as $item)
                            @if ($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox">
                                    {{ $item->name }}
                                </label>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif
                            <span id="caregiverCheckboxes_error" class="error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Services</b></label>
                        <div class="row col-md-12">
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                <option value="">Select Service</option>
                            </select>
                        </div>
                        <span id="service_id_error" class="error"></span>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="notification-email-saveId" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>