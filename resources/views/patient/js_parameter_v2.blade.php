<script>
    var _PATIENTORNTRNLIST = '{{ url("remote/robort-patient-orn-trn") }}';
    var _PATIENTREADINGLIST = '{{ url("remote/robort-patient-reading") }}';
    var _PATIENTMEDICATIONLIST = '{{ url("remote/robort-patient-medication") }}';

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
    var _AGENCY_NAME = '{{ trim($record->agency_name)}}';


    var selectedComplienceArray = [];
    var selectedComplienceFlag = true;

    var _SKILL_CATEGORY = "{{ url('alayacare-skill-category')}}";
    var _DELETEALAYASKILL = "{{ url('alayacare-delete-skill')}}";
    var _EDITALAYASKILL = "{{ url('alayacare-edit-skill')}}";
    var _UPLOAD_DOCUMENT_FOR_ALAYACARE = "{{ url('alayacare/alayacare-employee/alayacare-document-upload-new')}}";
    var _ALAYACARESKILLLIST = "{{ url('alayacare/alayacare-employee/alaycare-employee-skill-list')}}";
    var _DOWNLOADALAYAATTACHMENTFILES = "{{ url('download-attachment-file')}}";
    var _AGENCYID = '{{ $record->agency_id}}';
    var _PATIENT_AVAILABILITY_FOLLOWUP_DATE = "{{url('patient-avaibility-followup-date/')}}";
    var _RECORD_ID = "{{$record->id}}";
    var _LOAD_ESIGN_TEMPLATE = "{{url('esign/load-esign-template')}}";
    var _LOAD_DOCTOR_LIST = "{{url('esign/load-doctor-list')}}";
    var esignAllocateSigner = "{{ url('esign/template/allowcate-signer') }}";
    var saveEsignTemplate = "{{ url('esign/template/docusign-sent') }}";
    var saveEsignTemplateNew = "{{ url('esign/template/docusign-sent-new') }}";
    var _PATIENT_WISE_ESIGN_LIST = "{{ url('esign/patient-wise-esign-list') }}";
    var _STREAMLINED_FORM_LIST = "{{ url('esign/streamlined/form-list') }}";
    var _STREAMLINED_FORM_SEND = "{{ url('esign/streamlined/form-send') }}";
    var _STREAMLINED_FORM_COMPLETE = "{{ url('esign/streamlined/form-complete') }}";
    var _STREAMLINED_REQUIRE_SIGNATURE = "{{ url('esign/streamlined/require-signature') }}";
    var _GET_ALLOCATED_SIGNER = "{{ url('esign/allowcate-signer-request') }}";
    var _BASE_URL = "{{ url('/')}}";
    var _DELETE_ESIGN_TEMPLATE = "{{ url('esign/patient-docusign-delete') }}";
    var _SMS_EMAIL_ESIGN_TEMPLATE = "{{ url('esign/patient-send-sms-esign') }}";
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
    var  _SAVE_PATIENT_LINK_TO_HHA ="{{ url('patient/link-to-patient')}}";
    var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
    var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
    var _UPDATE_DOCUMENT_SERVICES = "{{ url('update-document-service')}}";
    var _CAREGIVER_HHA_SUBJECT = "{{ url('hha-caregiver/subject')}}";
    var _PATIENT_HHA_SUBJECT = "{{ url('hha-patient/subject')}}";
    var _PATIENT_REQUESTED_BY_ID_SERVICES = "{{ url('ajax-patient-requested-service')}}";
    $('#hha_due_date').datepicker();
    var userTypeFk ="{{ $user['user_type_fk']}}";
    var _PATIENT_NOTES ="{{ url('/patient/get-notes')}}";
    var _SAVE_PATIENT_NOTES ="{{ url('/patient/patient-notes')}}";

    var _PATIENT_WISE_SERVICE_REQUESTED_LIST = "{{ url('patient-wise-service-requested-list') }}";
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

    var _ESIGN_MOVE_DOCUMENT_STORE = "{{ url('esign/esign-move-document') }}";
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

    var _HELP_ME_WRITE_URL        = '{{ url("/patient/help-me-write") }}';
    var _HELP_ME_WRITE_REFINE_URL = '{{ url("/patient/help-me-write/refine") }}';
    var _PATIENT_DOCUMENT_ADD = '{{ url("/patient/document-send-patientId") }}';
    var _AI_ANALYSE_PROXY = '{{ url("/patient/ai-analyse-proxy") }}';
    var _AI_ANALYSE_BY_DOC = '{{ url("/patient/document") }}';
    var _PATIENT_DOCUMENT_LIST = '{{ url("/patient-document-ajax-list") }}';
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
    var _PATIENT_RECORD_NOTES_PERMISSION = @json(auth()->user()->can('flag-notes-change-status'));
    var _PATIENT_RECORD_NOTES_DELETE_PERMISSION = @json(auth()->user()->can('note-delete'));
    var _PATIENT_UPDATE_DOB = '{{ url("update-patient-dob/") }}';
   
    var saveUploadDocument = "{{ url('esign/template/write-document-upload') }}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var PAYMENT_PAY_URL = "{{  url('payment-pay-data') }}";
    var _CHANGE_PAYMENT_DETAILS = "{{  url('edit-payment-data') }}";
    var _ADD_PAYMENT_DETAILS = "{{  url('add-payment-data') }}";
    var _GET_SERVICES ="{{ url('get-services') }}";
    var _ASSIGN_DEPT_ID = "{{ $record->dept_id }}";
    var _GET_BRANCHES_BY_AGENCY_SERVICES = "{{ url('branch-link-ajax/get-branches-by-agency-services') }}";
    var _SAVE_BRANCH = "{{ url('patient/save-patient-branch') }}";
    var _SAVE_PHARMACY = "{{ url('patient/updatePharmacy') }}";
    var _SAVE_NO_MEDICATION_TAKEN = "{{ url('patient/updateNoMedicationTaken') }}";
    var SELECTED_BRANCH_ID = "{{ $record->branch_id }}";
    var _CHECK_MANDATORY = "{{ url('branch-link-ajax/check-mandatory') }}";
    var isBranchMandatory = false;
    var _SEND_RESOLUTION_SMS = "{{ route('patient-resolution-sms.send') }}";
    var _RESOLVE_RESOLUTION_SMS_MSG = "{{ route('patient-resolution-sms.resolve-message') }}";
