@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .actions {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .select2-container {
        width: 100% !important;
    }
    .modal-header .close {
        padding: 1.5rem;
        margin: -1rem -1rem -1rem auto;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">User Doc Approval</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" onclick="openCreateModal()" class="btn btn-primary cust-right-btn">
                        <i class="mdi mdi-plus"></i> Add User Doc Approval
                    </a>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter <span class="active-filter"></span>
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
                                                    <label for="search_name">Name</label>
                                                    <select name="name" class="form-control select2" id="search_name">
                                                        <option value="">-- All --</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="search_key">Key</label>
                                                    <select name="key" class="form-control select2" id="search_key">
                                                        <option value="">-- All --</option>
                                                        <option value="181">With MDO</option>
                                                        <option value="without_service">All Service</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadAjaxList()">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
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
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th>Name</th>
                                    <th style="width:20%;">Key</th>
                                    <th style="width:15%;">Created Date</th>
                                    <th style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="response_requested_id"></span>
            </div>
        </div>

    </div>
    <div id="blank_div">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>

@include('user_doc_approval._partial.create_modal')
@include('user_doc_approval._partial.edit_modal')

@include('include/footer')

<script>
    var _AJAX_LIST  = "{{ url('user-doc-approval-ajax-list') }}";
    var _STORE_URL  = "{{ url('user-doc-approval') }}";
    var _UPDATE_URL = "{{ url('user-doc-approval') }}";
    var _DELETE_URL = "{{ url('user-doc-approval') }}";
    var _SHOW_URL   = "{{ url('user-doc-approval') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';

    var _USER_LIST = {!! json_encode($userList->map(fn($u) => ['id'=>$u->id,'name'=>trim($u->first_name.' '.$u->last_name)])) !!};
</script>

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/modulejs/user_doc_approval/user_doc_approval.js') }}?time={{ time() }}"></script>
