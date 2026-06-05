@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
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
            <h5 class="mb-0 font-weight-bold">Branch Link Management</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('branch-link-add')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn" onclick="openCreateLinkModal()">
                        <i class="mdi mdi-plus"></i> Add Branch Link
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
                                                    <label>Branch</label>
                                                    <select name="branch_id" id="filter_branch_id" class="form-control">
                                                        <option value="">Select Branch</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency</label>
                                                    <select name="agency_id" id="filter_agency_id" class="form-control">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agencies as $agency)
                                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" autocomplete="off" name="created_date" class="form-control" id="link_created_date" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadBranchLinkList(1)">
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refreshLinkData();"><i class="mdi mdi-reload"></i> Clear</a>
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
                        <div class="table-responsive" id="branch-link-data-loader">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Name</th>
                                        <th>Agency</th>
                                        <th>Service</th>
                                        <th>Created Date</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="shimmer-loader">
                                    <tr>
                                        <td colspan="7"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span id="branch-link-list"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('branch_link._partial.modal.store_branch_link')
@include('branch_link._partial.modal.delete_branch_link')
@include('include/footer')
<script>
    var BRANCH_LINK_MASTER = "{{ url('branch-link') }}";
    var BRANCH_LINK_AJAX = "{{ url('branch-link-ajax/ajax-list') }}";
    var CHANGE_MANDATORY_OPTION = "{{ url('branch-link-ajax/change-mandatory') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var allBranches = @json($branches);
    var allAgencies = @json($agencies);
    var allServices = @json($services);
</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/branch/branch_link.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
