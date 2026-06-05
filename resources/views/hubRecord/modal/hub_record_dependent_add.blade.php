
<div class="modal fade" id="hub_add_dependent_modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
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
                    <div><h5><i class="mdi mdi-information mr-1"></i>Basic Fields</h5></div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group  mb-2">
                                <label class="mb-0">First Name<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter First Name " id="dep_first_name" name="first_name" value="">
                                <span id="dep_first_name_error" class="error "></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group  mb-2">
                                <label class="mb-0">Middle Name</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Middle Name " id="dep_middle_name_id" name="middle_name" value="<?php echo old('middle_name'); ?>">
                                <span id="dep_middle_name_error" class="error "></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Last Name<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Name " id="dep_last_name_id" name="last_name" value="<?php echo old('last_name'); ?>">
                                <span id="dep_last_name_error" class="error "></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Email</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Email " id="dep_email" name="email" value="<?php echo old('email'); ?>">
                                <span id="dep_email_error" class="error "></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-top-margin">
                            <div class="form-group mb-2">
                                <label class="mb-0">Date of Birth<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control bill_date form-control-sm " autocomplete="off" placeholder="Select  Date of Birth" id="dep_dob_id" name="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('dob'); ?>">
                                <span id="dep_dob_error" class="error "></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-top-margin">
                            <div class="form-group mb-2">
                                <label class="mb-0">Mobile<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Mobile" id="dep_mobile_no" onkeypress="return isNumber(event)" name="mobile" value="<?php echo old('mobile'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="dep_mobile_error" class="error "></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-top-margin">
                            <div class="form-group mb-2">
                                <label class="mb-0">Phone</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Phone" id="dep_phone" onkeypress="return isNumber(event)" name="phone" value="<?php echo old('phone'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="dep_phone_error" class="error "></span>

                            </div>
                        </div>
                        @if(Auth()->user()->view_ssn_hub ==1)
                        <div class="col-md-3 div-top-margin">
                            <div class="form-group mb-2">
                                <label class="mb-0">SSN <span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter SSN" id="dep_ssn" name="ssn" value="<?php echo old('ssn'); ?>">
                                <span id="dep_ssn_error" class="error "></span>

                            </div>
                        </div>
                        @endif
                        <div class="col-md-3 div-top-margin">
                            <div class="form-group mb-2">
                                <label class="mb-0">Gender<span class="text-danger mt-2">*</span></label>
                                <div class="col-sm-9 row">

                                    <div class="form-check mr-5 mt-0">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="dep_msp" name="gender" value="male" <?php if (old('gender') == 'male') {
                                                                                                                                } ?> > Male <i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check  mt-0">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="dep_msp" name="gender" value="female" <?php if (old('gender') == 'female') {
                                                                                                                                    echo "checked='checked'";
                                                                                                                                } ?>> Female<i class="input-helper"></i></label>
                                    </div>
                                </div>
                                <span id="dep_address2_error" class="error "><?php echo $errors->add_agency->first('gender'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3 div-padding-top">
                            <div class="form-group">
                                <label class="label-spacing">Relationship</label>
                                <select class="form-control" name="spouse" id="spouse">
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
                            <div class="form-group mb-2">
                                <label class="mb-0">Address 1</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Address 1" id="dep_address1" name="address1" value="<?php echo old('address1'); ?>">
                                <span id="dep_address1_error" class="error "><?php echo $errors->add_record->first('address1'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Address 2</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Address 2" id="dep_address2" name="address2" value="<?php echo old('address2'); ?>">
                                <span id="dep_address2_error" class="error "><?php echo $errors->add_record->first('address2'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">State</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter State" id="dep_state" name="state" value="<?php echo old('state'); ?>" maxlength="50">
                                <span id="dep_state_error" class="error "><?php echo $errors->add_record->first('state'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">City</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter City" id="dep_city" name="city" value="<?php echo old('city'); ?>" maxlength="50">
                                <span id="dep_city_error" class="error "><?php echo $errors->add_record->first('city'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">Zip Code</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Zip Code" id="dep_zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="<?php echo old('zip_code'); ?>">
                                <span id="dep_zip_code_error" class="error "><?php echo $errors->add_record->first('zip_code'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0">County</label>
                                <input type="text" class="form-control form-control-sm" id="dep_county" name="county" readonly onkeypress="return isNumber(event)" value="<?php echo old('county'); ?>">
                                <span id="dep_zip_code_error" class="error "><?php echo $errors->add_record->first('county'); ?></span>

                            </div>
                        </div>
                    </div>
                    <hr style="margin-top:-8px"/>
                    <div>
                        <h5><i class="fa fa-address-card mr-1"></i>Other Details </h5>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="label-spacing">Hire Date</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter" id="hire_date" name="hire_date"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('hire_date'); ?>" maxlength="50">
                                <span id="city_error" class="error mt-2"><?php echo $errors->add_record->first('hire_date'); ?></span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="label-spacing">Work Contact</label>
                                <input type="text" class="form-control  form-control-sm" placeholder="Enter Work Contact"  id="work_contact" name="work_contact" data-inputmask-alias="(999) 999-9999" im-insert="true" value="<?php echo old('work_contact'); ?>">
                          
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label class="label-spacing">Work Email</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Work Email" id="work_email" name="work_email" value="<?php echo old('work_email'); ?>">
                                <span id="work_email_error" class="error mt-2"><?php echo $errors->add_record->first('work_email'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="label-spacing">Last Work Date</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Work Date" id="last_worked_date" name="last_worked_date"   data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('last_worked_date'); ?>">
                                <span class="error mt-2"><?php echo $errors->add_record->first('last_worked_date'); ?></span>

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