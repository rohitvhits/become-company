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

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .hhaLoader {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgb(255 255 255 / 50%);
        z-index: 99;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .main-panel {
        position: relative;
    }

    .hide {
        display: none;
    }

    .hha-btn-wrapper {
        display: flex;
        align-items: center;
    }
</style>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<div class="main-panel">
<div class="col-12 loader-calender hhaLoader hide" id="load-caregiver-demographics">
                <img src="{{ asset('/ajax-loader.gif')}}"alt="loader" id="loader-patient-demographic-details" style="">
            </div>
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Create New Ny Best Medicals Appointment</h5>
        </div>
        <div class="col-12 grid-margin-top">
            @if (Session::has('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
        </div>
        <div class="row">
          
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='<?php echo URL::to('/patient/save'); ?>' name="adduser" method="post" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <input type="hidden" name="caregiver_id" id="cid">
                                <?php 
                                    if(isset($_GET['debug']) && $_GET['debug']==1){
                                        echo "<pre>";print_R($agencyList);die();
                                    }
                                ?>
                                <?php if ($user->agency_fk == '') { 
                                    $flag = 0;
                                    ?>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="" class="col-sm-3 col-form-label">Agency<span style="color:red">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="agency_id" class="form-control" id="agency_ids">
                                                    <option value="">Select Agency</option>
                                                    
                                                    <?php if (count($agencyList) > 0) {
                                                        foreach ($agencyList as $vsl) {
                                                            $flag = 0;
                                                            if($vsl->app_name !=""){
                                                                $flag = 1;
                                                            }
                                                    ?>
                                                            <option data-app-name="{{ $flag}}" value="<?php echo $vsl->id; ?>" <?php if (old('agency_id') == $vsl->id) {
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
                                                                <option value="{{$agn['id']}}" data-app-name="@if($agn['app_name'] !='') 1 @else '' @endif">{{$agn['agency_name']}}</option>
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
                                        <div class="col-md-8">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Type<span class="error mt-2">*</span></label>
                                                <div class="col-sm-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="type" class="form-check-input" id="msp" value="Caregiver" onclick="getResponse('Caregiver')" <?php if (old('type') == 'Caregiver') {
                                                                                                                                                                                        echo "checked='checked'";
                                                                                                                                                                                    } ?>>
                                                            Caregiver
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="type" class="form-check-input" id="msp" value="Patient" onclick="getResponse('Patient')" <?php if (old('type') == 'Patient') {
                                                                                                                                                                                    echo "checked='checked'";
                                                                                                                                                                                } ?>>
                                                            Patient
                                                        </label>
                                                    </div>
                                                </div>
                                                <span id="radio_type_error" class="error mt-2" style="margin-left:27%;"><?php echo $errors->add_agency->first('type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 hide" id="transition_aid" >
                                           
                                            <div class="col-sm-10">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="transition_aid" class="form-check-input" id="transition_aid" value="1" >
                                                        Transition Aid
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div>
                            </div>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Patient Code</label>

                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-11">
                                                    <div>
                                                        <input type="text" name="patient_code" value="" class="form-control" placeholder="Patient Code">

                                                        <span id="patient_code_error" class="error mt-2"><?php echo $errors->add_agency->first('patient_code'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 @if( $flag !='1') hide @else  @endif" id="agency_hha_enabled">
                                                    <a onclick="getHHADetails()" title="SYNC HHA"><i class="fa fa-exchange" aria-hidden="true"></i></a>

                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">First Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter First Name " id="agency_name" name="first_name" value="<?php echo old('first_name'); ?>">
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
                                            <input type="text" class="form-control charCls" placeholder="Enter Middle Name " id="middle_name_id" name="middle_name" value="<?php echo old('middle_name'); ?>">
                                            <span id="middle_name_error" class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Last Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter Last Name " id="last_name_id" name="last_name" value="<?php echo old('last_name'); ?>">
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
                                            <input type="text" class="form-control" placeholder="Enter Email " id="email" name="email" value="<?php echo old('email'); ?>">
                                            <span id="email_error" class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">SSN</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter SSN" id="ssn" name="ssn" value="<?php echo old('ssn'); ?>">
                                            <span id="ssn_error" class="error mt-2"><?php echo $errors->add_agency->first('ssn'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Date of Birth<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control bill_date " autocomplete="off" placeholder="Select  Date of Birth" id="dob_id" name="dob" value="<?php echo old('dob'); ?>">
                                            <span id="dob_error" class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Mobile<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile" onkeypress="return isNumber(event)" name="mobile" value="<?php echo old('mobile'); ?>" maxlength="15">
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
                                            <input type="text" class="form-control" placeholder="Enter Phone" id="phone" onkeypress="return isNumber(event)" name="phone" value="<?php echo old('phone'); ?>" maxlength="15">
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
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="male" <?php if (old('gender') == 'male') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Male <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="female" <?php if (old('gender') == 'female') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Female<i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp" name="gender" value="other" <?php if (old('gender') == 'other') {
                                                                                                                                            echo "checked='checked'";
                                                                                                                                        } ?>> Other<i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 hide" id="other_div_hide">
                                            <div class="form-group row">
                                                <input type="text" class="form-control" name="other_name" placeholder="Other Name">
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
                                            <input type="text" class="form-control" placeholder="Enter Address 1" id="address1" name="address1" value="<?php echo old('address1'); ?>">
                                            <span id="address1_error" class="error mt-2"><?php echo $errors->add_record->first('address1'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Apt/Suite/Floor</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Apt/Suite/Floor" id="address2" name="address2" value="<?php echo old('address2'); ?>">
                                            <span id="address2_error" class="error mt-2"><?php echo $errors->add_record->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter State" id="state" name="state" value="<?php echo old('state'); ?>" maxlength="50">
                                            <span id="state_error" class="error mt-2"><?php echo $errors->add_record->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter City" id="city" name="city" value="<?php echo old('city'); ?>" maxlength="50">
                                            <span id="city_error" class="error mt-2"><?php echo $errors->add_record->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code" id="zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="<?php echo old('zip_code'); ?>">
                                            <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('zip_code'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Country</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="county" name="county" readonly onkeypress="return isNumber(event)" value="<?php echo old('county'); ?>">
                                            <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Discipline <span id="dus_id" class="error" style="display:none">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="diciplin" id="diciplin_id">

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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Services<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                                <option value="">Select Service</option>
                                            </select>
                                            <span id="service_id_error" class="error mt-2"><?php echo $errors->add_agency->first('service_id'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($user->agency_fk == '106') { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Payment<span class="error mt-2">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="radio" name="hamaspik_payment" value="1"> Hamaspik 1
                                                <input type="radio" checked="checked" name="hamaspik_payment" value="2">Hamaspik 2
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Language</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="language" id="language_id">
                                                <option value="">Select Language</option>
                                                @foreach ($languages as $language)
                                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Payment Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="payment_type" id="payment_type">
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
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">FU Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select FU Date" id="fu_date_id" name="fu_date" value="<?php echo old('fu_date'); ?>" readonly>
                                            <span id="fu_date_error" class="error mt-2"><?php echo $errors->add_agency->first('fu_date'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Due Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" autocomplete="off" placeholder="Select Due Date" id="due_date_id" name="due_date" value="<?php echo old('due_date'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Insurance ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" autocomplete="off" placeholder="Enter Insurance ID" name="insurance_id" value="<?php echo old('insurance_id'); ?>">
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
                                                <option value="{{ $insurance->id }}">{{ $insurance->insurance_name }}
                                                </option>   
                                                @endforeach
                                                @endif
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Notes</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" placeholder="Notes" name="message" style="height: 50px"><?php echo old('message'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 hide" id="other_insurance" >
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Other Insurance Name<span class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="other_insurance_name" name="other_insurance_name" class="form-control" placeholder="Enter Other Insurance Name">
                                            <span id="other_insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_insurance_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">CIN/Medicaid Number<span style="color:red" class="hide" id="hideShowRed">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter CIN/Medicaid Number">
                                            <span id="cin_error" class="error mt-2"><?php echo $errors->add_agency->first('cin'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Emergency Contact Name</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" placeholder="Enter Emergency Contact Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Emergency Contact Number</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="emergency_phone" name="emergency_phone"  onkeypress="return isNumber(event)" class="form-control" placeholder="Enter Emergency Contact Number">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location / Branch</label>
                                        <div class="col-sm-9">
                                        <input type="text" id="location_branch" name="location_branch"   class="form-control" placeholder="Enter Location / Branch">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Medicare No</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Medicare No" id="medicare_no" onkeypress="return isNumber(event)" name="medicare_no" value="<?php echo old('medicare_no'); ?>" maxlength="15">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary mr-2" id="insertButton">Save</button>
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
        $('input[name="type"]').click(function(e) {
            var ctype = $('input[name="type"]:checked').val();
            $('#dus_id').attr('style', 'display:none');
            if (ctype == 'Caregiver') {
                $('#dus_id').attr('style', '');
            }

        });
        $("#service_id").select2({
            placeholder: "Select Service"
        });

        function validation() {

            var temp = 0;

            var agency_ids = $('#agency_ids').val();
            var agency_name = $('#agency_name').val();
            var last_name_id = $('#last_name_id').val();
            var phone = $('#phone').val();
            var mobile = $('#mobile').val();
            var service_id = $('#service_id').val();
            var gender = $('input[name="gender"]').is(":checked");
            var dob_id = $('#dob_id').val();
            var type = $('input[name="type"]:checked').val();
            var diciplin_id = $('#diciplin_id').val();
            var payment_type = $('#payment_type').val();
            var insurance_name = $('#insurance_name').val();
            var other_insurance_name = $('#other_insurance_name').val();
            var cin = $('#cin').val();
            var other_name = $('input[name="other_name"]').val();

            $("#agency_name_error").html("");
            $("#agency_error").html("");
            $("#email_error").html("");
            $("#phone_error").html("");
            $("#address2_error").html("");
            $("#gender_error").html("");
            $("#last_name_error").html("");
            $("#service_id_error").html("");
            $('#payment_type_error').html("");
            $("#mobile_error").html("");
            $("#dob_error").html("");
            $("#displine_error").html("");
            $('#radio_type_error').html("");
            $('#other_insurance_name_error').html("");
            $("#cin_error").html("");
            $("#other_name_error").html("");

            if ($('input[name="type"]').is(':checked') == false) {
                $('#radio_type_error').html("Please select Type");
                temp++;
            }
            <?php if ($user->agency_fk == '') { ?>
                if (agency_ids == "") {
                    $('#agency_error').html("Please select Agency");
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
            } else if (!notStartWithZero(mobile)) {
                $('#mobile_error').html("Please enter valid Mobile");
            }

            // if(payment_type ==''){
            //     $('#payment_type_error').html("Please select Payment Type");
            //     temp++;
            // }

            if (service_id == "") {
                $('#service_id_error').html("Please select Service");
                temp++;
            }
            if (gender == false) {
                $('#gender_error').html("Please select Gender");
                temp++;
            }else{
                if($('input[name="gender"]:checked').val().trim() =='other'){
                    if(other_name.trim() ==''){
                        $('#other_name_error').html("Other Name is required");
                        temp++;
                    }
                }
            }
            if (type == 'Caregiver') {
                if (diciplin_id == '') {
                    $("#displine_error").html("Please select Discipline");
                    temp++;
                }

            }

            if (insurance_name != '') {
                if (insurance_name == 'other') {
                    if(other_insurance_name.trim() ==''){
                        $("#other_insurance_name_error").html("Please enter Other Insurance Name");
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
    <link href="{{ asset('css/jquery-ui.css')}}">
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script>
        $("#bill_date").datepicker();
        $("#due_date_id").datepicker({
            minDate: 0
        });
        $("#fu_date_id").datepicker({
            minDate: 0
        });
        // $('.datepicker').datepicker({
        //     maxDate: 0
        // });

        <?php if (old('type') != '') { ?>
            var name = $('input[name="type"]:checked').val();

            getResponse(name);


        <?php } ?>


        function getResponse(id) {
            if (id != '') {
                var jsonencode = <?php echo json_encode(old('service_id')); ?>;
                $.ajax({
                    async: false,
                    global: false,
                    type: "GET",
                    url: "{{ url('ajax-service')}}",
                    data: {
                        "id": $('input[name="type"]:checked').val(),
                        "jsonencode": jsonencode,
                        'agency_id': $('#agency_ids').val()
                    },
                    success: function(res) {
                        if (res != '') {
                            htmlsresp = res;
                        } else {
                            htmlsresp += '<option value="">No record available</option>';
                        }
                        $('#service_id').html(htmlsresp);
                    }
                })
            }

            $('#hideShowRed').addClass('hide');
            if(id =='Patient'){
                $('#hideShowRed').removeClass('hide');
            }
            $('#transition_aid').addClass('hide');
            if(id =='Caregiver'){
                $('#transition_aid').removeClass('hide');
            }
        }
        $('#agency_ids').change(function(e) {
            var agency_ids = $('#agency_ids option:selected').attr('data-app-name');
           
            $('#agency_hha_enabled').removeClass('hide');

            if(agency_ids ==1){
                getResponse()
            }else{
                console.log("dsadasd");
                $('#agency_hha_enabled').addClass('hide');
            }
           
        })

        function getHHADetails() {
            $('#load-caregiver-demographics').removeClass('hide');
            var type = $('input[name="type"]').is(":checked");
            var patient_code = $('input[name="patient_code"]').val();
            var agency_ids = $('select[name="agency_id"]').val();
            var cnt = 0;
            $('#radio_type_error').html("");
            $('#patient_code_error').html("");
            $('#agency_error').html("");
            if (type == false) {
                $('#radio_type_error').html("Please select Type");
                cnt++;
            }
            if (type == true) {
                var type = $('input[name="type"]:checked').val();
            }
            if (patient_code.trim() == '') {
                $('#patient_code_error').html("Please select Type");
                cnt++;
            }

            if (agency_ids == '') {
                $('#agency_error').html("Please select Agency");
                cnt++;
            }

            if (cnt != 0) {
                $('#load-caregiver-demographics').addClass('hide');
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    type: "GET",
                    url: "{{ url('hha-patient-caregiver-details')}}",
                    data: {
                        "agency_id": agency_ids,
                        "type": type,
                        'patient_code': patient_code
                    },
                    success: function(res) {
                        setTimeout(()=>{
                            $('#load-caregiver-demographics').addClass('hide');
                        },5000)
                       
                        var json = res.data;
console.log(json)
                        if (json.length != 0) {
                            if (type == 'Caregiver') {
                                $('#cid').val(json[0].caregiver_id);
                            } else {
                                $('#cid').val(json[0].PatientID);
                            }
                            if (type == 'Caregiver') {
                                var fName = json[0].first_name;
                                var middle_name = json[0].middle_name;
                                var last_name = json[0].last_name;
                                var dob = json[0].dob;
                                var mobile_or_sms = json[0].mobile_or_sms;
                                var HomePhone = json[0].HomePhone;
                                var State = json[0].State;
                                var City = json[0].City;
                                var Zip5 = json[0].Zip5;
                                var ssn = json[0].ssn;
                                var emergencyName = json[0].emergencyName;
                                var emergencyPhone1 = json[0].emergencyPhone1;
                            }else{
                                var fName = json[0].firstName;
                                var middle_name = json[0].middleName;
                                var last_name = json[0].lastName;
                                var dob = json[0].dob;
                                var mobile_or_sms = json[0].home_phone;
                                var HomePhone = json[0].phone2;
                                var State = json[0].state;
                                var City = json[0].city;
                                var Zip5 = json[0].zip5;
                                var ssn = json[0].ssn;
                            }
                            $('input[name="first_name"]').val(fName)
                            $('input[name="middle_name"]').val(middle_name)
                            $('input[name="last_name"]').val(last_name)
                            $('input[name="dob"]').val(dob)
                            $('input[name="mobile"]').val(mobile_or_sms)
                            $('input[name="phone"]').val(HomePhone)
                            $('input[value="' + json[0].gender.toLowerCase() + '"]').prop("checked", true)
                            $('input[name="address1"]').val(json[0].address1)
                            $('input[name="address2"]').val(json[0].address2)
                            $('input[name="state"]').val(State)
                            $('input[name="city"]').val(City)
                            $('input[name="zip_code"]').val(Zip5);
                            $('input[name="ssn"]').val(ssn);
                            

                            $('#language_id option').filter(function() {
                                return $(this).text() === json[0].language;
                            }).prop('selected', true);
                           
                            $('input[name="cin"]').val(json[0].medicaid_number);
                           
                            if (json[0].discipline) {
                                $('select[name="diciplin"]').val(json[0].discipline);
                            }
                            if (json[0].medicaid_number) {
                                $('input[name="insurance_id"]').val(json[0].medicaid_number);
                            }

                            
                            $('input[name="emergency_contact_name"]').val(json[0].emergencyName);
                            $('input[name="emergency_phone"]').val(json[0].emergencyPhone1);
                            if (Zip5 != '') {
                                getCountyByZipCode(Zip5)
                            }
                        }
                    }
                })
            }
        }
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
    </script>
    <!-- End Date Picker -->
    @include('include/footer')