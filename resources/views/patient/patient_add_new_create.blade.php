@include('include/header')

@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
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
    .selected-highlight{
        color: white;
        background: orange;
    }

</style>
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<div class="main-panel">
    <div class="col-12 loader-calender hhaLoader hide" id="load-caregiver-demographics">
        <img src="{{ asset('/ajax-loader.gif')}}" alt="loader" id="loader-patient-demographic-details" style="">
    </div>
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Create New / Search For Existing</h5>
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
                    <form class="form-sample" action='<?php echo URL::to('/update-remaining-patient-details'); ?>' name="adduser" method="post" id="main_form_submit_id">
                        <div class="card-body">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="type" id="type_new" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <input type="hidden" name="caregiver_id" id="cid">

                                        <?php if ($user->agency_fk == '') {
                                            $flag = 0;
                                        ?>
                                            <div class="col-md-8">
                                                <div class="form-group ">
                                                    <label for="">Agency<span style="color:red">*</span></label>
                                                    <div>
                                                        <select name="agency_id" class="form-control" id="agency_ids">
                                                            <option value="">Select Agency</option>
                                                            @if(count($agencyList) > 0)
                                                            @foreach($agencyList as $vsl)
                                                            @php
                                                            $flag = 0;

                                                            @endphp
                                                            @if($vsl->app_name !="")
                                                            @php
                                                            $flag = 1;
                                                            @endphp
                                                            @endif
                                                            <option data-app-name="{{ $flag}}" value="{{ $vsl->id}}" @if(old('agency_id')==$vsl->id) selected @endif>
                                                                {{ $vsl->agency_name}}
                                                            </option>
                                                            @endforeach
                                                            @endif

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
                                                if ($vsl->id == $user->agency_fk) {
                                                    $tempArray['id'] = $vsl->id;
                                                    $tempArray['agency_name'] = $vsl->agency_name;
                                                    $tempArray['app_name'] = $vsl->app_name;
                                                    $finalArray[] = $tempArray;
                                                    if ($vsl->app_name != "") {

                                                        $flag = 1;
                                                    }
                                                }
                                            }

                                            $result = array_merge($finalArray, $userAgencyList);

                                        ?>

                                            @if(!empty($result[0]))
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="">Agency <span style="color:red">*</span></label>
                                                    <div>
                                                        @if(!empty($result[0]))
                                                        <select name="agency_id" class="form-control" id="agency_ids">
                                                            <option value="">Select Agency</option>
                                                            @foreach($result as $agn)
                                                            <option value="{{$agn['id']}}" data-app-name="@if($agn['app_name'] !='') 1 @else '' @endif">{{$agn['agency_name']}}</option>
                                                            @endforeach
                                                        </select>

                                                        @else
                                                        <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                                                        @endif
                                                        <span class="error mt-2" id="agency_error" for="file_name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                        <?php } ?>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-8">
                                        <label class="">Seach from Existing Record<span style="color:red">*</span> <a id="click_event" href="javascript:void(0)" data-toggle="modal" title="Add New" onclick="checkAgency()" ><i class="fa fa-plus-circle"></i> Add new</a></label>
                                            <div class="form-group">
                                        
                                                <input type="text" class="form-control search_patient" name="search_patient" id="search_patient" placeholder="Search By (First Name,Last Name,Mobile No,Date of Birth,Gender)">
                                                <span class="search_patient_error error"></span><br>
                                                <b>Notes:</b><p class="text-mute">Search Record (First Name,Last Name,Mobile No,Date of Birth,Gender)</p>
                                            </div>
                                            
                                        </div>
                                           
                                    </div>
                                    <div class="hide" id="selected_agency_list">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label"><b>Select Agency Name :</b> <span class="selected_agency"></span></label>
                                                    
                                                    <input type="hidden" name="selected_agency" id="selected_agency" value="{{ old('selected_agency')}}">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="row">    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                                                <div>
                                                    <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                                        
                                                    </select>
                                                    <span id="service_id_error" class="error mt-2"><?php echo $errors->add_agency->first('service_id'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Followup Date</label>
                                                <div>
                                                    <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select Followup Date" id="fu_date_id" name="fu_date" value="<?php echo old('fu_date'); ?>" readonly>
                                                    <span id="fu_date_error" class="error mt-2"><?php echo $errors->add_agency->first('fu_date'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Due Date</label>
                                                <div>
                                                    <input type="text" class="form-control" autocomplete="off" placeholder="Select Due Date" id="due_date_id" name="due_date" value="<?php echo old('due_date'); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row table-responsive"  id="show_demographic-detail">

                                        
                                    </div>
                                </div>
                            </div>
                            
                            
                            <?php if ($user->agency_fk == '106') { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label">Payment<span class="error mt-2">*</span></label>
                                            <div>
                                                <input type="radio" name="hamaspik_payment" value="1"> Hamaspik 1
                                                <input type="radio" checked="checked" name="hamaspik_payment" value="2">Hamaspik 2
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2" id="insertButton">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content -->

    <!-- /Page Content -->
    @include('patient._partial.modal.create_patient_demographic_new')
    @include('patient._partial.modal.search_modal_demographic_details')
    <script>
        var _FIND_PATIENT_DETAILS ="{{ url('search-patient-details')}}";
        var _TYPE_WISE_SERVICE_LIST ="{{ url('/ajax-service')}}";
        var _SAVE_PATIENT_DEMOGRAPHIC_DETAILS = "{{ url('save-patient-details')}}";
        var _CSRF_TOKEN = "{{ csrf_token()}}";
        var _GET_COUNTRY_CODE  ="{{ url('get-county')}}";
        var _DETAILS_PATIENTDATA = "{{ url('get-demo-graphic-details-data') }}";
        var _PATIENT_VIEW = "{{ url('patient/view') }}";
        var _HHA_PATIENT_DETAILS = "{{ url('hha-patient-caregiver-details')}}";
        var _GET_APPOINTMENT_EXISTING_DATA = "{{ url('search-total-appointment')}}";
        var _PATIENT_LISTING_PAGE = "{{ url('appointment')}}";
        var _GET_BRANCHES_BY_AGENCY_SERVICES = "{{ url('branch-link-ajax/get-branches-by-agency-services') }}";
        var _SEARCH_USERS_BY_AGENCY = "{{ url('agency/search-users-by-agency') }}";
    </script>
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
    <script src="{{ asset('assets/modulejs/patient/patient_demographic_new.js')}}?time={{ time()}}"></script>

    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}" type="text/css" />
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}?time={{ env('timestamp')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
    <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
    <script>

        
$("#due_date_id").datepicker({
    minDate: 0
});
$("#fu_date_id").datepicker({
    minDate: 0
});

</script>
    @include('include/footer')