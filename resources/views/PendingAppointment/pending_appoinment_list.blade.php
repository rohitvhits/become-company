 @include('include/header')
 @include('include/sidebar')

@php
$i = 0;
@endphp

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
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

        @include('patient/appointment_basic')
    
        <div class="row">
            <div class="col-12">
                <div class="wmd-view-topscroll">
                    <div class="scroll-div1">
                    </div>
                </div>
                <div class="wmd-view">
                    <div class="scroll-div2">
        
                        <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth table-width1">
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
                                    <th>FU Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1 + ($open_record_list->currentPage() - 1) * $open_record_list->perPage();
                                @endphp
                                @if (count($open_record_list) > 0)
                                    @foreach ($open_record_list as $row)
                                        <tr>
                                            <td><a href="{{ url('/patient/view/') }}/{{ $row->id }}">
                                                    #{{ $row->id }}</a>
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
                                        </tr>
                                    @endforeach
        
                                @endif
                                @if (count($open_record_list) == 0)
                                    <tr>
                                        <td colspan="12">
                                            <center><b>Data not found</b></center>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="pull-right pegination-margin">
            {{ $open_record_list->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>
     </div>

     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>

     @include('include/footer')

     @include('patient/appointment_search_js')
     
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>

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

         $(document).on('click', '.pagination a', function(event) {
             $('li').removeClass('active');
             $(this).parent('li').addClass('active');
             event.preventDefault();
             var myurl = $(this).attr('href');
             console.log(myurl);
             var page = $(this).attr('href').split('page=')[1];
             ajaxList(page)
         });
     </script>
