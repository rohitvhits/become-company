@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
<div class="main-panel main-page-box" style="margin-bottom:15%">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Referral Source Type Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                 
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12">Referral Source Type</label>
                                            <div class="col-sm-12">
                                                <select name="referral_source_type" id="referral_source_type" class="form-control">
                                                    <option value="">Select Referral Source Type</option>
                                                        @foreach($masterData as $mst)
                                                            <option value="{{ $mst->name}}">{{ $mst->name}}</option>
                                                        @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Created Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="created_date" readonly value="" class="datepickernn form-control" id="created_date" placeHolder="Created Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadReferralSourceType(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('referral-source-export')
                                            <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsv()"><i class="mdi mdi-file"></i>Export</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hideClass" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                   <th>#</th>
                                    <th>Referral Type</th>
                                    <th>Caregiver</th>
                                    <th>Patient</th>
                                   
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                   
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="ajax_response_id"></span>
            </div>
        </div>
        
    </div>
    
</div>

@include('include/footer')
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>

<script>
    var _REFERRAL_SOURCE_TYPE_AJAX = "{{ url('referral-source-report-ajax')}}";
    var _REFERRAL_SOURCE_TYPE_EXPORT_CSV = "{{ url('referral-source-report-export')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
</script>
<script type="text/javascript" src="{{ asset('/assets/modulejs/referralSourceType/referralSourceType.js')}}"></script>