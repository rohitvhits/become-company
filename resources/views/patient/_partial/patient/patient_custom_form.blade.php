<style>
    .attribute-section .field-container {
        margin-bottom: 0;
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .attribute-section .field-container:last-child {
        border-bottom: none;
    }

    .attribute-section .label-edit-container {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .attribute-section .label-edit-container dt {
        margin-right: 5px;
        font-weight: 600;
        color: #6c757d;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .attribute-section .field-value {
        font-size: 14px;
        color: #212529;
        font-weight: 500;
        min-height: 20px;
    }

    .attribute-section .field-value:empty::before {
        content: '-';
        color: #adb5bd;
    }

    .attribute-section .no-data {
        text-align: center;
        color: #6c757d;
        font-weight: 500;
        padding: 40px 20px;
    }

    .attribute-section .hidden-fields {
        display: none;
    }

    .attribute-section .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .attribute-section .form-check-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .attribute-section .form-check {
        display: flex;
        align-items: center;
    }

    .attribute-section .form-check-label {
        margin-left: 5px;
        margin-bottom: 0;
    }

    .attribute-section .checkbox-value-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .attribute-section .checkbox-value-list li {
        display: inline-block;
        background: #e9ecef;
        padding: 2px 10px;
        border-radius: 12px;
        margin: 2px 4px 2px 0;
        font-size: 13px;
    }

    .attribute-section .attribute-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }

    .attribute-section .attribute-grid {
        display: flex;
        flex-wrap: wrap;
    }

    .attribute-section .attribute-item {
        width: 50%;
        border-bottom: 1px solid #e9ecef;
        border-right: 1px solid #e9ecef;
        box-sizing: border-box;
    }

    .attribute-section .attribute-item:nth-child(2n) {
        border-right: none;
    }

    .attribute-section .attribute-item:last-child,
    .attribute-section .attribute-item:nth-last-child(2):nth-child(odd) {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .attribute-section .attribute-item {
            width: 100%;
            border-right: none;
        }

        .attribute-section .attribute-item:not(:last-child) {
            border-bottom: 1px solid #e9ecef;
        }

        .attribute-section .attribute-item:last-child {
            border-bottom: none;
        }
    }
</style>

@php
    $agencyWiseFieldData1 = [];
    $agencyWiseFieldArray1 = $agencyWiseFieldWithoutFormId->toArray();
    if (count($agencyWiseFieldArray1) > 0) {
        $agencyWiseFieldData1 = $agencyWiseFieldArray1;
    }
@endphp

<div class="attribute-section">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Attribute</p>
        <div class="action-buttons">
            @if (count($agencyWiseFieldData1) > 0)
                <a class="btn btn-secondary btn-sm cancel-advance-form-btn hidden-fields"
                    href="javascript:void(0)" style="display:none;">Cancel</a>
                <button type="submit" form="dynamicAgencyForm"
                    class="btn btn-primary btn-sm save-advance-form-btn hidden-fields"
                    style="display:none;">Save</button>
                <a class="btn btn-info btn-sm edit-advance-form-btn" href="javascript:void(0)"
                    data-aid="{{ $record->agency_id }}" data-pid="{{ $record->id }}">
                    <i class="mdi mdi-pencil"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <form id="dynamicAgencyForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="patient_id" name="patient_id" value="{{ $record->id }}">
        <input type="hidden" id="agency_id" name="agency_id" value="{{ $record->agency_id }}">

        @if (count($agencyWiseFieldData1) > 0)
            <div class="attribute-card">
                <div class="attribute-grid">
                    @foreach ($agencyWiseFieldData1 as $agencyWise)
                        @if (isset($agencyWise['fields']) && !empty($agencyWise['fields']))
                            <div class="attribute-item">
                                <div class="field-container">
                                    <div class="label-edit-container">
                                        <dt>{{ ucfirst($agencyWise['fields']['label']) }}</dt>
                                    </div>
                                    <dd class="mb-0">
                                        <span id="dynamic-field-{{ $agencyWise['fields']['id'] }}"
                                            class="field-value dynamic-form-value">
                                            @if ($agencyWise['fields']['type'] === 'date' &&
                                                    isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']])->format('m/d/Y') }}
                                            @elseif ($agencyWise['fields']['type'] === 'time' &&
                                                    isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']])->format('h:i A') }}
                                            @elseif ($agencyWise['fields']['type'] === 'checkbox' &&
                                                    isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]))
                                                @php
                                                    $serializedData = $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']];
                                                    if (is_string($serializedData) && @unserialize($serializedData) !== false) {
                                                        $values = unserialize($serializedData);
                                                    } else {
                                                        $values = [];
                                                    }
                                                @endphp
                                                @if (is_array($values))
                                                    <ul class="checkbox-value-list">
                                                        @foreach ($values as $value)
                                                            @if (!is_null($value) && $value !== '' && $value !== 'null')
                                                                <li>{{ $value }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
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
                                                    <div class="form-check-container hidden-fields" style="display:none;">
                                                        @foreach ($options as $option)
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                    name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                                    id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                    class="form-check-input"
                                                                    value="{{ $option }}"
                                                                    @if (isset($patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']]) &&
                                                                            $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] == $option) checked @endif>
                                                                <label class="form-check-label"
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
                                                        $serializedData = $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '';
                                                        $existingValues = is_string($serializedData) && @unserialize($serializedData) !== false
                                                            ? unserialize($serializedData)
                                                            : [];
                                                    @endphp
                                                    <div class="form-check-container hidden-fields" style="display:none;">
                                                        @foreach ($options as $option)
                                                            <div class="form-check">
                                                                <input type="hidden"
                                                                    name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                    value="null">
                                                                <input type="checkbox"
                                                                    name="fields[{{ $agencyWise['fields']['id'] }}][]"
                                                                    id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}-{{ $loop->index }}"
                                                                    class="form-check-input"
                                                                    value="{{ $option }}"
                                                                    @if (in_array($option, $existingValues)) checked @endif>
                                                                <label class="form-check-label"
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
                                                id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}"
                                                class="form-control hidden-fields"
                                                style="height: 100px; display:none;">{{ $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '' }}</textarea>
                                        @elseif ($agencyWise['fields']['type'] == 'information')
                                        @else
                                            <input type="{{ $agencyWise['fields']['type'] }}"
                                                name="fields[{{ $agencyWise['fields']['id'] }}]"
                                                id="advance-input-field-{{ $record->id }}-{{ $agencyWise['fields']['id'] }}"
                                                class="form-control hidden-fields"
                                                maxlength="{{ $agencyWise['fields']['set_character_limit'] ?? '' }}"
                                                value="{{ $patientAdvanceSubmitData[$agencyWise['agency_id']][$agencyWise['fields']['id']] ?? '' }}"
                                                style="display:none;">
                                        @endif
                                    </dd>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="attribute-card">
                <p class="no-data">No Data Found</p>
            </div>
        @endif
    </form>
</div>

<script>
    var _CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('assets/modulejs/patient_advance_form.js') }}?time={{ time() }}"></script>
