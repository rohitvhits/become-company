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

    .recordtabletdwidth th:nth-child(2),
    .recordtabletdwidth td:nth-child(2) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child(3),
    .recordtabletdwidth td:nth-child(3) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child(4),
    .recordtabletdwidth td:nth-child(4) {
        min-width: 220px;
        max-width: 220px;
        width: 220px;
    }

    .recordtabletdwidth th:nth-child(5),
    .recordtabletdwidth td:nth-child(5) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child(6),
    .recordtabletdwidth td:nth-child(6) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .recordtabletdwidth th:nth-child(7),
    .recordtabletdwidth td:nth-child(7) {
        min-width: 210px;
        max-width: 210px;
        width: 210px;
    }

    .recordtabletdwidth th:nth-child(8),
    .recordtabletdwidth td:nth-child(8) {
        min-width: 176px;
        max-width: 176px;
        width: 176px;
    }

    .recordtabletdwidth th:nth-child(9),
    .recordtabletdwidth td:nth-child(9) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child(10),
    .recordtabletdwidth td:nth-child(10) {
        min-width: 110px;
        max-width: 110px;
        width: 110px;
    }

    .recordtabletdwidth th:nth-child(11),
    .recordtabletdwidth td:nth-child(11) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child(12),
    .recordtabletdwidth td:nth-child(12) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child(13),
    .recordtabletdwidth td:nth-child(13) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth td,
    .recordtabletdwidth th {
        white-space: inherit;
    }

    .recordtabletdwidth th:nth-child(14),
    .recordtabletdwidth td:nth-child(14) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child(15),
    .recordtabletdwidth td:nth-child(15) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child(16),
    .recordtabletdwidth td:nth-child(16) {
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
                    <th  style="white-space:nowrap">
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
                            <button type="button" class="record_id" data-field="name" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="name" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th  style="white-space:nowrap">
                    <div class="sorting-div"><span>Email</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="email" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="email" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th  style="white-space:nowrap">
                    <div class="sorting-div"><span>Phone</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="phone" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="phone" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th  style="white-space:nowrap">
                    <div class="sorting-div"><span>EMC User</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emc" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emc" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Medicaid Issue</span>
                        <div class="sorting-btn">
                            <button  type="button" class="record_id" data-field="medicaid" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button   type="button" class="record_id" data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Record Form</span>
                        <div class="sorting-btn">
                            <button   type="button" class="record_id" data-field="ny_medicare_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button  type="button" class="record_id" data-field="ny_medicare_id" data-sort="asc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>CIN</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="cin" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button  type="button" class="record_id" data-field="cin"  data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Follow Date</span>
                        <div class="sorting-btn">
                            <button  type="button" class="record_id" data-field="follow_date" data-sort="asc"><i class="fa fa-sort-up"></i> </button><butto  type="button" class="record_id" data-field="follow_date" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>File Date</span>
                        <div class="sorting-btn">
                            <button  type="button" class="record_id" data-field="file_date" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button  type="button" class="record_id" data-field="file_date" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th>
                    <div class="sorting-div"><span>Status</span>
                        <div class="sorting-btn">
                            <button  type="button" class="record_id" data-field="patient_status" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button  type="button" class="record_id" data-field="patient_status" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>


                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Created Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="created_at" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="created_at" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>



                <th style="min-width:220px;white-space:nowrap">
                    <div class="sorting-div"><span>Created By</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="created_by" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="created_by" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Action</span>
                        <div class="sorting-btn">
                            <button><i class="fa fa-sort-up"></i> </button><button><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
            </tr>
            <form method="get" action="">
                <tr>


                    <td style="white-space:nowrap"><input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm"
                            id="searchid" value="search"></td>
                    @if (in_array($user->user_type_fk, [3, 4]))

                        <td style="white-space:nowrap">

                            <select class="form-control" name="agency_fk1" id="agency_fk"
                                onchange="getUserList(this.value)">
                                <option value="">Select agency</option>
                                <?php foreach ($agencyList as $rwAgency) { ?>
                                <option value="<?php echo $rwAgency->id; ?>" <?php echo $agency_fk == $rwAgency->id ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    @endif

                    <td><input class="form-control" type="text" name="name" id="name" value="<?php echo $name; ?>">
                    </td>
                    <td style="white-space:nowrap"><input class="form-control" type="text" name="email" id="email" value="<?php echo $email; ?>">
                    </td>
                    <td style="white-space:nowrap"><input class="form-control" type="text" name="phone" id="phone" value="<?php echo $phone; ?>">
                    </td>
                    <td style="white-space:nowrap">
                        @if (in_array($user->user_type_fk, [3, 4]))
                            <select name="emcuser" class="form-control" id="emc_user_id">
                                <option value="">Select Emc User</option>
                                <?php if(!empty($userList)){
                                foreach($userList as $ke){?>
                                <option value="<?php echo $ke->id; ?>" <?php if ($emcuser == $ke->id) {
    echo "selected='selected'";
} ?>><?php echo $ke->first_name . ' ' . $ke->last_name; ?></option>
                                <?php } }?>
                            </select>
                        @endif
                    </td>
                    <td style="white-space:nowrap">

                        <select class="form-control" name="medicaid_issue" id="medicaid_issue">
                            <option value="">Medicaid Issue </option>
                            <?php
                            foreach ($masterData as $rwStatusd) {
                                if (in_array($rwStatusd->master_type_fk, array("4"))) {?>
                            <option value="<?= $rwStatusd->id ?>"
                                <?= $medicaid_issue == $rwStatusd->id ? 'selected' : '' ?>><?= $rwStatusd->name ?>
                            </option>
                            <?php } }?>
                        </select>
                    </td>
                    <td style="white-space:nowrap">
                        <select name="record_form" class="form-control" id="record_form">
                            <option value="">Select</option>
                            <option value="1" <?php if (isset($record_form) && $record_form == 1) {
    echo "selected='selected'";
} ?>>Ny Best Medical Care</option>
                            <option value="0" <?php if (isset($record_form) && $record_form == 0) {
    echo "selected='selected'";
} ?>>NY Best Medicalss</option>

                        </select>
                    </td>
                    <td style="white-space:nowrap">
                        <input type="text" name="cin_id" id="cin_id" class="form-control"
                            value="@if (isset($cin_id) && $cin_id != '') {{ $cin_id }} @endif">
                    </td>
                    <td style="white-space:nowrap"><input autocomplete="off" type="text" name="follow_date" id="follow_date"
                            class="form-control datepicker" value="@if ($follows_date != '') {{ $follows_date }} @endif"></td>
                    <td style="white-space:nowrap"><input autocomplete="off" type="text" name="filed_date" id="filed_date"
                            class="form-control datepicker1" value="@if ($filed_date != '') {{ $filed_date }} @endif"></td>
                    <td>

                        <select class="form-control" name="patient_status" id="patient_status">
                            <option value="">Select Status </option>
                            @foreach ($masterData as $rwStatus)
                                @if (in_array($rwStatus->master_type_fk, ['3']))
                                    <option value="{{ $rwStatus->id }}" @if ($patient_status == $rwStatus->id) selected @endif>
                                        {{ $rwStatus->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td style="white-space:nowrap"><input autocomplete="off" type="text" id="created_date" name="created_date"
                            class="form-control datepicker_date" value="@if ($created_date != '') {{ $created_date }} @endif"></td>
                    <td style="white-space:nowrap">
                        <select id="created_by_id" name="created_by[]" class="js-example-basic-multiple w-100"
                            multiple="multiple">
                            <option value="">Select User</option>
                        </select>
                    </td>
                    <td style="white-space:nowrap"></td>
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
                        <td style="white-space:nowrap"><a href="{{ url('/record/') }}/{{ $row->id }}">{{ $row->id }}</a></td>
                        @if (in_array($user->user_type_fk, [3, 4]))
                            <td style="white-space:nowrap">
                                <span id="changeAgencyList{{ $row->id }}">
                                    <?php /* onclick="changeAgency(<?= $row->id?> ?>)" */?>
                                    <span id="{{ $row->id }}">{{ $row->agency_name }}</span>

                                </span>
                            </td>

                        @endif
                        <td >
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
                        <td style="white-space:nowrap" contenteditable="{{ in_array($user->user_type_fk, [3, 4]) ? 'true' : 'false' }}"
                            onBlur="saveToDatabase(this,'email','{{ $row->id }}')" onClick="editRow(this);">
                            {{ $row->email }} </td>
                        <td style="white-space:nowrap" contenteditable="{{ in_array($user->user_type_fk, [3, 4]) ? 'true' : 'false' }}"
                            onBlur="saveToDatabase(this,'phone','{{ $row->id }}')" onClick="editRow(this);">
                            {{ $row->phone }}

                        </td>
                        <td style="white-space:nowrap">{{ $row->emcusername}}</td>
                        <td style="white-space:nowrap"> @if (isset($masterDataArray[$row->medicaid_issue])) {{ $masterDataArray[$row->medicaid_issue] }} @endif </td>
                        <td style="white-space:nowrap">
                            @if ($row->ny_medicare_id != '')
                                <label class='badge badge-primary badge-pill'>Ny Best Medical Care</label>
                            @else
                                <label class='badge badge-info badge-pill'>NY Best Medicalss</label>
                            @endif
                        </td>
                        <td style="white-space:nowrap">{{ $row->cin }}</td>
                        
                        <td style="white-space:nowrap" data-sort="@if (in_array($user->user_type_fk, [3, 4])) strtotime($row->follow_date))   @else strtotime($row->agency_follow_date))  @endif">
                            <span id="follow{{ $row->id }}">
                                @php
                                    $follow_date = '';
                                @endphp
                                @if (in_array($user->user_type_fk, [3, 4]))
                                    <div class="holder">
                                        <input name="date" class="datepicker-input" type="hidden" />
                                        <div class="date"
                                            contenteditable="{{ in_array($user->user_type_fk, [3, 4]) ? 'true' : 'false' }}">
                                            @if ($row->follow_date != '') {{ date('m/d/Y', strtotime($row->follow_date)) }} @endif
                                        </div>
                                    </div>
                                @else
                                <div class="holder">
                                        <input name="date" class="datepicker-input" type="hidden" />
                                        <div class="date"
                                            contenteditable="{{ in_array($user->user_type_fk, [5, 6]) ? 'true' : 'false' }}">
                                            @if ($row->agency_follow_date != '') {{ date('m/d/Y', strtotime($row->agency_follow_date)) }} @endif
                                        </div>
                                    </div>
                                   
                                @endif
                            </span>

                        </td>
                        <td style="white-space:nowrap">
                            @if (isset($row->file_date) && $row->file_date != '')
                                {{ date('m/d/Y', strtotime($row->file_date)) }}
                            @endif
                        </td>
                        <td>
                            <span id="change_patient_status{{ $row->id }}">
                                <span onclick="changeStatus({{ $row->id }})"> {{ $row->patient_status_name }}</span>
                            </span>
                        </td>
                        <td style="white-space:nowrap">
                            @if (isset($row->created_at) && $row->created_at != '')
                                {{ date('m/d/Y', strtotime($row->created_at)) }}
                            @endif
                        </td>

                        <td style="min-width:220px;white-space:nowrap">{{ $row->username }}</td>

                        <td style="white-space:nowrap">
                            <a href="javascript:void(0)" onclick="showMessages('{{ $row->id }}')"
                                class="product-title"><i class="fa fa-commenting-o" aria-hidden="true"></i></a>
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
    // Shows the datepicker when clicking on the content editable div
    $('.date').click(function() {
        // Triggering the focus event of the hidden input, the datepicker will come up.
        $(this).parent().find('.datepicker-input').focus();
    });

    $('.js-example-basic-multiple').select2();
    $('#total_record').html({{ $query->total()}});
    function getUserList(val) {
                 var html_res = '';
                 $('#created_by_id').html("");
                 if (val != '') {


                     var respnse = <?php echo $user_all_list; ?>;
                     html_res = '<option value="">Select User</option>';
                     $.each(respnse, function(i, v) {
                         if (v.agency_fk == val) {
                             if (v.name != null) {
                                 var selected = '';

                                 if (v.selected == 1) {
                                     selected = 'selected="selected"';
                                 }
                                 html_res += '<option value="' + v.id + '" ' + selected + '>' + v.name + '</option>';
                             }
                         }
                     });

                     console.log(html_res)
                 }

                 $('#created_by_id').html(html_res);
             }
             @if ($agency_fk != '')
                 getUserList({{ $agency_fk }});
             @endif
</script>
