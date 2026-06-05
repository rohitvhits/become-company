@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

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

    .token-display {
        font-family: monospace;
        background-color: #f8f9fa;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        word-break: break-all;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">App Token Generator</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('create-app-token-generate')
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal-add-app-token" class="btn btn-primary cust-right-btn">
                        <i class="mdi mdi-plus"></i> Add New App Token
                    </a>
                    @endcan
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
                                                    <label for="search_app_name">App Name</label>
                                                    <input type="text" name="app_name" class="form-control" id="search_app_name" placeholder="Enter App Name">
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
                                    <th>ID</th>
                                    <th>App Name</th>
                                    <th>Token</th>
                                    <th>Referral Type</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="8"></td>
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

@include('app_tokens._partial.create_app_token_modal')
@include('app_tokens._partial.edit_app_token_modal')
@include('include/footer')

<script>
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var _APP_TOKENS_URL = "{{ url('app-tokens') }}";
    var _APP_TOKENS_AJAX_LIST = "{{ url('app-tokens-ajax-list') }}";
    var _SAVE_APP_TOKEN = "{{ url('app-tokens')}}";
</script>

<script src="{{ asset('assets/modulejs/app_tokens/app_tokens.js') }}?time={{ env('timestamp') }}"></script>
