@include('include/header')
<style type="text/css">
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 {
        background-color: #fff;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

</style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Announcement List</h5>
            <div class="page-rightbtns">
                <div>
                    @can('announcement-create')
                        <a href="{{ route('announcement.create') }}"
                            class="btn btn-primary btn-rounded btn-fw btn-sm showModalInsurance"><i class="mdi mdi-plus"> </i>
                            Add Announcement</a>
                    @endcan
                </div>
            </div>
        </div>

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

        <div class="row">
            <div class="col-12">
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    <!-- Insurance Start -->
    
    <!-- Insurance End -->

    @include('include/footer')
<script src="{{ asset('assets/modulejs/announcement.js')}}?time={{ env('timestamp')}}"></script>

    
    <script>
        var _AJAX_LIST ="{{ url('announcement-ajax-list')}}";
        var _STORE_DATA ="{{ route('announcement.store')}}"
    </script>
