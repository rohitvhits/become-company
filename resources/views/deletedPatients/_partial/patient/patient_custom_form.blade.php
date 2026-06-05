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

    .hidden-fields {
        display: none;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Attribute</p>
    @php
        $agencyWiseFieldData1 = [];
        $agencyWiseFieldArray1 = $agencyWiseFieldWithoutFormId->toArray();
        if (count($agencyWiseFieldArray1) > 0) {
            $agencyWiseFieldData1 = array_chunk($agencyWiseFieldArray1, (int) ceil(count($agencyWiseFieldArray1) / 3));
        }
    @endphp
</div>
<div class="container">
    <form id="dynamicAgencyForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12">
                <input type="hidden" id="patient_id" name="patient_id" value="{{ $record->id }}">
                <input type="hidden" id="agency_id" name="agency_id" value="{{ $record->agency_id }}">
                @if (count($agencyWiseFieldData1) > 0)
                    <a class="btn btn-info btn-fw btn-sm pull-right edit-advance-form-btn" href="javascript:void(0)"
                        data-aid="{{ $record->agency_id }}" data-pid="{{ $record->id }}">Edit</a>
                @endif
                <button type="submit"
                    class="btn btn-info btn-fw btn-sm pull-right save-advance-form-btn ml-2 hidden-fields"
                    style="display:none;">Save</button>
                <a class="btn btn-secondary btn-fw btn-sm pull-right cancel-advance-form-btn mb-3 hidden-fields"
                    href="javascript:void(0)" style="display:none;">Cancel</a>
            </div>
            @if (count($agencyWiseFieldData1) > 0)
                @foreach ($agencyWiseFieldData1 as $agencyWiseField)
                    @foreach ($agencyWiseField as $agencyWise)
                        @if (isset($agencyWise['fields']) && !empty($agencyWise['fields']))
                            @php
                                $colSize = 'col-md-12';
                                if (isset($agencyWise['fields']['size']) && $agencyWise['fields']['size'] == 'half') {
                                    $colSize = 'col-md-6';
                                }
                            @endphp
                            <div class="{{ $colSize }}">
                                <div class="box">
                                    <div class="field-container">
                                        <div class="label-edit-container">
                                            <dt>{{ ucfirst($agencyWise['fields']['label']) }}</dt>
                                        </div>
                                        <dd>
                                            <span id="dynamic-field-{{ $agencyWise['fields']['id'] }}"
                                                class="flex-grow-1 dynamic-form-value">
                                                @if (
                                                    $agencyWise['fields']['type'] === 'date' &&
                                                        isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                    {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']])->format('m/d/Y') }}
                                                @elseif (
                                                    $agencyWise['fields']['type'] === 'time' &&
                                                        isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                    {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']])->format('h:i A') }}
                                                @elseif (
                                                    $agencyWise['fields']['type'] === 'checkbox' &&
                                                        isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                    @php
                                                        $serializedData =
                                                            $patientAdvanceSubmitData[$agencyWise['agency_id']][
                                                                $agencyWise['fields']['id']
                                                            ];
                                                        if (is_string($serializedData) && @unserialize($serializedData) !== false) {
                                                            $values = unserialize($serializedData);
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
                                                    {{ $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '' }}
                                                @endif
                                            </span>
                                            @if (in_array($agencyWise['fields']['type'], ['select', 'radio', 'checkbox']))
                                                @php
                                                    $options = json_decode($agencyWise['fields']['options'], true);
                                                @endphp

                                                @if ($agencyWise['fields']['type'] == 'select')
                                                    <select style="display:none;"
                                                        name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                        id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}"
                                                        class="form-control hidden-fields">
                                                        <option value="">Select an option</option>
                                                        @if (is_array($options))
                                                            @foreach ($options as $option)
                                                                <option value="{{ $option }}"
                                                                    {{ isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]) && $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] == $option ? 'selected' : '' }}>
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
                                                                    <input type="radio" style="display:none;"
                                                                        name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                        id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                        class="form-check-input checkInput ml-1 hidden-fields"
                                                                        value="{{ $option }}"
                                                                        @if (isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]) &&
                                                                                $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] == $option) checked @endif>
                                                                    <label
                                                                        class="form-check-label checkInput hidden-fields"
                                                                        style="display:none;"
                                                                        for="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
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
                                                                $patientAdvanceSubmitData[$agencyWise['agency_id']][
                                                                    $agencyWise['fields']['id']
                                                                ] ?? '';

                                                            $existingValues =
                                                                is_string($serializedData) &&
                                                                @unserialize($serializedData) !== false
                                                                    ? unserialize($serializedData)
                                                                    : [];
                                                        @endphp
                                                        <div class="form-check-container">
                                                            @foreach ($options as $option)
                                                                <div class="form-check">
                                                                    <input type="hidden"
                                                                        name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                        value="null">
                                                                    <input type="checkbox" style="display:none;"
                                                                        name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                        id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                        class="form-check-input checkInput ml-1 hidden-fields"
                                                                        value="{{ $option }}"
                                                                        @if (in_array($option, $existingValues)) checked @endif>

                                                                    <label
                                                                        class="form-check-label checkInput hidden-fields"
                                                                        style="display:none;"
                                                                        for="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}">
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
                                                    id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}" class="form-control hidden-fields"
                                                    style="height: 100px;">{{ $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '' }}</textarea>
                                            @elseif ($agencyWise['fields']['type'] == 'information')
                                            @else
                                                <input type="{{ $agencyWise['fields']['type'] }}"
                                                    name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                    id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}"
                                                    class="form-control hidden-fields"
                                                    maxlength="{{ $agencyWise['fields']['set_character_limit'] ?? '' }}"
                                                    value="{{ $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '' }}">
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @else
                <div class="col-12">
                    <p class="no-data">No Data Found</p>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    var _CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('assets/modulejs/patient_advance_form.js') }}?time={{ time() }}"></script>
