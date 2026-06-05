<style>
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

    /* .disabled-icon {
        pointer-events: none;
        opacity: 0.5;
    } */

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
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <p class="card-title mb-0">Agency All Form</p>
                <p class="mb-0 tx-13">
                    @can('agency-all-form-create')
                        <a class="btn btn-info btn-fw btn-sm addFormModal" href="javascript:void(0)">
                            <i class="mdi mdi-plus"></i>Add Form</a>
                    @endcan
                </p>
            </div>
            <div class="mt-4">
                <?php
                if (isset($_GET['debug']) && $_GET['debug'] == 1) {
                    echo '<pre>';
                    print_r($formList);
                }
                
                ?>
                <div class="accordion accordion-solid-header" id="accordion-4" role="tablist">
                    @if ($formList->isEmpty())
                        <div class="card border-bottom no-data-div">
                            <div class="card-body">
                                <p class="no-data">No Data Found</p>
                            </div>
                        </div>
                    @else
                        @foreach ($formList as $form)
                            <div class="card border-bottom agencyAllFormList" data-id="{{ $form->id }}"
                                data-f-id="{{ $form->form_id }}">
                                <input type="hidden" id="formName{{ $form->form_id }}"
                                    value="{{ ucfirst($form->forms->title ?? '') }}">
                                <div class="card-header" role="tab" id="heading-{{ $form->id }}">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" href="#collapse-{{ $form->id }}"
                                            aria-expanded="false" aria-controls="collapse-{{ $form->id }}"
                                            class="">{{ ucfirst($form->forms->title ?? '') }}</a>
                                    </h6>
                                </div>
                                <form id="dynamicAgencyForm_{{ $form['id'] }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="form_id_{{ $form->form_id }}" name="form_id"
                                        value="{{ $form->form_id }}">
                                    <input type="hidden" id="patient_i_{{ $form->form_id }}" name="patient_id"
                                        value="{{ $record->id }}">
                                    <input type="hidden" id="agency_id_{{ $form->form_id }}" name="agency_id"
                                        value="{{ $record->agency_id }}">
                                    <input type="hidden" id="doctor_id_{{ $form->form_id }}" name="doctor_id"
                                        value="{{ $form->doctors->id }}">

                                    <div id="collapse-{{ $form->id }}" class="collapse" role="tabpanel"
                                        aria-labelledby="heading-{{ $form->id }}" data-parent="#accordion-4">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12" id="">
                                                    @can('agency-all-form-edit')
                                                    @if (($form['mark_as_completed'] != '1'))
                                                        <a class="btn btn-info btn-fw btn-sm pull-right edit-form-btn{{ $form->id }}"
                                                            onclick="editFormBtn('{{ $form->id }}')"
                                                            href="javascript:void(0)" data-id={{ $form->id }}
                                                            data-fid="{{ $form->form_id }}"
                                                            data-aid="{{ $record->agency_id }}"
                                                            data-pid="{{ $record->id }}">Edit</a>
                                                    @endif
                                                    @endcan
                                                    <button type="button"
                                                        class="btn btn-info btn-fw btn-sm pull-right save-form-btn{{ $form->id }} ml-2"
                                                        onclick="saveFormBtn('{{ $form->id }}')"
                                                        data-id="{{ $form['id'] }}" data-fid="{{ $form->form_id }}"
                                                        style="display:none;">Save</button>
                                                    <a class="btn btn-secondary btn-fw btn-sm pull-right cancel-form-btn{{ $form->id }}"
                                                        onclick="cancleFormBtn('{{ $form->id }}')"
                                                        href="javascript:void(0)" style="display:none;">Cancel</a>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="field-container">
                                                        <div class="label-edit-container">
                                                            <dt>Doctor Name</dt>
                                                        </div>
                                                        <dd>
                                                            <span
                                                                id="dynamic-field-{{ $form['id'] }}-{{ $form['form_id'] }}-doctor_name"
                                                                class="flex-grow-1 dynamic-form-value-{{ $form['id'] }}-{{ $form['form_id'] }}">
                                                                {{ $form->doctors->full_name }}
                                                            </span>
                                                            <?php
                                                            $oldDoctorId = $form->doctor_id;
                                                            ?>
                                                            <input type="hidden" name="formId" id="formId"
                                                                value="{{ $form->id }}">
                                                            <select name="doctor_id"
                                                                id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-doctor_name"
                                                                class="form-control" style="display:none;">
                                                                @foreach ($doctorList as $doctor)
                                                                    <option value="{{ $doctor->id }}"
                                                                        {{ $doctor->id == $oldDoctorId ? 'selected' : '' }}>
                                                                        {{ ucfirst($doctor->full_name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </dd>
                                                    </div>
                                                </div>

                                                @if (count($form->agencyMaster) > 0)
                                                    @foreach ($form->agencyMaster as $agencyWise)
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
                                                                        <span
                                                                            id="dynamic-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                            class="flex-grow-1 dynamic-form-value-{{ $form['id'] }}-{{ $form['form_id'] }}">

                                                                            @if (
                                                                                $agencyWise['fields']['type'] === 'date' &&
                                                                                    isset(
                                                                                        $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][
                                                                                            $agencyWise['fields']['id']
                                                                                        ]))
                                                                                {{ \Carbon\Carbon::parse($patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']])->format('m/d/Y') }}
                                                                            @elseif (
                                                                                $agencyWise['fields']['type'] === 'time' &&
                                                                                    isset(
                                                                                        $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][
                                                                                            $agencyWise['fields']['id']
                                                                                        ]))
                                                                                {{ \Carbon\Carbon::parse($patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']])->format('h:i A') }}
                                                                            @elseif (
                                                                                $agencyWise['fields']['type'] === 'checkbox' &&
                                                                                    isset(
                                                                                        $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][
                                                                                            $agencyWise['fields']['id']
                                                                                        ]))
                                                                                @php
                                                                                    $serializedData =
                                                                                        $patientSubmitData[$form->id][
                                                                                            $form->form_id
                                                                                        ][$form->agency_id][
                                                                                            $form->patient_id
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
                                                                                {{ $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']] ?? '' }}
                                                                            @endif
                                                                        </span>
                                                                        @if (in_array($agencyWise['fields']['type'], ['select', 'radio', 'checkbox']))
                                                                            @php
                                                                                $options = json_decode(
                                                                                    $agencyWise['fields']['options'],
                                                                                    true,
                                                                                );
                                                                            @endphp

                                                                            @if ($agencyWise['fields']['type'] == 'select')
                                                                                <select style="display:none;"
                                                                                    name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                                    id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                                    class="form-control">
                                                                                    <option value="">Select an
                                                                                        option</option>
                                                                                    @if (is_array($options))
                                                                                        @foreach ($options as $option)
                                                                                            <option
                                                                                                value="{{ $option }}"
                                                                                                {{ isset($patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']]) && $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']] == $option ? 'selected' : '' }}>
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
                                                                                                    style="display:none;"
                                                                                                    name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                                                    id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                                                    class="form-check-input checkInput{{ $form['id'] }}{{ $form['form_id'] }} ml-1"
                                                                                                    value="{{ $option }}"
                                                                                                    @if (isset(
                                                                                                            $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']]) &&
                                                                                                            $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][
                                                                                                                $agencyWise['fields']['id']
                                                                                                            ] == $option) checked @endif>
                                                                                                <label
                                                                                                    class="form-check-label checkInput{{ $form['id'] }}{{ $form['form_id'] }}"
                                                                                                    style="display:none;"
                                                                                                    for="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
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
                                                                                                $form->id
                                                                                            ][$form->form_id][
                                                                                                $form->agency_id
                                                                                            ][$form->patient_id][
                                                                                                $agencyWise['fields'][
                                                                                                    'id'
                                                                                                ]
                                                                                            ] ?? '';

                                                                                        $existingValues =
                                                                                            is_string(
                                                                                                $serializedData,
                                                                                            ) &&
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
                                                                                                    style="display:none;"
                                                                                                    name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                                                    id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                                                    class="form-check-input checkInput{{ $form['id'] }}{{ $form['form_id'] }} ml-1"
                                                                                                    value="{{ $option }}"
                                                                                                    @if (in_array($option, $existingValues)) checked @endif>
                                                                                                <label
                                                                                                    class="form-check-label checkInput{{ $form['id'] }}{{ $form['form_id'] }}"
                                                                                                    style="display:none;"
                                                                                                    for="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
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
                                                                                id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                                class="form-control" style="display:none; height: 100px;">{{ $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']] ?? '' }}</textarea>
                                                                        @else
                                                                            <input
                                                                                type="{{ $agencyWise['fields']['type'] }}"
                                                                                name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                                id="input-field-{{ $form['id'] }}-{{ $form['form_id'] }}-{{ $agencyWise['fields']['id'] }}"
                                                                                class="form-control"
                                                                                style="display:none;"
                                                                                maxlength="{{ $agencyWise['fields']['set_character_limit'] ?? '' }}"
                                                                                value="{{ $patientSubmitData[$form->id][$form->form_id][$form->agency_id][$form->patient_id][$agencyWise['fields']['id']] ?? '' }}">
                                                                        @endif
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{-- <div class="col-12">
                                                        <p class="no-data">No Data Found</p>
                                                    </div> --}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <input type="checkbox"
                                            name="mark_as_completed"
                                            class="mark_as_completed{{ $form['id'] }}"
                                            id="mark_as_completed{{ $form['id'] }}"
                                            style="display:none; margin-bottom: 12px;"
                                            value="1"{{ $form['mark_as_completed']=="1"? 'checked':'' }}>
                                            <label class="form-check-label ml-2 mr-4 mark_as_completed{{ $form['id'] }}" for="mark_as_completed{{ $form['id'] }}" style="display:none;">
                                                    <b>Mark as Completed</b>
                                            </label>
                                            @can('agency-all-form-edit')
                                            @if (($form['mark_as_completed'] != '1'))
                                                <a class="btn btn-info btn-fw btn-sm pull-right edit-form-btn{{ $form->id }}"
                                                    onclick="editFormBtn('{{ $form->id }}')"
                                                    href="javascript:void(0)" data-id={{ $form->id }}
                                                    data-fid="{{ $form->form_id }}"
                                                    data-aid="{{ $record->agency_id }}"
                                                    data-pid="{{ $record->id }}">Edit</a>
                                            @endif
                                            @endcan

                                            <a class="btn btn-secondary btn-fw btn-sm pull-right cancel-form-btn{{ $form->id }}"
                                                onclick="cancleFormBtn('{{ $form->id }}')"
                                                href="javascript:void(0)" style="display:none;">Cancel</a>

                                            <button type="button"
                                                class="btn btn-info btn-fw btn-sm pull-right save-form-btn{{ $form->id }} ml-2"
                                                onclick="saveFormBtn('{{ $form->id }}')"
                                                data-id="{{ $form['id'] }}" data-fid="{{ $form->form_id }}"
                                                style="display:none;">Save</button>

                                            @if (!empty($form['templateById']['id']))
                                            @if (($form['mark_as_completed'] === '1'))
                                                <a class="btn btn-secondary btn-fw btn-sm ml-2 mr-2 moveToEsign{{ $form->id }} addMoveToEsign"
                                                    data-template-id="{{ $form['templateById']['id'] ?? '' }}"
                                                    data-id="{{ $form->id }}" data-eid="{{ $record->id }}"
                                                    data-eidc="{{ $record->patient_code }}"
                                                    data-receipt-name="{{ $record->first_name . ' ' . $record->last_name }}"
                                                    data-type="caregiver" title="Move To Esign">
                                                    Move To Esign
                                                </a>
                                            @endif
                                                @can('agency-all-form-download')
                                                    <i class="fa fa-download download-icon downloadIcon disabled-icon ml-2 formdownloadbtn{{ $form->form_id }}"
                                                        data-id="{{ $form->id }}"
                                                        data-form-id="{{ $form->form_id }}"
                                                        data-patient-id="{{ $record->id }}"
                                                        data-agency-id="{{ $record->agency_id }}"
                                                        data-template-id="{{ $form['templateById']['id'] ?? '' }}"
                                                        title="All fields are required"></i>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('patient._partial.patient.agency_form_modal')

<script>
    var getTemplateData = "{{ route('get.templateData') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var storeData = "{{ route('store-agency-form') }}";
    var storeMoveToEsignData = "{{ route('store-move-to-esign') }}";
    var agencyFormDownloadPermission = @json(auth()->user()->can('agency-all-form-download'));
</script>
<script src="{{ asset('assets/modulejs/patient_custom_data.js') }}?time={{ time() }}"></script>
