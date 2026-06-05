<div class="modal fade" id="patient-tele-appointment" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Telehealth appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/patient/telehealth-add'); }}" name="adduser" method="post" id="telehealthPatientform">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token(); }}">
                    <input type="hidden" name="id" value="{{ $record->id; }}">
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Date <span style="color:red">*</span></label>
                        <input type="text" name="date" class="form-control" autocomplete="off" id="patient_telehealth_date_id" placeholder="mm/dd/yyyy" readonly><i class="date-icon fa fa-calendar" aria-hidden="true"></i>
                        <span id="patient_telehealth_date_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="nurse">Nurse <span style="color:red">*</span></label>
                        <select class="form-control select2" id="telehealth_nurse" name="telehealth_nurse">
                            <option value="">Select Nurse</option>
                            @foreach($nurse as $key => $user)
                            <option value="{{ $key }}"> C#{{ $key }} ({{$user['language']}}) </option>
                            @endforeach
                        </select>
                        <span id="telehealth_nurse_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Select Slot <span style="color:red">*</span></label>
                        <select class="form-control select2" id="patient_telehealth_time_slot" name="patient_telehealth_time_slot">
                            <option value="">Select Slot</option>

                            @if(isset($slot))
                                @foreach($slots as $key => $slot)
                                <option value="{{ $slots['id'] }}">{{ $slots['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span id="patient_telehealth_time_slot_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100" multiple="multiple"
                            name="tele_patient_service_id[]" id="tele_patient_service_id">
                            <option value="">Select Service</option>
                            @php $serviceArr = explode(',', $record->service_id); @endphp
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
                        <span class="error mt-2 text-danger" id="tele_patient_service_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="appointmentTeleSubmit();">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>