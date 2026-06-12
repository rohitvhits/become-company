@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
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

                     <a href="{{URL::to('/')}}/patient/add" class="btn btn-primary btn-rounded btn-fw btn-sm  ml-1"><i class="mdi mdi-plus"> </i> Add
                         Appointment </a>
                     <!-- <a href="javascript:void(0)" data-toggle="modal" class="btn btn-secondary btn-rounded btn-sm btn-fw pull-right" data-target="#exampleModal-5" data-whatever="@mdo"><i class="mdi mdi-file-export"></i>Import</a> -->

                     <a href="{{URL::to('/')}}/patient?is_past_show=true" class="btn btn-info btn-rounded btn-fw btn-sm">Past Appointment List</a>
                     <a href="{{URL::to('/')}}/patient" class="btn btn-light btn-rounded btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
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
                                         <label class="col-sm-12 ">Status</label>
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
                                                     <option value="<?php echo $rwAgency->id; ?>" <?php echo in_array($rwAgency->id, $selected_agency_fk) ? 'selected' : ''; ?>>
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
                                             <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code" value="<?php echo $patient_code; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Name</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name" value="<?php echo $full_name; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Mobile</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile" value="<?php echo $mobile; ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Services</label>
                                         <div class="col-sm-12">
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
                                         <label class="col-sm-12 ">Assign To</label>
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
                                         <label class="col-sm-12 ">Due Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="due_date" value="<?php echo $due_date; ?>" class="due_datenn form-control" id="due_date">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Appointment Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" value="<?php echo $appointment_date; ?>" id="appointment_date">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Location</label>
                                         <div class="col-sm-12">
                                             <select name="locationId[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="locationId">
                                                 <?php foreach ($location_list as $vsl) { ?>
                                                     <option value="<?php echo $vsl->id; ?>" <?php if (in_array($vsl->id, $selected_location_id)) {
                                                                                                    echo 'selected';
                                                                                                } ?>>
                                                         <?php echo $vsl->location_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Created Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="created_date" value="<?php echo $created_date; ?>" class="datepickernn form-control" id="created_date">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">SMS Status</label>
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
                                         <label class="col-sm-12 ">Discipline</label>
                                         <div class="col-sm-12 ">
                                             <select class="form-control" name="diciplin" id="diciplin_id">

                                                 <option value="">Select Discipline</option>
                                                 <option value="HHA" <?php if ($selected_discipline == 'HHA') {
                                                                            echo "selected='selected'";
                                                                        } ?>>HHA</option>
                                                 <option value="CDPAP" <?php if ($selected_discipline == 'CDPAP') {
                                                                            echo "selected='selected'";
                                                                        } ?>>CDPAP</option>
                                                 <option value="RN" <?php if ($selected_discipline == 'RN') {
                                                                        echo "selected='selected'";
                                                                    } ?>>RN</option>
                                                 <option value="LPN" <?php if ($selected_discipline == 'LPN') {
                                                                            echo "selected='selected'";
                                                                        } ?>>LPN</option>
                                                 <option value="Pre-HHA" <?php if ($selected_discipline == 'Pre-HHA') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Pre-HHA</option>
                                                 <option value="Pre-CDPAP" <?php if ($selected_discipline == 'Pre-CDPAP') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Pre-CDPAP</option>
                                                 <option value="OTHER" <?php if ($selected_discipline == 'OTHER') {
                                                                            echo "selected='selected'";
                                                                        } ?>>Other</option>

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
                                                 <option value="Caregiver" <?php if ($type == 'Caregiver') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Caregiver</option>
                                                 <option value="Patient" <?php if ($type == 'Patient') {
                                                                                echo "selected='selected'";
                                                                            } ?>>Patient</option>

                                             </select>

                                         </div>
                                     </div>
                                 </div>

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
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         <div class="row">
             <div class="col-12 ">
                <div class="table-responsive">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>

                             <th>ID</th>
                             <th class="no_warp">SMS</th>
                             <th>Status</th>
                             <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                 <th class="no_warp">Agency Name</th>
                             <?php } ?>
                             <th>Type/Discipline</th>
                             <th class="no_warp">Patient Code </th>
                             <th class="no_warp">Name/Mobile/Services </th>
                             <th class="no_warp">Assigned To</th>
                             <th class="no_warp">Due Date</th>
                             <th class="no_warp">Appointment Date - Location</th>
                             <th>Created Date</th>
                             <th>FU Date</th>
                             
                             <th>Action</th>
                         </tr>
                     </thead>
                     <tbody>

                         <?php if (count($query) > 0) {
                                $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                                foreach ($query as $row) {  ?>
                                 <tr>
                                     <td><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . '' . $row->id ?></a>
                                     </td>
                                     <td><?php if ($row->patient_sms_flag == 1) {
                                                echo "<span class='badge badge-success'>Sent</span>";
                                            } else {
                                                echo "<span class='badge badge-warning'>Pending</span>";
                                            } ?></td>
                                     <td>
                                         <?php

                                            if (strtolower($row->status) == 'pending') {
                                            ?>
                                             <label class='badge badge-warning'>Pending</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'booked') {
                                            ?>
                                             <label class='badge badge-info'>Booked</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'completed') {
                                            ?>
                                             <label class='badge badge-success'>Completed</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'cancelled') {
                                            ?>
                                             <label class='badge badge-danger'>Cancelled</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'noshow') {
                                            ?>
                                             <label class='badge badge-light'>No Show</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'refused') {
                                            ?>
                                             <label class='badge badge-danger'>Refused</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'processing') {
                                            ?>
                                             <label class='badge badge-info'>processing</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'arrived') {
                                            ?>
                                             <label class='badge badge-primary'>Arrived</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'checkin') {
                                            ?>
                                             <label class='badge badge-primary'>Mark as ClockIn</label>

                                         <?php } ?>
                                         <?php

                                            if (strtolower($row->status) == 'not interested') {
                                            ?>
                                             <label class='badge badge-primary'>Not Interested</label>

                                         <?php }
                                            if (strtolower($row->status) == 'hospitalized/rehab') {
                                            ?>
                                             <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                                         <?php }
                                            if (strtolower($row->status) == 'unabletocontact') {
                                            ?>
                                             <label class='badge badge-primary'>Unable To Contact</label>

                                         <?php } ?>

                                     </td>
                                     <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                         <td><?= $row->agency_name ?> </td>
                                     <?php } ?>

                                     <td><?php echo $row->type; ?>
                                         <br />
                                         <?php echo $row->diciplin; ?>

                                     </td>
                                    <td>{{ $row->patient_code}}</td>
                                     <td>
                                         <?php echo $row->first_name . ' ' . $row->last_name; ?><br />
                                         <?php echo $row->mobile; ?><br />
                                         <?php echo $row->name; ?><br />
                                     </td>
                                     <td>{{ $row->assignToUser!=null && isset($row->assignToUser->users) ? $row->assignToUser->users->full_name : 'N/A' }}</td>
                                     <td>
                                         @if($row->due_date!='')

                                         <?php if ($row->due_date != '1969-12-31') {
                                                echo date('m/d/Y h:i A', strtotime($row->due_date));
                                            } ?>

                                         @endif</td>

                                     <td>
                                         @if(strtolower($row->type) == 'caregiver')
                                         <?php if ($row->appointment_date != '') {
                                                echo date('m/d/Y', strtotime($row->appointment_date));
                                            } ?> <?php if ($row->start_time != '' && $row->end_time) {
                                                        $start_time = date('h:i A', strtotime($row->start_time));
                                                        $end_time = date('h:i A', strtotime($row->end_time));
                                                    ?><br /><?php
                                                            echo $start_time . ' - ' . $end_time;
                                                        } ?><br />
                                         <?php echo $row->location_name; ?><br />

                                         @endif
                                         @if(strtolower($row->type) == 'patient')
                                         @if ($row->appointment_date != '')
                                         {{date('m/d/Y h:i A', strtotime($row->appointment_date))}}
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
                                     
                                     <td>
                                        @if($row->archived_at !='')
                                        <a title="Unarchive" href="javascript:void(0)" onclick="getUnArchiveById(<?php echo $row->id; ?>)"><i class="fa fa-file-archive-o"></i></a>
                                        @else
                                        <a title="Archive" href="javascript:void(0)" onclick="getArchiveById(<?php echo $row->id; ?>)"><i class="fa fa-archive"></i></a>
                                        @endif
                                     
                                         <?php
                                            if (in_array($user->user_type_fk, array(3, 184))) {
                                                if (strtolower($row->type) == 'caregiver') {
                                                    if ($row->status == 'Pending') { ?>
                                                     <a href="javascript:void(0)" onclick="getSendSMS(<?php echo $row->id; ?>)">Send SMS</a>
                                                 <?php } else if ($row->status == 'booked') { ?>
                                                     <a href="javascript:void(0)" onclick="getRemainderSendSMS(<?php echo $row->id; ?>)">Reminder SMS</a>
                                         <?php }
                                                }
                                            } ?>

                                     </td>
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
                     <!-- {{ $query->appends(request()->query())->links() }}-->
                     {{ $query->links() }}
                 </div>
                </div>
             </div>

         </div>

     </div>

     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     <div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Import CSV Ny Best Medicals Appointments</h5>


                     <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="appps_id">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form class="forms-sample" name="adduser" method="post" id="formnew">
                         <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                         <?php
                            if ($user->agency_fk == '') {
                            ?>
                             <div class="form-group">
                                 <label for="message-text" class="col-form-label">Agency<span style="color:red">*</span>:</label>
                                 <select name="agency_id" class="form-control" id="agency_ids">
                                     <option value="">Select Agency</option>
                                     <?php if (count($agencyList) > 0) {
                                            foreach ($agencyList as $vsl) {
                                        ?>
                                             <option value="<?php echo $vsl->id; ?>"><?php echo $vsl->agency_name; ?></option>
                                     <?php }
                                        } ?>
                                 </select>
                                 <span class="error mt-2 text-danger" id="agency_error" for="file_name"></span>
                             </div>
                         <?php } else { ?>
                             <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                         <?php } ?>
                         <div class="form-group">
                             <label for="message-text" class="col-form-label">Upload CSV<span style="color:red">*</span>:</label>
                             <input type="file" class="form-control" id="timeidnew" name="images">
                             <span class="error mt-2 text-danger" id="images_error" for="file_name"></span>
                         </div>

                         <div class="form-group">
                             <p>Click here to download the <a href="{{ URL::to('/sample.csv') }}">sample file.</a></p>
                         </div>


                         <div class="modal-footer">
                             <div class="dot-opacity-loader" id="loaderss_id" style="display:none">
                                 <span></span>
                                 <span></span>
                                 <span></span>
                             </div>
                             <button type="button" onclick="getSubmit()" id="seacu" class="btn btn-success">Save</button>
                             <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <div class="modal fade" id="exampleModal-import" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Import Ny Best Medicals Appointments</h5>


                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>

                 <form action="<?php echo URL::to('/'); ?>/patient/patient-import" method="post" enctype="multipart/form-data" id="submitId">
                     <input type="hidden" name="order_data" value="" id="order_data">
                     <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                     <div class="modal-body" id="formnewNN">

                     </div>
                     <div class="modal-footer">
                         <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                         <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>


                     </div>

                 </form>
             </div>
         </div>
     </div>




     @include('include/footer')

     <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
     <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
     <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
     <script>
         /* ..Start.. For page refresh when search data then show search area */
         $(document).ready(function() {
             var url = window.location.search;
             var arguments = url.split('?')[1];
             //  var searchText = arguments.split('=')[0];
             //  if (searchText == 'sms_status') {
             //      $("#search-div").show();
             //  }
         });
         /* ..End.. For page refresh when search data then show search area */
         $("#searchbtns").click(function() {
             $("#search-div").toggle();
         });


         $(document).on("click", ".btnExport", function() {

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
             var isDiscipline = $("#diciplin_id").val();
             var type = $("#type").val();
             var patient_code = $("#patient_code").val();




             if (due_date == '' && sms_status == null && status == null && agency_fk == null && first_name == '' &&
                 mobile == '' && assign_user_id == null && appointment_date == '' && locationId == null && type == null &&
                 isDiscipline == null &&
                 created_date == '' && service_id == null && isArchived != true && patient_code.trim() !="") {
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
                 var links = "<?php echo URL::to('/'); ?>/patient/patient-export?sms_status=" + sms_status + "&status=" + status +
                     "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
                     service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
                     "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
                     created_date + "&is_archive=" + isArchived + "&dicipline=" + isDiscipline + "&type=" + type+'&patient_code='+patient_code;
                 window.location.href = links;
             }
         });

         $(document).on("click", ".searchAppoinment", function() {

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
             var isDiscipline = $("#diciplin_id").val();
             var type = $("#type").val();
             var patient_code = $("#patient_code").val();




             if (due_date == '' && sms_status == null && status == null && agency_fk == null && first_name == '' &&
                 mobile == '' && assign_user_id == null && appointment_date == '' && locationId == null && type == null &&
                 isDiscipline == null &&
                 created_date == '' && service_id == null && isArchived != true) {
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
                 var links = "<?php echo URL::to('/'); ?>/patient?sms_status=" + sms_status + "&status=" + status +
                     "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
                     service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
                     "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
                     created_date + "&is_archive=" + isArchived + "&dicipline=" + isDiscipline + "&type=" + type+'&patient_code='+patient_code;
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
             $('.due_datenn').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
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

                 $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })
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

                 $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
             })


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

         function getSubmit() {
             $('#loaderss_id').attr('style', 'display:block');
             var agency_ids = $('#agency_ids').val();
             var fimagesG = $('input[name="images"]').prop('files');
             var cnt = 0;
             $('#images_error').html("");
             $('#agency_error').html("");
             <?php
                if ($user->agency_fk == '') {
                ?>
                 if (agency_ids == '') {
                     $('#agency_error').html("Required");
                     cnt = 1;
                 }
             <?php } ?>
             if (fimagesG.length == 0) {
                 $('#images_error').html("Required");
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
                 return false;
             } else {
                 var foms = $('#formnew')[0];
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

                         $('#seacu').attr('data-target', "#exampleModal-import");
                         $('#seacu').attr('data-toggle', "modal");
                         $('#formnewNN').html(res);

                         setTimeout(function(e) {
                             $('#loaderss_id').attr('style', 'display:none');
                         }, 1000);
                         $('#appps_id').click();
                     }
                 })
             }
         }
         $('#submitId').submit(function(e) {
             $('#row_error').html("");
             var selected = [];
             var selected_data = [];
             $.each($(".selectvalues option:selected"), function() {
                 selected.push($(this).val());
                 if ($(this).val() != "") {
                     selected_data.push($(this).val());
                 }
             });
             console.log(selected_data.length);

             $('#order_data').val(selected.join());

             if (selected_data.length < 3) {
                 $('#row_error').html('Required.');
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
         function getArchiveById(id){
            var consi = confirm('Are you sure archive this record?');
            
            var selected_data = [];
            selected_data.push(id); 
            if(consi == true){
                    $.ajax({
                        async:false,
                        global:false,
                        type:"POST",
                        url:"<?php echo URL::to('/');?>/patient/patient-archive",
                        data:{'_token':"<?php echo csrf_token();?>",'patient_id':selected_data.join()},
                        success:function(res){
                            if(res ==1){
                                toastr.success('Appointment successfully archive.');
                                location.reload();
                            }else{
                                toastr.error('Sorry, something went wrong. Please try again.');
                            }
                        }
                    })
                }
               
            }
            
     </script>