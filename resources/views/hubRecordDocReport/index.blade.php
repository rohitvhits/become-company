@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/telehealth_book_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Hub Document Report</h5>
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
                                         <label class="col-sm-12">Company Name</label>
                                         <div class="col-sm-12">
                                             <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <?php foreach ($agencyList as $rwAgency) { ?>
                                                     <option value="<?php echo $rwAgency->id; ?>">
                                                         <?php echo $rwAgency->agency_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">First Name</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="first_name" id="first_name" value="">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Last Name</label>
                                         <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="last_name" id="last_name" value="">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Created Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="created_date" value="" class="datepickernn form-control" id="created_date">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Created By</label>
                                         <div class="col-sm-12">
                                             @if(!empty($agency_user_list[0]))
                                             <select name="created_by" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="created_by">
                                                 <option value="">Select Created By</option>
                                                 @foreach($agency_user_list as $val)
                                                 <option value="{{ $val->id}}">{{ $val->first_name}} {{ $val->last_name}}</option>
                                                 @endforeach

                                             </select>
                                             @else
                                             <input type="text" name="created_by_ny" id="created_by_ny">
                                             <input type="hidden" name="created_by_ny_id" id="created_by_ny_id">
                                             <input type="hidden" name="created_by_ny_name" id="created_by_ny_name">
                                             @endif
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
                                            value="Search" onclick="hubRecordList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('telehealth-booking-report-export')
                                            <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsv()">Export</a>
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
                                    <th>Id</th>
                                    <th>Hub Record</th>
                                    <th>Company Name</th>
                                    <th>Name</th>
                                    <th>Document Name</th>
                                    <th>Attachment</th>
                                    <th>Created Date / Created By</th>
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="hub_record_report_res"></span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
@include('include/footer')
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
<script>
    var _HUB_RECORD_LIST ="{{ url('hub-doc-report/ajax-list')}}";
    var _HUB_RECORD_CSV ="{{ url('hub-doc-report/export-csv')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var urlToken = "{{ url('search-nybest-user') }}";
    $('.fancybox').fancybox({
        toolbar: false,
        smallBtn: true,
        iframe: {
            preload: false
        }
    })
</script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('/assets/vendors/select2/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/hubRecordDocReport/hubRecordDocReport.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
