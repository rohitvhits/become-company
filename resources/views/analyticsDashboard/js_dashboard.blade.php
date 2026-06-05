<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    var CURRENT_INPROGRESS = "{{ url('current-inprogress')}}"   
    var CURRENT_CHECKIN = "{{ url('current-checkin')}}"   
    var RECENTLY_UPDATED_STATUS = "{{ url('recently-updated-status')}}"   
    var VISITING_AID_TYPE = "{{ url('visiting-aid-type')}}"   
    var RECENT_NOTES = "{{ url('recent-notes')}}"   
    var _VIEW_URL = "{{ url('patient/view')}}"   
    var _THIRD_PARTY_VIEW_URL = "{{ url('third-party-patient')}}"   
    var VISITING_DUE_DATE = "{{ url('visiting-due-date')}}"   
    var LOCATION_WISE_STATUS_DATA = "{{ url('location-status-data')}}"   
    var COUNT_DATA = "{{ url('count-status-data')}}"   
    var DOCUMENT_RECENT_DATA = "{{ url('document-recent-data')}}"   
    var _SERVICE_REQUEST_VIEW_URL = "{{ url('patient-service-requested')}}" 
    var AGENCY_WISE_STATUS_DATA = "{{ url('agency-status-data')}}"  
</script>
<script src="{{ asset('/assets/modulejs/analytics_dashboard/analytics_dashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>