@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<div class="main-panel">
    <div class="content-wrapper">
        <div class="col-12 grid-margin-top">

        </div>
        <div class="card">
            <div class="row list-name">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">Agency Mail Log List</h4>
                </div>
                <div class="col-sm-6">

                    <a href="{{ url('agencies-mail-log/export') }}?agency_id={{ $agency_id }}&email={{ $email }}&created_date={{ $created_date }}"
                        class="btn btn-warning btn-sm pull-right" style="margin-left:10px;"><i class="mdi mdi-file">
                        </i> Export CSV</a>&nbsp;&nbsp;
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">

                            <table id="" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Agency Name</th>
                                        <th>Email</th>
                                        <th>Created Date</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <form>
                                        <tr>
                                            <td><input class="btn btn-primary" type="submit" name="submit"
                                                    value="Search"></td>
                                            <td>
                                                <select name="agency_id" class="form-control">
                                                    <option value="">Select Agency</option>
                                                    @foreach ($agency_list as $val)
                                                        <option value="{{ $val->id }}" @if ($agency_id == $val->id)  selected @endif>
                                                            {{ $val->agency_name }}</option>

                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="email" class="form-control"
                                                    value="{{ $email }}" placeHolder="Enter Email">
                                            </td>
                                            <td>
                                                <input type="text" name="created_date" class="form-control datepickernn"
                                                    value="{{ $created_date }}" placeHolder="Enter Created Date">
                                            </td>
                                        </tr>
                                    </form>
                                    @php
                                        $i = 1 + ($agency_log->currentPage() - 1) * $agency_log->perPage();
                                    @endphp
                                    @if ($agency_log->total() != 0)

                                        @foreach ($agency_log as $row)
                                            <tr id="row{{ $row->id }}">
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    {{ $row->agency_name }}
                                                </td>
                                                <td style="max-width:200px;white-space:inherit">
                                                    {{ $row->notification_email }}
                                                </td>
                                                <td>
                                                    {{ date('m/d/Y h:i A', strtotime($row->created_date)) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if ($agency_log->total() == 0)
                                        <tr>
                                            <td colspan="12">
                                                <center><b>Data not found</b></center>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="pull-right pegination-margin">
                                {{ $agency_log->links('pagination::bootstrap-4') }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('include/footer')

    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
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
            });
        });
    </script>
