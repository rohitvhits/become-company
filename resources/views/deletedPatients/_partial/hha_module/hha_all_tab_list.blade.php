<div class="tabs-hha--scrollable">
    <button class="tabs_hha__scroller tabs_hha__scroller--left js-hha-action--scroll-left"><i class="fa fa-chevron-left"></i></button>
    <div class="tabs_hha__toggle-group">
        @if($auth->agency_fk =='106')
            @if($record->hha_id !="" || $record->link_hha_caregiver !="")
                <div class="tabs_hha__toggle tabs_hha__toggle--active">
                    <a class="nav-link" href="#hha-caregiver-demographic" onclick="getHHADemographic()" data-toggle="tab">Demographic Details</a>
                </div>
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-calender-section" data-toggle="tab">Calendar</a>
                </div>
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-caregiver-notes" onclick="refreshHHA()" data-toggle="tab"> Notes</a>
                </div>
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-caregiver-medical" onclick="getMedicalalList()"
                        data-toggle="tab">Medical</a>
                </div>
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-caregiver-other-compliance" onclick="refreshOtherCompliance()"
                        data-toggle="tab">Other Compliance</a>
                </div>
            @endif
        @endif
        @if($user['user_type_fk'] == 184)
            
                @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
                    @if ($record->type == 'Caregiver')
                        @can('hha-caregiver-demographic')
                            <div class="tabs_hha__toggle tabs_hha__toggle--active">
                                <a class="nav-link" class="active" href="#hha-caregiver-demographic" onclick="getHHADemographic()"
                                    data-toggle="tab">Demographic Details</a>
                            </div>
                        @endcan
                    @endif
                    @can('hha-sync-appointment-calendar')
                        <div class="tabs_hha__toggle">
                            <a class="nav-link" href="#hha-calender-section" data-toggle="tab">Calendar</a>
                        </div>
                    @endcan
                    @if ($record->type == 'Caregiver')
                        @can('hha-caregiver-preferences')
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-preferences-section" onclick="refreshCaregiverPreferencesData()"
                                    data-toggle="tab">Preferences</a>
                            </div>
                        @endcan
                        @can('hha-caregiver-avaibility')
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-avaibility" onclick="getCargiverAvaibility()"
                                    data-toggle="tab">Availability</a>
                            </div>
                        @endcan

                        @can('hha-calendar-notes')
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-notes" onclick="refreshHHA()"
                                    data-toggle="tab">Notes</a>
                            </div>
                        @endcan
                        @can('hha-calendar-inservice')
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-inservice" onclick="getInService()"
                                    data-toggle="tab">InService</a>
                            </div>
                        @endcan
                        @can('hha-calendar-medical')
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-medical" onclick="getMedicalalList()"
                                    data-toggle="tab">Medical</a>
                            </div>
                            <div class="tabs_hha__toggle">
                                <a class="nav-link" href="#hha-caregiver-other-compliance"
                                    onclick="refreshOtherCompliance()" data-toggle="tab">Other
                                    Compliance</a>
                            </div>
                        @endcan
                        @can('hha-caregiver-document')
                        <div class="tabs_hha__toggle">
                            <a class="nav-link" href="#hha-caregiver-document-section" data-toggle="tab" onclick="refreshDocumentData()">Document</a>
                        </div>
                        @endcan
                    @endif
                @endif
            
        @endif
        @if ($record->link_hha_patient != '' && $record->link_hha_patient != 0)
            @can('hha-patient-demographic')
                <div class="tabs_hha__toggle tabs_hha__toggle--active">
                    <a class="nav-link" href="#hha-demographic-details" onclick="getHHADemographicDetails()"
                        data-toggle="tab">Demographic Details</a>
                </div>
            @endcan
            @can('hha-sync-appointment-calendar')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-calender-section" data-toggle="tab">Calendar</a>
                </div>
            @endcan
            @can('hha-get-patient-authorization-info-details')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-get-patient-authorization-info-details"
                        onclick="GetPatientAuthorizationInfo()" data-toggle="tab">Authorization Info Section</a>
                </div>
            @endcan


            @can('hha-get-patient-notes')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-get-patient-notes" onclick="GetPatientNotes()"
                        data-toggle="tab">Notes</a>
                </div>
            @endcan

            @can('hha-get-patient-clinics')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-get-patient-clinics" onclick="GetPatientClinics()" data-toggle="tab">Clinical</a>
                </div>
            @endcan

            @can('hha-get-patient-poc-info')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-get-patient-poc-info" onclick="GetPatientPOCInfo()" data-toggle="tab">POC Info</a>
                </div>
            @endcan

            @can('hha-get-patient-changes-v2')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-get-patient-v2-changes-section" onclick="GetPatientChangesV2Info()" data-toggle="tab">Changes V2</a>
                </div>
            @endcan

            @can('hha-patient-document')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-patient-document-section" data-toggle="tab" onclick="refreshPatientDocumentData()">Document</a>
                </div>
            @endcan
            @can('hha-patient-contract')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-patient-contract-section" data-toggle="tab" onclick="refreshPatientContactData()">Contract</a>
                </div>
            @endcan
            @can('hha-patient-discipline')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-patient-discipline-section" data-toggle="tab" onclick="refreshPatientDisciplineData()">Discipline</a>
                </div>
            @endcan
            @can('hha-patient-preferences')
                <div class="tabs_hha__toggle">
                    <a class="nav-link" href="#hha-patient-preferences-section" data-toggle="tab" onclick="refreshPatientPreferencesData()">Preferences</a>
                </div>
            @endcan
        @endif
    </div>
    <button class="tabs_hha__scroller tabs_hha__scroller--right js-hha-action--scroll-right"><i class="fa fa-chevron-right"></i></button>
