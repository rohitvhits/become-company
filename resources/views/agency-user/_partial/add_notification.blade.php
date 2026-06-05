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
                        <input type="hidden" id="agency_id" name="agency_id" value="{{auth()->user()->agency_fk}}">
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
                                    <label>
                                        <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" value="{{ $item->name }}" data-id="{{ $item->id}}" class="notification_checkbox patient_checkbox">
                                        {{ $item->name }}
                                    </label>
                                </div>
                                @php $count++; @endphp
                                @endforeach
                                @endif

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
                                    <label>
                                        <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox">
                                        {{ $item->name }}
                                    </label>
                                </div>
                                @php $count++; @endphp
                                @endforeach
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Services</b></label>

                            <div class="row">
                                <select class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100 select2" multiple="multiple" name="service_id[]" id="service_id">
                                    <option value="">Select Service</option>
                                </select>
                                <span id="service_id_error" class="error mt-2"></span>
                            </div>
                        </div>
                        <span id="notification_email_error" class="error"></span>

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Discipline</b></label>

                            <div class="row">
                                <select class="js-example-basic-multiple w-100 select2" multiple="multiple" name="discipline_id[]" id="discipline_id">
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