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

    .recordtabletdwidth th:nth-child( {
                {
                2 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                2 - $i
            }
        }

    ) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                3 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                3 - $i
            }
        }

    ) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }


    .recordtabletdwidth th:nth-child( {
                {
                4 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                4 - $i
            }
        }

    ) {
        min-width: 220px;
        max-width: 220px;
        width: 220px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                5 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                5 - $i
            }
        }

    ) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                6 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                6 - $i
            }
        }

    ) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                7 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                7 - $i
            }
        }

    ) {
        min-width: 210px;
        max-width: 210px;
        width: 210px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                8 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                8 - $i
            }
        }

    ) {
        min-width: 176px;
        max-width: 176px;
        width: 176px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                9 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                9 - $i
            }
        }

    ) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                10 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                10 - $i
            }
        }

    ) {
        min-width: 110px;
        max-width: 110px;
        width: 110px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                11 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                11 - $i
            }
        }

    ) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                12 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                12 - $i
            }
        }

    ) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                13 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                13 - $i
            }
        }

    ) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth td,
    .recordtabletdwidth th {
        white-space: inherit;
    }

    .recordtabletdwidth th:nth-child( {
                {
                14 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                14 - $i
            }
        }

    ) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                15 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                15 - $i
            }
        }

    ) {
        min-width: 155px;
        max-width: 155px;
        width: 155px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                16 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                16 - $i
            }
        }

    ) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                17 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                17 - $i
            }
        }

    ) {
        min-width: 105px;
        max-width: 105px;
        width: 105px;
    }

    .recordtabletdwidth th:nth-child( {
                {
                18 - $i
            }
        }

    ),
    .recordtabletdwidth td:nth-child( {
                {
                18 - $i
            }
        }

    ) {
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
                    <div class="sorting-div"><span>No</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>User Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="user_name" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="user_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>

                <th>
                    <div class="sorting-div"><span>Ip Address</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="ip" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="ip" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>

                <th>
                    <div class="sorting-div"><span>Country</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="country" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="country" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>

                <th>
                    <div class="sorting-div"><span>Country Code</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="country_code" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="country_code" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th>
                    <div class="sorting-div"><span>Login status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="login_status" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="login_status" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="min-width:220px; white-space:nowrap">
                    <div class="sorting-div"><span>Created Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="created_at" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="created_at" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>

            </tr>
            <form method="get" action="">
                <tr>


                    <td style=" white-space:nowrap"><input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm" id="searchid" value="search"></td>

                    <td style="min-width:220px; white-space:nowrap"><input class="form-control" type="text" name="user_name" id="user_name" value="{{$userName}}">
                    </td>
                    <td style="min-width:220px; white-space:nowrap"><input class="form-control" type="text" name="ip" id="ip" value="{{$ip}}">
                    </td>
                    <td style="min-width:220px; white-space:nowrap"><input class="form-control" type="text" name="country" id="country" value="{{$country}}">
                    </td>
                    <td style="min-width:220px; white-space:nowrap"><input class="form-control" type="text" name="country_code" id="country_code" value="{{$countryCode}}">
                    </td>
                    <td style="min-width:220px;  white-space:nowrap">

                        <select class="form-control" name="login_status" id="login_status">
                            <option value="">Select Login status</option>

                            <option value="success" @if ($loginStatus=='success' ) selected @endif>
                                Success</option>
                            <option value="failed" @if ($loginStatus=='failed' ) selected @endif>
                                Failed</option>

                        </select>

                    <td style="white-space:nowrap">
                        <input autocomplete="off" type="text" id="created_date" name="created_date" class="form-control datepicker_date" value="@if ($createdAt != '') {{ $createdAt }} @endif">
                    </td>

                </tr>
            </form>
        </thead>
        <tbody>
            @php
            $i = 1 + ($logList->currentPage() - 1) * $logList->perPage();
            @endphp

            @forelse ($logList as $row)
            <tr>

                <td style="white-space:nowrap">{{$i++}}</td>

                <td style="min-width:220px; white-space:nowrap" data-sort="">
                    <a href="{{URL::to('/')}}/user-view/{{$row->user_id}}">{{ ucfirst($row->username) }}</a>
                </td>
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->ipaddress}}
                </td>


                <td style="min-width:220px; white-space:nowrap">{{$row->country}}</td>
                <td style="min-width:220px; white-space:nowrap">{{$row->country_code}}</td>
                <td style="min-width:220px; white-space:nowrap">{{ucfirst($row->login_status)}}</td>

                <td style="min-width:220px;  white-space:nowrap">
                    {{ date('m/d/Y h:i A', strtotime($row->created_at)) }}
                </td>

            </tr>

            @empty
            <tr>
                <td colspan="12">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $logList->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    $('#total_record').html("{{ $logList->total() }}");
    var start = moment().subtract(0, 'days');
var end = moment();
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
}, function (chosen_date, end_date) {

    $('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
        'MM/DD/YYYY'));
})
</script>