<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Required meta tags -->

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NY BEST MEDICAL</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.eot')}}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.ttf')}}">

    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.woff">
    <link href="{{ asset('/assets/css/vertical-layout-light/jquery-ui.css')}}" rel="stylesheet">
    <!-- base:css -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/mdi/css/materialdesignicons.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/css/vendor.bundle.base.css')}}">

    <!-- endinject -->

    <!-- plugin css for this page -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/jqvmap/jqvmap.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/css/horizontal-default-light/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/sweetalert2.min.css')}}">
    <link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- endinject -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
    <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />

    <link href="{{ asset('assets/vendors/select2/select2.min.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link href="<?= URL::to('assets/css/jquery-confirm.min.css') ?>" rel="stylesheet" />

    <style>
        .compact-view .form-control {
            padding: 0 !important;
            height: 24px;
        }

        .compact-view td {
            padding: 5px 10px;
        }

        .horizontal-menu .top-navbar {
            font-weight: 400;
            background: #1e1e2f;
            border-bottom: 1px solid #030303;
        }

        .horizontal-menu .top-navbar .navbar-menu-wrapper {
            color: #b1b1b5;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link .menu-title,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link .menu-title {
            color: #97C229 !important;
        }

        .horizontal-menu .bottom-navbar {
            background: #FFF;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item>.nav-link {
            color: #686868;
        }

        .agency-logo {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .agency-logo a {
            padding: 0 10px !important;
        }

        .text-danger {
            color: red !important;
        }

        .hide {
            display: none;
        }

        .select2-container--default .select2-selection--single {
            height: 39px;
        }
        @media (max-width: 991px) {

            .mobileView {
                padding-top: 60px;
            }
            .select2.select2-container{
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="sidebar-toggle-display sidebar-hidden">



    <!--Header-part-->

    <div class="container-scroller">

        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">
                <!-- <div class="container"></div> -->
                <div class="container-fluid">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo" href="javascript:void(0)"><img
                                src="<?= URL::to('img/logo-ny.png') ?>"></a>
                        <a class="navbar-brand brand-logo-mini" href="javascript:void(0)"><img
                                src="<?= URL::to('img/favicon.png') ?>"></a>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                            data-toggle="horizontal-menu-toggle">
                            <span class="mdi mdi-menu"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
        <!-- partial -->
         <div class="mobileView">
         <div class="container-fluid page-body-wrapper">
            <!-- partial -->
            <div class="">
                <div class="content-wrapper">
                    <h2 class="card-title">Demographic Details</h2>

                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <form class="form-sample" action='<?php echo URL::to('patient-update-with-sms/' . sha1($patient->id)); ?>' id="form-submit" name="adduser"  method="post">
                            @csrf    
                            <input type="hidden" name="id" value="{{ $id}}"> 
                            <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="col-form-label">Agency<span style="color:red">*</span></label>

                                                <select name="agency_id" class="form-control" id="agency_ids" disabled>
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

                                                <span class="error mt-2" id="agency_name_error" for="file_name"></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Type<span style="color:red">*</span></label>

                                                <input type="text" name="patient_type" id="patient_type" placeholder="Type" readonly value="<?php if (isset($patient->type) && $patient->type != '') {
                                                                                                                                                        echo $patient->type;
                                                                                                                                                    } ?>" class="form-control">
                                                <span id="patient_type_error" class="error mt-2"> </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Code</label>

                                                <input type="text" name="patient_code" placeholder="Code" id="patient_code" <?php if (isset($patient->patient_code) && $patient->patient_code != '') {?>
                                                                                                                                                       readonly
                                                                                                                                                    <?php } ?> value="<?php if (isset($patient->patient_code) && $patient->patient_code != '') {
                                                                                                                                                        echo $patient->patient_code;
                                                                                                                                                    } ?>" class="form-control"> <span id="patient_code_error" class="error mt-2">
                                                    <?php echo $errors->add_agency->first('patient_code'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">First Name<span style="color:red">*</span></label>

                                                <input type="text" class="form-control charCls" placeholder="Enter First Name " id="first_name" name="first_name" value="<?php echo $patient->first_name; ?>">
                                                <span id="first_name_error" class="error mt-2"><?php echo $errors->add_agency->first('first_name'); ?></span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Middle Name</label>

                                                <input type="text" class="form-control charCls" placeholder="Enter Middle Name " id="middle_name_id" name="middle_name" value="<?php echo $patient->middle_name; ?>">
                                                <span id="middle_name_error" class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Last Name<span style="color:red">*</span></label>

                                                <input type="text" class="form-control charCls" placeholder="Enter Last Name " id="last_name_id" name="last_name" value="<?php echo $patient->last_name; ?>">
                                                <span id="last_name_error" class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Email</label>

                                                <input type="text" class="form-control" placeholder="Enter Email " id="email" name="email" value="<?php echo $patient->email; ?>">
                                                <span id="email_error" class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Date of Birth<span style="color:red">*</span></label>

                                                <input type="date" class="form-control " autocomplete="off" placeholder="Select Date of Birth" id="dob_id" name="dob" value="<?php if ($patient->dob != '0000-00-00') {
                                                                                                                                                                                    echo ($patient->dob);
                                                                                                                                                                                } ?>">
                                                <span id="dob_error" class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">SSN</label>

                                                <input type="text" class="form-control" placeholder="Enter SSN" id="ssn" name="ssn" value="<?php echo $patient->ssn; ?>">
                                                <span id="ssn_error" class="error mt-2"><?php echo $errors->add_agency->first('ssn'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Mobile<span style="color:red">*</span></label>

                                                <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile" onkeypress="return isNumber(event)" name="mobile" value="<?php echo $patient->mobile; ?>" maxlength="15">
                                                <span id="mobile_error" class="error mt-2"><?php echo $errors->add_agency->first('mobile'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Phone</label>

                                                <input type="text" class="form-control" placeholder="Enter Phone" id="phone" onkeypress="return isNumber(event)" name="phone" value="<?php echo $patient->phone; ?>" maxlength="15">
                                                <span id="phone_error" class="error mt-2"><?php echo $errors->add_agency->first('phone'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Gender<span style="color:red">*</span></label>
                                                <div class="col-sm-9 row">
                                                    <div class="form-check">
                                                        <label class="form-check-label mr-3">
                                                            <input type="radio" class="form-check-input" id="msp" name="gender" value="male" <?php if ($patient->gender == 'male') {
                                                                                                                                                    echo "checked='checked'";
                                                                                                                                                } ?>> Male <i class="input-helper"></i></label>
                                                    </div>
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" class="form-check-input" id="msp" name="gender" value="female" <?php if ($patient->gender == 'female') {
                                                                                                                                                    echo "checked='checked'";
                                                                                                                                                } ?>> Female<i class="input-helper"></i></label>
                                                    </div>
                                                </div>

                                                <span id="gender_error" class="error mt-2" style="margin-left:27%;"><?php echo $errors->add_agency->first('gender'); ?></span>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Address <span style="color:red">*</span></label>

                                                <input type="text" class="form-control" placeholder="Enter Address 1" id="address1" name="address1" value="@if(isset($patient->address1) && $patient->address1 !='') {{$patient->address1 }} @else {{ old('address1') }} @endif">
                                                <span id="address1_error" class="error mt-2"><?php echo $errors->add_record->first('address1'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Apt/Suite/Floor</label>

                                                <input type="text" class="form-control" placeholder="Enter Apt/Suite/Floor" id="address2" name="address2" value="@if(isset($patient->address2) && $patient->address2 !='') {{$patient->address2 }} @else {{ old('address2') }} @endif">
                                                <span id="address2_error" class="error mt-2"><?php echo $errors->add_record->first('address2'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">State<span style="color:red">*</span></label>

                                                <input type="text" class="form-control charCls" placeholder="Enter State" id="state" name="state" value="@if(isset($patient->state) && $patient->state !='') {{$patient->state }} @else {{ old('state') }} @endif" maxlength="50">
                                                <span id="state_error" class="error mt-2"><?php echo $errors->add_record->first('state'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">City<span style="color:red">*</span></label>

                                                <input type="text" class="form-control charCls" placeholder="Enter City" id="city" name="city" value="@if(isset($patient->city) && $patient->city !='') {{$patient->city }} @else {{ old('city') }} @endif" maxlength="50">
                                                <span id="city_error" class="error mt-2"><?php echo $errors->add_record->first('city'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Zip Code<span style="color:red">*</span></label>

                                                <input type="text" class="form-control" placeholder="Enter Zip Code" id="zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="@if(isset($patient->zip_code) && $patient->zip_code !='') {{$patient->zip_code }} @else {{ old('zip_code') }} @endif">
                                                <span id="zip_code_error" class="error mt-2"><?php echo $errors->add_record->first('zip_code'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">County</label>

                                                <input type="text" class="form-control" id="county" name="county" readonly onkeypress="return isNumber(event)" value="@if($patient['county'] !=''){{ $patient['county'] }} @else {{ old('county')}} @endif">
                                                <span id="country_error" class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>

                                            </div>
                                        </div>
                                        @if(isset($patient->language) && $patient->language !='')
                                            @php
                                                $languageIds = $patient->language
                                            @endphp
                                        @else
                                        @php
                                                $languageIds = old('language')
                                            @endphp
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Language</label>
                                            
                                                <select class="form-control form-control-sm" name="language" id="language_id">
                                                    <option value="">Select Language</option>
                                                    @foreach ($languages as $language)
                                                    <option value="{{ $language->id }}" @if($languageIds ==$language->id) selected @endif>{{ $language->name }}</option>
                                                    @endforeach
                                                </select>
                                            
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Insurance ID</label>

                                                <input type="text" id="insurance_id" class="form-control" autocomplete="off" placeholder="Enter Insurance ID" name="insurance_id" value="@if(isset($patient->insurance_id) && $patient->insurance_id !='') {{ $patient->insurance_id }} @else {{ old('insurance_id')}} @endif">
                                                <span id="insurance_id_error" class="error mt-2"><?php echo $errors->add_record->first('insurance_id'); ?></span>

                                            </div>
                                        </div>
                                        <?php 
            if(isset($patient->insurance_name) && $patient->insurance_name !=""){
                $insurance_name= $patient->insurance_name;
            }else{
                $insurance_name= old('insurance_name');
            }
                                        ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Insurance Name</label>

                                                <select class="form-control" name="insurance_name" id="insurance_name">
                                                    <option value="">Select Insurance Name</option>
                                                    @if (count($insuranceList) > 0)
                                                    @foreach ($insuranceList as $insurance)
                                                    <option value="{{ $insurance->id }}" <?php if ($insurance_name == $insurance->id) {
                                                                                                echo "selected='selected'";
                                                                                            } ?>>{{ $insurance->insurance_name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                    <option value="other" <?php if ($insurance_name == 'other') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Other</option>
                                                </select>
                                                <span id="insurance_name_error" class="error mt-2"><?php echo $errors->add_record->first('insurance_name'); ?></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">CIN/Medicaid Number<span style="color:red" class="@if($patient->type =='Patient') @else hide @endif" id="hideShowRed"></span></label>
                                                <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter CIN/Medicaid Number" value="@if(isset($patient->cin) && $patient->cin !='') {{ $patient->cin }} @else {{ old('cin')}} @endif">
                                                <span id="cin_error" class="error mt-2"><?php echo $errors->add_agency->first('cin'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Emergency Contact Name</label>

                                                <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" placeholder="Enter Emergency Contact Name" value="@if(isset($patient->emergency_contact_name) && $patient->emergency_contact_name !='') {{ $patient->emergency_contact_name }} @else {{ old('emergency_contact_name') }} @endif">
                                                <span id="emergency_contact_name_error" class="error mt-2"><?php echo $errors->add_record->first('emergency_contact_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Emergency Contact Number</label>

                                                <input type="text" id="emergency_phone" name="emergency_phone" onkeypress="return isNumber(event)" class="form-control" placeholder="Enter Emergency Contact Number" value="@if(isset($patient->emergency_phone) && $patient->emergency_phone !=''){{ $patient->emergency_phone }} @else {{ old('emergency_phone')}} @endif">
                                                <span id="emergency_phone_error" class="error mt-2"><?php echo $errors->add_record->first('emergency_phone'); ?></span>


                                            </div>
                                        </div>
                                       
                                    </div>

                                    <div class="row">

                                        
                                        

                                        <div class="col-md-6 <?php if ($insurance_name == 'other') { ?><?php  } else { ?>hide <?php } ?>" id="other_insurance">
                                            <div class="form-group">
                                                <label class="col-form-label">Other Insurance Name</label>

                                                <input type="text" id="other_insurance_name" name="other_insurance_name" class="form-control" placeholder="Enter Other Insurance Name" value="@if(isset($patient->other_insurance_name) && $patient->other_insurance_name !=''){{ $patient->other_insurance_name }}@else {{ old('other_insurance_name')}} @endif">
                                                <span id="other_insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_insurance_name'); ?></span>

                                            </div>
                                        </div>
                                        <?php if ($patient->agency_id == '106') { ?>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label">Payment<span style="color:red">*</span><span class="error mt-2">*</span></label>

                                                    <input type="radio" name="hamaspik_payment" <?= $patient->hamaspik_payment == 1 ? 'checked' : '' ?> value="1">
                                                    Hamaspik 1
                                                    <input type="radio" name="hamaspik_payment" <?= $patient->hamaspik_payment == 1 ? '' : 'checked' ?> value="2">Hamaspik 2

                                                </div>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" id="insertButton"  class="btn btn-primary mr-2" onclick="return validation();">Save</button><img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none">
                                </div>
                            </form>


                        </div>

                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- main-panel ends -->
        </div>
         </div>
        
        <!-- page-body-wrapper ends -->


</body>

</html>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>


<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

<script>
    $("#service_id").select2({
        placeholder: "Select Service"
    });
    $('#ssn').keyup(function() {
        var val = this.value.replace(/\D/g, '');
        val = val.replace(/^(\d{3})/, '$1-');
        val = val.replace(/-(\d{2})/, '-$1-');
        val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
        this.value = val;
    });
    ssn = `<?= $patient->ssn; ?>`;
    var val = ssn.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    $("#ssn").val(val);

    insrance_name = $('#insurance_name').val();
    showhide();
    console.log(insrance_name);

    function showhide() {
        var insurance_name = $('#insurance_name').val();
        $('#other_insurance').addClass('hide');
        $('#other_insurance_name_error').html("");
        if (insurance_name == 'other') {
            $('#other_insurance').removeClass('hide');
        }
    }

    function notStartWithZero(phone) {
        var expr = /^[1-9]\d*$/;
        return expr.test(phone);
    };
    $('#insurance_name').change(function(e) {
        showhide();
    })


    function validation() {
        event.preventDefault();
        $('.order-listing-loader1').attr('style','');
        var temp = 0;
        var agency_ids = $('#agency_ids').val();
        var patient_code = $('#patient_code').val();
        var first_name = $('#first_name').val();
        var middle_name = $('#middle_name_id').val();
        var last_name_id = $('#last_name_id').val();
        var dob_id = $('#dob_id').val();
        var phone = $('#phone').val();
        var mobile = $('#mobile').val();
        var service_id = $('#service_id').val();
        var gender = $('input[name="gender"]').is(":checked");
        var diciplin_id = $('#diciplin_id').val();
        var payment_type = $('#payment_type').val();
        var type = $('input[name="patient_type"]').val();
        var cin = $('#cin').val();
        var email = $('#email').val();
        var ssn = $('#ssn').val();
        var address1 = $('#address1').val();
        var address2 = $('#address1').val();
        var state = $('#state').val();
        var city = $('#city').val();
        var zip_code = $('#zip_code').val();
        var county = $('#county').val();
        var insurance_name = $('#insurance_name').val();
        var location_branch = $('#location_branch').val();
        var emergency_phone = $('#emergency_phone').val();
        var emergency_contact_name = $('#emergency_contact_name').val();
        var other_insurance_name = $('#other_insurance_name').val();
        var notes = $('#notes').val();
        var insurance_id = $('#insurance_id').val();
        var language_id = $('#language_id').val();
        var insurance_name = $('#insurance_name').val();

        $("#agency_name_error").html("");
        $("#email_error").html("");
        $("#phone_error").html("");
        $("#mobile_error").html("");
        $("#dob_error").html("");
        $("#last_name_error").html("");
        $("#service_id_error").html("");
        $("#payment_type_error").html("");
        $("#agency_error").html("");
        $("#middle_name_error").html("");
        $("#email_error").html("");
        $("#ssn_error").html("");
        $("#address1_error").html("");
        $("#address2_error").html("");
        $("#state_error").html("");
        $("#city_error").html("");
        $("#zip_code_error").html("");
        $("#country_error").html("");
        $("#insurance_name_error").html("");
        $("#location_branch_error").html("");
        $("#emergency_phone_error").html("");
        $("#emergency_contact_name_error").html("");
        $("#cin_error").html("");
        $("#other_insurance_name_error").html("");
        $("#notes_error").html("");
        $("#insurance_id_error").html("");
        $('#diciplin_id_error').html("");


        // if (insurance_name.trim() != '') {
        //     if (insurance_name == 'other') {
        //         if (other_insurance_name.trim() == '') {
        //             $("#other_insurance_name_error").html("Please enter Other Insurance Name");
        //             temp++;
        //         }
        //     }
        // }
        
        if ($('input[name="type"]').val() == '') {
            $('#patient_type_error').html("Please select Type");
            temp++;
        }
      
      
     
        if (agency_ids.trim() == "") {
            $('#agency_name_error').html("Please enter Agency Name");
            temp++;
        }
        console.log(temp,'4')
        if (first_name.trim() == "") {
            $('#first_name_error').html("Please enter First Name");
            temp++;
        }
        console.log(temp,'5')
        if (last_name_id.trim() == "") {
            $('#last_name_error').html("Please enter Last Name");
            temp++;
        }

     
        // if (email.trim() == "") {
        //     $('#email_error').html("Please enter Email");
        //     temp++;
        // }
        console.log(temp,'8')
        if (dob_id == '') {
            $('#dob_error').html("Please select Date of Birth");
            temp++;
        }
        // console.log(temp,'9')
        // if (ssn.trim() == "") {
        //     $('#ssn_error').html("Please enter SSN");
        //     temp++;
        // }
        // console.log(temp,'10')
        // if (phone == "") {
        //     $('#phone_error').html("Please enter Phone");
        // }
        console.log(temp,'11')
        if (phone != "" && !notStartWithZero(phone)) {
            $('#phone_error').html("Please enter valid Phone");
        }
        console.log(temp,'12')
        if (mobile == "") {
            $('#mobile_error').html("Please enter Mobile");
            temp++;
        }
        console.log(temp,'13')
        if (mobile != "" && mobile.length < 10 || mobile.length > 15) {
            $('#mobile_error').html("Mobile should between 10 to 15 digit");
            temp++;
        }
        console.log(temp,'13')
        if (gender == false) {
            $('#gender_error').html("Please select Gender");
            temp++;
        }
       
        if(address1.trim() ==''){
            $('#address1_error').html("Please enter Address");
            temp++;
        }
      
        if (type == 'Patient') {

            // if (cin.trim() == "") {
            //     $('#cin_error').html("Please enter CIN/Medicaid Number");
            //     temp++;
            // }
        }

        if (state.trim() == "") {
            $('#state_error').html("Please enter state");
            temp++;
        }

        if (city.trim() == "") {
            $('#city_error').html("Please enter city");
            temp++;
        }
        console.log(temp,'13')
        if (zip_code.trim() == "") {
            $('#zip_code_error').html("Please enter zip code");
            temp++;
        }
  
        // if (county.trim() == "") {
        //     $('#country_error').html("Please enter coutry");
        //     temp++;
        // }
        
   
        // if (insurance_name.trim() == "") {
        //     $('#insurance_name_error').html("Please enter other insurance name");
        //     temp++;
        // }
        // console.log(temp,'13')
        
        console.log(temp,'13')
        if (temp == 0) {
            $("#insertButton").prop('disabled', true);
            $('#form-submit').submit();
        } else {
            $('.order-listing-loader1').attr('style','display:none');
            return false;
        }
    }

    $("#fu_date_id").datepicker({
        minDate: 0
    });

    $("#due_date_id").datepicker({
        minDate: 0
    });

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
            url: "<?= URL::to('get-countries') ?>",
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