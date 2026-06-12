<style>
#patient_add_modal .form-group label{
    margin-bottom: 0px;
    line-height:0px;
}
#patient_add_modal .form-check .form-check-label{
    line-height: 1.5;
}

#patient_add_modal .form-group{
    margin-bottom: 10px;
}
#patient_add_modal .modal-footer button{
    padding: 0.42rem 1.25rem;
}
#patient_add_modal .modal-header{
    padding: 15px 25px;
}
#patient_add_modal .modal-body{
    padding: 20px 25px;
}

    </style>
<div class="modal fade" id="patient_add_modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-lg" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Patient

                <div class="pull-right ml-5">
                (<span id="total_search_appointment">0</span>) Existing Records
                    </div>
                </h4>
                <button onclick="clearModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_new_patient">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <input type="hidden" name="agency_id" id="patient_agency_id">
                <input type="hidden" name="caregiver_id" id="cid" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 col-form-label">Type<span class="text-danger mt-2">*</span></label>
                                <div class="col-sm-9 row">
                                    @if(auth()->user()->record_access =="All" || auth()->user()->record_access =="Caregiver" )
                                    <div class="form-check mr-2">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="msp" name="type" value="Caregiver" onclick="getResponse('Caregiver','add')" <?php if (old('type') == 'Caregiver') {
                                                                                                                                                                        echo "checked='checked'";
                                                                                                                                                                    } ?>> Candidate/Caregiver <i class="input-helper"></i></label>
                                    </div>
                                    @endif
                                    @if(auth()->user()->record_access =="All" || auth()->user()->record_access =="Patient" )
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" name="type" class="form-check-input" id="msp" value="Patient" onclick="getResponse('Patient','add')" <?php if (old('type') == 'Patient') {
                                                                                                                                                                    echo "checked='checked'";
                                                                                                                                                                } ?>>
                                            Patient
                                        </label>
                                    </div>
                                    @endif
                                </div>

                                <span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('type'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-2 hide" id="transition_aid" >
                                           
                                            <div class="col-sm-10">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="transition_aid" class="form-check-input" id="transition_aid" value="1" >
                                                        Transition Aid
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                        <div class="col-md-3">
                            <div class="form-group ">
                                <label class="col-form-label">Code</label>
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <div>
                                                <input type="text" name="patient_code" value="" class="form-control form-control-sm" placeholder="Patient Code">

                                                <span id="patient_code_error" class="error mt-2"><?php echo $errors->add_agency->first('patient_code'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 @if( $flag !='1') hide @else  @endif" id="agency_hha_enabled">
                                            <a onclick="getHHADetails()" title="SYNC HHA"><i class="fa fa-exchange" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                              
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">First Name<span class="text-danger mt-2">*</span></label>
                              
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter First Name " id="agency_name" name="first_name" value="{{ old('first_name') }}" onBlur="getExistingUserData()">
                                    <span id="agency_name_error" class="error mt-2"><?php echo $errors->add_agency->first('first_name'); ?></span>
                               
                            </div>
                        </div>
                        

                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Middle Name</label>
                              
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Middle Name " id="middle_name_id" name="middle_name" value="{{ old('middle_name') }}">
                                    <span id="middle_name_error" class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Last Name<span class="text-danger mt-2">*</span></label>
                               
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Last Name " id="last_name_id" name="last_name" value="{{ old('last_name') }}" onBlur="getExistingUserData()">
                                    <span id="last_name_error" class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                              
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group ">
                                <label class="col-form-label">Email</label>
                              
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Email " id="email" name="email" value="<?php echo old('email'); ?>">
                                    <span id="email_error" class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>
                             
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Date of Birth<span class="text-danger mt-2">*</span></label>
                               
                                    <input type="text" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" class="form-control bill_date form-control-sm " autocomplete="off" placeholder="Date of Birth" id="dob_id" name="dob" value="<?php echo old('dob'); ?>"  onChange="getExistingUserData()"  min="1000-01-01" max="9999-12-31">
                                    <span id="dob_error" class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>
                               
                            </div>
                        </div>
                        


                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Mobile<span class="text-danger mt-2">*</span></label>
                               
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Mobile" id="mobile"  data-inputmask-alias="(999) 999-9999" onBlur="getExistingUserData()" onkeypress="return isNumber(event)" name="mobile" value="<?php echo old('mobile'); ?>" maxlength="15">
                                    <span id="mobile_error" class="error mt-2"><?php echo $errors->add_agency->first('mobile'); ?></span>
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">SSN</label>
                                
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter SSN" id="ssn" name="ssn" value="<?php echo old('ssn'); ?>">
                                    <span id="ssn_error" class="error mt-2"><?php echo $errors->add_agency->first('ssn'); ?></span>
                                
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Phone</label>
                               
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Phone" id="phone"  data-inputmask-alias="(999) 999-9999"  onkeypress="return isNumber(event)" name="phone" value="<?php echo old('phone'); ?>" maxlength="15">
                                    <span id="phone_error" class="error mt-2"><?php echo $errors->add_agency->first('phone'); ?></span>
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 col-form-label">Gender<span class="text-danger mt-2">*</span></label>
                                <div class="col-sm-12 row">

                                    <div class="form-check mr-1">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="msp" name="gender" value="male" <?php if (old('gender') == 'male') {
                                                                                                                                } ?>  > Male <i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check mr-1">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="msp" name="gender" value="female" <?php if (old('gender') == 'female') {
                                                                                                                                    echo "checked='checked'";
                                                                                                                                } ?>  > Female<i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check mr-1">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="other" <?php if (old('gender') == 'other') { echo "checked='checked'";} ?>> Other<i class="input-helper"></i></label>
                                            </div>
                                            <div class="col-sm-9 hide" id="other_div_hide">
                                                <div class="form-group row">
                                                    <input type="text" class="form-control form-control-sm" name="other_name" placeholder="Other Name">
                                                    <span id="other_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_name'); ?></span>
                                                </div>
                                            </div>
                                </div>
                                <span id="address2_error" class="error mt-2"><?php echo $errors->add_agency->first('gender'); ?></span>

                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Language</label>
                               
                                <select class="form-control form-control-sm" name="language" id="language_id">
                                    <option value="">Select Language</option>
                                    @foreach ($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                                    @endforeach
                                </select>
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Address 1</label>
                           
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Address 1" id="address1" name="address1" value="<?php echo old('address1'); ?>">
                                    <span id="address1_error" class="error mt-2"><?php echo $errors->add_record->first('address1'); ?></span>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Apt/Suite/Floor</label>
                               
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Apt/Suite/Floor" id="address2" name="address2" value="<?php echo old('address2'); ?>">
                                    <span id="address2_error" class="error mt-2"><?php echo $errors->add_record->first('address2'); ?></span>

                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label class=" col-form-label">City</label>
                               
                                    <input type="text" class="form-control charCls form-control-sm" placeholder="Enter City" id="city" name="city" value="<?php echo old('city'); ?>" maxlength="50">
                                    <span id="city_error" class="error mt-2"><?php echo $errors->add_record->first('city'); ?></span>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class=" col-form-label">State</label>
                                
                                    <input type="text" class="form-control charCls form-control-sm" placeholder="Enter State" id="state" name="state" value="<?php echo old('state'); ?>" maxlength="50">
                                    <span id="state_error" class="error mt-2"><?php echo $errors->add_record->first('state'); ?></span>
                                
                            </div>
                        </div>
                       
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Zip Code</label>
                              
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Zip Code" id="zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="<?php echo old('zip_code'); ?>">
                                    <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('zip_code'); ?></span>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">County</label>
                                
                                    <input type="text" class="form-control form-control-sm" id="county" name="county" readonly onkeypress="return isNumber(event)" value="<?php echo old('county'); ?>">
                                    <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>
                             
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Discipline</label>
                               
                                    <select class="form-control form-control-sm" name="diciplin" id="diciplin_id">

                                        <option value="">Select Discipline</option>
                                        @if (count($masterData) > 0)
                                        @foreach ($masterData as $master)
                                        @if ($master->master_type_fk == 26)
                                        <option value="{{ $master->name }}" <?php if (old('diciplin') == $master->name) {
                                                                                echo "selected='selected'";
                                                                            } ?>>{{ $master->name }}
                                        </option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                    <span id="displine_error" class="error mt-2"><?php echo $errors->add_agency->first('diciplin'); ?></span>
                                

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Payment Type</label>
                               
                                    <select class="form-control form-control-sm" name="payment_type" id="payment_type">
                                        <option value="">Select Payment Type</option>
                                        @if (count($masterData) > 0)
                                        @foreach ($masterData as $master)
                                        @if ($master->master_type_fk == 17)
                                        <option value="{{ $master->id }}">{{ $master->name }}
                                        </option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                    <span id="payment_type_error" class="error mt-2"><?php echo $errors->add_agency->first('payment_type'); ?></span>
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Insurance ID</label>
                               
                                    <input type="text" class="form-control form-control-sm" autocomplete="off" placeholder="Enter Insurance ID" name="insurance_id" value="<?php echo old('insurance_id'); ?>">
                            
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Insurance Name</label>
                             
                                    <select class="form-control form-control-sm" name="insurance_name" id="insurance_name">
                                        <option value="">Select Insurance Name</option>
                                        @if (count($insuranceList) > 0)
                                        @foreach ($insuranceList as $insurance)
                                        <option value="{{ $insurance->id }}">{{ $insurance->insurance_name }}
                                        </option>
                                        @endforeach
                                        @endif
                                        <option value="other">Other</option>
                                    </select>
                               
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Notes</label>
                              
                                    <textarea class="form-control form-control-sm" placeholder="Notes" name="message" style="height: 50px"><?php echo old('message'); ?></textarea>
                                
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">CIN/Medicaid Number<span class="hide text-danger mt-2" id="hideShowRedCIN">*</span></label>
                               
                                    <input type="text" id="cin" name="cin" class="form-control form-control-sm" placeholder="Enter CIN/Medicaid Number">
                                    <span id="cin_error" class="error mt-2"><?php echo $errors->add_agency->first('cin'); ?></span>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Emergency Contact Name</label>
                                
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control form-control-sm" placeholder="Enter Emergency Contact Name">
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Emergency Contact Number</label>
                              
                                    <input type="text" id="emergency_phone" name="emergency_phone" onkeypress="return isNumber(event)" class="form-control form-control-sm" placeholder="Enter Emergency Contact Number">
                              
                            </div>
                        </div>

                        <div class="col-md-3" id="location_branch_wrapper">
                            <div class="form-group">
                                <label class="col-form-label">Location / Branch</label>

                                    <input type="text" id="location_branch" name="location_branch" class="form-control form-control-sm" placeholder="Enter Location / Branch">

                            </div>
                        </div>
                        <div class="col-md-3 hide" id="branch_dropdown_wrapper">
                            <div class="form-group">
                                <label for="branch" class="col-form-label">Branch<span style="color:red">*</span></label>
                                <div>
                                    <select class="form-control form-control-sm" name="branch_id" id="patient_branch_id">
                                        <option value="">Select Branch</option>
                                    </select>
                                    <span class="error mt-2 text-danger location_branch_error" for="branch"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 hide" id="other_insurance">
                            <div class="form-group">
                                <label class="col-form-label">Other Insurance Name></label>
                              
                                    <input type="text" id="other_insurance_name" name="other_insurance_name" class="form-control form-control-sm" placeholder="Enter Other Insurance Name">
                                    <span id="other_insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_insurance_name'); ?></span>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Agency Rep</label>
                                <div>
                                    <input type="text" id="agency_user_ids" name="agency_user_ids" placeholder="Search Agency User" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Services<span class=" mt-2" style="color:red">*</span></label>
                                <div>
                                    <select class="js-example-basic-multiple w-100" multiple="multiple" name="create_service_id[]" id="create_service_id">

                                    </select>
                                    <span id="create_service_id_error" class="error mt-2"><?php echo $errors->add_agency->first('service_id'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Followup Date</label>
                                <div>
                                    <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select Followup Date" id="create_fu_date_id" name="fu_date" value="<?php echo old('fu_date'); ?>" readonly>
                                    <span id="fu_date_error" class="error mt-2"><?php echo $errors->add_agency->first('fu_date'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Due Date</label>
                                <div>
                                    <input type="text" class="form-control" autocomplete="off" placeholder="Select Due Date" id="create_due_date_id" name="due_date" value="<?php echo old('due_date'); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->agency_fk =="")
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-form-label">Referral Source Type<span class=" mt-2" style="color:red">*</span></label>
                               
                                    <select class="form-control form-control-sm" name="referral_type" id="referral_type">

                                        <option value="">Select Referral Source Type</option>
                                            @if (count($masterData) > 0)
                                                @foreach ($masterData as $master)
                                                    @if ($master->master_type_fk == 31)
                                                        <option value="{{ $master->name }}" <?php if (old('referral_type') == $master->name) {
                                                                                echo "selected='selected'";
                                                                            } ?>>{{ $master->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                    </select>
                                    <span id="referral_type_error" class="error mt-2"><?php echo $errors->add_agency->first('referral_type'); ?></span>
                                

                            </div>
                        </div>
                        @else
                           <input type="hidden" class="form-control form-control-sm" name="referral_type" id="referral_type" value="Agency via Portal">
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="clearModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePatientId">Save</button>
                </div>
            </form>
        </div>

    </div>
</div>