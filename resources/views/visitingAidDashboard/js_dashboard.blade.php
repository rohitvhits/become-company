<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _VISTING_AID_LIST_DATA = "{{ url('visting-list-ajax-data')}}"  
    var _VISTING_AID_COUNT_DATA = "{{ url('visting-count-data')}}"  
    var _VISTING_AGENCY_WISE = "{{ url('visting-agency-wise-data') }}"
    var _VISTING_SERVICE_STATUS = "{{ url('visting-service-status-wise-data') }}"
    var _VISTING_SERVICES_WISE = "{{ url('visting-services-wise-data') }}"
    var _VISTING_TYPE_DATA = "{{ url('visting-type-data') }}" 
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/visting_aid_dashboard/visting_aid_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawAgencyWiseDataChart);
    google.charts.setOnLoadCallback(drawTypeWiseChart);
    google.charts.setOnLoadCallback(drawServicesStatusWiseChart);
    google.charts.setOnLoadCallback(drawServicesWiseChart);
</script>