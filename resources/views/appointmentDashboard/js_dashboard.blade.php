<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _PATIENT_LIST = "{{url('appointment')}}";
    var STATUS_CHART_DATA = "{{url('status-appointment-data')}}";

    var _AGENCY_WISE_APPOINTMENT_DATA = "{{url('agency-wise-appointment-data')}}";
    var _SERVICES_WISE_APPOINTMENT_DATA = "{{url('services-wise-appointment-data')}}";
    var _LOCATION_WISE_APPOINTMENT_DATA = "{{url('location-wise-appointment-data')}}";
    var _USER_WISE_APPOINTMENT_DATA = "{{url('user-wise-appointment-data')}}";

    var _PATIENT_APPOINTMENT_MOTHLY = "{{url('patient-monthly-chart-data')}}";
    var _AGENCY_APPOINTMENT_MOTHLY = "{{url('agency-monthly-chart-data')}}";
    var MONTHLY_COMPARISION_CHART_DATA = "{{url('monthly-comparision-chart-data')}}";

    var TOTAL_COUNTS_DATA = "{{url('total-counts-data')}}";

    var _LOAD_AGENCY_DATA = "{{url('get-agency-data')}}";
    var _LOAD_LOCATIONS_DATA = "{{url('get-location-data')}}";
    var _LOAD_USER_DATA = "{{url('get-user-data')}}";
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/appointment_dashboard/appointment_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawStatusDataChart);
    google.charts.setOnLoadCallback(drawPatientMonthlyChart);
    google.charts.setOnLoadCallback(drawMonthlyCompareChart);
    google.charts.setOnLoadCallback(drawAgencyChart);
    google.charts.setOnLoadCallback(drawServicesChart);
    google.charts.setOnLoadCallback(drawLocationChart);
    google.charts.setOnLoadCallback(drawUserChart);
</script>