</script>

@if(isset($agencyDetails->robort_status) && $agencyDetails->robort_status ==1)
<script src="{{ asset('assets/modulejs/patient_robort.js') }}?time={{ strtotime(date('Y-m-d H:i')) }}"></script>
@endif

<script src="{{ asset('assets/modulejs/patient_alayacare.js') }}?time={{ strtotime(date('Y-m-d H:i')) }}"></script>
@if($record->agency_id =='106')
<script src="{{ asset('assets/modulejs/hama_patient_master.js') }}?time={{ strtotime(date('Y-m-d H:i')) }}"></script>
@endif

<script src="{{ asset('assets/modulejs/hha_module.js')}}?time={{ env('timestamp') }}"></script>

<script src="{{ asset('assets/modulejs/esign_module_new.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/esign_streamlined.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_notes.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/help_me_write.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency_all_form_table.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency_all_form_download.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_schedule_appointment.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/task_page.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/invoice_upload_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}?time={{ env('timestamp')}}"></script>

<script src="{{ asset('assets/modulejs/patient_detail.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/mdOrder/mdOrder.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/payment-log/payment-log.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/remote_focus/remote_focus.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/document_section.js') }}?time={{ env('timestamp')}}"></script>
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
    var _DELETED_FLAG_APPOINTMENT ="1";
    var _AUTH_AGENCY_ID = '{{ $auth->agency_fk}}';
    var _SCEDULE_TOTAL_COUNT = '{{ url("location-search-total-count")}}';
    var _SCEDULE_TOTAL_TIME_COUNT = '{{ url("location-search-total-time-count")}}';
    var USER_PAGE_DETAILS_PAGE = "{{url('user-page-detail-change')}}";
    var USER_ID = '{{ $auth->id}}';
    var SAVE_BASIC_DETAILS = '{{ URL("save-basic-details") }}';
    var SAVE_ADDRESS_DETAILS = '{{ URL("save-address-details") }}';
    var SAVE_OTHER_DETAILS = '{{ URL("save-other-details") }}';
    var GET_COUNTY = '{{ url("get-county") }}';
    var _INTEGRATION_ICON ="{{ asset('img/integration.svg')}}";
    var _MQ_ORDER_LIST ='{{ url("patient_md_order_list")}}';
    var _GET_DOCUMENT_LIST_BY_PATIENT_ID ='{{ url("patient_md_order_document_list")}}';
    var _SAVE_MQ_ORDER ='{{ url("save-patient-md-order")}}';
    var _EDIT_MQ_ORDER ='{{ url("edit-patient-md-order")}}';
    var _UPDATE_MQ_ORDER ='{{ url("update-patient-md-order")}}';
    var _DELETE_MQ_ORDER ='{{ url("delete-patient-md-order")}}';
    var _ALAYACARE_CLIENT ="{{ url('alaycare-client-data') }}";
    var _ALAYACARE_CLIENT_ID ='{{ $record->link_alaycare_client_id }}';
    var _ALAYACARE_CLIENT_NAME ='{{ $record->alaycare_name }}';
    var _UPDATE_ALAYACARE_CLIENT_NAME ="{{ url('patient/update-alaycare-client-id') }}";
    var _PATIENT_DOCUMENT_DETAILS_BY_ID ="{{ url('patient/document_details_by_id') }}";
    var _SEARCH_NYBEST_USER ="{{ url('search-nybest-user') }}";

    var _DOCUMENT_SEND_REPORT_DETAILS_BY_ID = "{{ url('document-review-by-id')}}";

    var _DELETE_DOCUMENT = "{{url('patient/document-delete/')}}";
    var _SEND_DOCUMENT_ARLA ="{{ url('send-document-arla') }}";
    var _PAYMENT_LIST ="{{ url('payment-data') }}";
    var _PAYMENT_CSV ="{{ url('payment-export-data') }}";
    var _GENARARE_AMOUNT_DETAILS ="{{ url('genrate-payment-amount-details') }}";
    var _GENARARE_PAYMENT_HISTORY ="{{ url('genrate-payment-history') }}";
    var _GET_REMOTE_DETAIL ="{{ url('remote/get-remote-details')}}";
    var _SEND_REMOTE_DEMOGRAPHIC_DETAILS = "{{ url('remote/send-remote-details')}}";

    var _SEND_EFAX_DOCUMENT = "{{ url('send-e-fax-document')}}";
    var _SEARCH_ALAYACARE_DATA = "{{ url('alaycare-emp-data') }}";
    var TASK_LIST = "{{url('tasks/task-list/')}}/";
    var TASK_STATUS_CHANGE = "{{url('tasks/task-change-status')}}";
    var TASK_AJAX = "{{ url('tasks/task-list-ajax')}}";
    var COMMENT_SAVE = "{{ url('tasks/task-comment-save') }}";
    var TASK_TIME_LOG_LIST = "{{ url('tasks/patient/task-time-log-list') }}";
    var TASK_COMMENT_LIST = "{{ url('tasks/task-comment-list') }}";
    var ACTIVITY_LOG = "{{ url('tasks/patient/activity-log-list') }}";
    var TASK_ASSIGN_USER = "{{ url('tasks/task-assign-to-user') }}";
    var TASK_DESCRIPTION_UPDATE = "{{ url('tasks/task-discription-update')}}";
    var FLAG_TASK = "{{ url('flag-change-task-status')}}";
    var TASK_DUE_DATE = "{{ url('tasks/task-due-date') }}";
    var TASK_TITLE_UPDATE = "{{ url('tasks/task-title-update') }}";
    var TASK_PRIORITY_CHANGE = "{{ url('tasks/task-priority-update') }}";
    var AUTH = "{{auth()->user()->id}}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var CLOCK_IN_OUT = "{{ url('tasks/patient/task-clock-in-out') }}";
    var _REMOTE_EMP_DATA = "{{ url('remote/remote-emp-data') }}";
    var _REMOTE_PATIENT_UPDATE = "{{ url('remote/update-remote-id') }}";
    var remoteID = _ROBORTID;
    var _UPDATE_ALAYACARE_DATA = "{{ url('patient/update-alaycare-id')}}";
    var _ALAYACARE_BRANCH_DATA = "{{ url('get-branch-alaycare-ajax')}}";
    var _GET_ALAYACARE_GROUP_BY_BRANCH_ID = "{{ url('get-group-by-branch-id')}}";
    var _ALAYACARE_EMP_DETAILS_ID ="{{ url('get-alayacare-emp-details')}}";
    var _UPDATE_PATIENT_NOTES ="{{ url('patient/update-patient-notes')}}";
    var _UNDO_ESIGN_DATA = "{{ url('esign/undo-esign-data')}}";
    var _EFAX_NO = '';
    @if(isset($agencyDetails->efax_no) && !empty($agencyDetails->efax_no))
       _EFAX_NO = "{{$agencyDetails->efax_no}}";
    @endif
    var _SEARCH_APPROVE_PATIENT_USER ="{{ url('get-approved-user-doc') }}";
    var _GET_QUESTIONS = "{{ url('get-doc-questions') }}";
    var _PATIENT_DELETE_NOTES = "{{ url('patient-notes-delete') }}";
    var _UNLINK_HHA_PATIENT = "{{ url('unlink-hha-patient')}}";
    var _UNLINK_HHA_CAREGIVER = "{{ url('unlink-hha-caregiver') }}";
    var _GENERATE_PATIENT_ESIGN_LINK = "{{ url('esign/generate-patient-esign-link')}}";
    var _GENERATE_QR_CODE_LINK = '{{ url("esign/get-qr-code-link/") }}';
    var GET_DOCUMENT_INTERNAL_USE_DATA = "{{ url('get-document-internal-use-data')}}";
    var _DYNAMIC_DOC_APPROVED_USERS = '{!! json_encode($dynamic_doc_approved_user) !!}';
    var _UPDATE_APPOINTMENT_REFERRAL_SOURCE = "{{ url('update-referral-source')}}";
    var _SAVE_RESOLUTION_DATA = "{{ url('save-resolution-data')}}";
    var _GET_RESOLUTION_DATA = "{{ url('get-resolution-data')}}";
    var APPOINTMENT_DATA = "{{ url('location-schedule-search1')}}";
    var SERVICEARR ='{{   json_encode($serviceArr); }}';
    var APT_ID = "{{ $record->appoinment_time_id }}";
    var SERVICE_IDS = "<?php echo json_encode(old('service_id')); ?>;";
    var APPADD = "{{ url('/patient/appointment-add') }}";
    var TELEHEALTH_PATIENT_SCHEDULE = "{{ url('/patient/telehealth-add') }}";
    var _RESOLUTION_REQUEST_SERVICE = "{{url('get-service-requested')}}";
    var SERVICE_STATUS_CHANGES = "{{url('save-pateint-service-requested')}}";
    var _TEXT_MESSAGE_IMAGES_DOWNLOAD="{{ url('dpp-text-message')}}";
    var _PATIENT_VIEW_LOGS = "{{ url('appointment-view-logs') }}";
    var _VIEW_LOGS_DETAILS ="{{ url('get-audit-view-log')}}";
    var _COMMON_ESIGN_VIEW_LOG = "{{ url('esign/view-esign-log')}}";
    var _COMMON_ESIGN_RESPONSE_VIEW_LOG = "{{ url('esign/view-esign-response-log')}}";
    var _PATIENT_COMBINE_APPOINTMENT = '{{ url("/patient/combine-appointment") }}';
    var _PATIENT_UNMERGE_APPOINTMENT = '{{ url("/patient/unmerge-appointment") }}';
    var _VIEW_DELETE_APPOINTMENT_SHOW ="{{ url('/deleted_appointment_show/')}}";
    var _HHA_CAREGIVER_MEDICAL_LIST ="{{ url('get-hha-caregiver-medical-list')}}";
    var _HHA_CAREGIVER_MEDICAL_RESULT_LIST = "{{ url('hha-caregiver-medical-results') }}";
    var _SAVE_HHA_MEDICAL_DETAILS = "{{ url('save-hha-caregiver-medical')}}";
    var _REMOTE_PATIENT_CARE_PLAN = "{{ url('remote/get-remote-patient-care-plan')}}";
    var _REMOTE_PATIENT_ACTIVITY_LOG = "{{ url('remote/get-remote-patient-activity-log')}}";
    var _GET_CAREGIVER_I9_ABC_DOCUMENT = "{{ url('get-caregiver-i9-abc-document')}}";
    var _UPDATE_CAREGIVER_I9_REQUIREMENT ="{{ url('update-caregiver-i9-requirement') }}";
    var _GET_CAREGIVER_I9_REQUIREMENT_DETAILs ="{{ url('get-caregiver-i9-requirement-detail') }}";
    var _EDIT_SERVICE ="{{ url('service-edit') }}";
    var _GET_RNPAD_URL_SERVICES = "{{ url('rnpad/rnpad-services-list')}}";
    var _RECORD_AGENCY_ID = "{{ $record->agency_id}}";
    var _SEND_RNPAD_DOCUMENT = "{{ url('rnpad/send-rnpad-document')}}";
    var _REMOTE_PATIENT_CARE_PLAN = "{{ url('remote/get-remote-patient-care-plan')}}";
    var _GET_HHA_MDO_ORDER = "{{ url('/hha/hha-mdo/mdo-document-list')}}";
    var _DOWNLOAD_HHA_MD_ORDER = "{{ url('/hha/hha-mdo/download-md-order-document')}}";
    var _HHA_CAREGIVER_OTHER_COMPLIANCE_LIST = "{{ url('hha/hha-other-compliance/all-other-compliance-list') }}";
    var _HHA_CAREGIVER_OTHER_COMPLIANCE_RESULT = "{{ url('hha/hha-other-compliance/hha-complience-medical-results')}}"
    var _SEND_HHA_MD_ORDER = "{{ url('/hha/hha-mdo/send-md-order-document')}}";
    var _GET_REMOTE_BASIC_DETAILS = "{{ url('remote/demographic-detail')}}";
    var LOCATION_WISE_DISABLE_DATES = @json($locationDisabledDates);
    var EDIT_SERVICE_ENABLE = "{{ $editService }}";
    var TASK_DEPT_UPDATE = "{{url('tasks/task-update-dept')}}";
    var _GET_DEPARTMENT = "{{ url('tasks/get-task-dept') }}";
    var _SAVE_DEPARTEMNT = "{{ url('patient/assign-department') }}";
    var _GET_TASK_HEALTH_URL_SERVICES = "{{ url('get-task-health-services') }}";
    var _SEND_TASK_HEALTH_DOCUMENT = "{{ url('send-task-health-document') }}";
    var _GET_THIRD_PARTY_DATA = "{{ url('get-third-party-data') }}";
    var _SAVE_THIRD_PARTY_DOC_DATA = "{{ url('save-third-party-doc-data') }}";
    var _ADVANCED_SEARCH_THIRD_PARTY = '{{ url("third-party/advanced-search-third-party")}}';
    var _SEARCH_THIRD_PARTY_LINK = "{{ url('link-to-third-party') }}";
    var _GET_VISITING_THIRD_PARTY_PENDING_MEDICAL = "{{ url('third-party/third-party-pending-medical')}}";
    var _GET_THIRD_PARTY_MEDICAL_RESULT_LIST = "{{ url('third-party/third-party-pending-medical-result')}}";
    var _SAVE_VISITING_THIRD_PARTY = "{{ url('third-party/save-visiting-third-party')}}";
    var _SEND_VISITING_THIRD_PARTY_DOCUMENT = "{{ url('third-party/send-visiting-third-party-document')}}";
    var _VISITING_THIRD_PARTY_CODE = @json($visiting_links['Visiting Aid'][0]->third_party_code ?? null);
    var _SEARCH_EMMACARE_EMPLOYEE = "{{ url('remote/search-emmacare-employee')}}";
    var _SEND_REMOTE_FOCUS_DOCUMENT = "{{ url('remote/patient-document-send')}}";
    /***************Calender side show HHA Modal************** */
    var _HHA_CAREGIVER_DETAIL_URL = "{{ url('hha/hha-caregiver/demographic-detail') }}";
    var _HHA_CALENDER_LIST="{{ url('hha/hha-caregiver/calendar-visits') }}";
    var _SEARCH_TASK_HEALTH_PATIENT = "{{ url('task-health/search-patients')}}";
    var _LINK_TASK_HEALTH_PATIENT = "{{ url('task-health/link-patient')}}";
    var _UNLINK_TASK_HEALTH_PATIENT = "{{ url('task-health/unlink-patient')}}";
    var _PATIENT_TASK_HEALTH_VISITS = "{{ url('patient-task-health-visits')}}";
    var _PATIENT_TASK_HEALTH_CA     = "{{ url('patient-task-health-critical-alerts')}}";
    var _TASK_HEALTH_CA_RESOLVE_BASE = "{{ url('task-health/critical-alerts')}}";
    var _TASK_HEALTH_VISIT_DETAIL_JSON = "{{ url('task-health/visit-detail-json')}}";
    var _SEND_INFLOWCARE_DOCUMENT = "{{ url('patient/send-document-inflowcare')}}";
    var _HHA_PATIENT_DETAIL_URL = "{{ url('hha/hha-patient/hha-demographic-detail')}}";
    var _HHA_PATIENT_CALENDER_LIST = '{{ url("hha/hha-patient/calendar-visits")}}';
    var _MERGE_RECORD_LIST = "{{ url('patient/merge-appointment-list')}}";
    var _PATIENT_NEW_UNMERGE_APPOINTMENT = "{{ url('patient/new-unmerge-appointment') }}";
    var _APPOINTMENT_LIST = "{{ url('patient/appointment-list')}}";
    var _HHA_CAREGIVER_PATIENT_NOTES = "{{ url('hha-caregiver/notes')}}";
    var _SEARCH_AGENCY_USER_LIST = "{{ url('agency/search-users-by-agency') }}";
    var _UPDATE_AGENCY_REP = "{{ url('patient/update-agency-user-rep') }}";
    var _FETCH_SKILL_WISE_DETAILS = "{{ url('alayacare/alayacare-employee/fetch-skill-details')}}";
    var _CALL_DETAILS_TAB_URL       = '{{ url("patient/" . $record->id . "/call-details/ajax") }}';
    var _CALL_DETAILS_MESSAGES_URL  = '{{ url("patient/" . $record->id . "/call-details/messages") }}';
    var _CALL_DETAILS_RECORDING_URL = '{{ url("call-details/recording") }}';
</script>
<script type="text/javascript" src="{{ asset('assets/modulejs/task_list.js')}}?time={{ env('timestamp')}}"></script>
<script>
$('#due_date_task_model_id_date').datetimepicker({
    "allowInputToggle": true,
    "showClose": true,
    "showClear": true,
    "showTodayButton": true,
    "format": "MM/DD/YYYY hh:mm:ss A",
});
</script>
<script type="text/javascript" src="{{ asset('assets/modulejs/portal_field_edit.js') }}?time={{ env('timestamp') }}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/telehealth_schedule.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/resolution.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_view_logs.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/hhaMDO/hha_mdo_order.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_link_to_third_party.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/appointment_call_details/appointment_call_log.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/hha_exchange/hha_exchange.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/task_health_link.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/hha_patient/hha_patient_module.js')}}?time={{ env('timestamp')}}"></script>