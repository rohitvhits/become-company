

<div class="modal fade" id="show-patient-cretae-poc-modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-xl" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Patient POC</h4>
                <button type="button" class="close" onclick="clearPaientPOCData();" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_patient_poc">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Office Id <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select type="text" onchange="getTaskDetails();" id="office_id" name="office_id" class="form-control">
                                    <option value=""> Select Office</option> 
                                </select>
                                <span id="office_id_error" class="text-danger"></span>
                            </div>
                            <label class="col-sm-2 col-form-label">Shift <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select type="text" id="shift" name="shift" class="form-control">
                                    <option value=""> Select Shift</option> 
                                    <option value="shift1"> Shift 1</option> 
                                    <option value="shift2"> Shift 2</option> 
                                    <option value="shift3"> Shift 3</option> 
                                    <option value="shift4"> Shift 4</option> 
                                </select>
                                <span id="shift_error" class="text-danger"></span>
                            </div>

                            <label class="col-sm-2 col-form-label">Shift Start date <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="start_date" readonly class="form-control" id="start_date_id" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                <span id="start_date_id_error" class="error"></span>
                            </div>
                            <label class="col-sm-2 col-form-label">Shift Stop date <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="stop_date" readonly class="form-control" id="stop_date_id" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                <span id="stop_date_id_error" class="error"></span>
                            </div>
                            <div id="task_list"><div class="table-responsive">
                                <table id="" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="15%" nowrap="">Task Id</th>
                                            <th width="10%" nowrap="">Minutes</th>
                                            <th width="5%" nowrap="">As <br/>Requested</th>
                                            <th width="20%" nowrap="">Times a Week(Min)-(Max)</th>
                                            <th width="20%" nowrap="">Instruction</th>
                                            <th width="35%" nowrap="">Days of Week</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>
                                                        <select type="text" id="task_id_{{$i}}" name="task_id[]" class="form-control task">
                                                            <option value=""> Select Task</option> 
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="row"> 
                                                            <div class="col-sm-8">
                                                                <input type="text" onkeypress="return isNumber(event)" class="form-control" id="minutes_{{$i}}" name="minutes[]" >
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-primary">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="as_requested[]" data-gtm-form-interact-field-id="0" value="0" class="form-check-input" id="as_requested_{{$i}}"><i class="input-helper"></i>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row"> 
                                                            <div class="col-sm-5">
                                                                <input type="text" onkeypress="return isNumber(event)" class="form-control" id="maxtime_{{$i}}" name="maxtime[]" >
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input type="text" onkeypress="return isNumber(event)" class="form-control" id="min_time_{{$i}}" name="mintime[]" >
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="instruction[]" id="instruction_{{$i}}">
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Sat"> Sat
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Sun"> Sun
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Mon"> Mon
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Tue"> Tue
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Wed"> Wed
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Thu"> Thu
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div class="form-check form-check-primary">
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" data-gtm-form-interact-field-id="0" class="form-check-input" name="days_{{$i}}[]" value="Fri"> Fri
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endfor
                                    </tbody>
                                </table>
                                <div class="pull-right pegination-margin">
                                </div>
                            </div></div>
                        </div>

                    </div>
      
                </div>

                <div class="modal-footer">
                    <div class="loader-inner d-none">
                        <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader">
                    </div>
                    <button type="button" onclick="clearPaientPOCData();" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePatientPOCdetails">Save</button>
                </div>
            </form>
        </div>

    </div>
</div>
