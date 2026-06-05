@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href=" {{URL::to('/') }}/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ URL::to('/') }}/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    ..select2-container {
        width: 200px !important;
    }

    .wmd-view-topscroll,
    .wmd-view {
        overflow-x: scroll;
        overflow-y: hidden;
        border: none 0px red;
    }

    .wmd-view-topscroll {
        height: 20px;
    }

    .scroll-div1 {

        overflow-x: scroll;
        overflow-y: hidden;
        height: 20px;
    }

    .scroll-div2 {
        height: 20px;
    }

    .scroll-div1,
    .scroll-div2 {
        width: 2000px;
    }
</style>
<div class="main-panel">

    <div class="content-wrapper">
        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
            @if (Session::has('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
        </div>
        <div class="card">
            <div class="row list-name">
                <div class="col-sm-5">
                    <h4 class="card-title">Login Log List (<span id="total_record"></span>)</h4>
                </div>
                <div class="col-sm-7 pull-right">


                    <a href="#" class="btn btn-success btn-btn-sm pull-right" id="export-data" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                    <a href="#" id="resetTable" class="btn btn-light btn-btn-sm pull-right"><i class="mdi mdi-reload"></i> Reset</a>
                </div>
            </div>

            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <span id="loginlog_list_id"></span>
                        <input type="hidden" name="" id="fields" value="id">
                        <input type="hidden" name="" id="sort" value="desc">
                        <input type="hidden" name="user_id" id="user_id" value="{{$id}}">
                    </div>
                </div>
            </div>
        </div>

        @include('include/footer')
        <script src=" {{ URL::to('/') }}/assets/vendors/select2/select2.min.js"></script>
        <script src=" {{ URL::to('/') }}/assets/js/select2.js"></script>
        <script src="{{ URL::to('/') }}/assets/css/toastr/toastr.min.js"></script>

        <script>
            var userLoginList = "{{ route('login-log-list') }}";
            var userLoginExport = "{{ route('login-log-export') }}";
        </script>
        <script type="text/javascript" src="{{ URL::to('/') }}/assets/js/moment.min.js"></script>
        <script type="text/javascript" src="{{ URL::to('/') }}/assets/js/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/daterangepicker.css" />
        <script type="text/javascript" src="{{ URL::to('/') }}/js/user-login-log.js"></script>