<script>
    var _TOTAL_AGENCY = "{{ url('total-agency')}}"
    var _TODAY_APPOITMENT = "{{ url('agency-today-appointment-data')}}"
    var _UPCOMMING_APPOITMENT = "{{ url('agency-upcomming-appointment-data')}}"
    var _URL_STATISTIC = "{{ url('agency-statistic-data')}}"
    var _NOTES = "{{ url('agency-notes-data')}}"
    var _NOTES_NYBEST = "{{ url('agency-notes-nybest-user-data')}}"
    var _URL_NOTES = "{{ url('/patient/view/')}}"    
    var _LOCATIONS = "{{ url('/agency-location-data/')}}";    
    var _ANNOUNCEMENT_DATA = "{{ url('/agency-announcement-data/')}}";  
</script>
<script src="{{ asset('/assets/modulejs/agency_dashboard/agency_dashboard.js')}}?time={{ time()}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>