</div>
<div class="tabs_hha__tabs-group">
    @if($auth->agency_fk =='106')
        @if($record->hha_id !="" || $record->link_hha_caregiver !="")
            <div class="tabs_hha__tab">
                @include('patient/_partial/all_tabs_section/hha_caregiver_demographic')
            </div>
            <div class="tabs_hha__tab">
                @include('patient/_partial/all_tabs_section/hha_calender_section')
            </div>
            <div class="tabs_hha__tab">
                @include('patient/_partial/all_tabs_section/hha_caregiver_notes')
            </div>
            <div class="tabs_hha__tab">
                @include('patient/_partial/all_tabs_section/hha_caregiver_medical')
            </div>
            <div class="tabs_hha__tab">
                @include('patient/_partial/all_tabs_section/hha_other_compliance')
            </div>
        @endif
    @endif
    @if($user['user_type_fk'] == 184)
       
            @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
                @if ($record->type == 'Caregiver')
                    @can('hha-caregiver-demographic')
                        <div class="tabs_hha__tab">
                            @include('patient._partial.hha_module.hha_caregiver_demographic')
                        </div>
                    @endcan
                    @can('hha-sync-appointment-calendar')
                        <div class="tabs_hha__tab">
                            @include('patient/_partial/all_tabs_section/hha_calender_section')
                        </div>
                    @endcan
                    @can('hha-caregiver-preferences')
                        <div class="tabs_hha__tab" id="hha-caregiver-preferences-section">
                            @include('patient._partial.hha_module.caregiverPrefernces.hha_caregiver_prefrences')
                        </div>
                    @endcan
                    @can('hha-caregiver-avaibility')
                        <div class="tabs_hha__tab">
                            @include('patient._partial.hha_module.hha_caregiver_availability')
                        </div>
                    @endcan
                    @can('hha-calendar-notes')
                        <div class="tabs_hha__tab">@include('patient/_partial/all_tabs_section/hha_caregiver_notes')</div>
                    @endcan
                    @can('hha-calendar-inservice')
                        <div class="tabs_hha__tab">
                            @include('patient/_partial/all_tabs_section/hha_caregiver_inService')
                        </div>
                    @endcan

                    @can('hha-calendar-medical')
                        <div class="tabs_hha__tab">@include('patient/_partial/all_tabs_section/hha_caregiver_medical')
                        </div>

                        <div class="tabs_hha__tab">
                            @include('patient/_partial/all_tabs_section/hha_other_compliance')
                        </div>
                    @endcan
                    <div class="tabs_hha__tab">
                        @include('patient._partial.hha_module.caregiverDocument.hha_caregiver_document')
                    </div>
                @endif
            @endif
       
    @endif
    @if ($record->link_hha_patient != '' && $record->link_hha_patient != 0)
        @if($record->type =='Patient')
            @can('hha-patient-demographic')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.hha_patient_demographic_details')
                </div>
            @endcan
            @can('hha-sync-appointment-calendar')
                <div class="tabs_hha__tab">
                    @include('patient/_partial/all_tabs_section/hha_calender_section')
                </div>
            @endcan
            @can('hha-get-patient-authorization-info-details')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.hha_patient_authorization_info')
                </div>
            @endcan
            @can('hha-get-patient-notes')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.hha_patient_notes_info')
                </div>
            @endcan
            @can('hha-get-patient-clinics')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.hha_patient_clinics_info')
                </div>
            @endcan
            @can('hha-get-patient-poc-info')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.hha_patient_poc_info')
                </div>
            @endcan
            @can('hha-get-patient-changes-v2')
            <div class="tabs_hha__tab"></div>
            @endcan
            @can('hha-patient-document')
                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.patientDocument.hha_patient_document')
                </div>
            @endcan
            @can('hha-patient-contract')
                <div class="tabs_hha__tab" id="hha-patient-contract-section">
                    @include('patient._partial.hha_module.patientContract.hha_patient_contract')
                </div>
            @endcan
            @can('hha-patient-discipline')
                <div class="tabs_hha__tab" id="hha-patient-discipline-section">
                    @include('patient._partial.hha_module.patientDisipline.hha_patient_discipline')
                </div>
            @endcan
            @can('hha-patient-preferences')

                <div class="tabs_hha__tab">
                    @include('patient._partial.hha_module.patientPrefernces.hha_patient_prefrences')
                </div>
            @endcan
        @endif

    @endif
</div>
</div>