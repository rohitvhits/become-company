@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #e3e7ed !important;
    border-radius: 0 !important;
    height: 40px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 10px !important;
}

span.select2.select2-container.select2-container--default {
    width: 100% !important;
}
.select2-container--default .select2-selection--multiple{
    border-radius: 0px !important;
    border: 1px solid #e3e7ed !important;
}
.hide{
    display:none
}
</style>
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Edit Ny Best Medicals Appointment</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='{{ url("patient/update")}}/{{ $patient->id }}' name="adduser" method="post" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <!-- <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Type<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="radio" name="type" value="Caregiver" onclick="getResponse('Caregiver')" <?= $patient->type == 'Caregiver' ? 'checked' : '' ?>> Caregiver
                                            <input type="radio" name="type" value="Patient" onclick="getResponse('Patient')" <?= $patient->type == 'Patient' ? 'checked' : '' ?>> Patient
                                            <br><span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('type'); ?></span>
                                        </div>
                                    </div>
                                </div> -->
                                <?php if ($user->agency_fk == '') { ?>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="" class="col-sm-3 col-form-label">Agency<span style="color:red">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="agency_id" class="form-control" id="agency_ids">
                                                    <option value="">Select Agency</option>
                                                    <?php if (count($agencyList) > 0) {
                                                        foreach ($agencyList as $vsl) {
                                                    ?>
                                                            <option value="<?php echo $vsl->id; ?>" <?php if ($patient->agency_id == $vsl->id) {
                                                          echo "selected='selected'";
                                                     } ?>>
                                                                <?php echo $vsl->agency_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>

                                                <span class="error mt-2" id="agency_error" for="file_name"></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php } else { 
                                    $flag = 0;
                                    $finalArray = [];
                                    foreach ($agencyList as $vsl) {
                                        $tempArray = [];
                                        if($vsl->id ==$user->agency_fk){
                                            $tempArray['id'] = $vsl->id;
                                            $tempArray['agency_name'] = $vsl->agency_name;
                                            $tempArray['app_name'] = $vsl->app_name;
                                            $finalArray[] = $tempArray;
                                            if($vsl->app_name !=""){
                                           
                                                $flag = 1;
                                        
                                            }
                                        }
                                    }

                                    $result = array_merge($finalArray,$userAgencyList);
                                
                                    ?>
                                    @if(!empty($result[0]))
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">Agency <span style="color:red">*</span></label>
                                                <div class="col-sm-9">
                                                    @if(!empty($result[0]))
                                                        <select name="agency_id" class="form-control"  id="agency_ids">
                                                            <option value="">Select Agency</option>
                                                            @foreach($result as $agn)
                                                                <option value="{{$agn['id']}}" @if($patient->agency_id == $agn['id']) selected @endif >{{$agn['agency_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                        
                                                    @else
                                                    <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                <?php } ?>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div  class="col-md-8">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Type<span class="error mt-2">*</span></label>
                                                <div class="col-sm-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="type" class="form-check-input" id="msp" value="Caregiver" onclick="getResponse('Caregiver')" <?php if (($patient->type) == 'Caregiver') { echo "checked='checked'"; } ?>>
                                                            Caregiver
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="type" class="form-check-input" id="msp" value="Patient" onclick="getResponse('Patient')" <?php if (($patient->type) == 'Patient') {
                                                                                                                                                                                    echo "checked='checked'";
                                                                                                                                                                                } ?>>
                                                            Patient
                                                        </label>
                                                    </div>
                                                </div>
                                                <span id="radio_type_error" class="error mt-2" style="margin-left:27%;"><?php echo $errors->add_agency->first('type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 <?php if (($patient->type) == 'Caregiver') { ?> <?php }else { ?>hide <?php } ?>" id="transition_aid" >
                                            <div class="col-sm-10">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="transition_aid" class="form-check-input" id="transition_aid" value="1" @if($patient->transition_aid ==1) checked @endif>
                                                        Transition Aid
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Patient Code</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="patient_code" placeholder="Patient Code" value="<?php if (isset($patient->patient_code) && $patient->patient_code != '') {
                                                                                                                            echo $patient->patient_code;
                                                                                                                        } ?>" class="form-control">

                                            <span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('patient_code'); ?></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">First Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter First Name " id="agency_name" name="first_name" value="<?php echo $patient->first_name; ?>">
                                            <span id="agency_name_error" class="error mt-2"><?php echo $errors->add_agency->first('first_name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                               
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Middle Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Middle Name " id="middle_name_id" name="middle_name" value="<?php echo $patient->middle_name; ?>">
                                            <span id="middle_name_error" class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Last Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Last Name " id="last_name_id" name="last_name" value="<?php echo $patient->last_name; ?>">
                                            <span id="last_name_error" class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Email " id="email" name="email" value="<?php echo $patient->email; ?>">
                                            <span id="email_error" class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Date of Birth<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control " autocomplete="off" placeholder="Select Date of Birth" id="dob_id" name="dob" value="<?php if ($patient->dob != '0000-00-00') {
                                                                                                                                                                                        echo ($patient->dob);
                                                                                                                                                                                    } ?>" >
                                            <span id="dob_error" class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">

                                
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">SSN</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter SSN" id="ssn" name="ssn" value="<?php echo $patient->ssn; ?>">
                                            <span id="ssn_error" class="error mt-2"><?php echo $errors->add_agency->first('ssn'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Mobile<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile" onkeypress="return isNumber(event)" name="mobile" value="<?php echo $patient->mobile; ?>" maxlength="15">
                                            <span id="mobile_error" class="error mt-2"><?php echo $errors->add_agency->first('mobile'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Phone" id="phone" onkeypress="return isNumber(event)" name="phone" value="<?php echo $patient->phone; ?>" maxlength="15">
                                            <span id="phone_error" class="error mt-2"><?php echo $errors->add_agency->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Gender<span class="error mt-2">*</span></label>
                                        <div class="col-sm-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="male" <?php if ($patient->gender == 'male') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Male <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="female" <?php if ($patient->gender == 'female') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Female<i class="input-helper"></i></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="other" <?php if ($patient->gender == 'other') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Other<i class="input-helper"></i></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-3 @if($patient->gender =='other') @else hide @endif" id="other_div_hide">
                                            <div class="form-group row">
                                                <input type="text" class="form-control" name="other_name" placeholder="Other Name" value="{{ $patient->other_gender}}">
                                                <span id="other_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_name'); ?></span>
                                            </div>
                                        </div>
                                        <span id="gender_error" class="error mt-2" style="margin-left:27%;"><?php echo $errors->add_agency->first('gender'); ?></span>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 1</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Address 1" id="address1" name="address1" value="{{ $patient['address1'] }}">
                                            <span id="address1_error" class="error mt-2"><?php echo $errors->add_record->first('address1'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Apt/Suite/Floor</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Apt/Suite/Floor" id="address2" name="address2" value="{{ $patient['address2'] }}">
                                            <span id="address2_error" class="error mt-2"><?php echo $errors->add_record->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter City" id="city" name="city" value="{{ $patient['city'] }}" maxlength="50">
                                            <span id="city_error" class="error mt-2"><?php echo $errors->add_record->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter State" id="state" name="state" value="{{ $patient['state'] }}" maxlength="50">
                                            <span id="state_error" class="error mt-2"><?php echo $errors->add_record->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code" id="zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="{{ $patient['zip_code'] }}">
                                            <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('zip_code'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">County</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="county" name="county" readonly onkeypress="return isNumber(event)" value="{{ $patient['county'] }}">
                                            <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Discipline </label>
                                        <div class="col-sm-9">
                                            <select class="js-example-basic-multiple w-100" name="diciplin" id="diciplin_id">

                                                <option value="">Select Discipline</option>

                                                @if (count($masterData) > 0)    
                                                @foreach ($masterData as $master)
                                                @if ($master->master_type_fk == 26)
                                                <option value="{{ $master->name }}" {{$patient->diciplin == $master->name ? 'selected' : '' }}>{{ $master->name }}
                                                </option>
                                                @endif
                                                @endforeach
                                                @endif

                                            </select>
                                        </div>

                                    </div>
                                </div>
                                @if(auth()->user()->agency_fk =="")
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Services<span class="error mt-2">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id" onchange="loadBranchDropdown();">

                                                    <option value="">Select Service</option>
                                                    <?php
                                                    $snsns = explode(',', $patient->service_id);
                                                    $fisnarray = array();
                                                    foreach ($snsns as $ks) {
                                                        $fisnarray[] = $ks;
                                                    }
                                                    if (count($serviceList) > 0) {
                                                        foreach ($serviceList as $ks) {
                                                            if ($ks->types == $patient->type) {
                                                    ?>
                                                                <option value="<?php echo $ks->id; ?>" <?php if (in_array($ks->id, $fisnarray)) {
                                                                                                            echo "selected='selected'";
                                                                                                        } ?>>
                                                                    <?php echo $ks->name; ?></option>
                                                    <?php }
                                                        }
                                                    } ?>
                                                </select>
                                                <span id="service_id_error" class="error mt-2"><?php echo $errors->add_agency->first('service_id'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                @else

                                @endif
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Language </label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="language" id="language_id">

                                                <option value="">Select Language</option>
                                                @foreach ($languages as $language)
                                                <option value="{{ $language->id }}" {{ $patient->language == $language->id ? 'selected' : '' }}>
                                                    {{ $language->name }}
                                                </option>
                                                @endforeach


                                            </select>
                                        </div>

                                    </div>
                                </div>
                                @if(auth()->user()->agency_fk =="")
                            <div class="col-sm-6">
                                    <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Payment Type </label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="payment_type" id="payment_type">
                                                <option value="">Select Payment Type</option>
                                                @if (count($masterData) > 0)    
                                                @foreach ($masterData as $master)
                                                @if ($master->master_type_fk == 17)
                                                <option value="{{ $master->id }}" {{$patient->payment_type == $master->id ? 'selected' : '' }}>{{ $master->name }}
                                                </option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                            <span id="payment_type_error" class="error mt-2"><?php echo $errors->add_agency->first('payment_type'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            </div>
                            <?php if ($patient->agency_id == '106') { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Payment <span class="error mt-2">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="radio" name="hamaspik_payment" <?= $patient->hamaspik_payment == 1 ? 'checked' : '' ?> value="1">
                                                Hamaspik 1
                                                <input type="radio" name="hamaspik_payment" <?= $patient->hamaspik_payment == 1 ? '' : 'checked' ?> value="2">Hamaspik 2

                                            </div>


                                        </div>
                                    </div>
                                </div>
                            <?php }

                            ?>
                            <div class="row">
                            

                                
                            </div>
                            <div class="row">
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">FU Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control  datepicker" autocomplete="off" placeholder="Enter FU Date" id="fu_date_id" name="fu_date" value="@if($patient->fu_date !=''){{ $patient->fu_date!='1969-12-31' ? date('m/d/Y',strtotime($patient->fu_date)) : '' }}@endif" readonly>
                                            <span id="fu_date_error" class="error mt-2"><?php echo $errors->add_agency->first('fu_date'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Due Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" autocomplete="off" placeholder="Select Due Date" id="due_date_id" name="due_date" value="@if($patient->due_date !=''){{ $patient->due_date !='1969-12-31' ? date('m/d/Y',strtotime($patient->due_date)) : '' }}@endif" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Insurance ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" autocomplete="off" placeholder="Enter Insurance ID"  name="insurance_id" value="{{ $patient->insurance_id }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Insurance Name</label>
                                        <div class="col-sm-9">
                                        <select class="form-control" name="insurance_name" id="insurance_name">
                                                <option value="">Select Insurance Name</option>
                                                @if (count($insuranceList) > 0)
                                                @foreach ($insuranceList as $insurance)
                                                <option value="{{ $insurance->id }}" @if($patient->insurance_name ==$insurance->id) selected @endif>{{ $insurance->insurance_name }}
                                                </option>   
                                                @endforeach
                                                @endif

                                                <option value="other" @if($patient->insurance_name =='other') selected @endif>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Notes</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" placeholder="Notes" name="message" style="height: 50px"><?php echo $patient->remarks; ?></textarea>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-6 @if($patient->insurance_name !='' && $patient->insurance_name =='other') @else hide @endif" id="other_insurance" >
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Other Insurance Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="other_insurance_name" name="other_insurance_name" class="form-control" placeholder="Enter Other Insurance Name" value="{{ $patient->other_insurance_name }}">
                                            <span id="other_insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_insurance_name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">CIN/Medicaid Number<span style="color:red" class="@if($patient->type =='Patient') @else hide @endif" id="hideShowRed">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter CIN/Medicaid Number" value="{{ $patient->cin }}">
                                            <span id="cin_error" class="error mt-2"><?php echo $errors->add_agency->first('cin'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Emergency Contact Name</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" placeholder="Enter Emergency Contact Name"  value="{{ $patient->emergency_contact_name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Emergency Contact Number</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="emergency_phone" name="emergency_phone"  onkeypress="return isNumber(event)" class="form-control" placeholder="Enter Emergency Contact Number"  value="{{ $patient->emergency_phone }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" id="location_branch_wrapper">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location / Branch</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="location_branch" name="location_branch"   class="form-control" placeholder="Enter Location / Branch" value="{{ $patient->location_branch }}">
                                        </div>
                                        <span class="error mt-2 text-danger location_branch_error" for="branch"></span>
                                    </div>
                                </div>

                                <div class="col-md-6 hide" id="branch_dropdown_wrapper">
                                    <div class="form-group row">
                                        <label for="branch" class="col-sm-3 col-form-label">Branch<span style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-sm" name="branch_id" id="patient_branch_id">
                                                <option value="">Select Branch</option>
                                            </select>
                                            <span class="error mt-2 text-danger location_branch_error" for="branch"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Medicare No</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Medicare No" id="medicare_no" name="medicare_no" value="{{ $patient->medicare_no }}" maxlength="15">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Main Content -->

    <!-- /Page Content -->
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js')}}"></script>
    <script>
        var _GET_BRANCHES_BY_AGENCY_SERVICES = "{{ url('branch-link-ajax/get-branches-by-agency-services') }}";
        var _CHECK_MANDATORY = "{{ url('branch-link-ajax/check-mandatory') }}";
        var SELECTED_BRANCH_ID = "{{ $patient->branch_id }}";
        var isBranchMandatory = false;
        $("#service_id").select2({
            placeholder: "Select Service"
        });


        function validation() {

            var temp = 0;
            var agency_ids = $('#agency_ids').val();
            var agency_name = $('#agency_name').val();
            var last_name_id = $('#last_name_id').val();
            var dob_id = $('#dob_id').val();
            var phone = $('#phone').val();
            var mobile = $('#mobile').val();
            var service_id = $('#service_id').val();
            var gender = $('input[name="gender"]').is(":checked");
            var payment_type = $('#payment_type').val();
            var type = $('input[name="type"]:checked').val();
            var cin = $('#cin').val();
            var other_name = $('input[name="other_name"]').val();

            $("#agency_name_error").html("");
            $("#email_error").html("");
            $("#phone_error").html("");
            $("#mobile_error").html("");
            $("#dob_error").html("");
            $("#last_name_error").html("");
            $("#address2_error").html("");
            $("#gender_error").html("");
            $("#service_id_error").html("");
            $("#payment_type_error").html("");
            $("#agency_error").html("");

            $('#radio_type_error').html("");
            $("#cin_error").html("");
            $(".location_branch_error").html("");

            if ($('input[name="type"]').is(':checked') == false) {
                $('#radio_type_error').html("Please select Type");
                temp++;
            }
            <?php if ($user->agency_fk == '') { ?>
                if (agency_ids == "") {
                    $('#agency_error').html("Required");
                    temp++;
                }
            <?php } ?>
            if (agency_name.trim() == "") {
                $('#agency_name_error').html("Please enter First Name");
                temp++;
            }
            if (last_name_id.trim() == "") {
                $('#last_name_error').html("Please enter Last Name");
                temp++;
            }

            if (dob_id == '') {
                $('#dob_error').html("Please select Date of Birth");
                temp++;
            }
            if (phone != "" && !notStartWithZero(phone)) {
                $('#phone_error').html("Please enter valid Phone");
            }
            if (mobile == "") {
                $('#mobile_error').html("Please enter Mobile");
                temp++;
            }
            <?php if ($user->agency_fk == '') { ?>
                if (service_id == "") {
                    $('#service_id_error').html("Please select Service");
                    temp++;
                }
            <?php }?>
            if (mobile != "" && mobile.length < 10 || mobile.length > 15) {
                $('#mobile_error').html("Mobile should between 10 to 15 digit");
                temp++;
            }
           
            if (gender == false) {
                $('#address2_error').html("Please select Gender");
                temp++;
            }else{
                if($('input[name="gender"]:checked').val().trim() =='other'){
                    if(other_name.trim() ==''){
                        $('#other_name_error').html("Other Name is required");
                        temp++;
                    }
                }
            }

            if(type =='Patient'){
                if (cin.trim() == "") {
                    $('#cin_error').html("Please enter CIN/Medicaid Number");
                    temp++;
                }
            }
            
            if (isBranchMandatory && !$('#branch_dropdown_wrapper').hasClass('hide')) {
                let branchVal = $('#patient_branch_id').val();
                if (!branchVal) {
                    $('.location_branch_error').html('Branch selection is mandatory for this agency and service combination');
                    temp++;
                }
            }

            if (temp == 0) {
                $("#insertButton").prop('disabled', true);
                return true;

            } else {
                return false;
            }
        }

        function isLatter(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (!(charCode >= 65 && charCode <= 120) && (charCode != 32 && charCode != 0)) {
                return false;
            }
            return true;
        }

        function isNumber(evt) {

            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

                return false;
            }
            return true;
        }

        function getCountyByZipCode(val) {

            $.ajax({
                async: false,
                global: false,
                url: "{{ url('get-county')}}",
                type: "post",
                data: {
                    zip_code: val,
                    _token: '<?php echo csrf_token(); ?>'
                },
                success: function(response) {

                    if (response != "County not found") {
                        $('#county').val(response);
                    } else {
                        $('#county').val('');
                    }
                }
            });
        }
    </script>
    <!-- Date Picker -->


    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script>
        $(".bill_date").datepicker({
            maxDate: 0
        });
        $("#due_date_id").datepicker({
            minDate : 0
        });

        $("#fu_date_id").datepicker({
            minDate : 0
        });
        <?php
        $sears = $patient->service_id;
        $sears = explode(',', $sears);
        foreach ($sears as $vsl) {
        }

        ?>

        function getResponse(id) {
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('patient/services-list')}}",
                data: {
                    'type': $('input[name="type"]:checked').val(),
                    'agency_id':$('#agency_ids').val()
                },
                success: function(response) {
                    var response = JSON.parse(response);
                    var htmlsresp = '';
                    $('#service_id').html("");
                    if (response.length != 0) {
                        htmlsresp += '<option value="">Select Service</option>';
                        $.each(response, function(i, v) {

                            htmlsresp += '<option value="' + v.id + '" >' + v.name + '</option>';

                        });

                    } else {
                        htmlsresp += '<option value="">No record available</option>';
                    }

                    $('#service_id').html(htmlsresp);


                }

            })

            $('#hideShowRed').addClass('hide');
            if(id =='Patient'){
                $('#hideShowRed').removeClass('hide');
            }

            $('#transition_aid').addClass('hide');
            if(id =='Caregiver'){
                $('#transition_aid').removeClass('hide');
            }
        }
        $('#agency_ids').change(function(e){
            getResponse()
        })

        $('#insurance_name').change(function(e){
            var insurance_name = $('#insurance_name').val();
            $('#other_insurance').addClass('hide');
            $('#other_insurance_name_error').html("");
            if(insurance_name =='other'){
                $('#other_insurance').removeClass('hide');
            }
        })

        $('input[name="gender"]').click(function(e){
            var name = $('input[name="gender"]:checked').val();
            $('#other_div_hide').addClass('hide');
            if(name.trim() =='other'){
                $('#other_div_hide').removeClass('hide');
            }
        })

        loadBranchDropdown();
    
        function loadBranchDropdown(){
            var agencyId = $('#agency_ids').val();
            var serviceIds = $('#service_id').val();

            if(!agencyId || !serviceIds || serviceIds.length === 0){
                resetBranchDropdown();
                return;
            }

            if(typeof _GET_BRANCHES_BY_AGENCY_SERVICES === 'undefined'){
                return;
            }

            $.ajax({
                type: "GET",
                url: _GET_BRANCHES_BY_AGENCY_SERVICES,
                data: {
                    'agency_id': agencyId,
                    'service_ids': serviceIds
                },
                success: function(res){
                    if(res.status && res.data && res.data.length > 0){
                        var html = '<option value="">Select Branch</option>';
                        $.each(res.data, function(i, branch){
                            html += '<option value="'+ branch.branch_id +'">'+ branch.branch_name +'</option>';
                        });
                        $('#patient_branch_id').html(html);
                        $('#branch_dropdown_wrapper').removeClass('hide');
                        $('#location_branch_wrapper').addClass('hide');
                        $('#location_branch').val('');
                        $('#patient_branch_id').val(SELECTED_BRANCH_ID);
                        checkBranchMandatory(agencyId, serviceIds);
                    } else {
                        resetBranchDropdown();
                    }
                },
                error: function(){
                    resetBranchDropdown();
                }
            });
        }

        function checkBranchMandatory(agencyId, serviceIds){
            isBranchMandatory = false;
            $.ajax({
                type: "GET",
                url: _CHECK_MANDATORY,
                data: {
                    'agency_id': agencyId,
                    'service_ids': serviceIds
                },
                success: function(res){
                    if(res.status && res.data == 1){
                        isBranchMandatory = true;
                    }
                }
            });
        }

        function resetBranchDropdown(){
            $('#patient_branch_id').html('<option value="">Select Branch</option>');
            $('#branch_dropdown_wrapper').addClass('hide');
            $('#location_branch_wrapper').removeClass('hide');
            isBranchMandatory = false;
        }

        function allowSpecialCharacters(e) {
            const key = e.key;

            // ✅ Allow control keys
            const allowedKeys = [
                "Backspace", "Delete", "Tab", "Escape", "Enter",
                "ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown",
                "Home", "End"
            ];

            if (allowedKeys.includes(key)) {
                return; // allow editing/navigation
            }

            // ✅ Allow Ctrl/Cmd shortcuts (copy, paste, select all)
            if (e.ctrlKey || e.metaKey) {
                return;
            }

            // ✅ Allow letters, space, hyphen
            if (!/^[a-zA-Z -]$/.test(key)) {
                e.preventDefault();
            }
        }

        $('#agency_name, #last_name_id, #middle_name_id').on('keydown', allowSpecialCharacters);
        $('#agency_name, #last_name_id, #middle_name_id').on('input', function () {
            this.value = this.value.replace(/[^a-zA-Z -]/g, '');
        });
    </script>


    <!-- End Date Picker -->
    @include('include/footer')