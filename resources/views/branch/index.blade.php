@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}">
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
            <h5 class="mb-0 font-weight-bold">Branch Management</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('branch-link-list')
                    <a target="_blank" class="btn btn-info cust-right-btn" href="{{ url('branch-link') }}">
                        <i class="fa fa-external-link"></i> Branch Link
                    </a>
                    @endcan
                    @can('branch-add')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn" onclick="openCreateModal()">
                        <i class="mdi mdi-plus"></i> Add Branch
                    </a>
                    @endcan
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr/>
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
                                                    <label>Branch Name</label>
                                                    <input type="text" name="branch_name" class="form-control" placeholder="Search branch..." value="" id="branch_name">
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
                                            <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadBranchList(1)">
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refreshBranchData();"><i class="mdi mdi-reload"></i> Clear</a>
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
                        <div class="table-responsive" id="branch-data-loader">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Branch Name</th>
                                        <th width="15%">Status</th>
                                        <th width="20%">Created Date</th>
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
                        <span id="branch-list"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('branch._partial.modal.store_branch')
@include('branch._partial.modal.delete_branch')
@include('include/footer')
<script>
    var BRANCH_MASTER = "{{ url('branch-master') }}";
    var BRANCH_AJAX = "{{ url('branch/ajax-list') }}";
    var BRANCH_STATUS = "{{ url('branch/status-update') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var urlToken = "{{ url('search-nybest-user') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/branch/branch.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
