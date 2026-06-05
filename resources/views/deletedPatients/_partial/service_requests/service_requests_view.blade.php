@include('include/header')
@include('include/sidebar')
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/patient.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/tabs.css')}}?time={{ env('timestamp')}}">

<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom mb-3">
                <div class="row col-md-12 d-flex align-items-center mb-3">
                    <h4 class="mb-0 font-weight-bold">
                        ID # <?= $record->id . ' - ' . ucwords($record->first_name) . ' ' . ucwords($record->last_name) . ' ' ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div style="height:300px; overflow-y: scroll;">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Basic Detail</h4>
                                </div>
                                <div class="card-body">
                                    <dl class="dl-horizontal">
                                        <dt>Patient Code</dt>
                                        <dd><?= isset($record->patient_code) && $record->patient_code != '' ? $record->patient_code : 'N/A'; ?></dd>

                                        <dt>First Name</dt>
                                        <dd><?= isset($record->first_name) && $record->first_name != '' ? $record->first_name : 'N/A'; ?></dd>

                                        <dt>Middle Name</dt>
                                        <dd><?= isset($record->middle_name) && $record->middle_name != '' ? $record->middle_name : 'N/A'; ?></dd>

                                        <dt>Last Name</dt>
                                        <dd><?= isset($record->last_name) && $record->last_name != '' ? $record->last_name : 'N/A'; ?></dd>

                                        <dt>Gender</dt>
                                        <dd><?= isset($record->gender) && $record->gender != '' ? ucfirst($record->gender) : 'N/A'; ?></dd>

                                        <dt>Mobile</dt>
                                        <dd><?= isset($record->mobile) && $record->mobile != '' ? $record->mobile : 'N/A'; ?></dd>

                                        <dt>Phone</dt>
                                        <dd><?= isset($record->phone) && $record->phone != '' ? $record->phone : 'N/A'; ?></dd>

                                        <dt>Country</dt>
                                        <dd><?= isset($record->county) && $record->county != '' ? $record->county : 'N/A'; ?></dd>

                                        <dt>State</dt>
                                        <dd><?= isset($record->state) && $record->state != '' ? $record->state : 'N/A'; ?></dd>

                                        <dt>City</dt>
                                        <dd><?= isset($record->city) && $record->city != '' ? $record->city : 'N/A'; ?></dd>

                                        <dt>Address1</dt>
                                        <dd><?= isset($record->address1) && $record->address1 != '' ? $record->address1 : 'N/A'; ?></dd>

                                        <dt>Apt/Suite/Floor</dt>
                                        <dd><?= isset($record->address2) && $record->address2 != '' ? $record->address2 : 'N/A'; ?></dd>

                                        <dt>Zipcode</dt>
                                        <dd><?= isset($record->zip_code) && $record->zip_code != '' ? $record->zip_code : 'N/A'; ?></dd>

                                        <dt>Date of Birth</dt>
                                        <dd><?= isset($record->dob) && $record->dob != '' && $record->dob != '0000-00-00' ? Common::convertMDY($record->dob) : 'N/A'; ?></dd>

                                        <?php if ($record->type == 'Caregiver') {
                                            if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                <dt>Emergency Phone</dt>
                                                <dd>
                                                    <span id="emergency_phones"><?= isset($record->emergency_phone) && $record->emergency_phone != '' ? $record->emergency_phone : 'N/A'; ?></span>
                                                    <a data-toggle="modal" data-target="#exampleModal-emergency_phone" data-whatever="@mdo" title="Emergency Phone" onclick="updatePhoneDetails('<?= $record->emergency_phone; ?>')">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </dd>
                                                <dt>Email</dt>
                                                <dd>
                                                    <span id="emergency_email"><?= isset($record->email) && $record->email != '' ? $record->email : 'N/A'; ?></span>
                                                    <a data-toggle="modal" data-target="#exampleModal-email" data-whatever="@mdo" title="Email" onclick="updateEmailDetails('<?= $record->email; ?>')">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </dd>
                                        <?php }
                                        } ?>

                                        <?php if ($user['user_type_fk'] == 184) { ?>
                                            <dt>Email</dt>
                                            <dd>
                                                <span id="emergency_email"><?= $record->email != '' ? $record->email : 'N/A'; ?></span>
                                                <a data-toggle="modal" data-target="#exampleModal-email" data-whatever="@mdo" title="Email" onclick="updateEmailDetails('<?= $record->email; ?>')">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </dd>
                                        <?php } ?>

                                        <dt>Insurance ID</dt>
                                        <dd><?= isset($record->insurance_id) && $record->insurance_id != '' ? $record->insurance_id : 'N/A'; ?></dd>

                                        <dt>Insurance Name</dt>
                                        <dd>
                                            <?php
                                            $otherName = "";
                                            if (isset($record->insuranceName) && $record->insuranceName == 'other' && isset($record->other_insurance_name) && $record->other_insurance_name != '') {
                                                $otherName = '( ' . $record->other_insurance_name . ' )';
                                            }
                                            echo isset($record->insuranceName) && $record->insuranceName != '' ? $record->insuranceName . ' ' . $otherName : 'N/A';
                                            ?>
                                        </dd>

                                        <dt>Emergency Contact Name</dt>
                                        <dd><?= isset($record->emergency_contact_name) && $record->emergency_contact_name != '' ? $record->emergency_contact_name : 'N/A'; ?></dd>

                                        <?php if ($record->agency_id != '319' && $record->agency_id != '106') { ?>
                                            <dt>Emergency Contact Number</dt>
                                            <dd><?= isset($record->emergency_phone) && $record->emergency_phone != '' ? $record->emergency_phone : 'N/A'; ?></dd>
                                        <?php } ?>

                                        <dt>SSN</dt>
                                        <dd><span><?= $record->ssn; ?></span></dd>
                                        <dt>CIN</dt>
                                        <dd><span><?= $record->cin; ?></span></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="tabs--container">
                                    <div class="tabs js-tabs">
                                        <div class="tabs--scrollable">
                                            <button class="tabs__scroller tabs__scroller--left js-action--scroll-left"><i class="fa fa-chevron-left"></i></button>

                                            <div class="tabs__toggle-group">
                                                <div class="tabs__toggle tabs__toggle--active">
                                                    <a class="nav-link" href="#document-service-section" data-toggle="tab" onclick="loadPatientRequestedServices()">Services</a>
                                                </div>

                                                <div class="tabs__toggle">
                                                    <a class="nav-link" href="#sms-logs-section" data-toggle="tab" onClick="smsLogs(1)">Services Logs</a>
                                                </div>
                                            </div>

                                            <button class="tabs__scroller tabs__scroller--right js-action--scroll-right"><i class="fa fa-chevron-right"></i></button>
                                        </div>

                                        <div class="tabs__tabs-group">
                                            <div class="tabs__tab">
                                                @include('patient._partial.service_requests._partial.patient_requested_services')
                                            </div>
                                            <div class="tabs__tab">
                                                @include('patient._partial.service_requests._partial.service_requested_log')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('include/footer')
<script>

    var _RECORD_ID = "{{$service_request_data->id}}";
    var _CSRF_TOKEN ="{{ csrf_token()}}";
    var _PATIENT_WISE_SERVICE_LIST ="{{ url('patient-service-wise-list') }}";
   var  _UPLOAD_DOCUMENT_REQUEST_SERVICE = "{{ url('upload-document-request-service') }}"
    var _PATIENT_ID ="";
    var _CAREGIVER_ID ="";
    var remoteID ="";
    var _ALAYACAREID ="";
</script>
<script src="{{ asset('assets/modulejs/tabs_design.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient_view.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>