@include('include/header')
@include('include/sidebar')

<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/global.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

<style>
    .cron-log-modal .modal-dialog {
        max-width: 800px;
    }
    .cron-log-modal .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
    .cron-log-modal pre {
        background: #f5f5f5;
        padding: 10px;
        border-radius: 4px;
        max-height: 200px;
        overflow: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 12px;
    }
    .error-log-cell {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Alayacare Cron Log (<span id="total_count">0</span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter
                    </a>
                </div>
            </div>
        </div>
        <hr />

        {{-- Filter Section --}}
        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>	Created Date</label>
                                                    <input type="text" name="date_range" id="date_range" class="form-control" autocomplete="off" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <input type="text" name="type" id="type" class="form-control" placeholder="Enter Type">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Cron Type</label>
                                                    <input type="text" name="cron_type" id="cron_type" class="form-control" placeholder="Enter Cron Type">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="cronLogList(1)">
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="resetData()">
                                                <i class="mdi mdi-reload"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id hasClass">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <th>#</th>
                                <th style="white-space:nowrap">ID</th>
                                <th style="white-space:nowrap">Type</th>
                                <th style="white-space:nowrap">Cron Type</th>
                                <th style="white-space:nowrap">Agency Name</th>
                                <th style="white-space:nowrap">Employee ID</th>
                                <th style="white-space:nowrap">Line</th>
                                <th style="white-space:nowrap">Error Log</th>
                                <th style="white-space:nowrap">Created Date</th>
                                <th style="white-space:nowrap">Action</th>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="resp"></span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'></div>
</div>

{{-- View Detail Modal --}}
@include('alayacare_cron_log._modal')

@include('include/footer')

<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/modulejs/alayacareCronLog/alayacare_cron_log.js')}}?time={{ env('timestamp')}}"></script>
<script>
var _AJAX_LIST = "{{ url('alayacare/alayacare-cron-log/list') }}";
var _VIEW_LOG = "{{ url('alayacare/alayacare-cron-log/view') }}";
    

</script>
