@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
 <style>
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     .wmd-view-topscroll,
     .wmd-view {
         overflow-x: scroll;
         overflow-y: hidden;
         border: none 0px red;
     }

     .wmd-view {
         overflow: auto;
         height: calc(100vh - 250px);
     }

     .wmd-view-topscroll {
         height: 20px;
     }

     .scroll-div1 {

         overflow-x: scroll;
         overflow-y: hidden;
         height: 20px;
         width: calc(1650px - -17px) !important;
     }

     .scroll-div2 {
         height: 20px;
     }

     .scroll-div1,
     .scroll-div2 {
         /* width: 1650px; */
     }

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 tr th:last-child {
         width: 100px;
     }

     .table-width1 tr th:nth-child(10) {
         width: 100px;
     }

     .table-width1 {
         background-color: #fff;
     }

     .table-width1 tr th:nth-child(11) {
         width: 152px;
     }

     .table-width1 tr th:nth-child(12) {
         white-space: nowrap;
     }

     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }

     .search-main1 {
         border-top: 1px solid #eeeeee;
         margin-left: -20px;
         margin-right: -20px;
     }

     .search-btn1,
     .search-btn1:hover,
     .search-btn1:active,
     .search-btn1:focus {
         background: #007bff !important;
         border: #007bff !important;
         border-radius: 20px;
         height: 36px;
     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .search-card1 {
         margin-bottom: 20px;
     }

     .search-card1 .form-group {
         margin-bottom: 0.5rem;
     }

     .search-card1 label {
         margin-bottom: 0;
     }

     .search-card1 .card-body {
         padding-bottom: 10px;
     }

     .search-card1 input[type=text] {
         border-radius: 4px;
         border-color: #aaa;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }

     .no_warp {
         white-space: nowrap;
     }

     .select2-container .select2-selection--single {
         height: 38px !important;
     }

     .tableData .add_new_record .left_record {
         left: -9px;
         right: unset !important;
     }

     .tableData .add_new_record {
         position: absolute;
         top: 0;

         background: #00BBE0;
         padding: 1px 5px;
         font-size: 10px;
         color: #fff;
         border-radius: 2px 2px 2px 2px;
         font-size: 10px !important;
     }

     .tableData .add_new_record::after {
         position: absolute;
         content: "";
         bottom: -6px;
         right: 0px;
         background: #b7b7b8;
         z-index: -1;
         width: 10px;
         height: 10px;

     }

     .tableData .add_new_record::after {
         left: 0px;
         border-radius: 0px 0px 0px 50px;
     }

     .service_id_by_patient_type .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     .pale-yellow-color {
         background-color: #ffecb3;
     }

    #exampleModal-patient-import .custom-file-label::after {
        content: "Browse";
    }

    #exampleModal-patient-import .form-control-lg {
        height: calc(2.5rem + 2px);
    }

    #exampleModal-patient-import .alert-info {
        background-color: #e3f2fd;
        color: #0d47a1;
    }
    .modal-scroll-inside {
        max-height: calc(100vh - 210px);
        overflow: auto;
    }

    .modal .modal-dialog{
        margin-top: 20px;
    }
    #exampleModal-patient-import .modal-footer{
        padding:4px 1px !important
    }

     /* Agency Toggle Button */
    .agency-filter-toggle-wrapper,.service-filter-toggle-wrapper,.branch-filter-toggle-wrapper {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
        gap: 6px;
    }
    .agency-toggle-btn, .service-toggle-btn, .branch-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 4px;
        border: 2px solid #ddd;
        background: #fff;
        cursor: pointer;
        transition: all 0.25s ease;
        padding: 0;
        vertical-align: middle;
        position: relative;
        flex-shrink: 0;
    }
    .agency-toggle-btn i, .service-toggle-btn i, .branch-toggle-btn i {
        font-size: 18px;
        line-height: 1;
        pointer-events: none;
        display: block;
    }
    /* Blue/Grey - Professional & Clear (Current Active) */
    .agency-toggle-btn[data-mode="include"], .service-toggle-btn[data-mode="include"], .branch-toggle-btn[data-mode="include"] {
        background-color: #cfe2ff !important;
        border-color: #0d6efd !important;
        color: #084298 !important;
    }
    .agency-toggle-btn[data-mode="include"]:hover, .service-toggle-btn[data-mode="include"]:hover, .branch-toggle-btn[data-mode="include"]:hover {
        background-color: #b6d4fe !important;
        transform: scale(1.05);
    }
    .agency-toggle-btn[data-mode="exclude"], .service-toggle-btn[data-mode="exclude"],.branch-toggle-btn[data-mode="exclude"] {
        background-color: #e9ecef !important;
        border-color: #6c757d !important;
        color: #495057 !important;
    }
    .agency-toggle-btn[data-mode="exclude"]:hover, .service-toggle-btn[data-mode="exclude"]:hover, .branch-toggle-btn[data-mode="exclude"]:hover {
        background-color: #dee2e6 !important;
        transform: scale(1.05);
    }
    .agency-toggle-btn:focus, .service-toggle-btn:focus, .branch-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }
    .agency-toggle-btn:active, .service-toggle-btn:active, .branch-toggle-btn:active  {
        transform: scale(0.95);
    }
    .agency-toggle-label,.service-toggle-label,.branch-toggle-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: color 0.25s ease;
        user-select: none;
        white-space: nowrap;
    }
    /* Match label colors with button colors */
    .agency-toggle-label.mode-include, .service-toggle-label.mode-include, .branch-toggle-label.mode-include {
        color: #0d6efd;
    }
    .agency-toggle-label.mode-exclude, .service-toggle-label.mode-exclude, .branch-toggle-label.mode-exclude {
        color: #6c757d;

    }

    #exampleModal-patient-import .modal-header{
        padding: 10px 25px !important;
    }

    #exampleModal-patient-view-import .modal-header{
        padding: 10px 25px !important;
    }

    .token-input-dropdown,
    .token-input-dropdown-facebook {
        max-height: 200px !important;
        overflow-y: auto !important;
        overflow-x: hidden;
    }
 </style>
 <div class="main-panel">
     @php
     $auth = auth()->user();
     @endphp

     <div class="content-wrapper">



         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Appointments ({{$total_record}})</h5>
             <div class="page-rightbtns">
                 <div>
                    @if(auth()->user()->agency_fk && Common::hasReportingToolAccess())
                    <a href="{{ url('report/custom-reports') }}" class="btn btn-success btn-rounded btn-sm btn-fw"><i class="mdi mdi-file-chart"></i>Reporting Tool</a>
                    @endif
                 @can('bulk-appointments-delete')
                    <a href="javascript:void(0)" class="btn btn-danger btn-rounded btn-sm btn-fw" onclick="bulkAppointmentDelete()"><i class="mdi mdi-delete"></i>Bulk Appointments Delete</a>
                    @endcan
                    @can('bulk-assign-user')
                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-sm btn-fw" data-toggle="modal" id="modals_bulk_assign_user" onclick="bulkAssignUserForAppointment()"><i class="mdi mdi-file-export"></i>Bulk Assign User</a>
                    @endcan
                    @if($auth->agency_fk != "" && $agency_enable_review)
                    <a href="javascript:void(0)" class="btn btn-warning btn-rounded btn-sm btn-fw" onclick="confirmReview()"><i class="fa fa-check-circle"></i> Bulk Review</a>
                    @endif
                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                        @can('appointment-import')
                            <a href="{{ url('patient/import')}}" class="btn btn-secondary btn-rounded btn-sm btn-fw"><i class="mdi mdi-file-export"></i>Import</a>
                        @endcan
                    @endif
                    <!-- <a href="{{URL::to('/')}}/patient/add" class="btn btn-primary btn-rounded btn-fw btn-sm  ml-1"><i class="mdi mdi-plus"> </i> Create New</a> -->

                    @php
                        $createAppointmentFlag =1;
                        if (auth()->user()->agency_fk != "") {
                            $createAppointmentFlag = 0;
                            if(!in_array('AddAppointment',$appointmentPermission)){
                                $createAppointmentFlag = 1;
                            }
                        }
                    @endphp

                    @if($createAppointmentFlag ==1)
                    <a href="{{URL::to('/')}}/patient-add-new" class="btn btn-primary btn-rounded btn-fw btn-sm  ml-1"><i class="mdi mdi-plus"> </i> Create New</a>
                    @endif
                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                    <a href="{{URL::to('/')}}/appointment?is_past_show=true" class="btn btn-info btn-rounded btn-fw btn-sm">Past Appointment List</a>
                    @endif
                    <a href="{{URL::to('/')}}/appointment" class="btn btn-light btn-rounded btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
                    <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i class="fa fa-search"></i></button>

                 </div>

             </div>
         </div>
         <div class="row ">
             <div class="col-sm-12">
                 <div class="card search-card1" id="search-div">
                     <div class="card-body">
                         <form method="get" id="formsubmit">
                             @csrf

                             <div class="row">


                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Status</label>
                                         <div class="col-sm-12">
                                             <select name="status[]" id="status_id" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <option value=""></option>
                                                 <option value="Pending" <?php if (in_array('Pending', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Pending</option>
                                                 <option value="cancelled" <?php if (in_array('cancelled', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Cancelled</option>

                                                 <option value="booked" <?php if (in_array('booked', $selected_status)) {
                                                                            echo "selected='selected'";
                                                                        } ?>>Booked</option>
                                                 <option value="completed" <?php if (in_array('completed', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Completed</option>

                                                 <option value="noshow" <?php if (in_array('noshow', $selected_status)) {
                                                                            echo "selected='selected'";
                                                                        } ?>>No Show</option>

                                                 <option value="arrived" <?php if (in_array('arrived', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Arrived</option>
                                                 <option value="processing" <?php if (in_array('processing', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Processing</option>
                                                 <option value="Not interested" <?php if (in_array('Not interested', $selected_status)) {
                                                                                    echo "selected='selected'";
                                                                                } ?>>Not Interested
                                                 </option>
                                                 <option value="hospitalized/rehab" <?php if (in_array('hospitalized/rehab', $selected_status)) {
                                                                                        echo "selected='selected'";
                                                                                    } ?>>
                                                     Hospitalized/Rehab</option>
                                                 <option value="unableToContact" <?php if (in_array('unableToContact', $selected_status)) {
                                                                                        echo "selected='selected'";
                                                                                    } ?>>Unable To Contact
                                                 </option>
                                                 <option value="refused" <?php if (in_array('refused', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Refused</option>
                                                 <option value="checkin" <?php if (in_array('checkin', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Mark as CheckIn</option>

                                                 <option value="Pending Termination" <?php if (in_array('Pending Termination', $selected_status)) {
                                                                                            echo "selected='selected'";
                                                                                        } ?>>Pending Termination</option>
                                                 <option value="Onhold" <?php if (in_array('Onhold', $selected_status)) {
                                                                            echo "selected='selected'";
                                                                        } ?>>On Hold</option>
                                                 <option value="On Leave" <?php if (in_array('On Leave', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>On Leave</option>
                                                 <option value="Terminated" <?php if (in_array('Terminated', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Terminated</option>
                                                 <option value="inactive" <?php if (in_array('inactive', $selected_status)) {
                                                                                echo "selected='selected'";
                                                                            } ?>>Inactive</option>
                                                 @foreach ($statuses as $key=> $status)
                                                    <option value="{{ $key }}" {{ in_array($key, $selected_status) ? "selected='selected'" : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 @if (in_array($user->user_type_fk, [3, 184]))
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Agency Name
                                             <span class="agency-filter-toggle-wrapper">
                                                <button type="button" class="agency-toggle-btn" id="agencyToggleBtn"
                                                        data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="agency-toggle-label mode-include" id="agencyToggleLabel">Include Agency</span>
                                            </span>
                                         </label>
                                         <div class="col-sm-12">
                                            <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                           value="@if(isset($agency_filter_type) && $agency_filter_type == 'exclude')exclude @else include @endif">
                                             <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <?php foreach ($agencyList as $rwAgency) { ?>
                                                     <option value="<?php echo $rwAgency->id; ?>" <?php echo in_array($rwAgency->id, $selected_agency_fk) ? 'selected' : ''; ?>>
                                                         <?php echo $rwAgency->agency_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 @endif

                                 @if ($user->agency_fk !="")
                                    @php
                                        $finalArray = [];
                                        foreach ($agencyList as $rwAgency){
                                            $tempArray = [];
                                            if($rwAgency->id ==$user->agency_fk){
                                                $tempArray['id'] = $rwAgency->id;
                                                $tempArray['agency_name'] = $rwAgency->agency_name;
                                                $finalArray[] = $tempArray;
                                            }
                                        }

                                        $result = array_merge($finalArray, $userAgencyList);
                                    @endphp
                                    @if(!empty($result[0]))
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="" class="col-sm-12 ">Agency Name

                                                </label>
                                                <div class="col-sm-12">
                                                <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                value="include">
                                                    <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                    @foreach($result as $agn)
                                                            <option value="{{$agn['id']}}" @if(in_array($agn['id'], $selected_agency_fk)) selected @endif>{{$agn['agency_name']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @endif
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Patient Code</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code" value="<?php echo $patient_code; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Name</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name" value="<?php echo $full_name; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Mobile</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile" value="<?php echo $mobile; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                        <label for="" class="col-sm-12 ">Services
                                            @if(auth()->user()->agency_fk == "")
                                             <span class="service-filter-toggle-wrapper">
                                                <button type="button" class="service-toggle-btn" id="serviceToggleBtn"
                                                        data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="service-toggle-label mode-include" id="serviceToggleLabel">Include Services</span>
                                            </span>
                                            @endif
                                         </label>
                                         <div class="col-sm-12">
                                            <input type="hidden" name="service_filter_type" id="service_filter_type"
                                                           value="@if(isset($service_filter_type) && $service_filter_type == 'exclude')exclude @else include @endif">
                                             <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="service_id[]" id="service_id">
                                                 <?php
                                                    foreach ($serviceList as $service) { ?>
                                                     <option value="<?php echo $service->id; ?>" <?php if (in_array($service->id, $selected_service_id)) {
                                                                                                        echo 'selected';
                                                                                                    } ?>>
                                                         <?php echo $service->name; ?></option>
                                                 <?php } ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Assign To</label>
                                         <div class="col-sm-12">
                                             <select name="assign_user_id[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="assign_user_id">
                                                 @if (!empty($assign_user_list[0]))
                                                 @foreach ($assign_user_list as $assigns)
                                                 <option value="{{ $assigns->id }}" @if (in_array($assigns->id, $selected_assign_user_id)) selected='selected' @endif>
                                                     {{ $assigns->name }}
                                                 </option>
                                                 @endforeach
                                                 @endif
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Due Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="due_date" value="<?php echo $due_date; ?>" class="due_datenn form-control" id="due_date" readonly>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Appointment Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" value="<?php echo $appointment_date; ?>" id="appointment_date" readonly>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Location</label>
                                         <div class="col-sm-12">
                                             <select name="locationId[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="locationId">
                                                 <?php foreach ($location_list as $vsl) { ?>
                                                     <option value="<?php echo $vsl->id; ?>" <?php if (in_array($vsl->id, $selected_location_id)) {
                                                                                                    echo 'selected';
                                                                                                } ?>>
                                                         <?php echo $vsl->address1; ?></option>
                                                 <?php } ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Created Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="created_date" value="<?php echo $created_date; ?>" class="datepickernn form-control" id="created_date">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">SMS Status</label>
                                         <div class="col-sm-12 ">
                                             <select name="sms_status[]" id="sms_status" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <option value="0" <?php if (in_array(0, $selected_sms_status)) {
                                                                        echo "selected='selected'";
                                                                    } ?>>Pending</option>
                                                 <option value="1" <?php if (in_array(1, $selected_sms_status)) {
                                                                        echo "selected='selected'";
                                                                    } ?>>Sent</option>

                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Discipline</label>
                                         <div class="col-sm-12 ">
                                             <select class="form-control" name="diciplin" id="diciplin_id">

                                                 <option value="">Select Discipline</option>
                                                 @if (count($masterData) > 0)
                                                 @foreach ($masterData as $master)
                                                 @if ($master->master_type_fk == 26)
                                                 <option value="{{ $master->name }}" <?php if ($selected_discipline == $master->name) {
                                                                                            echo "selected='selected'";
                                                                                        } ?>>{{ $master->name }}
                                                 </option>
                                                 @endif
                                                 @endforeach
                                                 @endif

                                             </select>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Type</label>
                                         <div class="col-sm-12 ">
                                             <select class="form-control" name="type" id="type" class="form-control">
                                                 <option value="">Select Type</option>
                                                   @if($auth->record_access !="Patient")
                                                   <option value="Caregiver" <?php if ($type == 'Caregiver') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Caregiver</option>
                                                   @endif
                                                   @if($auth->record_access !="Caregiver")
                                                   <option value="Patient" <?php if ($type == 'Patient') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Patient</option>
                                                   @endif



                                             </select>

                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">In Service Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="inservice_date" value="<?php echo $inservice_date; ?>" class="inservice_date form-control" id="inservice_date">
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Completed Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="completed_date" value="{{$completed_date}}" class="completed_date form-control" id="completed_date">
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Follow Up Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="follow_up_date" value="{{$follow_up_date}}" class="follow_up_date form-control" id="follow_up_date">
                                         </div>
                                     </div>
                                 </div>
                                 @if($auth->agency_fk ==106 || $auth->id == 482)
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Training Due Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="traning_date" value="{{$traning_date}}" class="traning_date form-control" id="traning_date">
                                         </div>
                                     </div>
                                 </div>
                                 @endif

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Created By</label>
                                         <div class="col-sm-12">
                                             @if(!empty($agency_user_list[0]))
                                             <select name="created_by" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="created_by">
                                                 <option value="">Select Created By</option>
                                                 @foreach($agency_user_list as $val)
                                                 <option value="{{ $val->id}}" @if($created_by==$val->id) selected @endif>{{ $val->first_name}} {{ $val->last_name}}</option>

                                                 @endforeach

                                             </select>
                                             @else
                                             <input type="text" name="created_by_ny" id="created_by_ny">
                                             <input type="hidden" name="created_by_ny_id" id="created_by_ny_id" value="{{ $created_by_id}}">
                                             <input type="hidden" name="created_by_ny_name" id="created_by_ny_name" value="{{ $created_by_name}}">

                                             @endif
                                         </div>
                                     </div>
                                 </div>

                                 @if (in_array($user->user_type_fk, [3, 184]))
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Training Status</label>
                                         <div class="col-sm-12">
                                             <select name="training_status[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="training_status" multiple>
                                                 <option value="">Select Training Status</option>
                                                 <option value="Completed" @if(in_array('Completed',$selected_training_status)) selected @endif>Completed</option>
                                                 <option value="Processing" @if(in_array('Processing',$selected_training_status)) selected @endif>Processing</option>
                                                 <option value="Refused" @if(in_array('Refused',$selected_training_status)) selected @endif>Refused</option>
                                                 <option value="Unable to reach" @if(in_array('Unable to reach',$selected_training_status)) selected @endif>Unable to reach</option>
                                                 <option value="Need to assistance" @if(in_array('Need to assistance',$selected_training_status)) selected @endif>Need to assistance</option>


                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 @endif
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Language</label>
                                         <div class="col-sm-12">
                                             <select name="language_id" class="form-control" id="language_id">
                                                 <option value="">Select Language</option>
                                                 @foreach ($language_list as $vsl)
                                                 <option value="{{$vsl->id}}" @if ($vsl->id == $selected_language_id) @php echo 'selected'; @endphp @endif>
                                                     {{$vsl->name}}
                                                 </option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Transition Aid</label>
                                         <div class="col-sm-12">
                                             <select name="transition_aid" class="form-control" id="transition_aid">
                                                 <option value="">Select Transition Aid</option>
                                                 <option value="1" @if( $selected_transition_aid=="1" ) @php echo 'selected' ; @endphp @endif>Yes</option>
                                                 <option value="0" @if( $selected_transition_aid=="0" ) @php echo 'selected' ; @endphp @endif>No</option>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Date Of Birth</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="dob" value="{{$dob}}" class="dob form-control" id="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" min="1000-01-01" max="9999-12-31">
                                         </div>
                                     </div>
                                 </div>
                                 @if (in_array($user->user_type_fk, [3, 184,6]))
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Last Status Updated Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="last_status_update" value="{{$last_status_update}}" class="form-control" id="last_status_update">
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Last Status Updated By</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="last_status_updated_by" id="last_status_updated_by">
                                             <input type="hidden" name="last_status_updated_by_id" id="last_status_updated_by_id" value="{{ $last_status_updated_by_id}}">
                                             <input type="hidden" name="last_status_updated_by_name" id="last_status_updated_by_name" value="{{ $last_status_updated_by_name}}">


                                         </div>
                                     </div>
                                 </div>
                                 @endif
                                 @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <div class="form-check form-check-flat form-check-primary" style="margin-top: 15px important ; margin-left: 5px !important;">
                                             <label class="form-check-label">
                                                 <input type="checkbox" class="form-check-input" id="archived" {{$is_archive=='true' ? 'checked' : ''}}>
                                                 Show Archived
                                                 <i class="input-helper"></i></label>
                                         </div>
                                     </div>
                                 </div>
                                 @endif
                                 @if (in_array($user->user_type_fk, [3,184]))
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Medication List</label>
                                         <div class="col-sm-12">
                                             <select name="medication_list" class="form-control" id="medication_list">
                                                 <option value="">Select Medication List</option>
                                                 <option value="Yes" {{ isset($medication_list) && $medication_list == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                 <option value="No" {{ isset($medication_list) && $medication_list == 'No' ? 'selected' : '' }}>No</option>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Insurance Elg</label>
                                         <div class="col-sm-12">
                                             <select name="insurance_elg" class="form-control" id="insurance_elg">
                                                 <option value="">Select Insurance Elg</option>
                                                 <option value="Yes" {{ isset($insurance_elg) && $insurance_elg == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                 <option value="No" {{ isset($insurance_elg) && $insurance_elg == 'No' ? 'selected' : '' }}>No</option>
                                             </select>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Mdo Tag</label>
                                         <div class="col-sm-12">
                                             <select name="mdo_tag" class="form-control" id="mdo_tag">
                                                 <option value="">Select Mdo Tag</option>
                                                 <option value="Yes" {{ isset($mdo_tag) && $mdo_tag == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                 <option value="No" {{ isset($mdo_tag) && $mdo_tag == 'No' ? 'selected' : '' }}>No</option>
                                             </select>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Branch
                                            <span class="branch-filter-toggle-wrapper">
                                                <button type="button" class="branch-toggle-btn" id="branchToggleBtn"
                                                        data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="branch-toggle-label mode-include" id="branchToggleLabel">Include Branch</span>
                                            </span>
                                        </label>
                                        <div class="col-sm-12">
                                            <input type="hidden" name="branch_filter_type" id="branch_filter_type"
                                                           value="@if(isset($branch_filter_type) && $branch_filter_type == 'exclude')exclude @else include @endif">
                                            <select class="form-control" name="filter_branch_id" id="filter_branch_id">
                                                <option value="">Select Branch</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">State</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="state" value="{{$state}}" class="form-control" id="state">
                                         </div>
                                     </div>
                                 </div>
                                 @endif
                                <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Agency Status</label>
                                         <div class="col-sm-12">
                                            <select class="form-control" name="agency_status" id="agency_status">
                                                <option value="">Select Agency Status</option>
                                                @foreach($field_data as $status)
                                                    <option value="{{ $status }}" {{ isset($status) && $status == $agency_status ? 'selected' : '' }}>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                         </div>
                                     </div>
                                 </div>
                                 @if(auth()->user()->agency_fk == "")
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Referral Type</label>
                                         <div class="col-sm-12">
                                            <select class="form-control" name="referral_type" id="referral_type">
                                                <option value="">Select Referral Type</option>
                                                @foreach($referralTypeList as $ref)
                                                    <option value="{{ $ref->name }}" {{ isset($referral_type) && $referral_type == $ref->name ? 'selected' : '' }}>{{ $ref->name }}</option>
                                                @endforeach
                                            </select>
                                         </div>
                                     </div>
                                 </div>
                                    @endif
                                  
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label for="" class="col-sm-12 ">Agency Rep</label>
                                         <div class="col-sm-12">
                                           <input type="text" name="agency_updated_by" id="agency_updated_by">

                                         </div>
                                     </div>
                                 </div>

                                @if(auth()->user()->agency_fk != "" && $agency_enable_review)
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <div class="form-check form-check-flat form-check-primary" style="margin-top: 15px; margin-left: 5px !important;">
                                             <label class="form-check-label">
                                                 <input type="checkbox" class="form-check-input" id="show_reviewed" {{ isset($is_reviewed) && $is_reviewed == 'true' ? 'checked' : '' }}>
                                                 Show Reviewed
                                                 <i class="input-helper"></i>
                                             </label>
                                         </div>
                                     </div>
                                 </div>
                                 @endif

                                 @if(auth()->user()->agency_fk == "")
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <div class="form-check form-check-flat form-check-primary" style="margin-top: 15px important ; margin-left: 5px !important;">
                                             <label class="form-check-label">
                                                 <input type="checkbox" class="form-check-input" value="0" id="record_read" {{$record_read === '0' ? 'checked' : ''}}>
                                                 Show New Portals
                                                 <i class="input-helper"></i></label>
                                         </div>
                                     </div>
                                 </div>
                                 @endif
                              
                             </div>
                            <input type="hidden" name="agency_updated_by_id" id="agency_updated_by_id" value="{{ $agency_updated_by_id}}">
                            <input type="hidden" name="agency_updated_by_name" id="agency_updated_by_name" value="{{ $agency_updated_by_name}}">
                            <input type="hidden" name="token_input_agency_id" id="token_input_agency_id" value="{{ $token_input_agency_id}}">

                             <div class="search-main1">
                                 <div class="search-inner">
                                     <div>
                                         <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                        <a href="javascript:void(0)"
                                            hrefd="{{URL::to('/')}}/patient/patient-export?agency_fk=&amp;full_name=&amp;status=&amp;appointment_date=&amp;location_id=&amp;service_id=&amp;type=&amp;created_date=&amp;sms_status=&amp;assign_user_id="
                                            class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport" id="test_agency"><i
                                                class="mdi mdi-file-export"></i>Export</a>
                                        @endif
                                     </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         <div class="row">
             <div class="col-12 ">
                 <div class="table-responsive tableData">
                     <table id="order-listing1" class="table table-bordered table-width1">
                         <thead>
                             <tr>
                             <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                               <th> <input type="checkbox" id="cboxId"></th>
                                <?php } ?>
                                @if($auth->agency_fk != "" && $agency_enable_review)
                                <th><input type="checkbox" id="reviewCboxId" title="Select All for Review"></th>
                                @endif
                                 <th>ID</th>
                                 <th class="no_warp">SMS</th>
                                 <th>Status</th>
                                 <th class="no_warp">Agency Name</th>
                                 <th class="no_warp">Agency Rep</th>
                                 <th>Type/Discipline</th>
                                 <th class="no_warp">Patient Code </th>
                                 <th class="no_warp">Name/Mobile/DOB/Services </th>
                                 <th class="no_warp">Assigned To</th>
                                 <th class="no_warp">Due Date</th>
                                 <th class="no_warp">Appointment Date - Location<br/>Telehealth Date</th>
                                 <th style="white-space:nowrap">Created Date</th>
                                 <th>FU Date</th>
                                 <th style="white-space:nowrap">In Service Date</th>
                                 <th style="white-space:nowrap">Completed Date</th>
                                 <th style="white-space:nowrap">Follow Up Date</th>
                                 @if($auth->agency_fk ==106 || $auth->id ==482)
                                 <th style="white-space:nowrap">Training Due Date</th>

                                 @endif
                                 @if (in_array($user->user_type_fk, [3, 184]))
                                 <th style="white-space:nowrap">Training Status</th>
                                 <th style="white-space:nowrap">Last Status Updated Date <br>/ Last Status Updated By</th>
                                 <th>Referral Type</th>

                                 @endif
                                 @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                 <th style="white-space:nowrap">Action</th>
                                 @endif
                             </tr>
                         </thead>
                         <tbody>

                             <?php
                                $flag = 0;
                                if (count($query) > 0) {
                                    $i = 1 + (($query->currentPage() - 1) * $query->perPage());

                                    foreach ($query as $row) {
                                        $flag = 0;
                                        if ($row->hha_id != "") {
                                            $flag = 1;
                                        } else {
                                            if ($row->type == 'Caregiver') {
                                                if ($row->link_hha_caregiver != "") {
                                                    $flag = 1;
                                                }
                                            }
                                            if ($row->type == 'Patient') {
                                                if ($row->link_hha_patient != "") {
                                                    $flag = 1;
                                                }
                                            }
                                        }
                                        $appointmentFlagClasss = '';
                                        if ($row->flag == '1') {
                                            $appointmentFlagClasss = "pale-yellow-color";
                                        }
                                        if ($row->flag == '0') {
                                            $appointmentFlagClasss = '';
                                        }
                                ?>
                                     <tr class="{{$appointmentFlagClasss}}">
                                     <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                        <td>
                                        <input type="checkbox" class="form-check-input cbox ml-0" value="{{ $row->id }}">
                                        </td>
                                        <?php } ?>
                                        @if($auth->agency_fk != "" && $agency_enable_review)
                                        <td>
                                            @if($row->is_reviewed != 1)
                                            <input type="checkbox" class="form-check-input review-cbox ml-0" value="{{ $row->id }}">
                                            @else
                                            <i class="fa fa-check-circle text-success" title="Already Reviewed"></i>
                                            @endif
                                        </td>
                                        @endif
                                         <td>
                                             <div>
                                                 <a title="" href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . '' . $row->id ?></a>
                                             </div>
                                             @if($row->record_read ==0)
                                             <div style="position:relative"><span class="add_new_record left_record">New</span></div>
                                             @endif
                                             @if($flag ==1)
                                             <img src="{{ asset('/img/hha.png')}}" title="HHA" alt="HHA" style="height: 15px; width: 15px;">
                                             @endif

                                             @if($row->alaycare_id !="")
                                             <img src="{{ asset('/img/alayacare.png')}}" title="Alayacare" alt="HHA" style="height: 15px; width: 15px;">
                                             @endif
                                             @if($row->robort_id !="")
                                             <img src="{{ asset('/img/emmacare.png')}}" title="Remote Focus" alt="HHA" style="height: 25px; width: 25px;">
                                             @endif
                                         </td>
                                         <td><?php if ($row->patient_sms_flag == 1) {
                                                    echo "<span class='badge badge-success'>Sent</span>";
                                                } else {
                                                    echo "<span class='badge badge-warning'>Pending</span>";
                                                } ?></td>
                                         <td id="status_{{$row->id}}">
                                             <?php

                                                if (strtolower($row->status) == 'pending') {
                                                ?>
                                                 <label for="" class='badge badge-warning'>Pending</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'booked') {
                                                ?>
                                                 <label for="" class='badge badge-info'>Booked</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'completed') {
                                                ?>
                                                 <label for="" class='badge badge-success'>Completed</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'cancelled' || strtolower($row->status) == 'pending termination') {
                                                ?>
                                                 <label for="" class='badge badge-danger'>{{ $row->status}}</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'noshow') {
                                                ?>
                                                 <label for="" class='badge badge-secondary'>No Show</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'refused' || strtolower($row->status) == 'terminated') {
                                                ?>
                                                 <label for="" class='badge badge-danger'>{{ ucfirst($row->status)}}</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'processing' || strtolower($row->status) == 'on leave') {
                                                ?>
                                                 <label for="" class='badge badge-info'>{{ $row->status}}</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'arrived') {
                                                ?>
                                                 <label for="" class='badge badge-primary'>Arrived</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'checkin') {
                                                ?>
                                                 <label for="" class='badge badge-primary'>Mark as ClockIn</label>

                                             <?php } ?>
                                             <?php

                                                if (strtolower($row->status) == 'not interested') {
                                                ?>
                                                 <label for="" class='badge badge-primary'>Not Interested</label>

                                             <?php }
                                                if (strtolower($row->status) == 'hospitalized/rehab') {
                                                ?>
                                                 <label for="" class='badge badge-secondary'>Hospitalized/Rehab</label>

                                             <?php }
                                                if (strtolower($row->status) == 'unabletocontact') {
                                                ?>
                                                 <label for="" class='badge badge-primary'>Unable To Contact</label>

                                             <?php } ?>

                                             <?php
                                                if (strtolower(trim($row->status)) == 'on hold') { ?>
                                                 <label for="" class='badge badge-secondary'>On Hold</label>
                                             <?php } ?><br>
                                                @if ($row->status == '1st Attempt - Unable to Contact' || $row->status == '2nd Attempt - Unable to Contact' || $row->status == '3rd Attempt - Unable to Contact' || $row->status == 'Patient Asked to Reschedule' || $row->status == 'New Order Received')
                                                    <label for="" class='badge badge-info'>{{$row->status}}</label>
                                                @endif

                                                @if ($row->status == 'Telehealth Completed' || $row->status == 'Telehealth Completed , Pending Forms' || $row->status == 'Form Completed' || $row->status == 'Service Provided')
                                                    <label for="" class='badge badge-success'>{{$row->status}}</label>
                                                @endif

                                                @if ($row->status == 'Patient Deceased' || $row->status == 'Appointment was missed' || $row->status == 'Appointment Missed' || $row->status == 'Closed Temporarily')
                                                    <label for="" class='badge badge-danger'>{{$row->status}}</label>
                                                @endif

                                                @if ($row->status == 'Signed' || $row->status == 'Signed & Sent Back to the Agency' || $row->status == 'New Form Requested')
                                                    <label for="" class='badge badge-primary'>{{$row->status}}</label>
                                                @endif
                                                @if (strtolower($row->status) == 'inactive')
                                                    <label for="" class='badge badge-danger'>{{ ucfirst($row->status)}}</label>
                                                @endif
                                                @if(in_array(strtolower($row->status),['cancelled','refused']))
                                                    {{ $row->reason_name}}
                                                    @if(!empty($row->otherreasonname))
                                                        <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $row->otherreasonname }}"></i>
                                                    @endif
                                                @endif

                                         </td>
                                         <td><?= $row->agency_name ?> </td>
                                        <td>{{$row->agencyRepUser}} </td>
                                         <td><?php echo $row->type; ?>
                                             <br />
                                             <?php echo $row->diciplin; ?>
                                             <br />
                                             @if($row->location_branch !="")
                                             <p class="text-muted" style="font-size:10px">({{ $row->location_branch}})</p>
                                             @endif


                                         </td>
                                         <td>{{ $row->patient_code}}</td>
                                         <td>
                                             <?php echo $row->first_name . ' ' . $row->last_name; ?><br />
                                             @if($row->mobile !="")
                                             <?php echo $row->mobile; ?><br />
                                             @endif
                                             <?php if (isset($row->dob) && $row->dob != '0001-01-01' && $row->dob != '1000-01-01') {
                                                    echo date('m/d/Y', strtotime($row->dob));
                                                } ?> @if($row->gender !="")(<?php echo $row->gender; ?>) @endif<br />

                                             <?php echo $row->name; ?><br />
                                         </td>
                                         <td>{{ $row->assignToUser!=null && isset($row->assignToUser->first_name) ? $row->assignToUser->first_name.' '.$row->assignToUser->last_name : 'N/A' }}</td>
                                         <td>
                                             @if($row->due_date!='')

                                             <?php if ($row->due_date != '1969-12-31' && $row->due_date != "0000-00-00") {
                                                    echo date('m/d/Y', strtotime($row->due_date));
                                                } ?>

                                             @endif</td>

                                         <td>
                                            @if(strtolower($row->type) == 'caregiver')
                                                @if(isset($row->appointment_date))
                                                    <label for="" class="badge badge-success">Schedule Appointment</label> <br/>
                                                    <?php if ($row->appointment_date != '') {
                                                            echo date('m/d/Y', strtotime($row->appointment_date));
                                                        } ?> <?php if ($row->start_time != '' && $row->end_time) {
                                                                    $start_time = date('h:i A', strtotime($row->start_time));
                                                                    $end_time = date('h:i A', strtotime($row->end_time));
                                                                ?><br /><?php
                                                                        echo $start_time . ' - ' . $end_time;
                                                                    } ?>
                                                        <br />
                                                    <?php echo $row->location_name; ?><br />
                                                @endif
                                                @if(isset($row->telehealth_date_time))
                                                    @if(isset($row->appointment_date))
                                                        <hr/>
                                                    @endif
                                                    <label for="" class="badge badge-primary">Telehealth Appointment</label>
                                                    <br/>
                                                    {{date('m/d/Y', strtotime($row->telehealth_date_time))}}<br />
                                                    {{$row->telehealth_time_frame ?: $row->telehealth_time_slot}} <br/>
                                                    Nurse: {{$row->telehealth_nurse}} <br/>
                                                @endif
                                             @endif
                                             @if(strtolower($row->type) == 'patient')
                                                @if ($row->appointment_date != '')
                                                    <label for="" class="badge badge-success">Schedule Appointment</label> <br/>
                                                    {{date('m/d/Y h:i A', strtotime($row->appointment_date))}}
                                                @endif
                                                @if(isset($row->telehealth_date_time))
                                                    @if(isset($row->appointment_date))
                                                        <hr/>
                                                    @endif
                                                    <label for="" class="badge badge-primary">Telehealth Appointment</label>
                                                    <br/>
                                                    {{date('m/d/Y', strtotime($row->telehealth_date_time))}}<br />
                                                    {{$row->telehealth_time_frame ?: $row->telehealth_time_slot}} <br/>
                                                    Nurse: {{$row->telehealth_nurse}} <br/>
                                                @endif
                                             @endif
                                         </td>

                                         <td><?php echo date('m/d/Y h:i A', strtotime($row->created_date)); ?><br />
                                             {{$row->created_by_username}}
                                         </td>
                                         <td> @if($row->fu_date!='')

                                             {{($row->fu_date !='' && $row->fu_date!='1969-12-31') ? date('m/d/Y', strtotime($row->fu_date)) : null}} <br />
                                             @endif
                                         </td>
                                         <td> @if($row->inservice_datetime!='')

                                             {{($row->inservice_datetime !='' && $row->inservice_datetime!='1969-12-31') ? date('m/d/Y  h:i A', strtotime($row->inservice_datetime)) : null}} <br />
                                             @endif
                                         </td>
                                         <td> @if($row->completed_date !='' && $row->completed_date !="0000-00-00 00:00:00")

                                             {{($row->completed_date !='' && $row->completed_date!='1969-12-31') ? date('m/d/Y  h:i A', strtotime($row->completed_date)) : null}} <br />
                                             @endif
                                         </td>
                                         <td>
                                             @if($row->follow_date!='')

                                             {{($row->follow_date !='' && $row->follow_date!='1969-12-31') ? date('m/d/Y  h:i A', strtotime($row->follow_date)) : null}} <br />
                                             @endif
                                         </td>
                                         @if($auth->agency_fk ==106 || $auth->id ==482)

                                         <td>
                                             @if($row->traning_due_date!='')

                                             {{($row->traning_due_date !='' && $row->traning_due_date!='1969-12-31') ? date('m/d/Y', strtotime($row->traning_due_date)) : null}} <br />
                                             @endif
                                         </td>
                                         @endif
                                         @if (in_array($user->user_type_fk, [3, 184]))
                                         <td>
                                             @if($row->training_status!='')

                                             {{($row->training_status !="")?$row->training_status:'N/A'}}
                                             @endif
                                         </td>
                                         <td>
                                             @if($row->last_status_update !='' && $row->last_status_update != "0000-00-00 00:00:00")

                                             {{ date('m/d/Y h:i A',strtotime($row->last_status_update))}}
                                             @endif
                                             <br>
                                             @if(isset($row->statusUpdatedUsers->id))
                                             {{$row->statusUpdatedUsers->first_name.' '.$row->statusUpdatedUsers->last_name}}
                                             @endif
                                         </td>
                                         <td>
                                        @if($row->referral_type !="")
                                        {{ ucfirst($row->referral_type)}}
                                        @else
                                            @if($row->hha_id !="" || $row->link_hha_caregiver !="" || $row->link_hha_patient !="")
                                                HHA Exchange

                                                @elseif($row->alaycare_id !="")
                                                Alayacare
                                                @elseif($row->robort_id !="")

                                                Remote Focus
                                                @elseif($row->platform_type =="VA")

                                                Visiting Aid
                                                @endif
                                        @endif
                                        </td>
                                         @endif

                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))

                                         <td>

                                             @php
                                                $isAgencyUser = $user->agency_fk != null;
                                                $canArchive = !$isAgencyUser || $row->enable_portal_archive == 1;
                                            @endphp

                                            @if($canArchive)

                                                @if($row->archived_at !='')
                                                    <a title="Unarchive" href="javascript:void(0)" onclick="getUnArchiveById(<?php echo $row->id; ?>)">
                                                        <i class="fa fa-file-archive-o"></i>
                                                    </a>
                                                @else
                                                    <a title="Archive" href="javascript:void(0)" onclick="getArchiveById(<?php echo $row->id; ?>)">
                                                        <i class="fa fa-archive"></i>
                                                    </a>
                                                @endif

                                            @endif


                                             <?php if (in_array($user->user_type_fk, array(3, 184))) {

                                                if(isset($row->telehealth_key) && !empty($row->telehealth_key)) {
                                                    if (strtolower($row->status) == 'pending') { ?>
                                                        <a href="javascript:void(0)" onclick="getSendSMS(<?php echo $row->id; ?>)">Telehealth Send SMS</a>
                                                    <?php }else if($row->status == 'booked'){ ?>
                                                        <a href="javascript:void(0)" onclick="getRemainderSendSMS(<?php echo $row->id; ?>)">Telehealth Reminder SMS</a>
                                                    <?php } } else { ?>
                                                        <?php if (strtolower($row->type) == 'caregiver') { if (strtolower($row->status) == 'pending') {?>
                                                            <a href="javascript:void(0)" onclick="getSendSMS(<?php echo $row->id; ?>)">Send SMS</a>
                                                        <?php } else if ($row->status == 'booked') { ?>
                                                            <a href="javascript:void(0)" onclick="getRemainderSendSMS(<?php echo $row->id; ?>)">Reminder SMS</a>
                                                            <?php } ?>
                                                        <?php } ?>

                                                    <?php }

                                                }?>
                                             @if($auth->user_type_fk ==184)
                                             <a href="javascript:void(0)" data-toggle="modal" data-target="#serviceByPatientTypeModal" onclick="getPatientId('<?php echo $row->id; ?>','{{ $row->type}}','{{ $row->agency_id}}')">Request Service</a>
                                             @endif
                                             @if($auth->agency_fk != "" && $agency_enable_review)
                                             <br>
                                             @if($row->is_reviewed == 1)
                                             <a href="javascript:void(0)" title="Mark as Unreviewed" onclick="confirmUnreview(<?php echo $row->id; ?>, '<?php echo addslashes($row->first_name.' '.$row->last_name); ?>')" style="text-decoration:none;">
                                                 <span class="badge badge-secondary" style="font-size:11px;cursor:pointer;"><i class="fa fa-undo"></i> Unreview</span>
                                             </a>
                                             @else
                                             <a href="javascript:void(0)" title="Mark as Reviewed" onclick="confirmReview(<?php echo $row->id; ?>, '<?php echo addslashes($row->first_name.' '.$row->last_name); ?>')" style="text-decoration:none;">
                                                 <span class="badge badge-warning" style="font-size:11px;cursor:pointer;color:#fff;"><i class="fa fa-check"></i> Review</span>
                                             </a>
                                             @endif
                                             @endif
                                         </td>
                                         @endif
                                     </tr>
                                 <?php }
                                } else { ?>
                                 <tr>
                                     <td colspan="20">
                                         <center><b>Data not found</b></center>
                                     </td>
                                 </tr>
                             <?php } ?>
                         </tbody>
                     </table>

                     <div class="pull-right pegination-margin">

                            {{$query->appends(request()->query())->links()}}
                     </div>
                 </div>
             </div>

         </div>

     </div>

     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     <div class="modal fade" id="exampleModal-patient-import" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
             <div class="modal-content border-0 shadow-lg">
                 <div class="modal-header  text-white" style="background-color:#000000 !important">
                     <h5 class="modal-title font-weight-bold" id="ModalLabel">
                         <i class="mdi mdi-file-import mr-2"></i>Import Appointments
                     </h5>
                     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="appps_id">
                         <span aria-hidden="true"  style="color:white !important">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body p-4">
                     <form class="forms-sample" name="adduser" method="post" id="importCsvForm">
                         <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                         <?php if ($user->agency_fk == '') { ?>
                             <div class="form-group">
                                 <label for="import_agency_ids" class="font-weight-semibold">
                                     <i class="mdi mdi-office-building text-primary mr-1"></i>Agency
                                     <span class="text-danger">*</span>
                                 </label>
                                 <select name="agency_id" class="form-control form-control-lg" id="import_agency_ids">
                                     <option value="">Select Agency</option>
                                     <?php if (count($agencyList) > 0) {
                                            foreach ($agencyList as $vsl) {
                                        ?>
                                             <option value="<?php echo $vsl->id; ?>"><?php echo $vsl->agency_name; ?></option>
                                     <?php }
                                        } ?>
                                 </select>
                                 <span class="error mt-2 text-danger d-block" id="agency_error"></span>
                             </div>
                         <?php } else { ?>
                             <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                         <?php } ?>

                         <div class="form-group">
                             <label for="upload_csv_file_id" class="font-weight-semibold">
                                 <i class="mdi mdi-file-upload text-primary mr-1"></i>Upload CSV File
                                 <span class="text-danger">*</span>
                             </label>
                             <div class="custom-file">
                                 <input type="file" class="custom-file-input" id="upload_csv_file_id" name="images" accept=".csv">
                                 <label class="custom-file-label" for="upload_csv_file_id">Choose file...</label>
                             </div>
                             <small class="form-text text-muted mt-2">
                                 <i class="mdi mdi-information-outline"></i> Only CSV files are allowed
                             </small>
                             <span class="error mt-2 text-danger d-block" id="images_error"></span>
                         </div>

                         <div class="alert alert-info border-0 shadow-sm">
                             <div class="d-flex align-items-center">
                                 <i class="mdi mdi-download-circle-outline text-info mr-2" style="font-size: 24px;"></i>
                                 <div>
                                     <strong>Need help?</strong>
                                     <p class="mb-0">Download the <a href="{{ URL::to('/sample.csv') }}" class="alert-link font-weight-bold">Sample CSV template</a> to see the correct format.</p>
                                 </div>
                             </div>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer border-top-0 bg-light">

                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" onclick="saveImportCsvFile()" id="seacu" class="btn btn-primary btn-sm px-4  mr-2">
                            <span class="spinner-border spinner-border-sm d-none" id="loaderss_id" role="status" aria-hidden="true"></span>

                            <span id="btn-text">Import</span>

                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>

                    </div>

                 </div>
             </div>
         </div>
     </div>

     <script>
         // Update file input label with selected filename
         document.addEventListener('DOMContentLoaded', function() {
             const fileInput = document.getElementById('upload_csv_file_id');
             if (fileInput) {
                 fileInput.addEventListener('change', function(e) {
                     const fileName = e.target.files[0]?.name || 'Choose file...';
                     const label = e.target.nextElementSibling;
                     if (label) {
                         label.textContent = fileName;
                     }
                 });
             }
         });
     </script>
     <div class="modal fade" id="exampleModal-patient-view-import" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
             <div class="modal-content border-0 shadow-lg">
                 <div class="modal-header text-white" style="background-color:#000000 !important">
                     <h5 class="modal-title font-weight-bold" id="ModalLabel">
                         <i class="mdi mdi-table-edit mr-2"></i>Map CSV Columns
                     </h5>
                     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"  style="color:white !important">&times;</span>
                     </button>
                 </div>

                 <form action="<?php echo URL::to('/'); ?>/patient/patient-import" method="post" enctype="multipart/form-data" id="submitId">
                     <input type="hidden" name="order_data" value="" id="order_data">
                     <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                     <div class="modal-scroll-mr">
                     <div class="modal-scroll-inside">
                     <div class="modal-body p-4" id="formnewNN">
                         <!-- CSV mapping content will be loaded here dynamically -->
                     </div>
                     </div>
                     </div>

                     <div class="modal-footer border-top-0 bg-light">
                         <div class="d-flex justify-content-between align-items-center w-100">
                             <div>
                                 <span class="text-muted">
                                     <i class="mdi mdi-information-outline mr-1"></i>
                                     <small>Map each CSV column to the corresponding field</small>
                                 </span>
                                 <span class="error mt-2 text-danger d-block" id="row_error"></span>
                             </div>
                             <div>
                                <button type="submit" name="submit" class="btn btn-primary btn-sm px-4 mr-2">
                                    <span class="spinner-border spinner-border-sm d-none" id="import_loaderss_id" role="status" aria-hidden="true"></span>
                                    </i>Confirm & Import
                                 </button>
                                 <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                                    </i>Cancel
                                 </button>

                             </div>
                         </div>
                     </div>
                 </form>
             </div>
         </div>
     </div>

     <!-- Export Column Selection Modal -->
     <div class="modal fade" id="exportColumnModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
             <div class="modal-content border-0 shadow-lg">
                 <div class="modal-header text-white" style="background-color:#000000 !important">
                     <h5 class="modal-title font-weight-bold" id="exportModalLabel">
                         <i class="mdi mdi-table-column mr-2"></i>Select Export Columns
                     </h5>
                     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true" style="color:white !important">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body p-4">
                     <div class="row mb-3">
                         <div class="col-12">
                             <div class="d-flex justify-content-between align-items-center mb-3">
                                 <p class="text-muted mb-0">
                                     <i class="mdi mdi-information-outline mr-1"></i>
                                     Select the columns you want to include in the export
                                 </p>
                                 <div>
                                     <button type="button" class="btn btn-sm btn-outline-primary mr-2" id="selectAllColumns">
                                         <i class="mdi mdi-check-all"></i> Select All
                                     </button>
                                     <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">
                                         <i class="mdi mdi-close-box-multiple-outline"></i> Deselect All
                                     </button>
                                 </div>
                             </div>
                             <hr>
                         </div>
                     </div>
                     <div class="row" id="exportColumnsList">
                         <!-- Columns will be dynamically added here -->
                     </div>
                 </div>
                 <div class="modal-footer border-top-0 bg-light">
                     <div class="d-flex justify-content-between align-items-center w-100">
                         <span class="text-muted">
                             <i class="mdi mdi-table-check mr-1"></i>
                             <small><strong id="selectedCount">0</strong> columns selected</small>
                         </span>
                         <div>
                             <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="confirmExport">
                                 <i class="mdi mdi-file-export"></i> Export Selected
                             </button>
                             <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                                 <i class="mdi mdi-close"></i> Cancel
                             </button>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <style>
         #exampleModal-patient-view-import .modal-header {
             border-bottom: 0;
             border-top-left-radius: 0rem;
             border-top-right-radius: 0rem;
         }

         #exampleModal-patient-view-import .modal-xl {
             max-width: 90%;
         }

         #exampleModal-patient-view-import .table-responsive {
             max-height: 500px;
             overflow-y: auto;
         }

         #exampleModal-patient-view-import .selectvalues {
             min-width: 150px;
             font-size: 0.875rem;
             height:36px !important
         }

         #exampleModal-patient-view-import .table th {
             position: sticky;
             top: 0;
             background-color: #f8f9fa;
             z-index: 10;
             box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
         }

         #exampleModal-patient-view-import .modal-footer {
             padding: 0.5rem 1.5rem;
         }

         /* Export Column Modal Styles */
         #exportColumnModal .column-toggle-item {
             margin-bottom: 12px;
         }

         #exportColumnModal .column-toggle-label {
             cursor: pointer;
             padding: 10px 15px;
             border: 1px solid #dee2e6;
             border-radius: 5px;
             transition: all 0.3s ease;
             background-color: #fff;
             display: flex;
             align-items: center;
             justify-content: space-between;
         }

         #exportColumnModal .column-toggle-label:hover {
             background-color: #f8f9fa;
             border-color: #007bff;
         }

         #exportColumnModal .column-toggle-input:checked + .column-toggle-label {
             background-color: #e7f3ff;
             border-color: #007bff;
             font-weight: 500;
         }

         #exportColumnModal .column-toggle-input {
             display: none;
         }

         #exportColumnModal .toggle-switch {
             width: 45px;
             height: 24px;
             background-color: #ccc;
             border-radius: 12px;
             position: relative;
             transition: background-color 0.3s;
         }

         #exportColumnModal .toggle-switch::after {
             content: '';
             position: absolute;
             width: 18px;
             height: 18px;
             border-radius: 50%;
             background-color: white;
             top: 3px;
             left: 3px;
             transition: transform 0.3s;
         }

         #exportColumnModal .column-toggle-input:checked + .column-toggle-label .toggle-switch {
             background-color: #28a745;
         }

         #exportColumnModal .column-toggle-input:checked + .column-toggle-label .toggle-switch::after {
             transform: translateX(21px);
         }

         #exportColumnModal .modal-body {
             max-height: 500px;
             overflow-y: auto;
         }
     </style>


     @include('patient._partial.service_requests.modal.add_service_request_modal')

     @include('patient._partial.modal.bulk_assign_user_modal')

     @include('include/footer')
     <script>
        var _GET_BRANCHES ="{{ url('get-branches')}}";
        var SELECTED_BRANCH_ID ="{{ $filter_branch_id??'' }}";
     </script>
     <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
     <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
     <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
     <script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>
     <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
     <script src="{{ asset('assets/modulejs/patient/patient_demographic.js')}}?time={{ env('timestamp')}}"></script>
     <script src="{{ asset('assets/modulejs/patient/patient_export_columns.js')}}?time={{ env('timestamp')}}"></script>
     <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
     <script>
         var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
         var _PATIENT_TYPE_WISE_SERVICE_REQUEST_SAVE = "{{ url('save-patient-type-wise-services') }}";
         var _PATIENT_WISE_SERVICE_REQUESTED_LIST = "{{ url('patient-wise-service-requested-list') }}";
         var _CSRF_TOKEN = "{{ csrf_token()}}";
         var _FLAG = 1;
         var _BULK_APPOINTMENT_DELETE ="{{ url('bulk-appointments-delete')}}";
         var _GET_DEPARTMENT = "{{ url('tasks/get-task-dept') }}";
         /* ..Start.. For page refresh when search data then show search area */
         $(document).ready(function() {
             var url = window.location.search;
             var arguments = url.split('?')[1];

         });
         $(":input").inputmask();
         /* ..End.. For page refresh when search data then show search area */
         $("#searchbtns").click(function() {
             $("#search-div").toggle();
         });


      $(document).on("click", ".btnExport", function() {
    var authAgencyFk = "{{ $auth->agency_fk}}";
    var authId = "{{ $auth->id}}";
    var user_type_fk = "{{ $auth->user_type_fk}}";
    var due_date = $('#due_date').val();
    var sms_status = $('#sms_status').val();
    var status = $('#status_id').val();
    var agency_fk = $('#agency_fk').val();
    var first_name = $('#agency_name').val();
    var mobile = $('#mobile').val();
    var assign_user_id = $('#assign_user_id').val();
    var appointment_date = $('#appointment_date').val();
    var locationId = $('#locationId').val();
    var created_date = $('#created_date').val();
    var service_id = $('#service_id').val();
    var isArchived = $("#archived").is(':checked');
    var isReviewed = $("#show_reviewed").is(':checked');
    var isDiscipline = $("#diciplin_id").val();
    var type = $("#type").val();
    var patient_code = $("#patient_code").val();

    var inservice_date = $('#inservice_date').val();
    var completed_date = $('#completed_date').val();
    var follow_up_date = $('#follow_up_date').val();
    var traning_date = $('#traning_date').val();
    var traning_status = $('#training_status').val();
    var language_id = $('#language_id').val();
    var transition_aid = $('#transition_aid').val();
    var dob = $('#dob').val();
    var last_status_update = $("#last_status_update").val();
    var last_status_updated_by_id = $("#last_status_updated_by_id").val();
    var agency_filter_type = $("#agency_filter_type").val();
    var service_filter_type = $("#service_filter_type").val();
    var medication_list = $("#medication_list").val();
    var insurance_elg = $("#insurance_elg").val();
    var debug = '{{ $debug}}';
    var mdo_tag = $("#mdo_tag").val();
    var filter_branch_id = $("#filter_branch_id").val();
    var branch_filter_type = $("#branch_filter_type").val();
    var state = $("#state").val();
    var agency_status = $("#agency_status").val();
    var referral_type = $("#referral_type").val();
    var record_read = $("#record_read").is(':checked') == true ? 0 : 1;
    if (due_date == '' && sms_status == null && status == null && agency_fk == null && first_name == '' &&
    mobile == '' && assign_user_id == null && appointment_date == '' && locationId == null && type == null &&
    isDiscipline == null &&
    created_date == '' && service_id == null && isArchived != true && patient_code.trim() != "" && completed_date == '' &&
    follow_up_date == '' && traning_status == '' && language_id == '' && transition_aid == '' && dob == '') {
    alert('Please select or enter any one search text');
    return false;
    } else {
    sms_status = sms_status != null ? sms_status : '';
    status = status != null ? status : '';
    agency_fk = agency_fk != null ? agency_fk : '';
    first_name = first_name != null ? first_name : '';
    mobile = mobile != null ? mobile : '';
    assign_user_id = assign_user_id != null ? assign_user_id : '';
    due_date = due_date != null ? due_date : '';
    appointment_date = appointment_date != null ? appointment_date : '';
    locationId = locationId != null ? locationId : '';
    created_date = created_date != null ? created_date : '';
    service_id = service_id != null ? service_id : '';
    isDiscipline = isDiscipline != null ? isDiscipline : '';
    type = type != null ? type : '';
    patient_code = patient_code != null ? patient_code : '';
    inservice_date = inservice_date != null ? inservice_date : '';
    completed_date = completed_date != null ? completed_date : '';
    follow_up_date = follow_up_date != null ? follow_up_date : '';
    traning_date = traning_date != null ? traning_date : '';
    traning_status = traning_status != null ? traning_status : '';
    language_id = language_id != null ? language_id : '';
    transition_aid = transition_aid != null ? transition_aid : '';
    dob = dob != null ? dob : '';
    last_status_update = last_status_update != null ? last_status_update : '';
    last_status_updated_by_id = last_status_updated_by_id != null ? last_status_updated_by_id : '';
    medication_list = medication_list != null ? medication_list : '';
    insurance_elg = insurance_elg != null ? insurance_elg : '';
    mdo_tag = mdo_tag != null ? mdo_tag : '';
    filter_branch_id = filter_branch_id != null ? filter_branch_id : '';
    state = state != null ? state : '';
    agency_status = agency_status != null ? agency_status : '';
    referral_type = referral_type != null ? referral_type : '';
    record_read = record_read != null ? record_read : '';
    var links = "<?php echo URL::to('/'); ?>/patient/patient-export?sms_status=" + sms_status + "&status=" + status +
    "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
    service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
    "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
    created_date + "&is_archive=" + isArchived + "&is_reviewed=" + isReviewed + "&dicipline=" + isDiscipline + "&type=" + type + '&patient_code=' +
    patient_code + '&inservice_date=' + inservice_date + "&is_past_show={{ $isPastShow}}" + '&completed_date=' +
    completed_date + '&follow_up_date=' + follow_up_date + '&transition_aid=' + transition_aid + '&language_id=' +
    language_id + '&dob=' + dob

    if (authAgencyFk == 106 || authId == 482) {
    links = links + "&traning_date=" + traning_date;
    }
    if (authAgencyFk != "") {
    var id = $('#created_by').val();
    if (id != "") {
    links = links + "&created_by=" + id;
    }
    } else {
    var id = $('#created_by_ny_id').val();


    links = links + "&created_by=" + id;
    }
    if (user_type_fk == 184) {
    links = links + "&traning_status=" + traning_status;
    }

    if (user_type_fk == 184 || user_type_fk == 6) {
        links = links + "&last_status_update=" + last_status_update + "&last_status_updated_by_id=" + last_status_updated_by_id;
    }
    if (user_type_fk == 184){
        links = links + "&insurance_elg=" + insurance_elg;
        links = links + "&medication_list=" + medication_list;
        links = links + "&mdo_tag=" + mdo_tag;
    }
    links = links + "&agency_filter_type=" + agency_filter_type;
    links = links + "&service_filter_type=" + service_filter_type;

    links = links + "&filter_branch_id=" + filter_branch_id;
    links = links + "&branch_filter_type=" + branch_filter_type;
    links = links + "&state=" + state;
    links = links + "&agency_status=" + agency_status;
    links = links + "&referral_type=" + referral_type;
    links = links + "&record_read=" + record_read;
    links = links + '&agency_rep='+$('#agency_updated_by').val()
    if(debug !=""){
    links = links + "&debug="+debug;
    }
    // Store export data globally for the modal
    exportData = {
    authAgencyFk: authAgencyFk,
    authId: authId,
    user_type_fk: user_type_fk,
    sms_status: sms_status,
    status: status,
    agency_fk: agency_fk,
    first_name: first_name,
    mobile: mobile,
    assign_user_id: assign_user_id,
    due_date: due_date,
    appointment_date: appointment_date,
    locationId: locationId,
    created_date: created_date,
    service_id: service_id,
    isDiscipline: isDiscipline,
    type: type,
    patient_code: patient_code,
    inservice_date: inservice_date,
    completed_date: completed_date,
    follow_up_date: follow_up_date,
    traning_date: traning_date,
    traning_status: traning_status,
    language_id: language_id,
    transition_aid: transition_aid,
    dob: dob,
    last_status_update: last_status_update,
    last_status_updated_by_id: last_status_updated_by_id,
    medication_list: medication_list,
    insurance_elg: insurance_elg,
    mdo_tag: mdo_tag,
    filter_branch_id: filter_branch_id,
    state: state,
    isArchived: isArchived,
    isReviewed: isReviewed,
    agency_filter_type: agency_filter_type,
    service_filter_type: service_filter_type,
    branch_filter_type: branch_filter_type,
    debug: debug,
    baseUrl: "<?php echo URL::to('/'); ?>",
    isPastShow: "{{ $isPastShow}}",
    agency_status: agency_status,
    referral_type: referral_type,
    record_read:record_read,
    agency_updated_by:$('#agency_updated_by').val()
    };

    // Show column selection modal
    populateExportColumns();

    // Try Bootstrap modal first, fallback to jQuery
    if (typeof $.fn.modal === 'function') {
    $('#exportColumnModal').modal('show');
    } else {
    $('#exportColumnModal').addClass('show').css('display', 'block');
    $('body').addClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').append('<div class="modal-backdrop fade show"></div>');
    }
    }
    });

         $(document).on("click", ".searchAppoinment", function() {
             var authAgencyFk = "{{ $auth->agency_fk}}";
             var authId = "{{ $auth->id}}";
             var user_type_fk = "{{ $auth->user_type_fk}}";
             var due_date = $('#due_date').val();
             var sms_status = $('#sms_status').val();
             var status = $('#status_id').val();
             var agency_fk = $('#agency_fk').val();
             var first_name = $('#agency_name').val();
             var mobile = $('#mobile').val();
             var assign_user_id = $('#assign_user_id').val();
             var appointment_date = $('#appointment_date').val();
             var locationId = $('#locationId').val();
             var created_date = $('#created_date').val();
             var service_id = $('#service_id').val();
             var isArchived = $("#archived").is(':checked');
             var isReviewed = $("#show_reviewed").is(':checked');
             var isDiscipline = $("#diciplin_id").val();
             var type = $("#type").val();
             var patient_code = $("#patient_code").val();
             var inservice_date = $("#inservice_date").val();
             var completed_date = $("#completed_date").val();
             var follow_up_date = $("#follow_up_date").val();
             var traning_date = $("#traning_date").val();
             var traning_status = $("#training_status").val();
             var language_id = $("#language_id").val();
             var transition_aid = $("#transition_aid").val();
             var dob = $("#dob").val();
             var last_status_update = $("#last_status_update").val();
             var last_status_updated_by_id = $("#last_status_updated_by_id").val();
             var last_status_updated_by_name = $("#last_status_updated_by_name").val();
             var agency_filter_type = $("#agency_filter_type").val();
             var service_filter_type = $("#service_filter_type").val();
             var medication_list = $("#medication_list").val();
             var insurance_elg = $("#insurance_elg").val();
             var mdo_tag = $("#mdo_tag").val();
             var filter_branch_id = $("#filter_branch_id").val();
             var branch_filter_type = $("#branch_filter_type").val();
             var state = $("#state").val();
             var agency_status = $("#agency_status").val();
             var referral_type = $("#referral_type").val();
             var record_read = $("#record_read").is(':checked') == true ? 0 : 1;   
             if (due_date == '' && sms_status == null && status == null && agency_fk == null && first_name == '' &&
                 mobile == '' && assign_user_id == null && appointment_date == '' && locationId == null && type == null &&
                 isDiscipline == null &&
                 created_date == '' && service_id == null && isArchived != true && completed_date == '' && follow_up_date == '' && traning_date == '' && traning_status == "" && dob == "" && medication_list == "" && insurance_elg == "" && mdo_tag == "" && filter_branch_id == "" && state == "" && agency_status == ""  && record_read == "") {
                 alert('Please select or enter any one search text');
                 return false;
             } else {
                 sms_status = sms_status != null ? sms_status : '';
                 status = status != null ? status : '';
                 agency_fk = agency_fk != null ? agency_fk : '';
                 first_name = first_name != null ? first_name : '';
                 mobile = mobile != null ? mobile : '';
                 assign_user_id = assign_user_id != null ? assign_user_id : '';
                 due_date = due_date != null ? due_date : '';
                 appointment_date = appointment_date != null ? appointment_date : '';
                 locationId = locationId != null ? locationId : '';
                 created_date = created_date != null ? created_date : '';
                 service_id = service_id != null ? service_id : '';
                 isDiscipline = isDiscipline != null ? isDiscipline : '';
                 type = type != null ? type : '';
                 patient_code = patient_code != null ? patient_code : '';
                 inservice_date = inservice_date != null ? inservice_date : '';
                 completed_date = completed_date != null ? completed_date : '';
                 follow_up_date = follow_up_date != null ? follow_up_date : '';
                 traning_date = traning_date != null ? traning_date : '';
                 traning_status = traning_status != null ? traning_status : '';
                 transition_aid = transition_aid != null ? transition_aid : '';
                 language_id = language_id != null ? language_id : '';
                 dob = dob != null ? dob : '';
                 last_status_update = last_status_update != null ? last_status_update : '';
                 last_status_updated_by_id = last_status_updated_by_id != null ? last_status_updated_by_id : '';
                 last_status_updated_by_name = last_status_updated_by_name != null ? last_status_updated_by_name : '';
                 agency_filter_type = agency_filter_type != null ? agency_filter_type : '';
                 service_filter_type = service_filter_type != null ? service_filter_type : '';
                 medication_list = medication_list != null ? medication_list : '';
                 insurance_elg = insurance_elg != null ? insurance_elg : '';
                 mdo_tag = mdo_tag != null ? mdo_tag : '';
                 filter_branch_id = filter_branch_id != null ? filter_branch_id : '';
                 branch_filter_type = branch_filter_type != null ? branch_filter_type : '';
                 state = state != null ? state : '';
                 agency_status = agency_status != null ? agency_status : '';
                 referral_type = referral_type != null ? referral_type : '';
                 record_read = record_read != null ? record_read : '';
                 var links = "<?php echo URL::to('/'); ?>/appointment?sms_status=" + sms_status + "&status=" + status +
                     "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
                     service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
                     "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
                     created_date + "&is_archive=" + isArchived + "&is_reviewed=" + isReviewed + "&dicipline=" + isDiscipline + "&type=" + type + '&patient_code=' + patient_code + '&inservice_date=' + inservice_date + "&is_past_show={{ $isPastShow}}" + '&completed_date=' + completed_date + '&follow_up_date=' + follow_up_date + '&transition_aid=' + transition_aid + '&language_id=' + language_id + '&dob=' + dob + '&medication_list=' + medication_list + '&insurance_elg=' + insurance_elg + '&mdo_tag=' + mdo_tag + '&filter_branch_id=' + filter_branch_id + '&state=' + state + '&agency_status=' + agency_status + '&referral_type=' + referral_type + '&record_read=' + record_read;
                 if (authAgencyFk == 106 || authId == 482) {
                     links = links + "&traning_date=" + traning_date;
                 }

                 if (authAgencyFk != "") {
                     var id = $('#created_by').val();
                     links = links + "&created_by=" + id + '&last_status_update=' + last_status_update + '&last_status_updated_by_id=' + last_status_updated_by_id + '&last_status_updated_by_name=' + last_status_updated_by_name;
                 } else {
                     var id = $('#created_by_ny_id').val();
                     var name = $('#created_by_ny_name').val();
                     links = links + "&created_by_ny_id=" + id + '&created_by_ny_name=' + name;
                 }

                 if (user_type_fk == 184) {
                     links = links + "&traning_status=" + traning_status + '&last_status_update=' + last_status_update + '&last_status_updated_by_id=' + last_status_updated_by_id + '&last_status_updated_by_name=' + last_status_updated_by_name
                 }
                 links = links + "&agency_filter_type=" + agency_filter_type;
                 links = links + "&service_filter_type=" + service_filter_type;
                 links = links + "&branch_filter_type=" + branch_filter_type;
                  links = links + "&agency_updated_by=" + $('#agency_updated_by').val()+"&agency_updated_by_id=" + $('#agency_updated_by_id').val()+"&agency_updated_by_name=" + $('#agency_updated_by_name').val()+"&token_input_agency_id="+$('#token_input_agency_id').val();
                 window.location.href = links;
             }
         });


         $(function() {
             var start = moment().subtract(0, 'days');
             var end = moment();
             $('.datepickernn').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.datepickernn').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });
             $('.inservice_date').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.inservice_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.inservice_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });
             $('.due_datenn').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.due_datenn').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

             $('.completed_date').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.completed_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })
            $('.completed_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

             $('.follow_up_date').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.follow_up_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.follow_up_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

             $('.traning_date').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],
                 }
             }, function(chosen_date, end_date) {

                 $('.traning_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.traning_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });
         });
     </script>

     <script>
         $(".datepicker").datepicker();

         $(function() {
             var start = moment().subtract(0, 'days');
             var end = moment();


             $('.datepicker1').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],

                 }
             }, function(chosen_date, end_date) {

                 $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })

             $('.datepicker1').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

         });
         // Binds the hidden input to be used as datepicker.
         $('.datepicker-input').datepicker({
             dateFormat: 'mm/dd/yy',
             onClose: function(dateText, inst) {
                 // When the date is selected, copy the value in the content editable div.
                 // If you don't need to do anything on the blur or focus event of the content editable div, you don't need to trigger them as I do in the line below.
                 if (dateText != '') {
                     $(this).parent().find('.date').focus().html(dateText).blur();
                 }
             }

         });



         toastr.options.closeButton = true;
         toastr.options.tapToDismiss = false;
         toastr.options = {
             "closeButton": true,
             "debug": false,
             "newestOnTop": false,
             "progressBar": false,
             "positionClass": "toast-top-right",
             "preventDuplicates": false,
             "onclick": null,
             "showDuration": "300",
             "hideDuration": "500",
             "timeOut": "3000",
             "extendedTimeOut": 0,
             "showEasing": "swing",
             "hideEasing": "linear",
             "showMethod": "fadeIn",
             "hideMethod": "fadeOut",
             "tapToDismiss": false
         };

         function getSendSMS(id) {
             var cons = confirm("Are you want to send sms?");
             buttons = $(this).closest('.master-status-dropdown');

             if (cons == true) {
                 $.ajax({
                     async: false,
                     global: false,
                     type: "GET",
                     url: "<?php echo URL::to('/'); ?>/patient/send-sms/" + id,
                     success: function(res) {
                         msg = 'SMS successfully sent.';
                         toastr.success('SMS successfully sent.');
                     }
                 })
             }


         }

         function getRemainderSendSMS(id) {
             var cons = confirm("Do you want to send sms?");


             if (cons == true) {
                 $.ajax({
                     async: false,
                     global: false,
                     type: "GET",
                     url: "<?php echo URL::to('/'); ?>/patient/send-remainder-sms/" + id,
                     success: function(res) {
                         msg = 'SMS successfully sent.';
                         toastr.success('Reminder sms successfully sent.');
                     }
                 })
             }


         }

         function saveImportCsvFile() {
             $('#loaderss_id').removeClass('d-none');
             $('#btn-text').text('Loading ...');
             var agency_ids = $('#import_agency_ids').val();
             var fimagesG = $('input[name="images"]').prop('files');
             var cnt = 0;
             $('#images_error').html("");
             $('#agency_error').html("");
             <?php
                if ($user->agency_fk == '') {
                ?>
                 if (agency_ids == '') {
                     $('#agency_error').html("Please select Agency");
                     cnt = 1;
                 }
             <?php } ?>
             if (fimagesG.length == 0) {
                 $('#images_error').html("Please select Upload Csv File");
                 cnt = 1;
             } else {
                 var FileUploadPath = fimagesG[0].name;
                 var Extension = FileUploadPath.substring(
                     FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
                 if (Extension == 'xlsx' || Extension == 'csv' || Extension == 'xls') {

                 } else {
                     $('#images_error').html("Only csv or excel file allowed");
                     cnt = 1;

                 }
             }
             if (cnt == 1) {
                $('#loaderss_id').addClass('d-none');
                $('#btn-text').text('Import');
                return false;
             } else {
                 var foms = $('#importCsvForm')[0];
                 var formData = new FormData(foms);
                 formData.append("_token", "<?php echo csrf_token(); ?>");

                 $.ajax({
                     async: false,
                     global: false,
                     processData: false,
                     contentType: false,
                     type: "POST",
                     url: "<?php echo URL::to('/patient/importdata'); ?>",
                     data: formData,
                     success: function(res) {

                        var modalEl = document.getElementById('exampleModal-patient-view-import');
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        $('#formnewNN').html(res);

                         setTimeout(function(e) {
                            $('#btn-text').text('Import');
                            $('#loaderss_id').addClass('d-none');
                         }, 1000);
                         $('#appps_id').click();
                    },
                    error:function(jqr){
                        $('#btn-text').text('Import');
                        $('#loaderss_id').addClass('d-none');
                    }
                 })
             }
         }
         $('#submitId').submit(function(e) {
            $('#import_loaderss_id').removeClass('d-none');
            $(this).find('[type="submit"]').prop('disabled', true).text('Importing...');
             $('#row_error').html("");
             var selected = [];
             var selected_data = [];


             $.each($(".selectvalues option:selected"), function() {
                 selected.push($(this).val());
                 if ($(this).val() != "") {
                     selected_data.push($(this).val());
                 }
             });

             $('#order_data').val(selected.join());

             if (selected_data.length < 3) {
                $('#import_loaderss_id').addClass('d-none');
                $(this).find('[type="submit"]').prop('disabled', false).text('Confirm & Import');
                 toastr.error('Please map all required fields')
                 return false;
             }

            const required = ['type', 'dob', 'first_name', 'last_name','mobile','gender','service_id'];
            const missingFields = [];
            hasError = false;
            var errorCount = 1;
            required.forEach(function(field) {
                if (!selected_data.includes(field)) {
                    if(field =='type'){
                        missingFields.push(errorCount+'. Record Type (only Caregiver or Patient)');
                    }
                    if(field =='dob'){
                        missingFields.push(errorCount+'. Date of Birth');
                    }
                    if(field =='first_name'){
                        missingFields.push(errorCount+'. First Name');
                    }
                    if(field =='last_name'){
                        missingFields.push(errorCount+'. Last Name');
                    }
                    if(field =='mobile'){
                        missingFields.push(errorCount+'.Mobile');
                    }
                    if(field =='gender'){
                        missingFields.push(errorCount+'. Gender (only Male or Female)');
                    }
                    if(field =='service_id'){
                        missingFields.push(errorCount+'. Services');
                    }
                    errorCount++;
                    hasError = true;
                }
            });

            if (hasError) {
                $('#import_loaderss_id').addClass('d-none');
                $(this).find('[type="submit"]').prop('disabled', false).text('Confirm & Import');
                toastr.error('Please map all required fields:<br>' + missingFields.join('<br>'), '', { allowHtml: true });
                return false;
            }

         });
     </script>
     <script type="text/javascript">
         $(function() {
             $(".wmd-view-topscroll").scroll(function() {
                 $(".wmd-view")
                     .scrollLeft($(".wmd-view-topscroll").scrollLeft());
             });
             $(".wmd-view").scroll(function() {
                 $(".wmd-view-topscroll")
                     .scrollLeft($(".wmd-view").scrollLeft());
             });
         });


         $("#main_checkBox1").click(function() {
             var names = $("#main_checkBox1").is(":checked");

             if (names == true) {
                 $('.cbox_id').prop('checked', true);
             } else {
                 $('.cbox_id').prop('checked', false);
             }
         });


         function getUnArchiveById(id) {
             var consi = confirm('Are you sure unarchive this record?');
             if (consi == true) {
                 var selected_data = [];
                 selected_data.push(id);
                 $.ajax({
                     async: false,
                     global: false,
                     type: "POST",
                     url: "<?php echo URL::to('/'); ?>/patient/patient-unarchive",
                     data: {
                         '_token': "<?php echo csrf_token(); ?>",
                         'patient_id': selected_data.join()
                     },
                     success: function(res) {
                         if (res == 1) {
                             toastr.success('Appointment successfully unarchive.');
                             location.reload();
                         } else {
                             toastr.error('Sorry, something went wrong. Please try again.');
                         }
                     }
                 });
             }
         }

         function getArchiveById(id) {
             var consi = confirm('Are you sure archive this record?');

             var selected_data = [];
             selected_data.push(id);
             if (consi == true) {
                 $.ajax({
                     async: false,
                     global: false,
                     type: "POST",
                     url: "<?php echo URL::to('/'); ?>/patient/patient-archive",
                     data: {
                         '_token': "<?php echo csrf_token(); ?>",
                         'patient_id': selected_data.join()
                     },
                     success: function(res) {
                         if (res == 1) {
                             toastr.success('Appointment successfully archive.');
                             location.reload();
                         } else {
                             toastr.error('Sorry, something went wrong. Please try again.');
                         }
                     }
                 })
             }

         }

         var urlToken = "{{ url('search-all-users') }}";
         var empId = '{{ $created_by_id }}';
         var empName = '{{ $created_by_name }}';
         $("#created_by_ny").tokenInput(urlToken, {
             appendTo: 'body',
             tokenLimit: 1,
             zindex: 9999,
             prePopulate: empId !== "" && empName !== "" ? [{
                 id: empId,
                 name: empName
             }] : [],
             onAdd: function(item) {
                 $('#created_by_ny_id').val(item.id);
                 $('#created_by_ny_name').val(item.name);
             },
             onDelete: function(item) {
                 $('#created_by_ny_id').val('');
                 $('#created_by_ny_name').val('');
             }
         });
         var start = moment().subtract(0, 'days');
         var end = moment();

         $('#last_status_update').daterangepicker({
             startDate: start,
             endDate: end,
             autoUpdateInput: false,
             startOfWeek: 'sunday',
             ranges: {
                 'Select Date': [start, end],
                 'Today': [moment(), moment()],
                 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                 'This Month': [moment().startOf('month'), moment().endOf('month')],
                 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                     'month').endOf('month')],
                 'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                     .endOf('month')
                 ],
                 'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                     .endOf('isoWeek')
                 ],
                 'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                     'weeks').endOf('isoWeek')],
             }
         }, function(chosen_date, end_date) {

             $('#last_status_update').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                 'MM/DD/YYYY'));
         })
         $('#last_status_update').on('apply.daterangepicker', function(ev, picker) {
            // Detect "Select Date"
            if (picker.chosenLabel === 'Select Date') {
                $(this).val('');
            } else {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            }
        });
         var lastUpdatedById = '{{ $last_status_updated_by_id }}';
         var lastUpdatedByempName = '{{$last_status_updated_by_name}}';
         $("#last_status_updated_by").tokenInput(urlToken, {

             tokenLimit: 1,
             zindex: 9999,
             prePopulate: lastUpdatedById !== "" && lastUpdatedByempName !== "" ? [{
                 id: lastUpdatedById,
                 name: lastUpdatedByempName
             }] : [],
             onAdd: function(item) {
                 $('#last_status_updated_by_id').val(item.id);
                 $('#last_status_updated_by_name').val(item.name);
             },
             onDelete: function(item) {
                 $('#last_status_updated_by_id').val('');
                 $('#last_status_updated_by_name').val('');
             }
         });

        $('body').on('click','#cboxId',function(e){
            var cbox = $('#cboxId').is(":checked");
            if(cbox){
                $('.cbox').prop("checked",true);
            }else{
                $('.cbox').prop("checked",false);
            }
        })

        function loadNyBestUser(){
            $("#bulk_user_id").tokenInput("destroy");
            $("#bulk_user_id").tokenInput(urlToken, {

                tokenLimit: 1,
                zindex: 9999,
                onReady: function() {
                    setTimeout(function () {
                        $(".token-input-dropdown").css({
                            "max-height": "180px",
                            "overflow-y": "auto"
                        });
                    }, 500);
                }

            });
        }

        // Agency Filter Toggle Button
    $(document).ready(function() {
        const $toggleBtn = $('#agencyToggleBtn');
        const $toggleLabel = $('#agencyToggleLabel');
        const $filterTypeInput = $('#agency_filter_type');
        if ($toggleBtn.length != 0) {
            currmode = $('#agency_filter_type').val().trim();
            updateButton(currmode);
        }

        // Update button appearance, label, and hidden input
        function updateButton(mode) {
            $toggleBtn.attr('data-mode', mode);
            $filterTypeInput.val(mode);
            if (mode.trim() === 'include') {
                $toggleBtn.html('<i class="mdi mdi-plus"></i>');
                $toggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $toggleLabel.text('Include Agency').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $toggleBtn.html('<i class="mdi mdi-minus"></i>');
                $toggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $toggleLabel.text('Exclude Agency').removeClass('mode-include').addClass('mode-exclude');
            }
        }
        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#agencyToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateButton(newMode);
        });


        const $serviceToggleBtn = $('#serviceToggleBtn');
        const $serviceToggleLabel = $('#serviceToggleLabel');
        const $serviceFilterTypeInput = $('#service_filter_type');
        if ($serviceToggleBtn.length != 0) {
            currmode = $('#service_filter_type').val().trim();
            updateServiceButton(currmode);
        }
        // Update button appearance, label, and hidden input
        function updateServiceButton(mode) {
            $serviceToggleBtn.attr('data-mode', mode);
            $serviceFilterTypeInput.val(mode);
            if (mode.trim() === 'include') {
                $serviceToggleBtn.html('<i class="mdi mdi-plus"></i>');
                $serviceToggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $serviceToggleLabel.text('Include Services').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $serviceToggleBtn.html('<i class="mdi mdi-minus"></i>');
                $serviceToggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $serviceToggleLabel.text('Exclude Services').removeClass('mode-include').addClass('mode-exclude');
            }
        }
        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#serviceToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateServiceButton(newMode);
        });




        const $branchToggleBtn = $('#branchToggleBtn');
        const $branchToggleLabel = $('#branchToggleLabel');
        const $branchFilterTypeInput = $('#branch_filter_type');
        if ($branchToggleBtn.length != 0) {
            currmode = $('#branch_filter_type').val().trim();
            updateBranchButton(currmode);
        }
        // Update button appearance, label, and hidden input
        function updateBranchButton(mode) {
            $branchToggleBtn.attr('data-mode', mode);
            $branchFilterTypeInput.val(mode);
            if (mode.trim() === 'include') {
                $branchToggleBtn.html('<i class="mdi mdi-plus"></i>');
                $branchToggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $branchToggleLabel.text('Include Branch').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $branchToggleBtn.html('<i class="mdi mdi-minus"></i>');
                $branchToggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $branchToggleLabel.text('Exclude Branch').removeClass('mode-include').addClass('mode-exclude');
            }
        }
        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#branchToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateBranchButton(newMode);
        });

    });

    function refreshPatientImportModal(){
       $('#images_error').html("");
       $('#agency_error').html("");
       $('#import_agency_ids').val("");
       resetData();
    }

    function resetData() {
        $('#import_agency_ids').val('');
        let oldInput = $('#upload_csv_file_id');
        let newInput = oldInput.clone().val('');  // clone, clear

        oldInput.replaceWith(newInput);           // replace in DOM

        // Clear label text
        $('.custom-file-label[for="upload_csv_file_id"]').text('Choose file');

        // Reattach change handler
        newInput.on('change', function () {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    }

    $('#upload_csv_file_id').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    
    var agencyEmpId = '{{ $agency_updated_by_id }}';
    var agencyEmpName = '{{ $agency_updated_by_name }}';
    var existingAgency = "{!! implode(',', $selected_agency_fk) !!}";
    if(existingAgency !=""){
        loadAgencyUser(existingAgency);
    }
    function loadAgencyUser(agencyIds){
        $('#agency_updated_by').tokenInput('destroy');
        var agencyids = $('#agency_fk').val();

        var flag = false;
        if(agencyids.includes($('#token_input_agency_id').val())){
            flag = true;
        }
        $('#agency_updated_by_id').val(agencyEmpId);
            $('#agency_updated_by_name').val(agencyEmpName);
        if(!flag){
            agencyEmpId ="";
            agencyEmpName="";
            $('#agency_updated_by_id').val('');
            $('#agency_updated_by_name').val('');
            $('#token_input_agency_id').val('');
        }
        
        var _SEARCH_USERS_BY_AGENCY = "{{ url('agency/search-users-by-agency') }}";
       
        $("#agency_updated_by").tokenInput(_SEARCH_USERS_BY_AGENCY+"?agency_id="+$('#agency_fk').val(), {

            tokenLimit: 1,
            zindex: 9999,
        
            prePopulate: agencyEmpId !== "" && agencyEmpName !== "" ? [{
                id: agencyEmpId,
                name: agencyEmpName
            }] : [],
            onAdd: function(item) {
                $('#agency_updated_by_id').val(item.id);
                $('#agency_updated_by_name').val(item.name);
                $('#token_input_agency_id').val(item.agency_id);
            },
            onDelete: function(item) {
                $('#agency_updated_by_id').val('');
                $('#agency_updated_by_name').val('');
                
            }
        });
    }

    $('#agency_fk').change(function(e){
        loadAgencyUser($('#agency_fk').val());
    })

    function confirmUnreview(patientId, patientName) {
        $.confirm({
            title: 'Mark as Unreviewed',
            content: 'Are you sure you want to mark <strong>' + patientName + '</strong> as unreviewed? This record will appear again in the agency listing.',
            type: 'red',
            buttons: {
                confirm: {
                    text: 'Yes, Mark as Unreviewed',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            type: 'POST',
                            url: '{{ url("/patient/mark-unreviewed") }}',
                            data: { _token: '{{ csrf_token() }}', patient_id: patientId },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                setTimeout(function(){ location.reload(); }, 1000);
                            },
                            error: function() {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        });
                    }
                },
                cancel: { text: 'Cancel', btnClass: 'btn-default' }
            }
        });
    }

    function confirmReview(patientId, patientName) {
        var ids, contentMsg;
        if (patientId) {
            ids = String(patientId);
            contentMsg = 'Are you sure you want to mark <strong>' + patientName + '</strong> as reviewed? This record will no longer appear in the agency listing.';
        } else {
            var selected = [];
            $('.review-cbox:checked').each(function() { selected.push($(this).val()); });
            if (selected.length === 0) {
                toastr.error('Please select at least one record to review.');
                return;
            }
            ids = selected.join(',');
            contentMsg = 'Are you sure you want to mark <strong>' + selected.length + ' selected record(s)</strong> as reviewed? These records will no longer appear in the agency listing.';
        }
        $.confirm({
            title: 'Mark as Reviewed',
            content: contentMsg,
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Yes, Mark as Reviewed',
                    btnClass: 'btn-primary',
                    action: function() {
                        $.ajax({
                            type: 'POST',
                            url: '{{ url("/patient/mark-reviewed") }}',
                            data: { _token: '{{ csrf_token() }}', patient_id: ids },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                setTimeout(function(){ location.reload(); }, 1000);
                            },
                            error: function() {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        });
                    }
                },
                cancel: { text: 'Cancel', btnClass: 'btn-default' }
            }
        });
    }

    $('#reviewCboxId').on('change', function() {
        $('.review-cbox').prop('checked', $(this).is(':checked'));
    });

</script>