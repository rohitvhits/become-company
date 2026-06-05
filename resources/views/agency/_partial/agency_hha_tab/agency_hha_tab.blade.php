<!-- HHA Detail Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-hospital-o mr-2"></i>HHA Detail
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="openEditPopup()">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-tag mr-2 text-muted"></i>App Name</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->app_name)?substr ($agencyDetails->app_name, -4):'-' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-key mr-2 text-muted"></i>App Key</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->app_key)?substr ($agencyDetails->app_key, -4):'-' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-lock mr-2 text-muted"></i>App Token</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->app_token)?substr ($agencyDetails->app_token, -4):'-' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-toggle-on mr-2 text-muted"></i>Enabled HHA</dt>
                            <input type="hidden" id="app_detail" name="app_detail" value="{{ ($agencyDetails->app_token) && ($agencyDetails->app_key) && ($agencyDetails->app_name) ? 1 : 0 }}">
                            <input type="hidden" id="edit_app_name" name="edit_app_name" value="{{$agencyDetails->app_name}}">
                            <input type="hidden" id="edit_app_token" name="edit_app_token" value="{{$agencyDetails->app_token}}">
                            <input type="hidden" id="edit_app_key" name="edit_app_key" value="{{$agencyDetails->app_key}}">
                            <dd>
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" name="enable_hha" class="enable_hha" {{ $agencyDetails->enable_hha != 0 ? 'checked' : ''}}>
                                    <span class="toggle-slider round"></span>
                                </label>
                            </dd>

                            <dt><i class="fa fa-building mr-2 text-muted"></i>Office Name</dt>
                            <dd>
                                <span class="office_id_div badge badge-info px-3 py-2">
                                    {{ ($agencyDetails->office_name)?$agencyDetails->office_name:'All' }}
                                </span>
                                <input type="hidden" id="office_id_resp" value="{{ $agencyDetails->office_id}}">
                                <a data-toggle="modal" data-target="#agency_add_app_detail" data-whatever="@mdo" title="HHA Offices" onclick="showHHAOffices()" class="ml-2">
                                    <i class="fa fa-edit text-primary"></i>
                                </a>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6" id="hide_show_hha_id" style="display:@if($agencyDetails->enable_hha ==1) @else none @endif">
                        <div class="alert alert-info border-left-primary mb-3">
                            <i class="fa fa-info-circle mr-2"></i>
                            <strong>HHA Integration Status:</strong> Active - Sync your data below
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HHA Sync Cards Section -->
<div class="row" id="hide_show_hha_id" style="display:@if($agencyDetails->enable_hha ==1) @else none @endif">
    <div class="col-12">
        <div class="row">
            <!-- Caregiver Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-primary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-user-md fa-2x text-primary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch All Caregiver</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 hha_total_caregiver_id" style="display:@if($totalCaregiver >0) @else none @endif">Total Caregiver</p>
                            <p class="text-muted mb-0 hha_total_caregiver_id" id="hha_total_caregiver_id" style="display:@if($totalCaregiver >0) @else none @endif">
                                <span class="badge badge-primary badge-pill">{{ $totalCaregiver}}</span>
                            </p>
                            @can('view-hha-caregiver-list')
                            <p class="text-muted mb-0">
                                <a target="_blank" href="{{ url('hha-caregiver-list')}}?agency_id={{ sha1($agencyDetails->id) }}" class="text-info">
                                    <i class="fa fa-eye mr-1"></i>View
                                </a>
                            </p>
                            @endcan
                        </div>
                        <div class="pull-left text-center w-100">
                            <a href="javascript:void(0)" class="hha_refresh_caregiver_id btn btn-outline-primary btn-sm" onclick="fetchCargiver()" style="display:@if($totalCaregiver == 0) @else none @endif">
                                <i class="fa fa-refresh mr-1"></i>Refresh
                            </a>
                            <img src="{{ asset('ajax-loader.gif') }}" alt="loader" class="loader" id="loadertagCaregiver" style="display:none;">
                            <a href="{{ url('sync-agency-caregiver')}}/{{ sha1($agencyDetails->id) }}" id="hhasyncCaregiver" target="_blank" style="display:@if($totalCaregiver >0) @else none @endif" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Employee
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HHA Medical Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-success-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-medkit fa-2x text-success"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch HHA Medical</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 hha_total_client_id" style="display:@if($totalClient >0) @else none @endif">Total HHA Medical</p>
                            <p class="text-muted mb-0 hha_total_client_id" id="hha_total_client_id" style="display:@if($totalClient >0) @else none @endif">
                                <span class="badge badge-success badge-pill">{{ $totalClient}}</span>
                            </p>
                        </div>
                        <div class="pull-left text-center w-100">
                            <a href="{{ url('fetch-hha-medical')}}/{{ sha1($agencyDetails->id) }}" id="hhasyncClient" target="_blank" target="_blank" class="btn btn-success btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync HHA Medical
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-info-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-wheelchair fa-2x text-info"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch All Patient</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 hha_total_patient_id" style="display:@if($totalPatient >0) @else none @endif">Total Patient</p>
                            <p class="text-muted mb-0 hha_total_patient_id" id="hha_total_patient_id" style="display:@if($totalPatient >0) @else none @endif">
                                <span class="badge badge-info badge-pill">{{ $totalPatient}}</span>
                            </p>
                        </div>
                        <div class="pull-left text-center w-100">
                            <a href="javascript:void(0)" class="hha_refresh_patient_id btn btn-outline-info btn-sm" onclick="fetchPatient()" style="display:@if($totalPatient == 0) @else none @endif">
                                <i class="fa fa-refresh mr-1"></i>Refresh
                            </a>
                            <img src="{{ asset('ajax-loader.gif') }}" alt="loader" class="loader" id="loadertagPatient" style="display:none;">
                            <a href="{{ url('sync-agency-patient')}}/{{ sha1($agencyDetails->id) }}?offset=0" id="hhasyncPatient" target="_blank" style="display:@if($totalPatient >0) @else none @endif" target="_blank" class="btn btn-info btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Patient
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Compliances Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-warning-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-file-text fa-2x text-warning"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Other Compliances</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 hha_total_client_id" style="display: none ">Total HHA Medical</p>
                            <p class="text-muted mb-0 hha_total_client_id" id="hha_total_client_id" style="display: none ">0</p>
                        </div>
                        <div class="pull-left text-center w-100">
                            <a href="{{ url('sync-hha-other-compliance')}}/{{ sha1($agencyDetails->id) }}" target="_blank" class="btn btn-warning btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Other
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HHA Office Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-secondary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-building fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">HHA Office</h4>
                        <div class="pull-left text-center w-100 mt-4">
                            <a href="javascript:void(0)" onclick="syncHHAOffice()" class="btn btn-secondary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Office
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-secondary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-building fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">HHA Setup Due Medical</h4>
                        <div class="pull-left text-center w-100 mt-4">
                            <a href="javascript:void(0)" onclick="syncHHAMedicalDocument()" class="btn btn-secondary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Setup Due Medical
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-secondary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-building fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Configuration POC Task</h4>
                        <div class="pull-left text-center w-100 mt-4">
                            <a href="{{ url('hha/hha-patient/configuration-poc-task')}}/{{ sha1($agencyDetails->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Configuration POC Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-secondary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-building fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Document Type</h4>
                        <div class="pull-left text-center w-100 mt-4">
                            <a href="{{ url('hha/hha-patient/document-poc-type')}}?id={{ sha1($agencyDetails->id) }}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Document Type
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HHA MDO Section -->
<div class="row mb-4 mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-cog mr-2"></i>HHA MDO
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="openEditMDOPopup()">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-id-card mr-2 text-muted"></i>Client ID</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2" id="mdo_html_client_id">{{ $agencyDetails->mdo_client_id }}</span>
                            </dd>

                            <dt><i class="fa fa-lock mr-2 text-muted"></i>Client Secret</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2"  id="mdo_html_client_secret">
                                    {{ $agencyDetails->mdo_client_secret }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-key mr-2 text-muted"></i>App Key</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2" id="mdo_html_api_token">{{ $agencyDetails->mdo_api_token }}</span>
                            </dd>
                            <dt><i class="fa fa-key mr-2 text-muted"></i>TXT ID</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2" id="mdo_html_txtID">{{ $agencyDetails->mdo_txtID }}</span>
                            </dd>
                            <dt><i class="fa fa-toggle-on mr-2 text-muted"></i>Enabled HHA MDO</dt>
                            <dd>
                                <label class="toggle-switch toggle-switch-success-mdo">
                                    <input type="checkbox" name="enable_hha_mdo" class="enable_hha_mdo">
                                    <span class="toggle-slider round"></span>
                                </label>
                            </dd>
                            <input type="hidden" id="value_client_id" value="{{ $agencyDetails->mdo_client_id }}">
                            <input type="hidden" id="value_client_secret" value="{{ $agencyDetails->mdo_client_secret }}">
                            <input type="hidden" id="value_api_token" value="{{ $agencyDetails->mdo_api_token }}">
                            <input type="hidden" id="value_txtID" value="{{ $agencyDetails->mdo_txtID }}">
                            <input type="hidden" id="value_toogles" value="{{ $agencyDetails->mdo_is_status }}">
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning border-left-warning">
                            <i class="fa fa-exclamation-triangle mr-2"></i>
                            <strong>Note:</strong> Configure MDO settings to enable integration
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HHA Document Type Section -->
<div class="row mb-4 mt-4">
    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>POC Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditPocDocumentType()">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="poc_doc_type_label">
                                    {{ $agencyDetails->poc_document_type_name ?? ($agencyDetails->poc_document_type_id ? $agencyDetails->poc_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>

                        <!-- Edit Form (hidden by default) -->
                        <div id="poc_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="poc_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="savePocDocumentType()">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelPocDocumentTypeEdit()">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the HHA POC document type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Patient Assessment Document Type Section -->
    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>Patient Assessment Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditDocType('patient_assessment')">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="patient_assessment_doc_type_label">
                                    {{ $agencyDetails->patient_assessment_document_type_name ?? ($agencyDetails->patient_assessment_document_type_id ? $agencyDetails->patient_assessment_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>
                        <div id="patient_assessment_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="patient_assessment_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveDocType('patient_assessment')">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelDocTypeEdit('patient_assessment')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the Patient Assessment document type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CMS 485 Document Type Section -->
<div class="row mb-4 mt-4">
    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>CMS 485 Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditDocType('cms_485')">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="cms_485_doc_type_label">
                                    {{ $agencyDetails->cms_485_document_type_name ?? ($agencyDetails->cms_485_document_type_id ? $agencyDetails->cms_485_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>
                        <div id="cms_485_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="cms_485_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveDocType('cms_485')">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelDocTypeEdit('cms_485')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the CMS 485 document type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Emergency Kardex Document Type Section -->
    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>Emergency Kardex Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditDocType('emergency_kardex')">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="emergency_kardex_doc_type_label">
                                    {{ $agencyDetails->emergency_kardex_document_type_name ?? ($agencyDetails->emergency_kardex_document_type_id ? $agencyDetails->emergency_kardex_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>
                        <div id="emergency_kardex_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="emergency_kardex_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveDocType('emergency_kardex')">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelDocTypeEdit('emergency_kardex')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the Emergency Kardex document type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supervision Document Type (simple) Section -->
<div class="row mb-4 mt-4">
    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>Supervision Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditDocType('supervision_simple')">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="supervision_simple_doc_type_label">
                                    {{ $agencyDetails->supervision_document_type_name ?? ($agencyDetails->supervision_document_type_id ? $agencyDetails->supervision_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>
                        <div id="supervision_simple_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="supervision_simple_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveDocType('supervision_simple')">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelDocTypeEdit('supervision_simple')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the Supervision document type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>Patient Package Document Type
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="syncAndEditDocType('patient_package')">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="patient_package_doc_type_label">
                                    {{ $agencyDetails->patient_package_document_type_name ?? ($agencyDetails->patient_package_document_type_id ? $agencyDetails->patient_package_document_type_id : 'Not Set') }}
                                </span>
                            </dd>
                        </dl>
                        <div id="patient_package_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="patient_package_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveDocType('patient_package')">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelDocTypeEdit('patient_package')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to sync and select the Patient Package type for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supervision Document Type Section -->
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<div class="row mb-4 mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-file-text mr-2"></i>Supervision Document Type
                    </h4>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">

                        {{-- Selected Document Type --}}
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-file-text-o mr-2 text-muted"></i>Selected Document Type</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2 ml-4" id="supervision_doc_type_label">
                                    {{ $agencyDetails->supervision_document_type_name ?? ($agencyDetails->supervision_document_type_id ? $agencyDetails->supervision_document_type_id : 'Not Set') }}
                                </span>
                                <button class="btn btn-outline-secondary btn-sm ml-2" id="supervision_edit_btn" style="height:26px;border-radius:5px" onclick="editSupervisionDocumentType(event)">
                                    <i class="fa fa-edit mr-1"></i>Edit
                                </button>
                            </dd>
                        </dl>

                        {{-- Edit form for Document Type (hidden by default) --}}
                        <div id="supervision_doc_type_edit_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Document Type</label>
                                <select id="supervision_document_type_select" class="form-control">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveSupervisionDocumentType()">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="cancelSupervisionDocumentTypeEdit()">
                                Cancel
                            </button>
                        </div>

                        <hr class="my-3">

                        {{-- Selected Medical & Medical Result (view mode) --}}
                        <div id="medical_combined_view">
                            <dl class="dl-horizontal agency-detail1">
                                <dt><i class="fa fa-medkit mr-2 text-muted"></i>Selected Medical</dt>
                                <dd>
                                    <div id="medical_badges_wrap" class="d-inline-flex flex-wrap align-items-center ml-4">
                                        <span class="text-muted small">Not Set</span>
                                    </div>
                                </dd>
                                <dt><i class="fa fa-stethoscope mr-2 text-muted"></i>Medical Result</dt>
                                <dd>
                                    <div id="medical_result_badge_wrap" class="d-inline-flex align-items-center ml-4">
                                        <span class="badge badge-light px-3 py-2 font-weight-normal" id="medical_result_label">Not Set</span>
                                    </div>
                                </dd>
                            </dl>
                            <button class="btn btn-outline-secondary btn-sm" id="open_medical_edit_btn"
                                style="height:26px;border-radius:5px"
                                onclick="openMedicalMultiselect(event)">
                                <i class="fa fa-edit mr-1"></i>Edit
                            </button>
                        </div>

                        {{-- Edit form for Medical & Result (hidden by default) --}}
                        <div id="medical_edit_combined_form" style="display:none;">
                            <div class="form-group">
                                <label class="font-weight-bold"><i class="fa fa-medkit mr-1"></i>Selected Medical</label>
                                <select id="medical_multiselect" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold"><i class="fa fa-stethoscope mr-1"></i>Medical Result</label>
                                <select id="medical_result_select" class="form-control">
                                    <option value="">-- Select Result --</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="saveMedicalAndResult()">
                                <i class="fa fa-save mr-1"></i>Save
                            </button>
                            <button class="btn btn-secondary btn-sm ml-2" onclick="closeMedicalAndResult()">
                                Cancel
                            </button>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info border-left-primary">
                            <i class="fa fa-info-circle mr-2"></i>
                            Click <strong>Edit</strong> to update the supervision document type, medical, and medical result for this agency.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var _SYNC_DOC_TYPE_URL   = "{{ url('hha/hha-patient/document-poc-type') }}";
    var _SAVE_DOC_TYPE_URL   = "{{ url('save-poc-document-type') }}";
    var _POC_AGENCY_SHA1_ID  = '{{ sha1($id) }}';
    var _POC_CURRENT_DOC_ID  = '{{ $agencyDetails->poc_document_type_id }}';
    var _GET_SUPERVISION_DOC_TYPES_URL  = "{{ url('hha-document-type') }}";
    var _SAVE_SUPERVISION_DOC_TYPE_URL  = "{{ url('save-supervision-document-type') }}";
    var _SUPERVISION_AGENCY_SHA1_ID     = '{{ sha1($id) }}';
    var _SUPERVISION_AGENCY_ID          = '{{ $id }}';
    var _SUPERVISION_CURRENT_DOC_ID     = "{{ $agencyDetails->supervision_document_type_id ?? '' }}";

    var _GET_AGENCY_MEDICALS_URL        = "{{ url('get-agency-other-compliance-medicals') }}";
    var _GET_MEDICAL_AND_RESULT_URL     = "{{ url('get-agency-medical-and-result') }}";
    var _GET_MEDICAL_RESULTS_URL        = "{{ url('get-agency-compliance-medical-results') }}";
    var _SAVE_MEDICAL_AND_RESULT_URL    = "{{ url('save-agency-medical-and-result') }}";
    var _AGENCY_MEDICAL_SHA1_ID         = '{{ sha1($id) }}';
    var _MEDICAL_RESULT_CURRENT_ID      = '';

    function syncAndEditPocDocumentType() {
        var btn = event.currentTarget;
        $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Syncing...');

        $.ajax({
            url: _SYNC_DOC_TYPE_URL,
            type: 'GET',
            data: { id: _POC_AGENCY_SHA1_ID },
            success: function(res) {
                var select = $('#poc_document_type_select');
                select.empty().append('<option value="">-- Select --</option>');
                $.each(res.data, function(i, doc) {
                    var selected = (doc.document_id == res.selected) ? 'selected' : '';
                    select.append('<option value="' + doc.document_id + '" ' + selected + '>' + doc.document_name + '</option>');
                });
                $('#poc_doc_type_edit_form').show();

            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function() {
                $(btn).prop('disabled', false).html('<i class="fa fa-edit mr-1"></i>Edit');
            }
        });
    }

    function savePocDocumentType() {
        var selectedId = $('#poc_document_type_select').val();
        var selectedText = $('#poc_document_type_select option:selected').text();

        if (!selectedId) {
            alert('Please select a document type.');
            return;
        }

        $.ajax({
            url: _SAVE_DOC_TYPE_URL,
            type: 'POST',
            data: {
                id: _POC_AGENCY_SHA1_ID,
                poc_document_type_id: selectedId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                    $('#poc_doc_type_label').text(selectedText);
                    $('#poc_doc_type_edit_form').hide();
                    toastr.success(res.error_msg);
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }

    function cancelPocDocumentTypeEdit() {
        $('#poc_doc_type_edit_form').hide();
    }

    // Shared helpers for Patient Assessment, CMS 485, Emergency Kardex document types
    var _DOC_TYPE_SAVE_URLS = {
        patient_assessment: "{{ url('save-patient-assessment-document-type') }}",
        cms_485:            "{{ url('save-cms485-document-type') }}",
        emergency_kardex:   "{{ url('save-emergency-kardex-document-type') }}",
        supervision_simple: "{{ url('save-supervision-simple-document-type') }}",
        patient_package: "{{ url('save-patient-package-document-type')}}"
    };
    var _DOC_TYPE_CURRENT_IDS = {
        patient_assessment: '{{ $agencyDetails->patient_assessment_document_type_id ?? "" }}',
        cms_485:            '{{ $agencyDetails->cms_485_document_type_id ?? "" }}',
        emergency_kardex:   '{{ $agencyDetails->emergency_kardex_document_type_id ?? "" }}',
        supervision_simple: '{{ $agencyDetails->supervision_document_type_id ?? "" }}',
        patient_package: '{{ $agencyDetails->patient_package_document_type_id ?? "" }}',
    };

    function syncAndEditDocType(type) {
        var btn = event.currentTarget;
        $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Syncing...');

        $.ajax({
            url: _SYNC_DOC_TYPE_URL,
            type: 'GET',
            data: { id: _POC_AGENCY_SHA1_ID },
            success: function(res) {
                var select = $('#' + type + '_document_type_select');
                select.empty().append('<option value="">-- Select --</option>');
                $.each(res.data, function(i, doc) {
                    var selected = (doc.document_id == _DOC_TYPE_CURRENT_IDS[type]) ? 'selected' : '';
                    select.append('<option value="' + doc.document_id + '" ' + selected + '>' + doc.document_name + '</option>');
                });
                $('#' + type + '_doc_type_edit_form').show();
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function() {
                $(btn).prop('disabled', false).html('<i class="fa fa-edit mr-1"></i>Edit');
            }
        });
    }

    function saveDocType(type) {
        var select     = $('#' + type + '_document_type_select');
        var selectedId = select.val();
        var selectedText = select.find('option:selected').text();

        if (!selectedId) {
            alert('Please select a document type.');
            return;
        }

        $.ajax({
            url: _DOC_TYPE_SAVE_URLS[type],
            type: 'POST',
            data: {
                id:                 _POC_AGENCY_SHA1_ID,
                document_type_id:   selectedId,
                document_type_name: selectedText,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                _DOC_TYPE_CURRENT_IDS[type] = selectedId;
                $('#' + type + '_doc_type_label').text(selectedText);
                $('#' + type + '_doc_type_edit_form').hide();
                toastr.success(res.error_msg);
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }

    function cancelDocTypeEdit(type) {
        $('#' + type + '_doc_type_edit_form').hide();
    }



    function editSupervisionDocumentType(e) {
        var btn = e.currentTarget;
        $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');

        $.ajax({
            url: _GET_SUPERVISION_DOC_TYPES_URL,
            type: 'GET',
            data: { agencyId: _SUPERVISION_AGENCY_ID },
            success: function(res) {
                var select = $('#supervision_document_type_select');
                select.empty().append('<option value="">-- Select --</option>');
                $.each(res.data, function(i, doc) {
                    var selected = (doc.id == _SUPERVISION_CURRENT_DOC_ID) ? 'selected' : '';
                    select.append('<option value="' + doc.id + '" ' + selected + '>' + doc.name + '</option>');
                });
                $('#supervision_doc_type_edit_form').show();
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function() {
                $(btn).prop('disabled', false).html('<i class="fa fa-edit mr-1"></i>Edit');
            }
        });
    }

    function saveSupervisionDocumentType() {
        var selectedId   = $('#supervision_document_type_select').val();
        var selectedText = $('#supervision_document_type_select option:selected').text();

        if (!selectedId) {
            alert('Please select a document type.');
            return;
        }

        $.ajax({
            url: _SAVE_SUPERVISION_DOC_TYPE_URL,
            type: 'POST',
            data: {
                id: _SUPERVISION_AGENCY_SHA1_ID,
                supervision_document_type_id:   selectedId,
                supervision_document_type_name: selectedText,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                _SUPERVISION_CURRENT_DOC_ID = selectedId;
                $('#supervision_doc_type_label').text(selectedText);
                $('#supervision_doc_type_edit_form').hide();
                toastr.success(res.error_msg);
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }

    function cancelSupervisionDocumentTypeEdit() {
        $('#supervision_doc_type_edit_form').hide();
    }


    // ---- Render helpers ----

    function renderMedicalBadges(data) {
        var wrap = $('#medical_badges_wrap');
        wrap.empty();
        if (!data || data.length === 0) {
            wrap.append('<span class="text-muted small">Not Set</span>');
            return;
        }
        $.each(data, function(i, item) {
            wrap.append('<span class="badge badge-info mr-1 mb-1 px-2 py-1" style="font-size:12px">' + (item.medical_name || item.medical_id) + '</span>');
        });
    }

    function renderMedicalResultBadge(data) {
        var first = data && data.length > 0 ? data[0] : null;
        var label = $('#medical_result_label');
        if (first && first.medical_result_id) {
            label.text(first.medical_result_name || first.medical_result_id)
                 .removeClass('badge-light').addClass('badge-info');
            _MEDICAL_RESULT_CURRENT_ID = first.medical_result_id;
        } else {
            label.text('Not Set').removeClass('badge-info').addClass('badge-light');
            _MEDICAL_RESULT_CURRENT_ID = '';
        }
    }

    // ---- Selected Medical + Medical Result functions ----

    function openMedicalMultiselect(e) {
        var btn = e.currentTarget;
        $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');

        $.ajax({
            url: _GET_MEDICAL_AND_RESULT_URL,
            type: 'GET',
            data: { id: _AGENCY_MEDICAL_SHA1_ID },
            success: function(res) {
                // Populate medical multiselect
                var sel = $('#medical_multiselect');
                sel.empty();

                var selectedIds = {};
                $.each(res.existing, function(i, item) {
                    selectedIds[item.medical_id] = item.medical_name;
                });

                $.each(res.options, function(i, opt) {
                    var isSelected = selectedIds.hasOwnProperty(opt.id) ? 'selected' : '';
                    sel.append('<option value="' + opt.id + '" data-name="' + opt.name + '" ' + isSelected + '>' + opt.name + '</option>');
                });

                sel.select2({ placeholder: '-- Select Medical --', allowClear: true, width: '100%' });
                sel.off('select2:select.medresult').on('select2:select.medresult', function(ev) {
                    loadMedicalResults(ev.params.data.id);
                });

                // Populate result select from combined response (pre-select saved result)
                var first = res.existing && res.existing.length > 0 ? res.existing[0] : null;
                _MEDICAL_RESULT_CURRENT_ID = first ? (first.medical_result_id || '') : '';

                var resultSel = $('#medical_result_select');
                resultSel.empty().append('<option value="">-- Select Result --</option>');
                $.each(res.results, function(i, item) {
                    var isSelected = (item.id == _MEDICAL_RESULT_CURRENT_ID) ? 'selected' : '';
                    resultSel.append('<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>');
                });

                $('#medical_combined_view').hide();
                $('#medical_edit_combined_form').show();
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function() {
                $(btn).prop('disabled', false).html('<i class="fa fa-edit mr-1"></i>Edit');
            }
        });
    }

    function closeMedicalAndResult() {
        $('#medical_edit_combined_form').hide();
        $('#medical_combined_view').show();
        if ($('#medical_multiselect').data('select2')) {
            $('#medical_multiselect').select2('destroy');
        }
    }

    function saveMedicalAndResult() {
        var sel      = $('#medical_multiselect');
        var medicals = [];
        sel.find('option:selected').each(function() {
            medicals.push({
                medical_id:   $(this).val(),
                medical_name: $(this).data('name') || $(this).text()
            });
        });

        var resultSel  = $('#medical_result_select');
        var resultId   = resultSel.val();
        var resultText = resultSel.find('option:selected').text();

        $.ajax({
            url: _SAVE_MEDICAL_AND_RESULT_URL,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id:                  _AGENCY_MEDICAL_SHA1_ID,
                medicals:            medicals,
                medical_result_id:   resultId,
                medical_result_name: resultText,
                _token:              $('meta[name="csrf-token"]').attr('content')
            }),
            success: function(res) {
                renderMedicalBadges(res.data);
                renderMedicalResultBadge(res.data);
                closeMedicalAndResult();
                toastr.success(res.error_msg);
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }

    // Called when user changes medical selection — reloads result options from HHA
    function loadMedicalResults(medicaid_id) {
        var sel = $('#medical_result_select');
        sel.prop('disabled', true).html('<option value="">Loading...</option>');

        $.ajax({
            url: _GET_MEDICAL_RESULTS_URL,
            type: 'GET',
            data: { id: _AGENCY_MEDICAL_SHA1_ID, medicaid_id: medicaid_id },
            success: function(res) {
                sel.empty().append('<option value="">-- Select Result --</option>');
                $.each(res.data, function(i, item) {
                    var isSelected = (item.id == _MEDICAL_RESULT_CURRENT_ID) ? 'selected' : '';
                    sel.append('<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>');
                });
                sel.prop('disabled', false);
            },
            error: function(jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
                sel.prop('disabled', false);
            }
        });
    }

    // On page load: render medical badges and medical result badge from agency_other_compliance_medicals
    $(document).ready(function() {
        $.ajax({
            url: _GET_AGENCY_MEDICALS_URL,
            type: 'GET',
            data: { id: _AGENCY_MEDICAL_SHA1_ID },
            success: function(res) {
                renderMedicalBadges(res.data);
                renderMedicalResultBadge(res.data);
            }
        });
    });
</script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>

<style>
    /* Custom styles for improved UI */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-primary-light {
        background-color: rgba(0, 123, 255, 0.1);
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-secondary-light {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .border-left-primary {
        border-left: 4px solid #007bff;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107;
    }
    .agency-detail1 dt {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
    }
    .agency-detail1 dd {
        margin-bottom: 15px;
    }
    .card-title {
        font-size: 1.1rem;
    }
</style>