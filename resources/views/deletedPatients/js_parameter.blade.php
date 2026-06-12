<script>
    var _PATIENTORNTRNLIST = '{{ url("robort-patient-orn-trn") }}';
    var _PATIENTREADINGLIST = '{{ url("robort-patient-reading") }}';
    var _PATIENTMEDICATIONLIST = '{{ url("robort-patient-medication") }}';

    var _ALAYACARESKILLS = '{{ url("alaycare-employee-skill") }}';
    var _ALAYACARESCHEDULAR = '{{ url("alaycare-employee-scheduler") }}';
    var _ROBORTID = '{{ $record->robort_id }}';
    var _ALAYACAREID = '{{ $record->alaycare_id }}';
    var _ALAYACAREVISITDETAILS = '{{ url("alaycare-visit-details") }}';
    var loaderImages = "{{ asset('/ajax-loader.gif') }}";
    var _ALAYACAREEMPLOYEENOTESLIST = '{{ url("alaycare-employee-notes") }}';
    var _ALAYACAREEMPLOYEENOTESTYPE = '{{ url("alaycare-employee-notes-type") }}';
    var _CREATEALAYACAREEMPLOYEENOTES = '{{ url("create-alaycare-employee-notes") }}';
    var _ALAYACARESKILLUPDATES = '{{ url("alaycare-employee-skill-update") }}';

    var _SCHEDULECLIENTLIST = "{{ url('schedule-client') }}";
    var _DOCUMENTATTACHMENTUPLOADS = "{{ url('alayacare-document-upload') }}";
    var _DOCUMENTATTACHMENTUPLOADSLIST = "{{ url('alayacare-upload-document-list') }}";

    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var _RECORD_TYPE = '{{ $record->type}}';
    var _AGENCY_NAME = '{{ $record->agency_name}}';


    var selectedComplienceArray = [];
    var selectedComplienceFlag = true;

    var _SKILL_CATEGORY = "{{ url('alayacare-skill-category')}}";
    var _DELETEALAYASKILL = "{{ url('alayacare-delete-skill')}}";
    var _EDITALAYASKILL = "{{ url('alayacare-edit-skill')}}";
    var _DOCUMENTATTACHMENTUPLOADSNEW = "{{ url('alayacare-document-upload-new')}}";
    var _DOWNLOADALAYAATTACHMENTFILES = "{{ url('download-attachment-file')}}";
    var _AGENCYID = '{{ $record->agency_id}}';
    var _PATIENT_AVAILABILITY_FOLLOWUP_DATE = "{{url('patient-avaibility-followup-date/')}}";
    var _RECORD_ID = "{{$record->id}}";
    var _LOAD_ESIGN_TEMPLATE = "{{url('esign/load-esign-template')}}";
    var _LOAD_DOCTOR_LIST = "{{url('esign/load-doctor-list')}}";
    var esignAllocateSigner = "{{ url('esign/template/allowcate-signer') }}";
    var saveEsignTemplate = "{{ url('esign/template/docusign-sent') }}";
    var saveEsignTemplateNew = "{{ url('esign/template/docusign-sent-new') }}";
    var _PATIENT_WISE_ESIGN_LIST = "{{ url('esign/delete-patient-wise-esign-list') }}";
    var _GET_ALLOCATED_SIGNER = "{{ url('esign/allowcate-signer-request') }}";
    var _BASE_URL = "{{ url('/')}}";
    var _DELETE_ESIGN_TEMPLATE = "{{ url('esign/patient-docusign-delete') }}";
    var _SMS_EMAIL_ESIGN_TEMPLATE = "{{ url('esign/patient-send-sms-esign') }}";
    var _BULK_SMS_EMAIL_ESIGN = "{{ url('esign/bulk-send-sms-esign') }}";
    var userNewId = "{{ $record->id }}";
    var _DOWNLOAD_URL = '{{ url("dre/")}}';
    var _SEND_DOCUMENT_MAIL = "{{ url('patient/send-document-mail') }}"
     //start new design
     var _UNDO_STATUS_URL = "{{ url('esign/pdf/undo-status') }}";
    var _LOG_URL ="{{ url('esign/get-log-details') }}";
   
    var _SEND_DOCUMENT_MAIL = "{{ url('patient/send-document-mail') }}"
    var caregiverId = "";
   @if($record->type =='Caregiver')
        @if($record->link_hha_caregiver !="")
            var caregiverId = '{{$record->link_hha_caregiver }}';
        @else
        var caregiverId = '{{$record->hha_id }}';

        @endif
    @endif
    var patientId = "";
    @if($record->type =='Patient')

        @if($record->link_hha_patient !="")
            var patientId = '{{$record->link_hha_patient }}';
        @else
        var patientId = '{{$record->hha_id }}';
        @endif
    @endif

    var _CAREGIVER_ID =caregiverId;
    var _PATIENT_ID =patientId;
    var _HHA_CAREGIVER_DETAILS ='{{ url("hha-caregiver-detail") }}';

    var _HHA_PATIENT_COORDINATOR = "{{url('hha-patient-coordinator')}}";

    var _HHAPATIENTDEMOGRAPHICSDETAILS =  "{{ url('get-patient-demographics') }}";
    var _HHAPATIENTAUTHORIZATIONINFO =  "{{ url('get-patient-authorization-info') }}";
    var _LINK_HHA_PATIENT_ID = "{{$record->link_hha_patient}}";
    var  _HHA_CAREGIVER_AVAILABILITY ="{{ url('hha-caregiver-avaibility')}}";
    var  _HHA_PATIENT_NOTES ="{{ url('hha-patient/notes')}}";
    var  _HHA_PATIENT_CLINICS ="{{ url('hha-patient/clinic')}}";
    var  _SEARCH_HHA_PATIENT ="{{ url('search-hha-patient-code')}}";
    var  _LINK_TO_HHA_PATIENT ="{{ url('link-to-hha-patient')}}";
    var  _SAVE_PATIENT_LINK_TO_HHA ="{{ url('delete-patient/link-to-patient')}}";
    var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
    var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
    var _UPDATE_DOCUMENT_SERVICES = "{{ url('update-document-service')}}";
    var _CAREGIVER_HHA_SUBJECT = "{{ url('hha-caregiver/subject')}}";
    var _PATIENT_HHA_SUBJECT = "{{ url('hha-patient/subject')}}";
    var _PATIENT_REQUESTED_BY_ID_SERVICES = "{{ url('ajax-patient-requested-service')}}";
    $('#hha_due_date').datepicker();
    var userTypeFk ="{{ $user['user_type_fk']}}";
    var _PATIENT_NOTES ="{{ url('/deleted-patient-get-notes')}}";
    var _SAVE_PATIENT_NOTES ="{{ url('/delete-patient/patient-notes')}}";

    var _PATIENT_WISE_SERVICE_REQUESTED_LIST = "{{ url('delete-patient-wise-service-requested-list') }}";
    var _PATIENT_WISE_SERVICE_REQUESTED_VIEW = "{{ url('/patient-wise-service-requested/view') }}";
    var _SCHEDULE_ADD_WITH_SERVICE_REQUESTED = "{{ url('/patient-service-requested/appointment-add') }}";

    var _FIRST_NAME = "{{$record->first_name}}";
    var _LAST_NAME = "{{$record->last_name}}";
    var _SERVICE = "{{$record->service}}";
    var _EMAIL = "{{$record->email}}";
    var _DOB = "{{$record->dob}}";
    var _GENDER = "{{$record->gender}}";
    var _SERVICE = "{{$record->service}}";
   
    var _GET_PATIENT_WISE_SERVICES = "{{ url('patient-wise-services') }}";
    var _PATIENT_WISE_SERVICE_EMAIL_REQUEST_SAVE = "{{ url('save-service-email') }}";
    var _GET_PATIENT_TYPE_WISE_SERVICES = "{{ url('ajax-service') }}";
    var _PATIENT_TYPE_WISE_SERVICE_REQUEST_SAVE = "{{ url('save-patient-type-wise-services') }}";
    $('#service_eid').select2();
    var _THIRD_PARTY_ID ="{{ $record->link_third_party}}";
    var _LINK_VISITING_AIDS ="{{ url('link-visiting-aids-services')}}";
    var _FLAG = 2;

    var _ESIGN_MOVE_DOCUMENT_STORE = "{{ url('esign-move-document') }}";
    var _AGENCY_ALL_FORM_TABLE_LIST = "{{ url('agency-all-form-table-list') }}";
    var _INVOICE_TABLE_LIST = "{{ url('invoice-upload-ajax-list') }}";
    var _INVOICE_URL = "{{ url('invoice-save') }}";
    var _AGENCY_ALL_FORM_TABLE_VIEW = "{{ url('/agency-all-form-table/view') }}";
    var storeData = "{{ route('store-agency-form') }}";

    var getTemplateData = "{{ route('get.templateData') }}";
    var storeMoveToEsignData = "{{ route('store-move-to-esign') }}";
    var agencyFormDownloadPermission = @json(auth()->user()->can('agency-all-form-download'));
    var agencyFormMoveToEsignPermission = @json(auth()->user()->can('agency-all-form-move-to-esign'));
    var agencyFormMarkAsCompletedPermission = @json(auth()->user()->can('agency-all-form-mark-as-completed'));
    var _PATIENT_LOCATION_SCHEDULE_SEARCH = '{{ url("/location-schedule-search1") }}';
    var _EXISTING_APPOINTMENT_TIME_ID = '{{ $record->appoinment_time_id ?? 0 }}';
    var _GET_AGENCIES = '{{ url("get-agencies") }}';
    var _CHANGE_SERVICE_STATUS = "{{ url('change-service-status') }}";
    var _PATIENT_VIEW ="{{ url('/patient/view')}}";
    var  _HHA_SEARCH_PATIENT_POC ="{{ url('hha-patient/search-patient-poc')}}";
    var _PATIENT_TASK_LIST = '{{ url("tasks/patient/task-list") }}';
    var task_list_ajax = "{{ url('tasks/task-detail')}}";
    var comment_list="{{ url('tasks/task-comment-list') }}";
    var change_status_url = "{{url('tasks/task-change-status')}}";
    var _PATIENT_TASK_ADD = '{{ url("tasks/patient/task-add") }}';
    var activity_log="{{ url('tasks/patient/activity-log-list') }}";
    var page = 1;
    var page_url="{{ url('tasks/patient/task-time-log-list') }}" + "?page="+ page;
    var assign_user_url = "{{ url('tasks/task-assign-to-user') }}";
    var save_comment="{{ url('tasks/task-comment-save') }}";
    var task_discription_url = "{{ url('tasks/task-discription-update')}}";
    var auth_id = "{{auth()->user()->id}}";
    var save_status_url = change_status_url;
    var search_link = "{{ url('task-list-page-ajax') }}" + "?page=";

    //page url
    var task_list_page_url = "{{url('tasks/task-list/')}}/";
    var task_list_ajax_url = "{{ url('task-list-page-ajax') }}" + "?page=";
    var _TASK_CLOCK_IN_OUT ="{{ url('tasks/patient/task-clock-in-out') }}";

    var _INVOICE_DOCUMENT = '{{ url("/invoice") }}';
    var _PATIENT_RECORD_DELETE = "{{url('patient/delete/')}}";

    
    var _EXISTING_APPOINTMENT_TIME_ID = '{{ $record->appoinment_time_id ?? 0 }}';
    $('.document_completed_date').datepicker({
        startDate:new Date()
    });

    var _HHA_UPDATE_COMPLIANCE_DOCUMENT = "{{ url('hha/hha-other-compliance/update-complience-document')}}";
    var _HHA_PATIENT_CHANGES_V2 ="{{ url('hha-patient-changes-v2') }}";
    var _HHA_PATIENT_AUTHORIZATION_CHNAGES_V2 ="{{ url('hha-patient-authorization-changes-v2') }}";
    
    var _PATIENT_DEMOGRAPHIC_SMS_LINK = "{{ url('send-sms-patient-demographic')}}";
    
    var _HHA_ADD_PATIENT_POC_DETAILS =  "{{ url('hha-add-patient-poc-deatils') }}";
    var _HHA_PATIENT_POC_OFFICE_DETAILS =  "{{ url('hha-patient-poc-office-deatils') }}";
    var _HHA_PATIENT_POC_TASK_DETAILS =  "{{ url('hha-patient-poc-task-deatils') }}";
    var _HHA_CAREGIVER_DOCUMENT_DETAILS = "{{ url('hha-caregiver-document-details') }}";
    var _HHA_CAREGIVER_DOCUMENT_TYPE_DETAILS = "{{ url('hha-caregiver-document-type-details') }}";
    var _SAVE_HHA_CAREGIVER_DOCUMENT = "{{ url('save-hha-caregiver-document') }}";

    var _HHA_PATIENT_DOCUMENT_DETAILS = "{{ url('hha-patient-document-details') }}";
    var _HHA_PATIENT_DOCUMENT_TYPE_DETAILS = "{{ url('hha-patient-document-type-details') }}";
    var _SAVE_HHA_PATIENT_DOCUMENT = "{{ url('save-hha-patient-document') }}";

    var _PATIENT_DOCUMENT_ADD = '{{ url("/patient/document-send-patientId") }}';
    var _PATIENT_DOCUMENT_LIST = '{{ url("/delete-patient-document-ajax-list") }}';
    var _PATIENT_OTHER_COMPLIENCE = "{{ url('hha/hha-other-compliances/hha-other-complience') }}";
    var _HHA_PATIENT_CONTRACT = '{{ url("hha-patient-contract") }}';
    var _HHA_PATIENT_DISCIPLINE = '{{ url("hha-patient-discipline") }}';
    var _HHA_PATIENT_PREFERENCES = "{{ url('hha-patient-prefrences') }}";
    var _HHA_PATIENT_DOWNLOAD_DOCUMENT = "{{ url('hha-patient-download-doc') }}";
    var _HHA_CAREGIVER_DOWNLOAD_DOCUMENT = "{{ url('hha-caregiver-download-doc') }}";
    var _HHA_CAREGIVER_PREFERENCES  = "{{ url('hha-caregiver-prefrences') }}";
    var _DISABLE_DATE  = "{{$disable_date}}";
    var _PATIENT_UPDATE_LANGUAGE = "{{ url('patient-language-update')}}";
    var _PATIENT_UPDATE_MOBILE = "{{ url('patient-mobile-update')}}";
    var _PATIENT_UPDATE_PHONE = "{{ url('patient-phone-update')}}";
    var _PATIENT_DELETE_TASK = '{{ url("tasks/task-list/") }}';
    var _PATIENT_RECORD_FLAG = '{{ url("flag-change-status/") }}';
    var _PATIENT_RECORD_DOC_FLAG = '{{ url("flag-change-document-status/") }}';
    var _PATIENT_RECORD_NOTES_FLAG = '{{ url("flag-change-notes-status/") }}';
    var _PATIENT_RECORD_TASK_FLAG = '{{ url("flag-change-task-status/") }}';
    var _PATIENT_RECORD_TASK_PERMISSION = @json(auth()->user()->can('flag-notes-change-status'));
    var _PATIENT_RECORD_NOTES_PERMISSION = @json(auth()->user()->can('flag-notes-change-status'));
    var _PATIENT_RECORD_NOTES_DELETE_PERMISSION = @json(auth()->user()->can('note-delete'));
    var _PATIENT_UPDATE_DOB = '{{ url("update-patient-dob/") }}';

    // $('#id_0').datetimepicker({
    //         "allowInputToggle": true,
    //         "showClose": true,
    //         "showClear": true,
    //         "showTodayButton": true,
    //         "format": "MM/DD/YYYY hh:mm:ss A",
    // });
    var saveUploadDocument = "{{ url('write-document-upload') }}";
    var _ESIGN_HISTROY  = "{{ url('esign/esign-history')}}";
