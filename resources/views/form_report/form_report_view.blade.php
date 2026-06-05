<meta name="csrf-token" content="{{ csrf_token() }}">

<title>NY BEST MEDICAL</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.eot">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.ttf">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.woff">
<link href="<?= URL::to('assets/css/vertical-layout-light/jquery-ui.css') ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/css/vendor.bundle.base.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/jqvmap/jqvmap.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/flag-icon-css/css/flag-icon.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/css/horizontal-default-light/style.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/css/sweetalert2.min.css') ?>">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />
<link href="<?= URL::to('assets/css/select2.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<link href="<?= URL::to('assets/css/jquery-confirm.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet"
    type="text/css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

<style>
    .compact-view .form-control {
        padding: 0 !important;
        height: 24px;
    }

    .compact-view td {
        padding: 5px 10px;
    }

    .horizontal-menu .top-navbar {
        font-weight: 400;
        background: #1e1e2f;
        border-bottom: 1px solid #030303;
    }

    .horizontal-menu .top-navbar .navbar-menu-wrapper {
        color: #b1b1b5;
    }

    .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link .menu-title,
    .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link i,
    .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link i,
    .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link .menu-title {
        color: #97C229 !important;
    }

    .horizontal-menu .bottom-navbar {
        background: #FFF;
    }

    .horizontal-menu .bottom-navbar .page-navigation>.nav-item>.nav-link {
        color: #686868;
    }

    li.select2-selection__choice {
        padding: 5px !important;
        font-size: 1rem !important;
    }

    .agency-logo {
        display: flex;
        align-items: center;
        padding: 10px 0;
    }

    .agency-logo a {
        padding: 0 10px !important;
    }

    .text-danger {
        color: red !important;
    }

    .field-container {
        margin-bottom: 15px;
    }

    .label-edit-container {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .label-edit-container dt {
        margin-right: 5px;
        font-weight: bold;
    }

    .edit-icon {
        cursor: pointer;
        color: black;
    }

    .no-data {
        text-align: center;
        color: black;
        font-weight: bold;
    }

    .card-footer {
        display: flex;
        justify-content: flex-end;
        padding: 10px;
    }

    .download-icon {
        font-size: 20px;
        cursor: pointer;
    }

    .form-check-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
    }

    .form-check {
        display: flex;
        align-items: center;
        box-sizing: border-box;
    }

    .form-check-label {
        margin-right: 5px;
    }
</style>

<div class="col-lg-12 grid-margin stretch-card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <p class="card-title mb-0"><b>{{ $agency_all_form_data['forms']['title'] ?? '' }}</b></p>
            </div>
            <div class="mt-4">
                <div class="card border-bottom agencyAllFormList" data-id="{{ $agency_all_form_data->id ?? '' }}"
                    data-f-id="{{ $agency_all_form_data->form_id ?? '' }}">
                    <form id="dynamicAgencyForm_{{ $agency_all_form_data['id'] ?? '' }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="form_id_{{ $agency_all_form_data->form_id ?? '' }}" name="form_id"
                            value="{{ $agency_all_form_data->form_id ?? '' }}">
                        <input type="hidden" id="patient_i_{{ $agency_all_form_data->form_id ?? '' }}"
                            name="patient_id" value="{{ $agency_all_form_data->patient_id ?? '' }}">
                        <input type="hidden" id="agency_id_{{ $agency_all_form_data->form_id ?? '' }}"
                            name="agency_id" value="{{ $agency_all_form_data->agency_id ?? '' }}">
                        <input type="hidden" id="doctor_id_{{ $agency_all_form_data->form_id ?? '' }}"
                            name="doctor_id" value="{{ $agency_all_form_data->doctors->id ?? '' }}">
                        <input type="hidden" id="status" name="status" value="{{ $status }}">
                        <input type="hidden" name="type_value" value="FancyBox">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="field-container">
                                        <div class="label-edit-container">
                                            <dt>Doctor Name</dt>
                                        </div>
                                        <dd>
                                            @if ($agency_all_form_data['mark_as_completed'] === '1')
                                                <span
                                                    id="dynamic-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-doctor_name"
                                                    class="flex-grow-1 dynamic-form-value-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}">
                                                    {{ $agency_all_form_data->doctors->full_name }}
                                                </span>
                                            @endif
                                            <?php
                                            $oldDoctorId = $agency_all_form_data->doctor_id;
                                            ?>
                                            <input type="hidden" name="formId" id="formId"
                                                value="{{ $agency_all_form_data->id }}">
                                            @if ($agency_all_form_data['mark_as_completed'] != '1')
                                                <select name="doctor_id"
                                                    id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-doctor_name"
                                                    class="form-control">
                                                    <option value="">Select Doctor</option>
                                                    @foreach ($doctorList as $doctor)
                                                        <option value="{{ $doctor->id }}"
                                                            {{ $doctor->id == $oldDoctorId ? 'selected' : '' }}>
                                                            {{ ucfirst($doctor->full_name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="col-sm-11 ml-auto pl-0 mt-2 doctor_id_error"
                                                    style="color:red">
                                                    @error('doctor_id')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            @endif
                                        </dd>
                                    </div>
                                </div>

                                @if (count($agency_all_form_data->agencyMaster) > 0)
                                    @foreach ($agency_all_form_data->agencyMaster->groupBy('fields.form_group_id') as $formGroupId => $groupedFields)
                                        @if (isset($formGroupId))
                                            <legend>
                                                {{ $groupedFields->first()['fields']['formGroup']['title'] ?? '' }}
                                            </legend>
                                            @foreach ($groupedFields as $agencyWise)
                                                @if (isset($agencyWise['fields']) && !empty($agencyWise['fields']))
                                                    @php
                                                        $colSize = 'col-md-12';
                                                        if (
                                                            isset($agencyWise['fields']['size']) &&
                                                            $agencyWise['fields']['size'] == 'half'
                                                        ) {
                                                            $colSize = 'col-md-6';
                                                        }
                                                    @endphp
                                                    <div class="{{ $colSize }}">
                                                        <div class="field-container">
                                                            <div class="label-edit-container">
                                                                <dt>{{ ucfirst($agencyWise['fields']['label']) }}
                                                                </dt>
                                                            </div>
                                                            <dd>
                                                                @if ($agency_all_form_data['mark_as_completed'] === '1')
                                                                    <span
                                                                        id="dynamic-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                        class="flex-grow-1 dynamic-form-value-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}">

                                                                        @if (
                                                                            $agencyWise['fields']['type'] === 'date' &&
                                                                                isset(
                                                                                    $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][
                                                                                        $agency_all_form_data->agency_id
                                                                                    ][$agency_all_form_data->patient_id][$agencyWise['fields']['id']]))
                                                                            {{ \Carbon\Carbon::parse($patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']])->format('m/d/Y') }}
                                                                        @elseif (
                                                                            $agencyWise['fields']['type'] === 'time' &&
                                                                                isset(
                                                                                    $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][
                                                                                        $agency_all_form_data->agency_id
                                                                                    ][$agency_all_form_data->patient_id][$agencyWise['fields']['id']]))
                                                                            {{ \Carbon\Carbon::parse($patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']])->format('h:i A') }}
                                                                        @elseif (
                                                                            $agencyWise['fields']['type'] === 'checkbox' &&
                                                                                isset(
                                                                                    $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][
                                                                                        $agency_all_form_data->agency_id
                                                                                    ][$agency_all_form_data->patient_id][$agencyWise['fields']['id']]))
                                                                            @php
                                                                                $serializedData =
                                                                                    $patientSubmitData[
                                                                                        $agency_all_form_data->id
                                                                                    ][$agency_all_form_data->form_id][
                                                                                        $agency_all_form_data->agency_id
                                                                                    ][
                                                                                        $agency_all_form_data
                                                                                            ->patient_id
                                                                                    ][$agencyWise['fields']['id']];
                                                                                if (
                                                                                    is_string($serializedData) &&
                                                                                    @unserialize($serializedData) !== false
                                                                                ) {
                                                                                    $values = unserialize(
                                                                                        $serializedData,
                                                                                    );
                                                                                } else {
                                                                                    $values = [];
                                                                                }
                                                                            @endphp
                                                                            @if (is_array($values))
                                                                                @foreach ($values as $value)
                                                                                    @if (!is_null($value) && $value !== '' && $value !== 'null')
                                                                                        <li>{{ $value }}
                                                                                        </li>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                        @else
                                                                            {{ $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']] ?? '' }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                                @if ($agency_all_form_data['mark_as_completed'] != '1')
                                                                    @if (in_array($agencyWise['fields']['type'], ['select', 'radio', 'checkbox']))
                                                                        @php
                                                                            $options = json_decode(
                                                                                $agencyWise['fields']['options'],
                                                                                true,
                                                                            );
                                                                        @endphp

                                                                        @if ($agencyWise['fields']['type'] == 'select')
                                                                            <select
                                                                                name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                                id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                                class="form-control">
                                                                                <option value="">Select an
                                                                                    option</option>
                                                                                @if (is_array($options))
                                                                                    @foreach ($options as $option)
                                                                                        <option
                                                                                            value="{{ $option }}"
                                                                                            {{ isset($patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']]) && $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']] == $option ? 'selected' : '' }}>
                                                                                            {{ $option }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        @elseif($agencyWise['fields']['type'] == 'radio')
                                                                            @if (is_array($options))
                                                                                <div class="form-check-container">
                                                                                    @foreach ($options as $option)
                                                                                        <div class="form-check">
                                                                                            <input type="radio"
                                                                                                name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                                                id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                                                class="form-check-input checkInput{{ $agency_all_form_data['id'] }}{{ $agency_all_form_data['form_id'] }} ml-1"
                                                                                                value="{{ $option }}"
                                                                                                @if (isset(
                                                                                                        $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][
                                                                                                            $agency_all_form_data->patient_id
                                                                                                        ][$agencyWise['fields']['id']]) &&
                                                                                                        $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][
                                                                                                            $agency_all_form_data->patient_id
                                                                                                        ][$agencyWise['fields']['id']] == $option) checked @endif>
                                                                                            <label
                                                                                                class="form-check-label checkInput{{ $agency_all_form_data['id'] }}{{ $agency_all_form_data['form_id'] }}"
                                                                                                for="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
                                                                                                {{ $option }}
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        @elseif($agencyWise['fields']['type'] == 'checkbox')
                                                                            @if (is_array($options))
                                                                                @php
                                                                                    $serializedData =
                                                                                        $patientSubmitData[
                                                                                            $agency_all_form_data->id
                                                                                        ][
                                                                                            $agency_all_form_data
                                                                                                ->form_id
                                                                                        ][
                                                                                            $agency_all_form_data
                                                                                                ->agency_id
                                                                                        ][
                                                                                            $agency_all_form_data
                                                                                                ->patient_id
                                                                                        ][
                                                                                            $agencyWise['fields']['id']
                                                                                        ] ?? '';

                                                                                    $existingValues =
                                                                                        is_string($serializedData) &&
                                                                                        @unserialize($serializedData) !== false
                                                                                            ? unserialize(
                                                                                                $serializedData,
                                                                                            )
                                                                                            : [];
                                                                                @endphp
                                                                                <div class="form-check-container">
                                                                                    @foreach ($options as $option)
                                                                                        <div class="form-check">
                                                                                            <input type="hidden"
                                                                                                name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                                                value="null">
                                                                                            <input type="checkbox"
                                                                                                name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                                                id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                                                class="form-check-input checkInput{{ $agency_all_form_data['id'] }}{{ $agency_all_form_data['form_id'] }} ml-1"
                                                                                                value="{{ $option }}"
                                                                                                @if (in_array($option, $existingValues)) checked @endif>
                                                                                            <label
                                                                                                class="form-check-label checkInput{{ $agency_all_form_data['id'] }}{{ $agency_all_form_data['form_id'] }}"
                                                                                                for="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
                                                                                                {{ $option }}
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    @elseif ($agencyWise['fields']['type'] == 'textarea')
                                                                        <textarea name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                            maxlength="{{ $agencyWise['fields']['set_character_limit'] ?? '' }}"
                                                                            id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                            class="form-control" style="height: 100px;">{{ $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']] ?? '' }}</textarea>
                                                                    @elseif ($agencyWise['fields']['type'] == 'information')
                                                                    @else
                                                                        <input
                                                                            type="{{ $agencyWise['fields']['type'] }}"
                                                                            name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                            id="input-field-{{ $agency_all_form_data['id'] }}-{{ $agency_all_form_data['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                            class="form-control"
                                                                            maxlength="{{ $agencyWise['fields']['set_character_limit'] ?? '' }}"
                                                                            value="{{ $patientSubmitData[$agency_all_form_data->id][$agency_all_form_data->form_id][$agency_all_form_data->agency_id][$agency_all_form_data->patient_id][$agencyWise['fields']['id']] ?? '' }}">
                                                                    @endif
                                                                @endif
                                                            </dd>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @else
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            @if ($agency_all_form_data['mark_as_completed'] != '1')
                                @can('agency-all-form-mark-as-completed')
                                    <input type="checkbox" name="mark_as_completed"
                                        class="mark_as_completed{{ $agency_all_form_data['id'] }}"
                                        id="mark_as_completed{{ $agency_all_form_data['id'] }}"
                                        style="margin-bottom: 12px;"
                                        value="1"{{ $agency_all_form_data['mark_as_completed'] == '1' ? 'checked' : '' }}>
                                    <label
                                        class="form-check-label ml-2 mr-4 mark_as_completed{{ $agency_all_form_data['id'] }}"
                                        for="mark_as_completed{{ $agency_all_form_data['id'] }}">
                                        <b>Mark as Completed</b>
                                    </label>
                                @endcan

                                <button type="button"
                                    class="btn btn-info btn-fw btn-sm pull-right save-form-btn{{ $agency_all_form_data->id }} ml-2"
                                    onclick="saveFormBtn('{{ $agency_all_form_data->id }}');"
                                    data-id="{{ $agency_all_form_data['id'] }}"
                                    data-fid="{{ $agency_all_form_data->form_id }}">Save</button>
                            @endif
                            @if (!empty($agency_all_form_data['templateById']['id']))
                                @if ($agency_all_form_data['mark_as_completed'] === '1')
                                    @can('agency-all-form-move-to-esign')
                                        <a class="btn btn-secondary btn-fw btn-sm ml-2 mr-2 moveToEsign{{ $agency_all_form_data->id }} addMoveToEsign"
                                            data-template-id="{{ $agency_all_form_data['templateById']['id'] ?? '' }}"
                                            data-id="{{ $agency_all_form_data->id }}" data-eid="{{ $record->id }}"
                                            data-eidc="{{ $record->patient_code }}"
                                            data-receipt-name="{{ $record->first_name . ' ' . $record->last_name }}"
                                            data-type="caregiver" title="Move To Esign">
                                            Move To Esign
                                        </a>
                                    @endcan
                                @endif
                                @can('agency-all-form-download')
                                    <i class="fa fa-download download-icon downloadIcon disabled-icon ml-2 formdownloadbtn{{ $agency_all_form_data->form_id }}"
                                        data-id="{{ $agency_all_form_data->id }}"
                                        data-form-id="{{ $agency_all_form_data->form_id }}"
                                        data-patient-id="{{ $record->id }}"
                                        data-agency-id="{{ $record->agency_id }}"
                                        data-template-id="{{ $agency_all_form_data['templateById']['id'] ?? '' }}"
                                        data-form-name="{{ $agency_all_form_data->forms['title'] }}"
                                        title="Download PDF"></i>
                                @endcan
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>

<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>

<!-- Plugin js for this page-->
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.world.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/peity/jquery.peity.min.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery.flot.dashes.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<!-- End plugin js for this page-->

<!-- inject:js -->
<script src="<?= URL::to('assets/js/off-canvas.js') ?>"></script>
<script src="<?= URL::to('assets/js/hoverable-collapse.js') ?>"></script>
<script src="<?= URL::to('assets/js/template.js') ?>"></script>
<script src="<?= URL::to('assets/js/settings.js') ?>"></script>
<script src="<?= URL::to('assets/js/todolist.js') ?>"></script>
<!-- endinject -->

<!-- plugin js for this page -->
<script src="<?= URL::to('assets/vendors/datatables.net/jquery.dataTables.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/data-table.js') ?>"></script>
<!-- plugin js for this page -->
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/dashboard.js') ?>"></script>
<!-- End custom js for this page-->

<script src="<?= URL::to('assets/js/sweetalert2.min.js') ?>"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<script src="{{ asset('assets/js/tribute.js') }}"></script>
<script src="{{ asset('assets/modulejs/form_report/form_report.js') }}?time={{ time() }}"></script>
<script src="{{ asset('js/jquery_new.min.js') }}"></script>

<script>
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var storePatientCustomData = "{{ url('store-patient-custom-data') }}";
    var storeData = "{{ route('store-agency-form') }}";
    var _FORM_REPORT_LIST = "{{ url('form-report-ajax-list') }}";
    var getTemplateData = "{{ route('get.templateData') }}";
    var storeMoveToEsignData = "{{ route('store-move-to-esign') }}";
</script>

<script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
