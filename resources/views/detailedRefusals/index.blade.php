@include('include/header')
@include('include/sidebar')

<style>
    .card .card-body {
        padding: 9px 10px !important;
    }

    .card .card-title {
        font-size: 13px !important;
    }
</style>
<style>
    .horizontal-menu .custom-nav,
    .horizontal-menu .bottom-navbar .page-navigation{
           position: unset ;
    }
</style>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<div class="main-panel">
    <div class="content-wrapper">
        @canany(['detailed-refusals-report', 'referrals-analytics-dashboard-report', 'weekly-monthly-states-report'])
            @include('referralsWeight/reports-nav')
        @endcan
        <div class="page-title-main">
            <div class="row">
                <div class="col-md-2">
                    <h5 class="mb-0 font-weight-bold">Detailed Refusals</h5>
                </div>
                <div class="col-md-10" style="margin-bottom:10px;">
                    <div class="col-md-12">
                        <img src="{{ asset('/ajax-loader.gif') }}" alt="loader"
                            id="loaderDetailedRefusals" style="display:none">
                        <div>
                            <div class="row">
                                
                                <div class="col-md-3">
                                    {{-- <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                      
                                        <label for="agencyId" style="margin-right: 10px;">Agency</label>
                                  --}}
                                  <div>
                                    <label class="col-sm-12 ">Agency</label>
                                        <select name="agencyId[]" id="agencyId" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" >
                        
                                        @foreach ($agency_list as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-2">
                                    <div>
                                        <label class="col-sm-12 ">Type</label>
                                    <select class="form-control" id="record_type">
                                        <option value="Patient">Patient</option>
                                        <option value="Caregiver">Caregiver</option>

                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div>
                                        <label class="col-sm-12 ">Location</label>
                                    <select class="form-control" id="location_id">
                                        <option value="">Select Location</option>
                                        @foreach ($location_list as $lct)
                                            <option value="{{ $lct->id }}">{{ $lct->address1 }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div>
                                        <label class="col-sm-12 ">Created Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" readonly name="created_date" value="{{ $dateRange }}" class="datepickernn form-control" id="created_date">
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div>
                                        <label class="col-sm-12 ">Last Status Updated Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" readonly name="last_updated_date" value="" class="datepickernn form-control" id="last_updated_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 30px;">
                                   
                                    <div class="col-sm-12">
                                <button class="btn btn-primary btn-sm" id="resetFilterBtn">Reset</button>
                                <input type="button" value="Export" id="exportBtn"  class="btn btn-info btn-fw btn-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 grid-margin-top"></div>

        <div class="row">
            <div class="col-6">
                <div class="row">

                    @foreach ($refusedStatus as $refusal)
                        <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                            <div class="card">

                                <div class="card-body">
                                    <h4 class="card-title">Total {{ $refusal->name }}</h4>
                                    <div class="d-flex justify-content-between">

                                        <p class="text-muted"> <span
                                                    class="total-class" id="total_{{ $refusal->id }}">0</span></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
            <div class="col-lg-6 grid-margin grid-margin-lg-0 stretch-card">

                <div class="card">

                    <div class="card-body">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div></div>
                            </div>
                        </div>
                        <h4 class="card-title"> Chart</h4>
                        <canvas id="pieChart" width="520" height="260"
                            style="display: block; width: 520px; height: 260px;"
                            class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        </div>
    </div>
</div>
<div class="row" id="blank_div" style='margin-top: 30px;'>

</div>
<script>
    var _CHATURL = "{{ url('detailed-refusals-graph-ajax') }}";
</script>
@include('include/footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ asset('assets/js/xlsx.full.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/DetailedRefusalsChart.js') }}?time={{ env('timestamp') }}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
<script src="<?= URL::to('assets/js/chart.min.js') ?>"></script>

<script>
    
    $(function() {
        getData();
    });

    function getData() {
        $('#loaderDetailedRefusals').attr('style', '');
        var agencyId = $('#agencyId').val();
        $.ajax({
            url:  _CHATURL,
            type: 'GET',
            data: {
                'agency_id': agencyId,
                'record_type': $('#record_type').val(),
                'location_id': $('#location_id').val(),
                'created_date':$('#created_date').val(),
                'last_updated_date':$('#last_updated_date').val()
            },
            success: function(data) {
                $('#loaderDetailedRefusals').attr('style', 'display:none');
                var json = data;
                if (json.length === 0) {
                    $('.total-class').html(0);
                    return;
                }
                json.forEach(function(item) {
                    var elementId = `#total_${item.reason_id}`;

                    if ($(elementId).length) {
                        $(elementId).html(item.count);
                    } else {
                        $(elementId).html(0);
                        console.warn("Element not found:", elementId);
                    }
                });
            }
        });

    }

    $('#agencyId,#record_type,#location_id,#created_date').change(function(e) {
        getData();
        loadChart();
    })

    function redirection(status = "") {
        var agencyId = $('#agencyId').val();
        var url = "{{ url('patient') }}?status=" + status + "&agency_fk=" + agencyId + '&type=' + $('#record_type')
            .val() + '&locationId=' + $('#location_id').val();
        window.open(url, '_blank');

    }
</script>