</script>

@if($record->robort_id !="")
<script src="{{ asset('assets/modulejs/patient_robort.js') }}?time={{ env('timestamp')}}"></script>
@endif
@if($record->alaycare_id !="")
<script src="{{ asset('assets/modulejs/patient_alayacare.js') }}?time={{ env('timestamp')}}"></script>
@endif
@if($record->agency_id =='106')
<script src="{{ asset('assets/modulejs/hama_patient_master.js') }}?time={{ env('timestamp')}}"></script>

@endif

<script src="{{ asset('assets/modulejs/hha_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/esign_move_document_module.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/esign_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/esign_module_new_delete.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_notes.js')}}?time={{ time()}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>
<!-- <script src="{{ asset('assets/modulejs/patient_wise_agency.js')}}?time={{ env('timestamp')}}"></script> -->
<script src="{{ asset('assets/modulejs/agency_all_form_table.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency_all_form_download.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_schedule_appointment.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/task_page.js')}}?time={{ time()}}"></script>
<script src="{{ asset('assets/modulejs/invoice_upload_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient/patient_demographic.js') }}?time={{ env('timestamp')}}"></script>

<script>
    var esignView = @json(auth()->user()->can('esign-view'));
    var esignDelete = @json(auth()->user()->can('esign-delete'));
    var esignSendSms = @json(auth()->user()->can('esign-send-sms'));
    var esignViewLog = @json(auth()->user()->can('esign-view-log'));
    var esignPdfDownload = @json(auth()->user()->can('esign-pdf-download'));
    var esignMoveDocument = @json(auth()->user()->can('esign-move-document'));
    var esignRevert = @json(auth()->user()->can('esign-revert'));
    var esignReview = @json(auth()->user()->can('esign-review'));
    var _AUTH_AGENCY_ID = '{{ $auth->agency_fk}}';
    var _DELETED_FLAG_APPOINTMENT ="0";

    var _MERGE_RECORD_LIST = "{{ url('patient/delete-merge-appointment-list')}}";
    var _PATIENT_NEW_UNMERGE_APPOINTMENT = "{{ url('patient/new-unmerge-appointment') }}"
</script>