@if (in_array($user->user_type_fk, [5, 6]))
    @php
        $i = 1;
    @endphp
@else
    @php
        $i = 0;
    @endphp
@endif

<style>
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    ..select2-container {
        width: 200px !important;
    }

    .table-head-fix tbody {
        display: block;
        max-height: calc(100vh - 350px);
        overflow-y: scroll;
    }

    .table-head-fix tbody::-webkit-scrollbar {
        width: 0;
        height: 0;
    }

    .table-head-fix thead,
    .table-head-fix tbody tr {
        display: table;
        width: 100%;
    }

    .recordtabletdwidth th:nth-child(1),
    .recordtabletdwidth td:nth-child(1) {
        min-width: 100px;
        max-width: 100px;
        width: 100px;
    }

    .recordtabletdwidth th:nth-child({{ 2 - $i }}),
    .recordtabletdwidth td:nth-child({{ 2 - $i }}) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child({{ 3 - $i }}),
    .recordtabletdwidth td:nth-child({{ 3 - $i }}) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }


    .recordtabletdwidth th:nth-child({{ 4 - $i }}),
    .recordtabletdwidth td:nth-child({{ 4 - $i }}) {
        min-width: 220px;
        max-width: 220px;
        width: 220px;
    }

    .recordtabletdwidth th:nth-child({{ 5 - $i }}),
    .recordtabletdwidth td:nth-child({{ 5 - $i }}) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child({{ 6 - $i }}),
    .recordtabletdwidth td:nth-child({{ 6 - $i }}) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .recordtabletdwidth th:nth-child({{ 7 - $i }}),
    .recordtabletdwidth td:nth-child({{ 7 - $i }}) {
        min-width: 210px;
        max-width: 210px;
        width: 210px;
    }

    .recordtabletdwidth th:nth-child({{ 8 - $i }}),
    .recordtabletdwidth td:nth-child({{ 8 - $i }}) {
        min-width: 176px;
        max-width: 176px;
        width: 176px;
    }

    .recordtabletdwidth th:nth-child({{ 9 - $i }}),
    .recordtabletdwidth td:nth-child({{ 9 - $i }}) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child({{ 10 - $i }}),
    .recordtabletdwidth td:nth-child({{ 10 - $i }}) {
        min-width: 110px;
        max-width: 110px;
        width: 110px;
    }

    .recordtabletdwidth th:nth-child({{ 11 - $i }}),
    .recordtabletdwidth td:nth-child({{ 11 - $i }}) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child({{ 12 - $i }}),
    .recordtabletdwidth td:nth-child({{ 12 - $i }}) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child({{ 13 - $i }}),
    .recordtabletdwidth td:nth-child({{ 13 - $i }}) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth td,
    .recordtabletdwidth th {
        white-space: inherit;
    }

    .recordtabletdwidth th:nth-child({{ 14 - $i }}),
    .recordtabletdwidth td:nth-child({{ 14 - $i }}) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth th:nth-child({{ 15 - $i }}),
    .recordtabletdwidth td:nth-child({{ 15 - $i }}) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth th:nth-child({{ 16 - $i }}),
    .recordtabletdwidth td:nth-child({{ 16 - $i }}) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child({{ 17 - $i }}),
    .recordtabletdwidth td:nth-child({{ 17 - $i }}) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }



    .sorting-btn {
        display: flex;
        flex-direction: column;
        margin-left: auto;
    }

    .sorting-div {
        display: flex;
        align-items: center;
    }

    .sorting-btn button {
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent;
        line-height: 0.5;
    }

    .sorting-btn button i {
        line-height: 0.3;
    }

    .order-listing-loader {
        position: absolute;
        left: 0;
        top: 0;
        background: #ffffff94;
        bottom: 0;
        right: 0;
        width: 100%;
        font-size: 30px;
        display: none;
        align-items: center;
        justify-content: center;

    }

