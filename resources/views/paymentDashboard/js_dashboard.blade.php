<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    var COUNT_DATA = "{{url('get-count-data')}}";
    var AGENCY_WISE_DATA = "{{url('agency-wise-payment-data')}}";
    var LOCATION_WISE_DATA = "{{url('location-wise-payment-data')}}";
    var SERVICE_WISE_DATA = "{{url('service-wise-payment-data')}}";
    var _PAYMENT_URL = "{{url('payment-log-report')}}";
    var _PAYMENT_CHART = "{{url('payment-chart-data')}}";
    var _MONTHLY_DATA = "{{url('monthly-payment-chart')}}";
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/paymentDashboard/paymentDashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>
     google.charts.load('current', {
            packages: ['corechart', 'piechart']
        });

        // Callback function to draw the chart
        google.charts.setOnLoadCallback(drawChart);
        google.charts.setOnLoadCallback(drawMonthlyChart);
</script>
