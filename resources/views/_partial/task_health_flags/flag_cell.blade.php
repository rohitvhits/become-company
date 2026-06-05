@php
    $pocUser  = $flags?->pocCheckedByUser;
    $mdoUser  = $flags?->mdoCheckedByUser;
    $alrtUser = $flags?->alertCheckedByUser;
    $supvUser = $flags?->supervisionCheckedByUser;
    $assmUser = $flags?->assessmentCheckedByUser;
    $kdxUser  = $flags?->kardexCheckedByUser;
    $pkgUser  = $flags?->patientPackageDocCheckedByUser;
    $updUser  = $flags?->updatedByUser;
    $fd = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/y h:i A') : '—';
    $fn = fn($u) => $u ? ($u->first_name . ' ' . substr($u->last_name, 0, 1) . '.') : '—';

    $pocTitle  = $flags?->poc_check        ? 'POC: Checked by '        . $fn($pocUser)  . ' · ' . $fd($flags?->poc_check_date)        : 'POC: Not checked';
    $mdoTitle  = $flags?->mdo_check        ? 'MDO: Checked by '        . $fn($mdoUser)  . ' · ' . $fd($flags?->mdo_check_date)        : 'MDO: Not checked';
    $alrtTitle = $flags?->alert_check      ? 'Alert: Checked by '      . $fn($alrtUser) . ' · ' . $fd($flags?->alert_check_date)      : 'Alert: Not checked';
    $supvTitle = $flags?->supervision_check ? 'Supervision: Checked by ' . $fn($supvUser) . ' · ' . $fd($flags?->supervision_check_date) : 'Supervision: Not checked';
    $assmTitle = $flags?->assessment_check          ? 'Assessment: Checked by '          . $fn($assmUser) . ' · ' . $fd($flags?->assessment_check_date)          : 'Assessment: Not checked';
    $kdxTitle  = $flags?->kardex_check              ? 'Kardex: Checked by '              . $fn($kdxUser)  . ' · ' . $fd($flags?->kardex_check_date)              : 'Kardex: Not checked';
    $pkgTitle  = $flags?->patient_package_doc_check ? 'Patient Package Doc: Checked by ' . $fn($pkgUser)  . ' · ' . $fd($flags?->patient_package_doc_check_date) : 'Patient Package Doc: Not checked';

    $infoJson = json_encode([
        'name'            => $flagName ?? '',
        'poc'             => $flags?->poc_check        ? 1 : 0,
        'poc_by'          => $fn($pocUser),
        'poc_date'        => $fd($flags?->poc_check_date),
        'mdo'             => $flags?->mdo_check        ? 1 : 0,
        'mdo_by'          => $fn($mdoUser),
        'mdo_date'        => $fd($flags?->mdo_check_date),
        'alert'           => $flags?->alert_check      ? 1 : 0,
        'alert_by'        => $fn($alrtUser),
        'alert_date'      => $fd($flags?->alert_check_date),
        'supervision'     => $flags?->supervision_check ? 1 : 0,
        'supervision_by'  => $fn($supvUser),
        'supervision_date'=> $fd($flags?->supervision_check_date),
        'assessment'      => $flags?->assessment_check  ? 1 : 0,
        'assessment_by'   => $fn($assmUser),
        'assessment_date' => $fd($flags?->assessment_check_date),
        'kardex'                  => $flags?->kardex_check              ? 1 : 0,
        'kardex_by'               => $fn($kdxUser),
        'kardex_date'             => $fd($flags?->kardex_check_date),
        'patient_package_doc'     => $flags?->patient_package_doc_check ? 1 : 0,
        'patient_package_doc_by'  => $fn($pkgUser),
        'patient_package_doc_date'=> $fd($flags?->patient_package_doc_check_date),
        'upd_by'                  => $fn($updUser),
        'upd_at'          => $fd($flags?->updated_at),
    ]);
@endphp

{{-- Read-only visual checkboxes --}}
<div class="thf-ro-wrap">
    <label class="thf-ro-label {{ $flags?->poc_check        ? 'is-checked' : '' }}" data-flag="poc"        title="{{ $pocTitle }}">
        <span class="thf-ro-cb"></span> POC
    </label>
    <label class="thf-ro-label {{ $flags?->mdo_check        ? 'is-checked' : '' }}" data-flag="mdo"        title="{{ $mdoTitle }}">
        <span class="thf-ro-cb"></span> MDO
    </label>
    <label class="thf-ro-label {{ $flags?->alert_check      ? 'is-checked' : '' }}" data-flag="alert"      title="{{ $alrtTitle }}">
        <span class="thf-ro-cb"></span> Alert
    </label>
    <label class="thf-ro-label {{ $flags?->supervision_check ? 'is-checked' : '' }}" data-flag="supervision" title="{{ $supvTitle }}">
        <span class="thf-ro-cb"></span> Supv
    </label>
    <label class="thf-ro-label {{ $flags?->assessment_check  ? 'is-checked' : '' }}" data-flag="assessment"  title="{{ $assmTitle }}">
        <span class="thf-ro-cb"></span> Asmt
    </label>
    <label class="thf-ro-label {{ $flags?->kardex_check              ? 'is-checked' : '' }}" data-flag="kardex"               title="{{ $kdxTitle }}">
        <span class="thf-ro-cb"></span> Kdx
    </label>
    <label class="thf-ro-label {{ $flags?->patient_package_doc_check ? 'is-checked' : '' }}" data-flag="patient_package_doc" title="{{ $pkgTitle }}">
        <span class="thf-ro-cb"></span> Pkg
    </label>
</div>

{{-- Single Manage Flags button --}}
<button type="button" onclick="showSendPOCButton('{{ $flags?->poc_check ? 1 : 0 }}')"
    class="thf-manage-btn flag-on thf-open-flag"
    data-name="{{ $flagName ?? '' }}"
    data-poc="{{ $flags?->poc_check        ? 1 : 0 }}"
    data-mdo="{{ $flags?->mdo_check        ? 1 : 0 }}"
    data-alert="{{ $flags?->alert_check      ? 1 : 0 }}"
    data-supervision="{{ $flags?->supervision_check ? 1 : 0 }}"
    data-assessment="{{ $flags?->assessment_check  ? 1 : 0 }}"
    data-kardex="{{ $flags?->kardex_check              ? 1 : 0 }}"
    data-patient-package-doc="{{ $flags?->patient_package_doc_check ? 1 : 0 }}"
    data-info="{{ $infoJson }}"
    @isset($flagMasterId)    data-master-id="{{ $flagMasterId }}"       @endisset
    @isset($flagThPatientId) data-th-patient-id="{{ $flagThPatientId }}" @endisset
    @isset($flagPatientId)   data-patient-id="{{ $flagPatientId }}"     @endisset
    @isset($flagTaskId)      data-task-id="{{ $flagTaskId }}"           @endisset>
    <i class="mdi mdi-flag-checkered"></i> Action
</button>
<script>
    function showSendPOCButton(poc){
        $('#hide_show_send_poc').addClass('hide');
        if(poc ==0){
            $('#hide_show_send_poc').removeClass('hide');
        }
    }
</script>
