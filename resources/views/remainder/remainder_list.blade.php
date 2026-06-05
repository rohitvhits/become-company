 @include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/sweetalert.min.css" rel="stylesheet" type="text/css" />

 <style>
   #order-listing_length,
   #order-listing_paginate,
   #order-listing_info {
     display: none;
   }

   #order-listing_filter {
     text-align: right;
   }

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
     width: 3000px;
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
           <h4 class="card-title">Reminder ({{$user_list->total()}})</h4>
         </div>
         <div class="col-sm-7">

           <a href="<?php echo URL::to('/reminder/add') ?>" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-plus"> </i> Add Reminder </a>
         </div>
       </div>
       <div class="card-body compact-view">
         <div class="row">
           <div class="col-12">

             <div class="table-responsive">

               <table id="order-listing1" class="table table-bordered">
                 <thead>
                   <tr>


                     <th>#</th>

                     <th>Title</th>
                     <th>Description</th>
                     <th>Date Started</th>
                     <th>Due Date</th>
                     <th>Created Date</th>
                     <th>Created By</th>
                     <th>Status</th>
                     <th>Action</th>

                   </tr>

                 </thead>
                 <tbody>
                   <?php if ($user_list->total() != 0) {
                      $i = 1 + (($user_list->currentPage() - 1) * $user_list->perPage());
                      foreach ($user_list as $row) {  ?>

                       <tr>
                         <td><?= '#' . ' ' . $row->id ?></td>
                         <td><?= $row->title ?></td>
                         <td><?= $row->message ?></td>
                         <td><?php if ($row->start_date != '') {
                                echo date('m-d-Y', strtotime($row->start_date));
                              } ?></td>
                         <td><?php if ($row->end_date != '') {
                                echo date('m-d-Y', strtotime($row->end_date));
                              } ?></td>
                         <td><?php echo date('m-d-Y h:i A', strtotime($row->created_date)); ?></td>
                         <td><?php echo $row->fullname; ?></td>
                         <td>
                           <?php
                            if ($row->status == 'Pending') {
                              echo "<span class='badge badge-secondary'>{$row->status}</span>";
                            } elseif ($row->status == 'Started') {
                              echo "<span class='badge badge-primary'>{$row->status}</span>";
                            } elseif ($row->status == 'Processing') {
                              echo "<span class='badge badge-info'>{$row->status}</span>";
                            } elseif ($row->status == 'On-Hold') {
                              echo "<span class='badge badge-warning'>{$row->status}</span>";
                            } elseif ($row->status == 'Over Due') {
                              echo "<span class='badge badge-danger'>{$row->status}</span>";
                            } elseif ($row->status == 'Complete') {
                              echo "<span class='badge badge-success'>{$row->status}</span>";
                            }
                            ?>

                         </td>
                         <td>
                           <?php
                            $flag = 0;
                            $explode = explode(',', $row->employee_id);
                            if (count($explode) > 0) {
                              foreach ($explode as $sk) {
                                if ($auth['id'] == $sk) {
                                  $flag = 1;
                                }
                              }
                            }
                            ?>

                           <?php if (($auth['id'] == $row->created_by)) {

                            ?>
                             <a href="<?php echo URL::to('/'); ?>/reminder/edit/<?php echo $row->id; ?>"><i class="fa fa-pencil"></i></a>
                             <a href="<?php echo URL::to('/'); ?>/reminder/delete/<?php echo $row->id; ?>" onclick="return confirm('Are you sure remove this record?')"><i class="fa fa-trash"></i></a>
                           <?php } ?>
                           <?php if ($flag == 1) { ?>
                             <?php if ($row->status == 'Pending') { ?>
                               <a href="javascript:void(0)" title="Started" onclick="getStatus('Started',<?php echo $row->id; ?>)"><i class="fa fa-play"></i></a>
                             <?php } else if ($row->status == 'Started') {  ?>
                               <a href="javascript:void(0)" title="Processing" onclick="getStatus('Processing',<?php echo $row->id; ?>)"><i class="fa fa-refresh"></i></a>


                             <?php } else if ($row->status == 'Processing') { ?>
                               <a href="javascript:void(0)" title="Complete" onclick="getStatus('Complete',<?php echo $row->id; ?>)"><i class="fa fa-thumbs-up"></i></a>
                             <?php } ?>
                           <?php } ?>

                         </td>
                       </tr>
                     <?php }
                    }
                    if ($user_list->total() == 0) { ?>
                     <tr>
                       <td colspan="4">
                         <center><b>Data not found</b></center>
                       </td>
                     </tr>
                   <?php } ?>
                 </tbody>
               </table>

               <div class="pull-right pegination-margin">
                 {{$user_list->links("pagination::bootstrap-4")}}
               </div>

             </div>

           </div>
         </div>
       </div>
     </div>
   </div>



   @include('include/footer')

   <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
   <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
   <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>

   <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
   <script src="<?php echo URL::to('/'); ?>/assets/sweetalert.min.js"></script>
   <script>
     $(".datepicker").datepicker();


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

     function getStatus(val, id) {
       if (id != '' && val != '') {
         swal({
             title: "Are you sure change status?",
             text: "",
             type: "warning",
             showCancelButton: true,
             confirmButtonColor: '#DD6B55',
             confirmButtonText: 'Yes',
             cancelButtonText: "No",
             closeOnConfirm: false,
             closeOnCancel: false
           },
           function(isConfirm) {

             if (isConfirm) {
               $.ajax({
                 async: false,
                 global: false,
                 type: "GET",
                 url: "<?php echo URL::to('/'); ?>/reminder/change-status",
                 data: {
                   'id': id,
                   'status': val
                 },
                 success: function(res) {
                   if (res == 1) {
                     toastr.success("Status successfully updated to " + val);
                     location.reload();
                   } else {
                     toastr.error("Sorry, something went wrong. Please try again.");

                   }
                 }
               });
               swal.close();
             } else {
               swal.close();
             }
           });
       } else {
         toastr.error("Sorry, something went wrong. Please try again.");
       }
     }
   </script>