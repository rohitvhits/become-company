<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var _TOTAL_AGENCY = "{{ url('employee-total-agency')}}"
    var _TODAY_APPOITMENT = "{{ url('employee-today-appointment-data')}}"
    var _UPCOMMING_APPOITMENT = "{{ url('employee-upcomming-appointment-data')}}"
    var _URL_STATISTIC = "{{ url('statistic-data')}}"
    var _TASK = "{{ url('task-data')}}"
    var _NOTES = "{{ url('notes-data')}}"
    var _URL_NOTES = "{{ url('/patient/view/')}}"
    var _ESIGN_DATA = "{{ url('esign-data')}}"
    var _PATIENT_LIST_SEARCH = "{{ url('patient')}}"
    var _ANNOUNCEMENT_DATA = "{{ url('/employee-announcement-data')}}"; 
    var _LOAD_AGENCY = "{{ url('/load-agency-list')}}"; 
    </script>
<script src="{{ asset('/assets/vendors/chart.js/Chart.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/vendors/chartist/chartist.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('/assets/modulejs/employee_dashboard/employee_dashboard.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>