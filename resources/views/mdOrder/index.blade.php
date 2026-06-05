@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/document_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<div class="main-panel">
    @php
    $auth = auth()->user();
    @endphp
    <div class="content-wrapper">



        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">MD Order Report(<span id="total_record_id"></span>)</h5>

        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            <div class="row">
                            
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Agency</label>
                                    <div class="col-sm-12">
                                        <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <?php foreach ($agency_list as $rwAgency) { ?>
                                                     <option value="<?php echo $rwAgency->id; ?>" >
                                                         <?php echo $rwAgency->agency_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Portal Id</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off" class="form-control"  id="patient_id">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Start Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off" class="form-control start_date"  id="start_date">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">End Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off"  class="form-control end_date" id="end_date">
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <input type="button" name="search" class="btn btn-primary btn-rounded" id="search-data" value="Search" onclick="loadMDOrderReportList(1)">
                        <a href="javascript:void(0)" class="btn btn-secondary btn-rounded" onclick="refresh()">Clear</a>
                       
                        <a href="javascript:void(0)" class="btn btn-success btn-rounded" onclick="exportCsv()">Export</a>
                       
                    
                        <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" class="hide">

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 ">
                <div class="card">
                    <div class="card-body">
                    <span id="response_mqorder_id"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row" style='margin-top: 50px;'>
        <pre id='toastrOptions'></pre>
    </div>
</div>


@include('include/footer')
<script>
   
    $('.start_date').datepicker();
    $('.end_date').datepicker();
    var _MQ_LIST ="{{ url('md-order-report/ajax-list')}}";
    var _MQ_EXPORT_CSV ="{{ url('md-order-report/export-csv')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
</script>
<script type="text/javascript" src="{{ asset('assets/modulejs/mdOrder/mdOrder.js')}}?time={{ env('timestamp')}}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>