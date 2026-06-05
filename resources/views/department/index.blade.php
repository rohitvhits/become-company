@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }} ">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}">
<link rel="stylesheet" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

/* Badge pulse animation */
.badge-pulse {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.3);
    }
    100% {
        transform: scale(1);
    }
}

/* Highlight animations for table rows */
.highlight-success {
    background-color: #d4edda !important;
    transition: background-color 1.5s ease-out;
}

.highlight-danger {
    background-color: #f8d7da !important;
    transition: background-color 0.3s ease-in;
}

/* Row fade in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.token-input-input-token input{
    width: 422px !important;
}
</style>
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Department Management</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('department-add')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn" onclick="openCreateModal()">
                        <i class="mdi mdi-plus"></i> Add Department
                    </a>
                    @endcan
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row ">
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
                                                    <label>Department Name</label>
                                                    <input type="text" name="search" class="form-control" placeholder="Search department..." value="" id="dp_name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" autocomplete="off" name="created_date" class="form-control" id="created_date" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created By</label>
                                                    <input type="text" autocomplete="off" class="form-control" name="created_by" id="created_by" style="width:100% !important" placeholder="Created By">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search"
                                                class="btn search-btn1 searchAppoinment" id="search-data"
                                                value="Search" onclick="loadDepartmentList(1)">
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refreshDepData();"><i class="mdi mdi-reload"></i> Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Department Table -->
                        <div class="table-responsive" id="dep-wise-data-loader">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="10%">Department Name</th>
                                        <th>Assigned Users</th>
                                        <th width="15%">Created Date</th>
                                        <th width="15%">Created By</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="shimmer-loader">
                                    <tr>
                                        <td colspan="6"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span id="department-list"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('department._partial.modal.store_department')
@include('department._partial.modal.delete_department')
@include('include/footer')
<script>
    var assignedUsers = [];
    var USER_ROUTE = "{{url('user-view')}}";
    var allUsers = @json($allUsers);
    var TASK_DEPARTMENT = "{{ url('tasks/department-master') }}";
    var TASK_DEPARTMENT_AJAX = "{{ url('tasks/department/ajax-list') }}";
    var TASK_DEPARTMENT_STATUS = "{{ url('tasks/department/status-update') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var urlToken = "{{ url('search-nybest-user') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/task_department/task_department.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>


