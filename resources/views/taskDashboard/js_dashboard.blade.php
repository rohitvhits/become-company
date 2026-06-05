<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _TOTAL_TASK = "{{ url('total-count-task-data')}}"   
    var _TASK_LIST_SEARCH = "{{ url('tasks/task-list')}}"   
    var _TASK_PRIORITY_CHART_DATA = "{{ url('task-priority-chart-data')}}"   
    var _TASK_LIST_DATA = "{{ url('task-list-data')}}"   
    var _PATIENT_WISE_TASK_DATA = "{{ url('patient-wise-task-data')}}"   
    var _ASSIGNEE_WISE_TASK_DATA = "{{ url('assignee-wise-task-data')}}"   
</script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/task_dashboard/task_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawPriorityDataChart);
    google.charts.setOnLoadCallback(drawPatientTaskChart);
    google.charts.setOnLoadCallback(drawAssigneeTaskChart);
</script>