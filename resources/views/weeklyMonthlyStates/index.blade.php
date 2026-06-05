@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/audit_log_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .horizontal-menu .custom-nav,
    .horizontal-menu .bottom-navbar .page-navigation{
           position: unset ;
    }
</style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">
        @canany(['detailed-refusals-report','referrals-analytics-dashboard-report','weekly-monthly-states-report'])
        @include('referralsWeight/reports-nav')
     @endcan     
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Weekly Monthly States Report</h5>
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
                                            <label class="col-sm-12 ">Type</label>
                                            <div class="col-sm-12">
                                                <select name="type" id="type" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" >
                                                        <option value=""> All </option>
                                                        <option value="Patient"> Patient </option>
                                                        <option value="Caregiver"> Caregiver </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                        @if (in_array($user->user_type_fk, [3, 184]))

                                                <label class="col-sm-12 ">Agency Name</label>
                                                <div class="col-sm-12">
                                                    <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                        <?php foreach ($agencies as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency->id; ?>">
                                                                <?php echo $rwAgency->agency_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                        @endif
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Created Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="created_date" value="" class="datepickernn form-control" id="created_date">
                                         </div>
                                     </div>
                                 </div>
                                     <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Last Status Updated Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" readonly name="last_updated_date" value="" class="datepickernn form-control" id="last_updated_date">
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
                                            value="Search" onclick="weeklyMonthlyList(1)">
                                            <input type="button" value="Export" id="exportBtn"  class="btn btn-info  btn-rounded btn-fw btn-sm">
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
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
                    <div class="col-md-12 pl-0 table-responsive">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Week Of</th>
                                    <th class="text-center">Services</th>
                                    <th class="text-center">Grand Total</th>
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
                <span id="report_res"></span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>

    </div>

</div>
@include('include/footer')
<script>
    var _LIST ="{{ url('weekly-monthly-states-ajax')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var urlToken = "{{ url('search-nybest-user') }}";
</script>
<script src="{{ asset('assets/js/xlsx.full.min.js')}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('/assets/vendors/select2/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/weeklyMonthlyStates/weeklyMonthlyStates.js')}}?time={{ env('timestamp')}}"></script>