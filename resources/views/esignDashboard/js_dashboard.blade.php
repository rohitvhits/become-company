<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _TOTAL_ESIGN_DATA = "{{ url('total-esign-data')}}"
    var ESIGN_DATA = "{{ url('esign-data')}}"
    var _GRAPH_TOTAL_STATUS = "{{ url('get-status-wise-graph-data')}}"
    var _ESIGN_REPORT_SEARCH = "{{ url('/esign-report')}}"
    var _TEMPLATE_GRAPH = "{{ url('get-template-usage-graph-data') }}"
    var _REVIEW_ESIGN_GRAPH = "{{ url('get-review-esign-graph-data') }}"
    var _CREATED_ESIGN_GRAPH = "{{ url('get-created-esign-graph-data') }}"
    var ESIGN_REPORT_PERMISSION = @json(auth()->user()->can('esign-report-list'));
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/esign_dashboard/esign_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawStatusChart);
    google.charts.setOnLoadCallback(drawTemplateDataChart);
    google.charts.setOnLoadCallback(drawReviewdEsignDataChart);
    google.charts.setOnLoadCallback(drawCreatedEsignDataChart);
</script>