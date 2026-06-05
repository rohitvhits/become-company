 @include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <style>
   span.select2.select2-container.select2-container--default {
     width: 200px !important;
   }

   .wmd-view-topscroll,
   .wmd-view {
     overflow-x: scroll;
     overflow-y: hidden;
     border: none 0px red;
   }

   .wmd-view-topscroll {
     height: 20px;
   }

   .scroll-div1 {

     overflow-x: scroll;
     overflow-y: hidden;
     height: 20px;
   }

   .scroll-div2 {

     height: 20px;
   }

   .scroll-div1,
   .scroll-div2 {
     width: 2500px;
   }
 </style>
 <div class="main-panel">
   <?php
    $auth = auth()->user();
    ?>
   <div class="content-wrapper">
     <div class="card">
       <div class="row list-name">
         <div class="col-sm-5 card-title">
           <h4 class="card-title">Garbage List ({{$garbase_list->total()}})</h4>
         </div>
         <div class="col-sm-7">


         </div>
       </div>
       <div class="card-body compact-view">
         <div class="row">
           <div class="col-12">
             <div class="wmd-view-topscroll">
               <div class="scroll-div1">
               </div>
             </div>
             <div class="wmd-view">
               <div class="scroll-div2">



                 <table id="order-listing1" class="table table-bordered">
                   <thead>
                     <tr>

                       <th>#</th>
                       <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                         <th>Agency Name</th>
                       <?php } ?>
                       <th>Doctor Name</th>
                       <th>Type</th>
                       <th>Full Name</th>

                       <th>Phone</th>
                       <th>Mobile</th>
                       <th>Date of Birth</th>
                       <th>Location</th>
                       <th>Appointment Date</th>
                       <th>Appointment Time</th>
                       <th>Service</th>
                       <th>Created Date</th>
                       <th>SMS Status</th>
                       <th>Booked Via</th>
                       <th>Status</th>

                       <th></th>
                     </tr>
                     <form method="get" action="">
                       <tr>


                         <td><input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search"></td>
                         <?php

                          if (in_array($user->user_type_fk, array(3, 184))) { ?>
                           <td>
                             <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                               <select name="agency_fk" id="agency_fk" class="form-control">
                                 <option value="">Select agency</option>
                                 <?php foreach ($agencyList as $rwAgency) { ?>
                                   <option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk) == $rwAgency->id) ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
                                 <?php } ?>
                               </select><?php } ?>
                           </td>
                         <?php } ?>

                         <td>

                           <select name="doctor_id" class="form-control">
                             <option value=""></option>
                             <?php foreach ($doctor_list as $rwAgencyD) { ?>
                               <option value="<?php echo $rwAgencyD->id ?>" <?php echo (($doctor_id) == $rwAgencyD->id) ? 'selected' : ''; ?>><?php echo $rwAgencyD->full_name; ?></option>
                             <?php } ?>
                           </select>
                         </td>
                         <td>
                           <select name="type" class="form-control">
                             <option value="">Select Type</option>
                             <option value="Caregiver" <?php if ($type == 'Caregiver') {
                                                          echo "selected='selected'";
                                                        } ?>>Caregiver</option>
                             <option value="Patient" <?php if ($type == 'Patient') {
                                                        echo "selected='selected'";
                                                      } ?>>Patient</option>

                           </select>
                         </td>

                         <td><input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name" value="<?php echo $full_name ?>"></td>

                         <td><input type="text" autocomplete="off" class="form-control" name="phone" id="phone" value="<?php echo  $phone ?>"></td>
                         <td><input type="text" autocomplete="off" class="form-control" name="mobile" id="mobile" value="<?php echo  $mobile ?>"></td>
                         <td><input type="text" autocomplete="off" class="form-control datepicker" name="age" value="<?php echo  $age ?>"></td>
                         <td>
                           <select name="locationId" class="form-control">
                             <option value="">Select Location</option>
                             <?php foreach ($location_list as $vsl) { ?>
                               <option value="<?php echo $vsl->id; ?>" <?php if ($location_id == $vsl->id) {
                                                                        echo "selected ='selected'";
                                                                      } ?>><?php echo $vsl->location_name; ?></option>
                             <?php } ?>
                           </select>
                         </td>
                         <td><input type="text" autocomplete="off" name="appointment_date" style="width:110px;" class="datepicker1 form-control" value="<?php echo $appointment_date; ?>"></td>
                         <td></td>
                         <td>
                           <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">

                             <option value="">Select Service</option>
                             <?php
                              $serviceArray = array();
                              if (!empty($service_id)) {
                                foreach ($service_id as $v) {
                                  $serviceArray[] = $v;
                                }
                              }
                              foreach ($serviceList as $service) { ?>
                               <option value="<?php echo $service->id; ?>" <?php if (in_array($service->id, $serviceArray)) {
                                                                            echo "selected ='selected'";
                                                                          } ?>><?php echo $service->name; ?></option>
                             <?php } ?>
                           </select>

                         </td>
                         <td>
                           <input type="text" name="created_date" value="<?php echo $created_date; ?>" class="datepickernn form-control" style="width:86px">
                         </td>
                         <td>
                           <select name="sms_status" id="status_id" class="form-control">
                             <option value="">Select</option>
                             <option value="0" <?php if (isset($sms_status) && $sms_status == 0) {
                                                  echo "selected='selected'";
                                                } ?>>Pending</option>
                             <option value="1" <?php if (isset($sms_status) && $sms_status == 1) {
                                                  echo "selected='selected'";
                                                } ?>>Sent</option>

                           </select>
                         </td>
                         <td>

                         </td>


                         <td>
                           <select name="status" id="status_id" class="form-control">
                             <option value=""></option>
                             <option value="Pending" <?php if ($status == 'Pending') {
                                                        echo "selected='selected'";
                                                      } ?>>Pending</option>
                             <option value="booked" <?php if ($status == 'booked') {
                                                      echo "selected='selected'";
                                                    } ?>>Booked</option>
                             <option value="completed" <?php if ($status == 'completed') {
                                                          echo "selected='selected'";
                                                        } ?>>Completed</option>

                             <option value="noshow" <?php if ($status == 'noshow') {
                                                      echo "selected='selected'";
                                                    } ?>>No Show</option>

                             <option value="arrived" <?php if ($status == 'arrived') {
                                                        echo "selected='selected'";
                                                      } ?>>Arrived</option>
                             <option value="processing" <?php if ($status == 'processing') {
                                                          echo "selected='selected'";
                                                        } ?>>Processing</option>
                             <option value="Not interested" <?php if ($status == 'Not interested') {
                                                              echo "selected='selected'";
                                                            } ?>>Not Interested</option>
                             <option value="hospitalized/rehab" <?php if ($status == 'hospitalized/rehab') {
                                                                  echo "selected='selected'";
                                                                } ?>>Hospitalized/Rehab</option>
                             <option value="unableToContact" <?php if ($status == 'unableToContact') {
                                                                echo "selected='selected'";
                                                              } ?>>Unable To Contact</option>
                             <option value="refused" <?php if ($status == 'refused') {
                                                        echo "selected='selected'";
                                                      } ?>>Refused</option>
                             <option value="checkin" <?php if ($status == 'checkin') {
                                                        echo "selected='selected'";
                                                      } ?>>Mark as CheckIn</option>
                           </select>

                         </td>


                         <td>
                           <input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw" value="search">
                         </td>
                       </tr>
                     </form>

                   </thead>
                   <tbody>

                     <?php if ($garbase_list->total() != 0) {
                        $i = 1 + (($garbase_list->currentPage() - 1) * $garbase_list->perPage());
                        foreach ($garbase_list as $row) {  ?>
                         <tr>

                           <td><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . ' ' . $row->id ?></a></td>
                           <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                             <td><?= $row->agency_name ?></td>
                           <?php } ?>
                           <td><?php echo $row->full_name; ?></td>
                           <td><?php echo $row->type; ?></td>
                           <td><?php echo $row->first_name . ' ' . $row->last_name; ?></td>

                           <td><?php echo $row->phone; ?></td>
                           <td><?php echo $row->mobile; ?></td>
                           <td><?php if ($row->dob != '0000-00-00') {
                                  echo Common::convertMDY($row->dob);
                                } ?></td>
                           <td><?php echo $row->full_address; ?></td>
                           <td><?php if ($row->appointment_date != '') {
                                  echo Common::convertMDY($row->appointment_date);
                                } ?></td>
                           <td><?php if ($row->start_time != '' && $row->end_time) {
                                  $start_time = date('h:i A', strtotime($row->start_time));
                                  $end_time = date('h:i A', strtotime($row->end_time));

                                  echo $start_time . ' - ' . $end_time;
                                } ?></td>

                           <td><?php echo $row->name; ?></td>
                           <td><?php echo date('m-d-Y', strtotime($row->created_date)); ?></td>
                           <td><?php if ($row->patient_sms_flag == 1) {
                                  echo "<span class='badge badge-success'>Sent</span>";
                                } else {
                                  echo "<span class='badge badge-primary'>Pending</span>";
                                }; ?></td>
                           <td><?php echo ucfirst($row->appointment_mode); ?></td>
                           <td>
                             <?php

                              if ($row->status == 'Pending') {
                              ?>
                               <label class='badge badge-warning badge-pill'>Pending</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'booked') {
                              ?>
                               <label class='badge badge-info badge-pill'>Booked</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'completed') {
                              ?>
                               <label class='badge badge-success badge-pill'>Completed</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'cancelled') {
                              ?>
                               <label class='badge badge-danger badge-pill'>Cancelled</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'noshow') {
                              ?>
                               <label class='badge badge-light badge-pill'>No Show</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'refused') {
                              ?>
                               <label class='badge badge-danger badge-pill'>Refused</label>

                             <?php } ?>

                           </td>
                           <td>

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
                   {{$garbase_list->links("pagination::bootstrap-4")}}
                 </div>
               </div>

             </div>
           </div>
         </div>
       </div>
     </div>
   </div>




   @include('include/footer')

   <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
   <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
   <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
   <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
   <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
   <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
   <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
   <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
   <script>
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
   </script>
   <script>
     $(".datepicker").datepicker();
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
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
           'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
           'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
         }
       }, function(chosen_date, end_date) {

         $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
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
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
           'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
           'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
         }
       }, function(chosen_date, end_date) {

         $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
       })
     });
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
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
           'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
           'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],

         }
       }, function(chosen_date, end_date) {

         $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
       })


     });
   </script>