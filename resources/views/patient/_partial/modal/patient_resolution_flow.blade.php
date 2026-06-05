<!-- Modal Content -->
 <style>
     .select2-container {
      z-index: 99999 !important;
  }
  </style>
<div style="display:none;" id="patientResolutionModal">
    <h2 class="modal-title" style="margin: 0; font-size: 1.4rem; font-weight: 600; text-align:left;">Chart Resolution</h2>
    <div class="resolution-steps">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div id="stepSummary" class="step-summary" style="color: #555; font-size: 0.98rem;"></div>
        </div>
        <div class="progress-bar">
            <div class="progress" id="progressIndicator" style="width: 33%;"></div>
        </div>
        <!-- Step 1: Team Selection -->
        <div class="step-content" id="step-1">
            <form id="teamForm">
                <div class="team-radio-group">
                    @foreach($team_resolution as $key => $team)
                        @if($key == 'supervisor')
                            @if(in_array(auth()->user()->id, $resolution_supervisor_access))
                                <label class="radio-label"><input type="radio" name="team" value="{{$key}}" required>{{ $team }}</label>
                            @endif
                        @else
                            <label class="radio-label"><input type="radio" name="team" value="{{$key}}" required>{{ $team }}</label>
                        @endif
                    @endforeach
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" id="toStep2" class="btn cust-right-btn btn-primary nextStepBtn" disabled><i class="fa fa-arrow-right"></i>Next <span></span></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Step 2: Clinicians Options -->
        <div class="step-content" id="step-2" style="display:none;">
            <form id="step2Form">
                <div id="step2div">
                    @foreach($resolution_array as  $key => $resolution)
                        <div id="{{$key}}" style="display:none;" class="clinicalradio-group">
                            @foreach($resolution as $res)
                                <label class="radio-label"><input type="radio" name="step2-option" value="{{$res}}"> {{$res}}</label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div id="otherTeamOptions" style="display:none;">
                    <p>No additional options for this team.</p>
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" class="btn cust-right-btn btn-secondary backStepBtn"><i class="fa fa-arrow-left"></i>Back <span></span></button>
                        <button type="button" id="toStep3" class="btn cust-right-btn btn-primary nextStepBtn" disabled><i class="fa fa-arrow-right"></i>Next <span></span></button>
                        <button type="button" style="display:none" id="toStepSubmitId" class="btn cust-right-btn btn-success ml-1 toStepSubmit"><i class="fa fa-save"></i>Submit <span></span></button>
                        <img src="{{ asset('ajax-loader.gif')}}" alt="loader" class="loader_class" style="display:none">
                    </div>
                </div>
            </form>
        </div>
        <!-- Step 3: Cancelled Reason -->
        <div class="step-content" id="step-3" style="display:none;">
            <form id="lastStepForm">
                <div id="cancelledReasonDiv" style="display:none;">
                    <label for="cancelledReason"><b>Please select a reason:</b></label><br>
                    @foreach($masterData as $val)
                        @if($val->master_type_fk == 33)
                            <label class="radio-label">
                            <input type="radio" name="cancel_reason" value="{{$val->id}}" required> {{$val->name}}</label>
                        @endif
                    @endforeach
                    <div class="error mt-2 text-danger" id="cancel_error"></div>

                    <div id="cancelOtherTextDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Other reason:</b><span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="other_cancel_reason" value="" name="other_cancel_reason">
                        <div class="error mt-2 text-danger" id="other_cancel_reason_error"></div>
                    </div>

                    <div id="cancelNotesServiceRequestDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Request Services:</b><span style="color:red">*</span></label>
                        <select class="form-control select2 w-100" name="form_request_service_id[]" id="cancel_form_request_service_id">
                            <option value="">Select Request Service</option>
                        </select>
                        <div class="error mt-2 text-danger" id="cancel_request_error"></div>
                    </div>

                    <label for="cancelledReason"><b>Notes:</b></label><br>
                    <textarea id="cancelledReason" name="cancelledReason" rows="3" style="width:100%;" placeHolder="Enter Notes"></textarea>
                    <span class="error mt-2 text-danger" id="cancel_notes_error"></span>
                </div>
                <div id="refusedReasonDiv" style="display:none;">
                    <label for="refusedReason"><b>Please select a reason:</b></label><br>
                    @foreach($masterData as $val)
                        @if($val->master_type_fk == 32)
                            <label class="radio-label">
                            <input type="radio" name="refuse_reason" value="{{$val->id}}" required> {{$val->name}}</label>
                        @endif
                    @endforeach
                    <div class="error mt-2 text-danger" id="refuse_error"></div>
                    
                    <div id="refuseOtherTextDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Other reason:</b><span style="color:red">*</span></label>
                        <input type="text" id="other_refuse_reason" value="" class="form-control" name="other_refuse_reason">
                        <div class="error mt-2 text-danger" id="other_refuse_reason_error"></div>
                    </div>

                    <div id="refuseNotesServiceRequestDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Request Services:</b><span style="color:red">*</span></label>
                        <select class="form-control select2 w-100" name="form_request_service_id[]" id="refuse_form_request_service_id">
                            <option value="">Select Request Service</option>
                        </select>
                        <div class="error mt-2 text-danger" id="refuse_request_error"></div>
                    </div>

                    <label for="refusedReason"><b>Notes:</b></label><br>
                    <textarea id="refusedReason" name="refusedReason" rows="3" style="width:100%;" placeHolder="Enter Notes"></textarea>
                    <span class="error mt-2 text-danger" id="refuse_notes_error"></span>
                </div>
                <div id="notesDiv" style="display:none;">
                    <div id="notesServiceDiv" style="display:none">
                        <label class="col-form-label"><b>Services:</b><span class="error mt-2">*</span></label><br/>
                        <select class="js-example-basic-multiple w-100 form_res_new_service_id" multiple="multiple" name="service_id[]" id="form_res_service_id">
                            @php $resserviceArr = explode(',', $record->service_id);

                            @endphp

                            
                            @if (count($serviceList) > 0)
                            @foreach ($serviceList as $ks)
                            @if (strtolower($ks->types) == strtolower($record->type))

                            <option value="{{$ks->id}}" <?php if (in_array($ks->id, $resserviceArr)) { ?>selected<?php } ?>>{{ $ks->name }}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                        <input type="hidden" id="init_services" value="{{$record->service_id}}">
                        <span class="error mt-2 text-danger" id="res_chart_services_error"></span>
                    </div>

                    <div id="notesServiceRequestDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Request Services:</b><span style="color:red">*</span></label>
                        <select class="form-control select2 w-100" name="form_request_service_id[]" id="form_request_service_id">
                            <option value="">Select Request Service</option>
                        </select>
                        <div class="error mt-2 text-danger" id="notes_request_error"></div>
                    </div>
                    <div id="notesshowingDiv">
                        <label for="notes"><b>Notes:</b></label><br>
                        <textarea id="notes" name="notes" rows="3" style="width:100%;" placeHolder="Enter Notes"></textarea>
                        <span class="error mt-2 text-danger" id="notes_error"></span>
                    </div>
                </div>
                <div id="bookDiv" style="display:none;">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                    <?php 
                        $locationsIds = [];
                        if(auth()->user()->agency_fk !=""){
                            $locationsIds = ['49','55'];
                        }
                    ?>
                    <?php if ($record->type == 'Caregiver') { ?>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
                            <select name="res_location_id" class="form-control" id="res_location_id" onchange="getTimeSearchResolution()">
                                <option value="">Select Location</option>
                                
                                <?php foreach ($location_list as $ks) { 
                                    if(!in_array($ks->id,$locationsIds)){
                                    ?>
                                    <option value="<?php echo $ks->id; ?>" <?php if ($record->location_id == $ks->id) {
                                                                                echo "selected='selected'";
                                                                            } ?>><?php echo $ks->address1; ?>
                                    </option>
                                <?php } } ?>
                            </select>

                            <span id="res_location_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>
                    <?php } ?>
                    <?php
                    $dates = '';
                    $time = '';
                    if ($record->appointment_date != '') {
                        $dates = date('m/d/Y', strtotime($record->appointment_date));
                        $time = date('H:i:s', strtotime($record->appointment_date));
                    } ?>
                    <div class="form-group setDate">
                        <label for="recipient-name" class="col-form-label">Appointment Date <span style="color:red">*</span>:</label>
                        <input readonly type="text" name="date" class="form-control resolution_date" autocomplete="off" id="res_date_id" onchange="getTimeSearchResolution()" value="<?php echo $dates; ?>">
                        <span id="res_date_error" class="error mt-2 text-danger" for="document_type"></span>
                        @if ($record->type == 'Caregiver')
                            <div id="res_date_time_div" class=""></div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
                        <?php if ($record->type == 'Caregiver') { ?>
                            <select name="time" class="form-control" id="res_timeid">
                                <option value="">Select Appointment Time</option>
                            </select>

                        <?php } else { ?>
                            <input type="time" name="time" class="form-control" id="res_times_id" value="<?php echo $time; ?>">

                        <?php } ?>
                        <span id="res_time_error" class="error mt-2 text-danger" for="document_type"></span>
                        <div id="res_date_time_count_div" class=""></div>
                    </div>



                    <div class="form-group">

                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100 res_new_service_id" multiple="multiple" name="service_id[]" id="res_service_id">
                            @php $resserviceArr = explode(',', $record->service_id);

                            @endphp

                            
                            @if (count($serviceList) > 0)
                            @foreach ($serviceList as $ks)
                            @if (strtolower($ks->types) == strtolower($record->type))

                            <option value="{{$ks->id}}" <?php if (in_array($ks->id, $resserviceArr)) { ?>selected<?php } ?>>{{ $ks->name }}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="res_service_error"></span>

                    </div>
                    @if ($record->type == 'Patient')
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Location<span style="color:red">*</span>:</label>
                        <select name="location_id" class="form-control" id="res_location_id">
                            <option value="">Select Location</option>
                            @if (count($locations) > 0)
                            @foreach ($locations as $location)
                                @if(!in_array($location->id,$locationsIds))
                                <option value="{{$location->id}}" {{($record->location_id ==  $location->id) ? 'selected' : '' }}>{{$location->address1}}
                                </option>
                                @endif
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="res_location_error"></span>

                    </div>
                    @endif
                </div>
                <div id="telehaelthbookDiv" style="display:none;">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Date <span style="color:red">*</span></label>
                        <input type="text" name="date" class="form-control" autocomplete="off" id="res_patient_telehealth_date_id" placeholder="mm/dd/yyyy" readonly><i class="date-icon fa fa-calendar" aria-hidden="true"></i>
                        <span id="res_patient_telehealth_date_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="nurse">Nurse <span style="color:red">*</span></label>
                        <select class="form-control select2" id="res_telehealth_nurse" name="res_telehealth_nurse">
                            <option value="">Select Nurse</option>
                            @foreach($nurse as $key => $user)
                            <option value="{{ $key }}"> C#{{ $key }} ({{$user['language']}}) </option>
                            @endforeach
                        </select>
                        <span id="res_telehealth_nurse_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Select Slot <span style="color:red">*</span></label>
                        <select class="form-control select2" id="res_patient_telehealth_time_slot" name="res_patient_telehealth_time_slot">
                            <option value="">Select Slot</option>

                            @if(isset($slot))
                                @foreach($slots as $key => $slot)
                                <option value="{{ $slots['id'] }}">{{ $slots['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span id="res_patient_telehealth_time_slot_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100" multiple="multiple"
                            name="res_tele_patient_service_id[]" id="res_tele_patient_service_id">
                            @php $serviceArr = explode(',', $record->service_id); @endphp
                            @if (count($serviceList) > 0)
                                @foreach ($serviceList as $ks)
                                    @if ($ks->types == $record->type)
                                        <option value="{{ $ks->id }}"
                                            <?php if (in_array($ks->id, $serviceArr)) { ?>selected<?php } ?>>{{ $ks->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="res_tele_patient_service_error"></span>
                    </div>
                </div>
                <div id="otherResolutionDiv" style="display:none;">
                    <p>No further information required for this resolution.</p>
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" class="btn cust-right-btn btn-secondary backStepBtn"><i class="fa fa-arrow-left"></i>Back <span></span></button>
                        <button type="button" id="toStep4" class="btn cust-right-btn btn-primary nextStepBtn"><i class="fa fa-arrow-right"></i>Next <span></span></button>
                        <button type="button" style="display:none" id="toStep3SubmitId" class="btn cust-right-btn btn-success ml-1 toStepSubmit"><i class="fa fa-save"></i>Submit <span></span></button>
                        <img src="{{ asset('ajax-loader.gif')}}" alt="loader" class="loader_class" style="display:none">
                    </div>
                </div>
            </form>
        </div>

        <div class="step-content" id="step-4" style="display:none;">
            <form id="lastStepForm">
                <div id="booknotesServiceRequestDiv" style="display:none">
                    <label for="recipient-name" class="col-form-label"><b>Request Services:</b><span style="color:red">*</span></label>
                    <select class="form-control select2 w-100" name="book_form_request_service_id[]" id="book_form_request_service_id">
                        <option value="">Select Request Service</option>
                    </select>
                    <div class="error mt-2 text-danger" id="book_request_error"></div>
                </div>
                <div id="bookNotesDiv" style="display:none;">
                    <label for="notes"><b>Notes:</b></label><br>
                    <textarea id="res_notes" name="notes" rows="3" style="width:100%;" placeHolder="Enter notes"></textarea>
                    <span class="error mt-2 text-danger" id="res_notes_error"></span>
                </div>
                <div id="otherResolutionDiv" style="display:none;">
                    <p>No further information required for this resolution.</p>
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" class="btn cust-right-btn btn-secondary backStepBtn"><i class="fa fa-arrow-left"></i>Back <span></span></button>
                        <button type="button" id="toStepBookSubmitId" class="btn cust-right-btn btn-success ml-1 toStepSubmit"><i class="fa fa-save"></i>Submit <span></span></button>
                        <img src="{{ asset('ajax-loader.gif')}}" alt="loader" class="loader_class" style="display:none">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>