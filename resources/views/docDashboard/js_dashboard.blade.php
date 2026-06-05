<script>
    var DOCUMENT_WISE_DATA = "{{url('get-doc-count-data')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
    var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
    var _DELETE_DOCUMENT = "{{url('patient/document-ajax-delete/')}}";
    var _CSRF_TOKEN = "{{ csrf_token()}}";
    var _UPDATE_DOCUMENT_SERVICES = "{{ url('update-document-service')}}";

    var _DOCUMENT_REDIRECTION_FLAG = 1;
    var _EXPORT_CSV_WITHOUT_SERVICE = "{{ url('document-export-csv-new')}}";
    var _SHOW_DOCUMENT_NAME ="{{ url('/temp-download-url')}}";
    var _DOCUMENT_SEND_REPORT_DETAILS_BY_ID = "{{ url('document-review-by-id') }}"
    var _UPLOAD_DOCUMENT_REVIEW_BY_ID = "{{ url('update-document-review-internal') }}";
    var _SEARCH_CREATED_BY_USER = "{{ url('search-nybest-all-user') }}";
    var _SEARCH_NYBEST_USER = "{{ url('search-nybest-user') }}";
    var _GET_SERVICES_OF_DOCUMENT = "{{ url('get-service-of-doc-id') }}";
    var _GET_QUESTIONS = "{{ url('get-doc-questions') }}";
    var SAVE_DOC_NAME = "{{ url('update-document-name') }}";
    var PATIENT_URL = "{{ url('patient/view') }}";
    var DOC_URL = "{{ url('write-document') }}";
    var uniqId = "{{uniqid()}}";
</script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('/assets/modulejs/docDashboard/docDashboard.js')}}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_module.js')}}?time={{ env('timestamp')}}"></script>

