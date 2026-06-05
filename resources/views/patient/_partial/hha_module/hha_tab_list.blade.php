<div class="tab-content right-section-tab-content" id="hha-exchange">
    <div class="tab-pane" id="hha-calender-section">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Calendar</p>
        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag12" style="display: none; ">
                </div>
                <div id="calendar" class="full-calendar"></div>

            </div>

        </div>

    </div>

    <div class="tab-pane" id="hha-caregiver-notes">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Notes</p>
            @if($auth->agency_fk !=106)
            <p class="mb-0 tx-13">
                <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHACaregiverSubject()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                    Add</a>

            </p>
            @endif
        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                </div>

            </div>
            <div class="col-12">
                <table class="table table-bordered" id="chat-messages-news-dataTable">
                    <thead>
                        <th>No</th>
                        <th>Notes</th>

                        <th>Created Date</th>
                    </thead>
                    <tbody id="chat-messages-news">

                    </tbody>
                </table>
                <div id="hha-caregiver-notes-pagination"></div>
            </div>

        </div>
    </div>

    <div class="tab-pane" id="hha-caregiver-medical">
    <div class="d-flex align-items-center justify-content-between mb-3" style="gap:50px">
            
            <p class="card-title mb-0">Medical</p>
  
            <div class="row align-items-center" style="width:100%">
                <div class="col-md-4">
                    <select class="form-control" id="hha_status_id" onChange="getMedicalalList()">
                        <option value="">Select</option>
                        @foreach($hhaStatusList as $val)
                            <option value="{{ $val->status }}">{{ $val->status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-right">
                    <a class="btn btn-info btn-sm w-100" onclick="refreshMedical()" data-whatever="@mdo">
                        <i class="mdi mdi-sync"></i> SYNC Medical
                    </a>
                </div>
                <div class="col-md-4 text-right">
                    <a data-toggle="modal" class="btn btn-primary btn-sm w-100" onclick="addRefreshMedical()" data-whatever="@mdo">
                        <i class="mdi mdi-plus"></i> Add Medical
                    </a>
                </div>
            </div>
    
</div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                </div>

            </div>
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <th>No</th>
                        <th>Medical Name</th>
                        <th>Status</th>
                        <th>Medical Due Date</th>
                        <th>Date Perform</th>
                        <th>Result</th>
                    </thead>
                    <tbody id="tbody_id">

                    </tbody>
                </table>
            </div>

        </div>


    </div>

    <div class="tab-pane" id="hha-caregiver-inservice">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">InService</p>
            @can('hha-calendar-add-inservice')
            <!-- <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHACaregiverSubject()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                                                            Add</a> -->
            @endcan

        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                </div>

            </div>
            <div class="col-12">
                <table class="table table-bordered" id="caregiver_inservice_datatable">
                    <thead>
                        <th>No</th>
                        <th>Topic Name</th>
                        <th>InService Date</th>
                        <th>From Time</th>
                        <th>End Time</th>
                        <th>Description</th>
                    </thead>
                    <tbody id="caregiver_inservice_id">

                    </tbody>
                </table>
            </div>

        </div>


    </div>

    <div class="tab-pane" id="hha-caregiver-other-compliance">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Other Compliance</p>
            <p class="mb-0 tx-13    row pull-right">
                <!-- <a  class="btn btn-info btn-sm" onclick="refreshOtherCompliance()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                                                                    SYNC Caregiver Other Compliance</a>
                                                    </p> -->


        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList11" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag1211" style="display:none">
                </div>

            </div>
            <div class="col-12" style="max-height: 500px;overflow-y: auto;">
                <table class="table table-bordered">
                    <thead>
                        <th>No</th>
                        <th>Medical Id</th>
                        <th>Medical Name</th>
                        <th>Status</th>
                        <th>Result</th>
                        <th>Notes</th>
                        <th>Due Date</th>
                        <th>Date Performed</th>
                        <th>Modified Date</th>
                    </thead>
                    <tbody id="tbody_compliance_id">

                    </tbody>
                </table>
            </div>

        </div>


    </div>

    <div class="tab-pane" id="hha-caregiver-document-section">
      
        @include('patient._partial.hha_module.caregiverDocument.hha_caregiver_document')
    </div>
    @if($record->type =='Caregiver')
    <div class="tab-pane active" id="hha-caregiver-demographic">
        @include('patient._partial.hha_module.hha_caregiver_demographic')
    </div>
    @endif
    @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
    
    @can('hha-caregiver-preferences')
        <div class="tab-pane" id="hha-caregiver-preferences-section">
            @include('patient._partial.hha_module.caregiverPrefernces.hha_caregiver_prefrences')
        </div>
    @endcan
    
    @endif
    <div class="tab-pane" id="hha-caregiver-avaibility">
        @include('patient._partial.hha_module.hha_caregiver_availability')
    </div>

    @if($record->link_hha_patient != "" && $record->link_hha_patient != 0)
    <div class="tab-pane active" id="hha-demographic-details">
        @include('patient._partial.hha_module.hha_patient_demographic_details')
    </div>

    <div class="tab-pane" id="hha-get-patient-authorization-info-details">
        @include('patient._partial.hha_module.hha_patient_authorization_info')
    </div>
    <div class="tab-pane" id="hha-get-patient-notes">
        @include('patient._partial.hha_module.hha_patient_notes_info')
    </div>
    <div class="tab-pane" id="hha-get-patient-clinics">
        @include('patient._partial.hha_module.hha_patient_clinics_info')
    </div>
    <div class="tab-pane" id="hha-get-patient-poc-info">
        @include('patient._partial.hha_module.hha_patient_poc_info')
    </div>
    <div class="tab-pane" id="hha-get-patient-v2-changes-section">
        @include('patient._partial.hha_module.hha_patient_v2_changes')
    </div>
    <div class="tab-pane" id="hha-get-patient-authorization-changes-section">
        @include('patient._partial.hha_module.hha_patient_authorization_changes_info')
    </div>
    @can('hha-patient-document')
    <div class="tab-pane" id="hha-patient-document-section">
        @include('patient._partial.hha_module.patientDocument.hha_patient_document')
    </div>
    @endcan
    <div class="tab-pane" id="hha-patient-coordinator">
        @include('patient._partial.hha_patient_cordinator')
    </div>


    @can('hha-patient-contract')
    <div class="tab-pane" id="hha-patient-contract-section">
        @include('patient._partial.hha_module.patientContract.hha_patient_contract')
    </div>
    @endcan
    @can('hha-patient-discipline')
    <div class="tab-pane" id="hha-patient-discipline-section">
        @include('patient._partial.hha_module.patientDisipline.hha_patient_discipline')
    </div>
    @endcan
    @can('hha-patient-preferences')
    <div class="tab-pane" id="hha-patient-preferences-section">
        @include('patient._partial.hha_module.patientPrefernces.hha_patient_prefrences')
    </div>
    @endcan
    @can('hha-patient-md-order')
    <div class="tab-pane" id="hha-patient-mdo-order-section">
        @include('patient._partial.hha_module.hha_mdorder.hha_mdorder_list')
    </div>
    @endcan
    @endif
</div>