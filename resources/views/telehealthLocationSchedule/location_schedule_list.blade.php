@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link href="{{ asset('/assets/bootstrap-datetimepicker.min.css')}}" type="text/css" media="all" rel="stylesheet" />
<link href="{{ asset('/assets/modulejs/css/telehealth_location.css')}}" type="text/css" media="all" rel="stylesheet" />
<link href="<?php echo URL::to('/'); ?>/assets/css/global.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/modulejs/css/task-module.css')}}" type="text/css" media="all" rel="stylesheet" />
<div class="main-panel">
     @php
         $auth = auth()->user();
     @endphp
     <div class="content-wrapper">
         <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Telehealth Location Schedule</h5>
            <div class="page-rightbtns">
                <div>
                    @can('add-telehealth-location-schedule')
                        <a class="add-modal-edit btn btn-primary btn-rounded btn-fw btn-sm" data-toggle="modal" data-target="#addModal" data-id="{{ $location_id}}" href="javascript::void(0)"><i class="mdi mdi-plus"> </i> Add Schedule </a>
                    @endcan
                </div>
            </div>
         </div>

         <div class="card common-card-box">
            <div class="card-body table-responsive tele-loc-wise-data-loader">
            <table id="" class="table table-bordered ">
                    <thead>
                    <th width="5%">#</th>
                        <th width="20%">Title</th>
                        <th width="15%">Day</th>
                        <th width="15%">Start Time</th>
                        <th width="15%">End Time</th>
                        <th width="5%">Status</th>
                        <th width="10%">Time Slot <br/>(In Minutes)</th>
                        <th width="15%">Action</th>
                    </thead>
                    <tbody class="shimmer-loader">
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                    </tbody>
                </table>
            </div> 
            <span id="location_resp_id">
            </span>         
        </div> 

        <div class="card common-card-box mt-3">
            <div class="page-title-main m-3">
                <h5 class="mb-0 font-weight-bold">Location Schedule Logs</h5>
                <div class="page-rightbtns">
                    <div>
                    <a onclick="getData()" class="pull-right btn btn-primary btn-rounded btn-fw btn-sm" href="javascript::void(0)"> Show Logs </a>
                                        </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12" id="logList">
                    </div>
                </div>
            </div>
        </div>
     </div>
</div>
@include('telehealthLocationSchedule.add_schedule')
@include('telehealthLocationSchedule.edit_schedule')
@include('include/footer')
<link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/telehealth_location_schedule.js')}}"></script>

<script>
    var SCHEDULE_LOG = "{{ url('telehealth-schedule-view-logs') }}";
    var LOCATION_ID = "{{ $location_id }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var SCHEDULE_STATUS = "{{ url('schedule-status-change') }}";
    var TELEHEALTH_LOCATION_SCHEDULE = "{{ url('telehealth-location-schedule') }}";
    var TELEHEALTH_LOCATION_SCHEDULE_AJAX = "{{ url('telehealth-location-schedule-ajax') }}";
</script>
