@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .select2-container {
        width: 100% !important;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Agencies Listing</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('agency-add')
                    <a href="<?php echo URL::to('/agency/add'); ?>" class="btn btn-primary cust-right-btn">
                        <i class="mdi mdi-plus"></i> Add Agency
                    </a>
                    @endcan

                    @can('agency-export')
                    <a href="" class="btn btn-info cust-right-btn" id="btn_export_agency" onclick="agencyExportData()">
                        <i class="mdi mdi-file-export"></i> Export
                    </a>
                    @endcan

                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E; color: #fff;">
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
                            <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="agency_name">Agency Name</label>
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="agency_name" id="agency_name"
                                                    placeholder="Agency Name">
                                                <span class="error ml-2" id="error_all"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="email">Email</label>
                                                <input type="text" autocomplete="off" class="form-control"
                                                    name="email" id="email"
                                                    placeholder="Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="phone">Phone</label>
                                                <input type="text" autocomplete="off" class="form-control"
                                                    name="phone" id="phone"
                                                    placeholder="Phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="city">City</label>
                                                <input type="text" autocomplete="off" class="form-control"
                                                    name="city" id="city"
                                                    placeholder="City">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="is_sms">Enable SMS</label>
                                                <select name="is_sms" class="form-control" id="is_sms">
                                                    <option value="">Select Enable SMS</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content: left !important;">
                                        <input type="button" class="btn search-btn1 searchAppoinment" id="search-data" value="Search">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="agencyReset()">
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
                <div class="agency-data-loader shimmer_id table-responsive">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">#</th>
                                    <th>Record#</th>
                                    <th>Agency Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Enable SMS</th>
                                    <th>Integration</th>
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
                <div class="table-responsive">
                    <span id="response_agency_list"></span>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var _AGENCY_AJAX = "{{ url('agency-ajax-list') }}";
        var _AGENCY_EXPORT = "{{ url('agency-export') }}";
    </script>
    <script src="{{ asset('assets/modulejs/agency/agency_list_module.js') }}?time={{ env('timestamp') }}"></script>

    @include('include/footer')
