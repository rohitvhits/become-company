@include('include/header')
@include('include/sidebar')
<style>
    .page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

</style>
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Send Log(<span id="total_record_id">0</span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" autocomplete="off" name="created_date" class="form-control" id="created_date" readonly placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadHHASendLogAjax(1)">
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refreshHHASendLog()"><i class="mdi mdi-reload"></i> Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id table-responsive">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="white-space:nowrap">Portal ID</th>
                                    <th style="white-space:nowrap">Patient Name</th>
                                    <th style="white-space:nowrap">Caregiver ID</th>
                                    <th style="white-space:nowrap">Type</th>
                                    <th style="white-space:nowrap">Module Name</th>
                                    <th style="white-space:nowrap">Created Date</th>
                                    <th style="white-space:nowrap">Created By</th>
                                    <th style="white-space:nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="9"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table table-responsive">
                    <span id="response_hha_send_log"></span>
                </div>
            </div>
        </div>

    </div>

    <div class="row" id="blank_div" style='margin-top: 25px;'></div>

</div>

<!-- View Modal -->
<div class="modal fade" id="hha-send-log-modal" tabindex="-1" aria-labelledby="hhaSendLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 70%;">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title" id="hhaSendLogModalLabel">Send Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="hha-send-log-modal-body" style="max-height:500px;overflow-y:auto;background-color:white">
            </div>
            <div  class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />

<script>
    var _HHA_SEND_LOG_AJAX = "{{ url('hha/hha-send-log/ajax-list') }}";
    var _HHA_SEND_LOG_VIEW = "{{ url('hha/hha-send-log/view-detail') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
</script>

<script src="{{ asset('assets/modulejs/hhaSendLog/hha_send_log.js')}}?time={{ time() }}"></script>