</style>
<div class="table-responsive">
    <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Record</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="id" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                @if (in_array($user->user_type_fk, [3, 4]))
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Agency Name</span>
                            <div class="sorting-btn">
                                <button type="button" class="record_id" data-field="agency_fk" data-sort="asc"><i
                                        class="fa fa-sort-up"></i> </button><button type="button"
                                    class="record_id" data-field="agency_fk" data-sort="desc"><i
                                        class="fa fa-sort-down"></i> </button>
                            </div>
                        </div>
                    </th>
                @endif
                <th>
                    <div class="sorting-div"><span>Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="name" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="name" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
               
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="patient_status_name" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="patient_status_name" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Closed Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="closed_date" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="closed_date" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Closed Reason</span>
                        
                    </div>
                </th>
            </tr>
            <form method="get" action="">
                <tr>


                    <td style="white-space:nowrap"><input type="button" name="search"
                            class="btn btn-primary btn-fw pull-right btn-sm" id="searchid" value="search"></td>
                    @if (in_array($user->user_type_fk, [3, 4]))

                        <td style="white-space:nowrap">

                            <select class="form-control" name="agency_fk1" id="agency_fk"
                                >
                                <option value="">Select agency</option>
                                <?php foreach ($agencyList as $rwAgency) { ?>
                                <option value="<?php echo $rwAgency->id; ?>" <?php echo $agency_fk == $rwAgency->id ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    @endif

                    <td><input class="form-control" type="text" name="name" id="name" value="<?php echo $name; ?>">
                    </td>
                    <td style="white-space:nowrap">

                        <select class="form-control" name="status" id="status"
                            >
                            <option value="">Select Status</option>
                            <?php foreach ($master_list as $masters) { ?>
                            <option value="<?php echo $masters->id; ?>" @if($status ==$masters->id) selected @endif><?php echo $masters->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td style="white-space:nowrap"><input autocomplete="off" type="text" id="closed_date"
                            name="closed_date" class="form-control datepicker_date" value="@if ($created_date != '') {{ $created_date }} @endif">
                    </td>
                    <td></td>
                    
                </tr>
            </form>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $row)
                    <tr>
                        <td style="white-space:nowrap"><a
                                href="{{ url('/record/') }}/{{ $row->id }}" target="_blank">{{ $row->id }}</a></td>
                        @if (in_array($user->user_type_fk, [3, 4]))
                            <td style="white-space:nowrap">
                                <span id="changeAgencyList{{ $row->id }}">

                                    <span id="{{ $row->id }}">{{ $row->agency_name }}</span>

                                </span>
                            </td>

                        @endif
                        <td>
                            @if (in_array($user->user_type_fk, [3, 4]))

                                <span contenteditable="true"
                                    onBlur="saveToDatabase(this,'first_name','{{ $row->id }}')"
                                    onClick="editRow(this);">
                                    <?= $row->first_name != '' ? $row->first_name : '  ' ?>

                                </span>
                                <span contenteditable="true"
                                    onBlur="saveToDatabase(this,'middle_name','{{ $row->id }}')"
                                    onClick="editRow(this);">@if ($row->middle_name != '') {{ $row->middle_name }} @endif</span>
                                <span contenteditable="true"
                                    onBlur="saveToDatabase(this,'last_name','{{ $row->id }}')"
                                    onClick="editRow(this);">@if ($row->last_name != '') {{ $row->last_name }} @endif</span>
                            @endif

                            @if (!in_array($user->user_type_fk, [3, 4]))
                                {{ $row->first_name }} {{ $row->middle_name }} {{ $row->last_name }}


                            @endif

                        </td>
                        <td style="white-space:nowrap">
                        {{ $row->statusName }}
                        </td>
                        
                        <td style="white-space:nowrap">
                            @if (isset($row->closed_date) && $row->closed_date != '')
                                {{ date('m/d/Y h:i A', strtotime($row->closed_date)) }}
                            @endif
                        </td>
                        
                        <td style="white-space:nowrap">
                            
                                {{ $row->name }}
                        
                        </td>
                        
                    </tr>

                @endforeach

            @endif
            @if (count($query) == 0)
                <tr>
                    <td colspan="12">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

<script>
    $('.datepicker').datepicker();
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
        $('#dob_id').datepicker({
            changeMonth: true,
            changeYear: true,
        });



        $('.datepicker_date').daterangepicker({
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

            $('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

    });
    // Binds the hidden input to be used as datepicker.
    
    // Shows the datepicker when clicking on the content editable div
    $('.date').click(function() {
        // Triggering the focus event of the hidden input, the datepicker will come up.
        $(this).parent().find('.datepicker-input').focus();
    });

   
    $('#total_record').html({{ $query->total() }});
</script>
