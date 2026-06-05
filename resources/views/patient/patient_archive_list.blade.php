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
         width: 1650px;
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
 </style>
 <div class="main-panel">
     <?php
        $auth = auth()->user();
        ?>
     <div class="content-wrapper">

     <div class="page-title-main">
        <h5 class="mb-0 font-weight-bold">{{ ucfirst($listHeadingName) }} ({{ $open_record_list->total() }})</h5>
            <div class="page-rightbtns">
                <div>
             
                    <a href="<?php echo URL::to('/'); ?>/{{ $appointmentUrl }}" class="btn btn-light btn-sm btn-rounded btn-fw ml-1"><i
                            class="mdi mdi-reload"></i> Reset</a>
                    
                </div>
            </div>
        </div>

         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
             <div class="alert alert-success alert-dismissible fade show" role="alert">
                 <strong>{{ Session::get('success') }}</strong>
                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             @endif
             @if (Session::has('error'))
             <div class="alert alert-warning alert-dismissible fade show" role="alert">
                 <strong>{{ Session::get('error') }}</strong>
                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             @endif
         </div>
         <div class="row ">
    <div class="col-sm-12">
        <div class="card search-card1" id="search-div">
            <div class="card-body">
                <form method="get" id="formsubmit">
                    <input type="hidden" name="_token" value="T2fdzK1ShOFrIaDGtfR43XwT91A6Ahjq88isXJeQ">
                    <input type="hidden" name="status_update" id="status_update" value="{{ $listHeadingName }}">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">SMS Status</label>
                                <div class="col-sm-12 ">
                                    <select name="sms_status[]" id="sms_status"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple">
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
                        @if ($appointmentUrl == 'upcomming-appoinment' || $appointmentUrl == 'archive-list')
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Status</label>
                                    <div class="col-sm-12">
                                        <select name="status[]" id="status_id"
                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                            multiple="multiple">
                                            <option value=""></option>
                                            <option value="Pending" <?php if (in_array('Pending', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Pending</option>
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
                        @endif
                        @if (in_array($user->user_type_fk, [3, 184]))
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Agency Name</label>
                                    <div class="col-sm-12">
                                        <select name="agency_fk[]" id="agency_fk"
                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                            multiple="multiple">
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
                                <label class="col-sm-12 ">Name</label>
                                <div class="col-sm-12">
                                    <input autocomplete="off" type="text" class="form-control" name="first_name"
                                        id="agency_name" value="<?php echo $full_name; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Mobile</label>
                                <div class="col-sm-12">
                                    <input autocomplete="off" type="text" class="form-control" name="mobile"
                                        id="mobile" value="<?php echo $mobile; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Services</label>
                                <div class="col-sm-12">
                                    <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple"
                                        name="service_id[]" id="service_id">
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
                                    <select name="assign_user_id[]"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple" id="assign_user_id">
                                        @if (!empty($assign_user_list[0]))
                                            @foreach ($assign_user_list as $assigns)
                                                <option value="{{ $assigns->id }}"
                                                    @if (in_array($assigns->id, $selected_assign_user_id)) selected='selected' @endif>
                                                    {{ $assigns->name }}</option>
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
                                    <input type="text" name="due_date" value="<?php echo $due_date; ?>"
                                        class="due_datenn form-control" id="due_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Appointment Date</label>
                                <div class="col-sm-12">
                                    <input type="text" autocomplete="off" name="appointment_date"
                                        class="datepicker1 form-control" value="<?php echo $appointment_date; ?>"
                                        id="appointment_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Location</label>
                                <div class="col-sm-12">
                                    <select name="locationId[]"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple" id="locationId">
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
                                    <input type="text" name="created_date" value="<?php echo $created_date; ?>"
                                        class="datepickernn form-control" id="created_date">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="search-main1">
                        <div class="search-inner">
                            <div>
                                <input type="button" name="search"
                                    class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                    value="Search">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

         <div class="row">
             <div class="col-12">

                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             
                             <th>#</th>
                             <th>SMS Status</th>
                             <th>Status</th>
                             <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                 <th>Agency Name</th>
                             <?php } ?>
                             <th>Type</th>
                             <th>Record From</th>
                             <th>Name/Mobile/Services </th>
                             <th>Assigned To</th>
                             <th>Due Date</th>
                             <th>Appointment Date - Location</th>
                             <th>Created Date</th>
                             <th>Fu Date</th>
                             <th>Action</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php if ($query->total() != 0) {
                                $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                                foreach ($query as $row) {  ?>
                                 <tr>
                                    
                                     <td><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . ' ' . $row->id ?></a>
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
                                         <td>{{ ucwords($row->agency_name) }}</td>
                                     <?php } ?>

                                     <td>{{ ucwords($row->type) }}</td>

                                     <td>
                                         <?php

                                            if ($row->record_id != '') { ?>
                                             <label class='badge badge-info'>NY Best Medicalss</label>
                                         <?php } else { ?>
                                             <label class='badge badge-secondary'>Ny Best Medical Care</label>
                                         <?php } ?>
                                     </td>

                                     <td>{{ ucwords($row->first_name) }} {{ ucwords($row->last_name) }}<br />
                                         <?php echo $row->mobile; ?><br />
                                         <?php echo $row->name; ?><br />
                                     </td>

                                     <td>{{ $row->assign_user_name }}</td>
                                     <td><?php if ($row->due_date != '') {
                                                echo date('m-d-Y', strtotime($row->due_date));
                                            } ?></td>

                                     <td><?php if ($row->appointment_date != '') {
                                                echo Common::convertMDY($row->appointment_date);
                                            } ?> <?php if ($row->start_time != '' && $row->end_time) {
                                                $start_time = date('h:i A', strtotime($row->start_time));
                                                $end_time = date('h:i A', strtotime($row->end_time));
                                            ?><br /><?php
                                                        echo $start_time . ' - ' . $end_time;
                                                    } ?><br />
                                         <?php echo $row->location_name; ?><br />
                                     </td>

                                     <td><?php echo date('m-d-Y', strtotime($row->created_date)); ?><br />
                                     </td>
                                     <td><?php echo date('m-d-Y', strtotime($row->fu_date)); ?><br />
                                     </td>
                                     <td>
                                     <a title="Unarchive" href="javascript:void(0)" onclick="getArchive(<?php echo $row->id; ?>)"><i class="fa fa-file-archive-o"></i></a>
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
                     {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
                 </div>


             </div>
         </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>

     @include('include/footer')

     @include('patient/appointment_search_js')

     <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>

     <script>
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

         function getArchive(id) {
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
     </script>