@php
$i = 0;
@endphp

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
        background: #7571f9 !important;
        border: #7571f9 !important;
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
@include('patient/appointment_basic')
{{-- <div class="table-responsive"> --}}
{{-- <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div> --}}
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
                        {{-- <form method="get" action="" id="search_form">
                            <tr>
                                <td>
                                    <button name="button" value="Search" class="btn btn-primary btn-sm btn-rounded"
                                        id="search_id">Search</button>

                                </td>
                                <td>
                                    <select name="agency_id" id="agency_ids" class="form-control">
                                        <option value="">Select Agency</option>
                                        @if (!empty($agency_list))
                                            @foreach ($agency_list as $val)
                                                <option value="{{ $val->id }}"
                                                    @if ($agency_name == $val->id) selected @endif>
                                                    {{ $val->agency_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select name="doctor_id" id="doctor_id" class="form-control">
                                        <option value="">Select Doctor</option>
                                        @if (!empty($doctor_list))
                                            @foreach ($doctor_list as $doc)
                                                <option value="{{ $doc->id }}"
                                                    @if ($doctor_id == $val->id) selected @endif>
                                                    {{ $doc->full_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select name="type" class="form-control" id="type">
                                        <option value="">Select Type</option>
                                        <option value="Caregiver" @if ($type == 'Caregiver') selected @endif>
                                            Caregiver
                                        </option>
                                        <option value="Patient" @if ($type == 'Caregiver') selected @endif>
                                            Patient
                                        </option>

                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="full_name" id="full_name"
                                        value="{{ $full_name }}" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="phone_no" id="phone_no" value="{{ $phone_no }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="dob" id="dob" value="{{ $dob }}"
                                        class="datepicker form-control">
                                </td>
                                <td>
                                    <select name="locationId" class="form-control" id="locationId">
                                        <option value="">Select Location</option>
                                        @foreach ($location_list as $vsl)
                                            <option value="{{ $vsl->id }}"
                                                @if ($locationId == $vsl->id) selected @endif>{{ $vsl->address1 }}
                                                {{ $vsl->city }}</option>
                                        @endforeach

                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="appoinment_date" id="appoinment_date"
                                        value="{{ $appoinment_date }}" class="datepicker1 form-control">
                                </td>
                                <td>

                                </td>

                                <td>
                                    <select class="js-example-basic-multiple w-100" multiple="multiple"
                                        name="service_id[]" id="service_id">

                                        <option value="">Select Service</option>
                                        @php
                                            $final_array = [];
                                            if (!empty($service_id)) {
                                                foreach ($service_id as $vals) {
                                                    $final_array[] = $vals;
                                                }
                                            }
                                        @endphp

                                        @foreach ($serviceList as $service)
                                            <option value="{{ $service->id }}"
                                                @if (in_array($service->id, $final_array)) selected @endif>{{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </td>
                                <td>


                                </td>
                                <td>
                                    <input type="text" name="created_date" value="{{ $datepickernn }}"
                                        id="datepickernn" class="datepickernn form-control" style="width:86px">
                                </td>



                            </tr>
                        </form> --}}
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
        {{-- </div> --}}
        <div class="pull-right pegination-margin">
            {{ $open_record_list->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>


        @include('patient/appointment_search_js')


        <script>
            var totalCount = {{ $open_record_list->total() }};
            $(".datepicker").datepicker();
            $(document).ready(function(e) {

                setTimeout(function(e) {
                    $('#totalCount_id').html(totalCount);
                }, 1000);
            });
        </script>
        <script>
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

            });

            $('.js-example-basic-multiple').select2();
        </script>
