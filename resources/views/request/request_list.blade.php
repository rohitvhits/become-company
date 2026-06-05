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
            <h5 class="mb-0 font-weight-bold">Requested List ({{ $query->total() }})</h5>
            <div class="page-rightbtns">
                <div>

                    <a href="{{ URL::to('/') }}/request-list" class="btn btn-light btn-rounded btn-fw btn-sm"><i
                            class="mdi mdi-reload"></i> Reset</a>
                    <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                            class="fa fa-search"></i></button>

                </div>

            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div" style="display: none;">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            @csrf

                            <div class="row">


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
                            <th><input type="checkbox" id="main_checkBox1"><br>
                                <span class="main_checkBox1_error" style="color:red"></span>
                            </th>
                            <th>ID</th>

                            <th>Status</th>
                            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                            <th class="no_warp">Agency Name</th>
                            <?php } ?>
                            <th>Type/Discipline</th>
                            <th class="no_warp">Name/Mobile/Services </th>
                            <th class="no_warp">Assigned To</th>
                            <th class="no_warp">Due Date</th>
                            <th class="no_warp">Appointment Date - Location</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($query) > 0) {
                               $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                               foreach ($query as $row) {  ?>
                        <tr>
                            <td><input type="checkbox" class="cbox_id" value="<?php echo $row->patient_id; ?>"
                                    id="cbox_id<?php echo $row->id; ?>"></td>
                            <td><a
                                    href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->patient_id; ?>"><?= '#' . '' . $row->patient_id ?></a>
                            </td>

                            <td id="{{ $row->id }}">
                                <?php

                                           if (strtolower($row->status) == 'pending') {
                                           ?>
                                <label class='badge badge-warning'>Pending</label>

                                <?php } ?>
                                <?php

                                           if (strtolower($row->status) == 'approve') {
                                           ?>
                                <label class='badge badge-success'>Approved</label>

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

                            <td>
                                <?php echo $row->first_name . ' ' . $row->last_name; ?><br />
                                <?php echo $row->mobile; ?><br />
                                <?php echo $row->name; ?><br />
                            </td>
                            <td>{{ $row->assignToUser != null && isset($row->assignToUser->users) ? $row->assignToUser->users->full_name : 'N/A' }}
                            </td>
                            <td><?php if ($row->due_date!='' &&  $row->due_date != '1969-12-31') {
                                echo date('m/d/Y h:i A', strtotime($row->due_date)); 
                            } ?></td>

                            <td><?php if ($row->location_time_id_slot != '') {
                                echo date('m/d/Y', strtotime($row->appointment_date)) . '<br>' . date('h:i:s A', strtotime($row->start_time)) . ' To ' . date('h:i:s A', strtotime($row->end_time));
                            } else {
                                echo date('m/d/Y', strtotime($row->appointment_date)) . '<br>' . date('h:i:s A', strtotime($row->appointment_time));
                            } ?>
                                <br />
                                {{ $row->address1 . ' ' . $row->city }}<br />

                            </td>

                            <td><?php echo date('m/d/Y h:i A', strtotime($row->created_date)); ?><br />


                            @if(isset($row->users->first_name))
                                {{ $row->users->first_name . ' ' . $row->users->last_name }}
                                @endif
                            </td>
                            <?php if ($row->status != 'Approve') { ?>
                            <td>{{ $row->fu_date != '' && $row->fu_date != '1969-12-31' ? date('m/d/Y', strtotime($row->fu_date)) : null }}
                                <br />
                                <a href="javascriopt:void(0);" id="approve_status"
                                    class="btn btn-success btn-rounded btn-fw btn-sm ml-1"
                                    onclick="approveStatus({{ $row->id }},'{{ $row->status }}')"><i
                                        class="mdi mdi-relode">Approve</i></a>
                            </td>
                            <?php } ?>
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
                    {{ $query->links('pagination::bootstrap-4') }}
                </div>

            </div>

        </div>

    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    @include('include/footer')
    <script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <script>
        function approveStatus(id, status) {
            $.ajax({
                type: "GET",
                url: "{{ url('patient/approveStatus') }}",
                data: {
                    'id': id,
                    'status': status,
                },
                success: function(res) {
                    let html = '';
                    if (res.data.status == "1") {
                        html = '<label class="badge badge-success">Approved</label>';
                        toastr.success(res.error_msg);
                        $('#approve_status').hide();
                    } else {
                        html = '<label class="badge badge-danger">Pending</label>';
                        toastr.error(res.error_msg);
                    }
                    $('#' + res.data.id).html(html);
                }
            });
        }
    </script>
