@include('include/header')
@include('include/sidebar')
<style type="text/css">
    

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-card1 {
        margin-bottom: 20px;
    }

    .search-card1 .form-group {
        margin-bottom: 0.5rem;
    }

    .search-card1 label {
        margin-bottom: 0;
    }

    .search-card1 .card-body {
        padding-bottom: 10px;
    }

    .search-card1 input[type=text] {
        border-radius: 4px;
        border-color: #aaa;
    }

    .srch-icon {
        padding: 0 !important;
        width: 40px;
        height: 40px;
    }
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-container {
        width: 200px !important;
    }

 
    .sorting-btn {
        display: flex;
        flex-direction: column;
        margin-left: auto;
    }

    .sorting-div {
        display: flex;
        align-items: center;
    }

    .sorting-btn button {
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent;
        line-height: 0.5;
    }

    .sorting-btn button i {
        line-height: 0.3;
    }

    .order-listing-loader {
        position: absolute;
        left: 0;
        top: 0;
        background: #ffffff94;
        bottom: 0;
        right: 0;
        width: 100%;
        font-size: 30px;
        display: none;
        align-items: center;
        justify-content: center;

    }
    .table-width1 {
    background-color: #fff;
}
</style>
<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Log List</h5>
            <div class="page-rightbtns">
                <div>
                  
                    <a href="<?php echo URL::to('/'); ?>/user" class="btn btn-light btn-sm btn-rounded btn-fw ml-1"><i
                            class="mdi mdi-reload"></i>
                        Reset</a>
                    @can('user-export')
                        <a href="" class="btn btn-success btn-sm btn-rounded btn-fw ml-1" id="test_user"
                            onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                    @endcan
                    <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                            class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div" style="display: none;">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Patient Id</label>
                                        <div class="col-sm-12 ">
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="patient_id" id="patient_id">
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search"
                                            class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                            value="Search">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <div class="order-listing-loader">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth table-width1">
                        <thead>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>No</span>
                                
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>UserName</span>
                                
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Old Response</span>
                                
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>New Response</span>
                                
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Created Date</span>
                                
                                </div>
                            </th>
                        </thead>
                        <tbody id="response_id">
                        </tbody>
                    </table>
                </div>
                <div class="pull-right pegination-margin" id="paginateId">
                   
                </div>

                <input type="hidden" name="" id="fields" value="id">
                <input type="hidden" name="" id="sort" value="desc">

            </div>
        </div>
    </div>
    

    @include('include/footer')
    <script>
        $('#searchbtns').click(function(e){
            $('#search-div').attr('style','')
        });
        
        var userLogList = "{{ url('user-log-list')}}";
        var userLogExport = "{{ route('all-log-export') }}";
    </script> 
    <script type="text/javascript" src="{{ URL::to('/') }}/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('/') }}/assets/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/daterangepicker.css" />
    <script type="text/javascript" src="{{ URL::to('/') }}/js/patient-log.js?time={{ env('timestamp')}}"></script>