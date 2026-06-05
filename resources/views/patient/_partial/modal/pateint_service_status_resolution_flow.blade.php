<div style="display:none;" id="patientServiceResolutionModal">
    <h2 class="modal-title" style="margin: 0; font-size: 1.4rem; font-weight: 600; text-align:left;">Chart Service Resolution</h2>
    <div class="resolution-steps">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div id="serviceStepSummary" class="step-summary" style="color: #555; font-size: 0.98rem;"></div>
        </div>
        <div class="progress-bar">
            <div class="progress" id="serviceProgressIndicator" style="width: 33%;"></div>
        </div>
        <!-- Step 1: Team Selection -->
        <div class="step-content" id="service-step-1">
            <form id="serviceTeamForm">
                <div class="team-radio-group">
                    @foreach($team_resolution as $key => $team)
                        @if($key == 'supervisor')
                            @if(in_array(auth()->user()->id, $resolution_supervisor_access))
                                <label class="service-radio-label"><input type="radio" name="service_team" value="{{$key}}" required>{{ $team }}</label>
                            @endif
                        @else
                            <label class="service-radio-label"><input type="radio" name="service_team" value="{{$key}}" required>{{ $team }}</label>
                        @endif
                    @endforeach
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" id="serviceToStep2" class="btn cust-right-btn btn-primary nextStepBtn" disabled><i class="fa fa-arrow-right"></i>Next <span></span></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Step 2: Clinicians Options -->
        <div class="step-content" id="service-step-2" style="display:none;">
            <form id="serviceStep2Form">
                <div id="serviceStep2div">
                    @foreach($resolution_array as $key => $resolution)
                        <div id="service_{{$key}}" style="display:none;" class="clinicalradio-group">
                            @foreach($resolution as $res)
                                @if($res != 'New Order Received' && $res != 'New Form Requested')
                                    <label class="service-radio-label"><input type="radio" name="services-step2-option" value="{{$res}}"> {{$res}}</label>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div id="serviceOtherTeamOptions" style="display:none;">
                    <p>No additional options for this team.</p>
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" class="btn cust-right-btn btn-secondary serviceBackStepBtn"><i class="fa fa-arrow-left"></i>Back <span></span></button>
                        <button type="button" id="serviceToStep3" class="btn cust-right-btn btn-primary nextStepBtn" style="display:none"><i class="fa fa-arrow-right"></i>Next <span></span></button>
                        <button type="button" id="serviceToStepSubmitId" class="btn cust-right-btn btn-success ml-1 serviceToStepSubmit"><i class="fa fa-save"></i>Submit <span></span></button>
                        <img src="{{ asset('ajax-loader.gif')}}" alt="loader" class="service_loader_class" style="display:none">
                    </div>
                </div>
            </form>
        </div>

        <!-- Step 3: Cancelled Reason -->
        <div class="step-content" id="service-step-3" style="display:none;">
            <form id="serviceStep3Form">
                <div id="serviceCancelledReasonDiv" style="display:none;">
                    <label for="cancelledReason"><b>Please select a reason:</b></label><br>
                    @foreach($masterData as $val)
                        @if($val->master_type_fk == 33)
                            <label class="radio-label">
                            <input type="radio" name="service_cancel_reason" value="{{$val->id}}" required> {{$val->name}}</label>
                        @endif
                    @endforeach
                    <div class="error mt-2 text-danger" id="service_cancel_error"></div>
                    <div id="serviceCancelOtherTextDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Other reason:</b><span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="service_other_cancel_reason" value="" name="service_other_cancel_reason">
                        <div class="error mt-2 text-danger" id="service_other_cancel_reason_error"></div>
                    </div>
                </div>
                <div id="serviceRefusedReasonDiv" style="display:none;">
                    <label for="refusedReason"><b>Please select a reason:</b></label><br>
                    @foreach($masterData as $val)
                        @if($val->master_type_fk == 32)
                            <label class="radio-label">
                            <input type="radio" name="service_refuse_reason" value="{{$val->id}}" required> {{$val->name}}</label>
                        @endif
                    @endforeach
                    <div class="error mt-2 text-danger" id="service_refuse_error"></div>

                    <div id="serviceRefuseOtherTextDiv" style="display:none">
                        <label for="recipient-name" class="col-form-label"><b>Other reason:</b><span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="service_other_refuse_reason" value="" name="service_other_refuse_reason">
                        <div class="error mt-2 text-danger" id="service_other_refuse_reason_error"></div>
                    </div>
                </div>
                <hr/>
                <div class="page-rightbtns cust-page-rightbtns">
                    <div>
                        <button type="button" class="btn cust-right-btn btn-secondary serviceBackStepBtn"><i class="fa fa-arrow-left"></i>Back <span></span></button>
                        <button type="button" id="serviceToStepSubmitId" class="btn cust-right-btn btn-success ml-1 serviceToStepSubmit"><i class="fa fa-save"></i>Submit <span></span></button>
                        <img src="{{ asset('ajax-loader.gif')}}" alt="loader" class="service_loader_class" style="display:none">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>