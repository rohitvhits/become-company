<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _TOTAL_PATIENT = "{{ url('total-patient-caregiver')}}"
    var _GRAPH_TOTAL_CAREGIVER_PATIENT = "{{ url('agency-wise-patient-caregiver-graph')}}"
    var _GRAPH_TOTAL_SERVICE = "{{ url('service-wise-graph')}}"
    var _AJAX_SERVICES = "{{ url('ajax-service')}}"
    var _GRAPH_TOTAL_LOCATION = "{{ url('location-wise-patient-caregiver-graph')}}"
    var _TODAY_APPOITMENT = "{{ url('today-appointment-data')}}"
    var _UPCOMMING_APPOITMENT = "{{ url('upcomming-appointment-data')}}"
    var _NOTES = "{{ url('get-notes-data')}}"
    var _STATUS_WISE_GRAPH = "{{ url('status-wise-graph')}}"
    var _URL_NOTES = "{{ url('/patient/view/')}}"
    var _PATIENT_LIST_SEARCH = "{{ url('/patient-service-requested')}}"
    var NOTES_LIST_AJAX = "{{ url('/notes-all-data')}}"
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/user_dashboard/user_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawAgencyDataChart);
    google.charts.setOnLoadCallback(drawServiceDataChart);
    google.charts.setOnLoadCallback(loadLocationChart);
    google.charts.setOnLoadCallback(drawStatusDataChart);
</script>