<div class="modal fade" id="exampleModal-44" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Telehealth appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/patient/telehealth-add');}}" name="adduser" method="post" id="telehealthform">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" value="{{$record->id}}">
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Language <span style="color:red">*</span>:</label>
                        <select name="language" id="telehealth_language" class="form-control">
                            <option value="">Select Language</option>
                            @foreach($language_list as $lan)
                                <option value="{{$lan['id']}}">{{$lan['name']}}</option>
                            @endforeach
                        </select>
                        <span id="telehealth_language_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Date <span style="color:red">*</span>:</label>
                        <input type="text" name="date" class="form-control" autocomplete="off" id="telehealth_date_id" placeholder="mm/dd/yyyy" readonly><i class="date-icon fa fa-calendar" aria-hidden="true"></i>
                        <span id="telehealth_date_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>

                    <!-- Compact Slot Availability Information -->
                    <div class="slot-availability-container mb-3" style="display: none;">
                        <div class="availability-summary">
                            <div class="availability-item">
                                <span class="availability-label">Available Slots:</span>
                                <span class="availability-value available" id="available_slots">0</span>
                            </div>
                            <div class="availability-item">
                                <span class="availability-label">Booked Slots:</span>
                                <span class="availability-value booked" id="booked_slots">0</span>
                            </div>
                            <div class="availability-item">
                                <span class="availability-label">Total Slots:</span>
                                <span class="availability-value total" id="total_slots">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- No Slots Available Message -->
                    <div class="no-slots-message alert alert-info" style="display: none;">
                        <i class="fa fa-info-circle mr-2"></i>
                        <span class="message-text">No slots available for the selected date and language. Please try a different date or check back later.</span>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Time Slot <span style="color:red">*</span>:</label>
                        <select name="telehealth_time_slot" id="telehealth_time_slot" class="form-control">
                            <option value="">Select Time Slot</option>
                        </select>
                        <span id="telehealth_time_slot_error" class="error mt-2 error" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100" multiple="multiple"
                            name="tele_caregiver_service_id[]" id="tele_caregiver_service_id">
                            <option value="">Select Service</option>
                            @php
				$serviceArr = explode(',', $record->service_id);
			    @endphp
                            @if (count($serviceList) > 0)
                                @foreach ($serviceList as $ks)
                                    @if ($ks->types == $record->type)
                                        <option value="{{ $ks->id }}" {{ in_array($ks->id, $serviceArr) ? 'selected' : '' }}>
                                            {{ $ks->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="tele_caregiver_service_error"></span>
                    </div>
                    <!-- Existing Appointment Information -->
                    <div class="existing-appointment-info alert alert-info" style="display: none;">
                        <h6>Existing Appointment Details:</h6>
                        <div class="appointment-details">
                            <p><strong>Date:</strong> <span id="existing_appointment_date"></span></p>
                            <p><strong>Time:</strong> <span id="existing_appointment_time"></span></p>
                            <p><strong>Nurse:</strong> <span id="existing_appointment_nurse"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="telehealthSubmit();">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
