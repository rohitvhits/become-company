@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 
<link href="{{ asset('assets/modulejs/css/requested_service.css')}}" rel="stylesheet" type="text/css" />


<div class="main-panel">
    @php
    $auth = auth()->user();
    @endphp
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Requested Services ()</h5>

        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">

                    <form method="get" id="formsubmit">
                        <div class="card-body">
                            @csrf
                            @php
                            $status =['Pending','cancelled','booked','completed','noshow','arrived','processing','Not interested','hospitalized/rehab','Unable To Contact','Refused','Mark as CheckIn','Pending Termination','On Hold','On Leave','Terminated']
                            @endphp
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Status</label>
                                        <div class="col-sm-12">
                                            <select name="status[]" id="status_id" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                <option value=""></option>
                                                @foreach($status as $val)
                                                <option value="{{ str_replace(' ','',$val)}}">{{ $val}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if (in_array($user->user_type_fk, [3, 184]))
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Agency Name</label>
                                        <div class="col-sm-12">
                                            <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                <?php foreach ($agencyList as $rwAgency) { ?>
                                                    <option value="<?php echo $rwAgency->id; ?>">
                                                        <?php echo $rwAgency->agency_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Patient Code</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Name</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Mobile</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Appointment Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="appointment_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Location</label>
                                        <div class="col-sm-12">
                                            <select name="locationId[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="locationId">
                                                <?php foreach ($location_list as $vsl) { ?>
                                                    <option value="<?php echo $vsl->id; ?>">
                                                        <?php echo $vsl->address1; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="created_date" class="datepickernn form-control" id="created_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">SMS Status</label>
                                        <div class="col-sm-12 ">
                                            <select name="sms_status[]" id="sms_status" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                <option value="0">Pending</option>
                                                <option value="1">Sent</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Discipline</label>
                                        <div class="col-sm-12 ">
                                            <select class="form-control" name="diciplin" id="diciplin_id">

                                                <option value="">Select Discipline</option>
                                                <option value="HHA">HHA</option>
                                                <option value="CDPAP">CDPAP</option>
                                                <option value="RN">RN</option>
                                                <option value="LPN">LPN</option>
                                                <option value="Pre-HHA">Pre-HHA</option>
                                                <option value="Pre-CDPAP">Pre-CDPAP</option>
                                                <option value="OTHER">Other</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Type</label>
                                        <div class="col-sm-12 ">
                                            <select class="form-control" name="type" id="type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="Caregiver">Caregiver</option>
                                                <option value="Patient">Patient</option>

                                            </select>

                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Completed Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="completed_date" class="completed_date form-control" id="completed_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Follow Up Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="follow_up_date" class="follow_up_date form-control" id="follow_up_date">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                        <a href="javascript:void(0)" hrefd="{{URL::to('/')}}/patient/patient-export?agency_fk=&amp;full_name=&amp;status=&amp;appointment_date=&amp;location_id=&amp;service_id=&amp;type=&amp;created_date=&amp;sms_status=&amp;assign_user_id=" class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport" id="test_agency"><i class="mdi mdi-file-export"></i>Export</a>
                                        <!-- <a href="{{URL::to('/')}}/patient" class="btn btn-light btn-rounded btn-fw btn-sm"><i class="mdi mdi-reload"></i> Clear</a> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="response_requested_id">

            </div>

        </div>

    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>


    @include('include/footer')
    <script>
        var _AJAX_LIST = "{{ url('/requested-service-ajax-list')}}";
        var _PATIENT_VIEW = "{{ url('/patient/view')}}";
    </script>

    <script src="{{ asset('assets/modulejs/requested_service.js')}}"></script>
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js')}}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />