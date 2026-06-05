<div class="modal fade" id="hub_add_modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-lg" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create New Hub</h4>
                <button onclick="clearModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_new_hub">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <div class="modal-body">
                    <div style="margin-top:-10px"><h5><i class="mdi mdi-information mr-1"></i>Basic Fields</h5></div>
                    <div class="row">
                        <input type="hidden" id="agency_id" name="agency_id" value="">
                        <!-- <div class="col-md-3" id="agency-div">
                            <div  class="form-group mb-2">
                                <label>Company Name<span class="text-danger mt-2">*</span></label>
                                <select name="agency_id" id="agency_id" class="form-control select2-design cal-padding-0 w-100">
                                    <option value="">Select Company</option>
                                    <?php foreach ($agencyList as $rwAgency) { ?>
                                        <option value="<?php echo $rwAgency->id; ?>">
                                            <?php echo $rwAgency->agency_name; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="agency_name_error" class="error mt-2"></span>
                            </div>
                        </div> -->
                        
                        <div class="col-md-3">
                            <div class="form-group  mb-2">
                                <label  class="mb-0">First Name<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter First Name " id="dep_first_name" name="first_name" value="">
                                <span id="dep_first_name_error" class="error mt-2 error_html"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Middle Name</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Middle Name " id="dep_middle_name_id" name="middle_name" value="<?php echo old('middle_name'); ?>">
                                <span id="dep_middle_name_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Last Name<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Name " id="dep_last_name_id" name="last_name" value="<?php echo old('last_name'); ?>">
                                <span id="dep_last_name_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group  mb-2">
                                <label class="mb-0">Email</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Email " id="dep_email" name="email" value="<?php echo old('email'); ?>">
                                <span id="dep_email_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group mb-2">
                                <label class="mb-0">Date of Birth<span class="text-danger mt-2">*</span></label>

                                <input type="text"  class="form-control bill_date form-control-sm " autocomplete="off" placeholder="Enter  Date of Birth" id="dep_dob_id" name="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('dob'); ?>">
                                <span id="dep_dob_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Mobile<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Mobile" id="dep_mobile_no" onkeypress="return isNumber(event)" name="mobile" value="<?php echo old('mobile'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="dep_mobile_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Phone</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Phone" id="dep_phone" onkeypress="return isNumber(event)" name="phone" value="<?php echo old('phone'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="dep_phone_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        @if(Auth()->user()->view_ssn_hub ==1)
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">SSN<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter SSN" id="dep_ssn" name="ssn" value="<?php echo old('ssn'); ?>">
                                <span id="dep_ssn_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        @endif
                        <div class="col-md-3 div-padding-top">
                            <div class="form-group mb-2">
                                <label  class="mb-0">Gender<span class="text-danger mt-2">*</span></label>
                                <div class="col-sm-12 row">

                                    <div class="form-check mr-5 mt-0">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="gender" value="male" @if(old('gender') == 'male') checked='checked' @endif> Male <i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check mr-5 mt-0">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="gender" value="female" @if(old('gender') == 'female') checked='checked' @endif> Female<i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check mt-0">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="gender" value="other" @if(old('gender') == 'other') checked='checked' @endif> Other<i class="input-helper"></i></label>
                                    </div>
                                </div>
                                <div id="other_gender_div" class="mt-1" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Other Name" id="dep_other_gender" name="other_gender" value="{{ old('other_gender') }}">
                                </div>
                                <span id="dep_address2_error" class="error mt-2 error_html" style="margin-top:-16px !important">{{ $errors->add_agency->first('gender') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3 div-padding-top">
                            <div class="form-group  mb-2">
                                <label  class="mb-0">Relationship</label>
                                <select class="form-control" name="spouse" id="dep_spouse">
                                    <option value="">Select Relationship</option>
                                    @foreach(Common::relationship() as $vs)
                                    <option value="{{ $vs}}">{{$vs}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div>
                        <h5><i class="fa fa-address-card mr-1"></i>Address Details </h5>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-3">
                            <div class="form-group  mb-2">
                                <label  class="mb-0">Address 1</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Address 1" id="dep_address1" name="address1" value="<?php echo old('address1'); ?>">
                                <span id="dep_address1_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('address1'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Address 2</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Address 2" id="dep_address2" name="address2" value="<?php echo old('address2'); ?>">
                               

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">State</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter State" id="dep_state" name="state" value="<?php echo old('state'); ?>" maxlength="50">
                                <span id="dep_state_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('state'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">City</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter City" id="dep_city" name="city" value="<?php echo old('city'); ?>" maxlength="50">
                                <span id="dep_city_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('city'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-padding-top">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Zip Code</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Zip Code" id="dep_zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="<?php echo old('zip_code'); ?>">
                                <span id="dep_zip_code_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('zip_code'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-padding-top">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">County</label>

                                <input type="text" class="form-control form-control-sm" id="dep_county" name="county" readonly onkeypress="return isNumber(event)" value="<?php echo old('county'); ?>">
                                <span id="dep_county_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('county'); ?></span>

                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div>
                        <h5><i class="fa fa-address-card mr-1"></i>Other Details </h5>
                    </div>
                    <div class="row">
                        <div class="col-md-3 ">
                            <div class="form-group  mb-2" >
                                <label class="mb-0">Member Id<span class="text-danger mt-2"></span></label>
                                <input type="text" class="form-control  form-control-sm" placeholder="Enter Member Id " id="dep_member_id" name="member_id" value="">
                                <span id="dep_member_id_error" class="error mt-2 error_html"></span>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group  mb-2" >
                                <label class="mb-0">Employee Code<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control  form-control-sm" placeholder="Enter Employee Code " id="dep_employee_code" name="employee_code" value="">
                                <span id="dep_employee_code_error" class="error mt-2 error_html"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Hire Date</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Hire Date" id="dep_hire_date" name="hire_date"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('hire_date'); ?>" maxlength="50">
                                <span id="dep_hire_date_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('hire_date'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Work Contact</label>
                                <input type="text" class="form-control  form-control-sm" placeholder="Enter Work Contact"  id="dep_work_contact" name="work_contact" data-inputmask-alias="(999) 999-9999" im-insert="true" value="<?php echo old('work_contact'); ?>">
                          
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Work Email</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Work Email" id="dep_work_email" name="work_email" value="<?php echo old('work_email'); ?>">
                                <span id="dep_work_email_error" class="error mt-2 error_html"><?php echo $errors->add_record->first('work_email'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div  class="form-group mb-2">
                                <label  class="mb-0">Last Work Date</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Work Date" id="last_worked_date" name="last_worked_date"   data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('last_worked_date'); ?>">
                                <span class="error mt-2 error_html"><?php echo $errors->add_record->first('last_worked_date'); ?></span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clearModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveHub" >Save</button>
                </div>
            </form>
        </div>
    </div>
</div>