@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css" />
<style>
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
            <h5 class="mb-0 font-weight-bold">Esign Documents</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('asign-emc-esign')
                    <a href="javascript:void(0)" id="open-assign-user-modal" class="btn cust-right-btn" style="background-color: #28a745;color:#fff;"><i class="mdi mdi-plus"></i>Assign Esign User</a>
                    @endcan
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i> Filter</a>
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
                                            <label>Template Name</label>
                                            <input type="text" name="template_name" class="form-control" id="filter_template_name" placeholder="Enter Template Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <label>Status</label>
                                            <select name="status" id="filter_status" class="form-control">
                                                <option value="">All</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <label>Created Date</label>
                                            <input type="text" name="created_date" class="form-control" id="filter_created_date" placeholder="Select Date Range" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadDashboardList()">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="resetFilters()"><i class="mdi mdi-reload"></i> Reset</a>
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
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Agency Name</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Template Name</th>
                                    <th>Document Status</th>
                                    <th>Created Date</th>
                                    <th>Created By</th>
                                    <th>Completed Date / By</th>
                                    <th>Approved Date / By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="11"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="response_requested_id"></span>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 15%;'>
        <pre id='toastrOptions'></pre>
    </div>
  
</div>

<!-- Default Assign Esign User Modal -->
<div class="modal fade" id="assignEsignUserModal" tabindex="-1" aria-labelledby="assignEsignUserModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="assignEsignUserModalLabel">Default Assign Esign User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Search User <span class="error">*</span></label>
                    <input type="text" id="assign_esign_user_search" name="assign_esign_user_search" />
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-success" id="btn-assign-esign-user-submit">
                        <span class="spinner-border spinner-border-sm d-none" id="assign-user-loader"></span>
                        Submit
                    </button>
                    <button type="button" class="btn btn-sm btn-light" id="btn-refresh-assign-user-list">
                        <i class="mdi mdi-reload"></i> Refresh
                    </button>
                </div>
                <hr />
                <h6 class="font-weight-bold">Assigned Users</h6>
                <div class="location-wise-data-loader assign_user_shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Assigned Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="default-assign-user-list-container"></div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<script>
var _LOAD_DATA_URL = "{{ url('esign/esign-patient-dashboard/esign-dashboard-ajax-list') }}";
var _SEARCH_NYBEST_USER = "{{ url('search-nybest-user') }}";
var _STORE_ASSIGN_USER_URL = "{{ url('esign/esign-patient-dashboard/default-assign-esign-user/store') }}";
var _LIST_ASSIGN_USER_URL = "{{ url('esign/esign-patient-dashboard/default-assign-esign-user/list') }}";
var _DELETE_ASSIGN_USER_URL = "{{ url('esign/esign-patient-dashboard/default-assign-esign-user/delete') }}";
var _CSRF_TOKEN = '{{ csrf_token()}}';
</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets\modulejs\esignPatientDashboard\esign_patient_dashboard.js')}}?time={{ env('timestamp')}}"></script>