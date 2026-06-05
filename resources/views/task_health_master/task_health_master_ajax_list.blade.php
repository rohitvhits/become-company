<div class="">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:5%">Patient Id</th>
                <th style="width:7%">TH Patient ID</th>
                <th style="width:5%">Task ID</th>
                <th style="width:9%">Agency Name</th>
                <th style="width:0%">Patient Name</th>
                <th style="width:6%">Type<br/>Gender</th>
                <th style="width:6%">DOB</th>
                <th style="width:7%">Mobile<br/>Phone</th>
                <th style="width:8%">Critical Alert</th>
                <th style="width:8%">Created Date</th>
                <th style="width:5%">Flags</th>
                <th style="width:5%">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            @php
                $isDeleted = isset($row->patientDetails) && $row->patientDetails->deleted_flag === 'Y';
                $isResend = empty($row->old_patient_id) ? '' : 'Y';
                $rowClass  = ($isDeleted) ? 'table-danger' : '';
                $flags = $row->flags;

                // Parse critical alert
                $ca = null;
                if ($row->latestCriticalAlert && $row->latestCriticalAlert->critical_alerts) {
                    $raw = @unserialize($row->latestCriticalAlert->critical_alerts);
                    if ($raw === false) {
                        $raw = json_decode($row->latestCriticalAlert->critical_alerts, true);
                    }
                    $ca = is_array($raw) ? $raw : null;
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ $i++}}</td>
                <td>
                    <a href="{{url('patient/view/'.$row->patient_id)}}" target="_blank" title="View Patient">{{ $row->patient_id }}</a>
                    @if($isResend)
                        <span class="badge badge-info" title="Red Flag">Converted</span>
                    @endif
                </td>
                <td>{{ $row->task_health_patient_id ?: '—' }}</td>
                <td><a href="javascript:void(0)"
                           onclick="openVisitModal('{{ $row->task_id }}')"
                           data-toggle="tooltip" title="View Visit Detail"
                           style="color:#007bff;font-weight:600;">
                            #{{ $row->task_id }}
                        </a>
                </td>
                <td>{{ $row->agencyDetails->agency_name??''}}</td>
                <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                <td>{{ $row->type}}<br/>{{$row->gender}}</td>
                <td>{{ $row->dob ? date('m/d/Y',strtotime($row->dob)) : ''}}</td>
                <td>{{ $row->mobile}}<br/>{{ $row->phone}}</td>
                {{-- Critical Alert --}}
                <td>
                    @if(is_null($ca))
                        <span class="" title="No alert data">—</span>
                    @elseif(!($ca['alert'] ?? false))
                        <span class="label label-success" title="No critical findings">Clear</span>
                    @else
                        @php
                            $findings  = $ca['findings'] ?? [];
                            $summary   = $ca['summary']  ?? '';
                            $popLines  = [];
                            if ($summary)        { $popLines[] = '<p style="margin:0 0 6px;">' . e($summary) . '</p>'; }
                            if (count($findings)) {
                                $popLines[] = '<ul style="margin:0;padding-left:16px;">';
                                foreach ($findings as $f) { $popLines[] = '<li>' . e($f) . '</li>'; }
                                $popLines[] = '</ul>';
                            }
                            $popoverHtml = implode('', $popLines) ?: '—';
                        @endphp
                        <span class="label label-danger th-critical-alert-badge"
                              style="cursor:pointer;"
                              tabindex="0"
                              data-toggle="popover"
                              data-trigger="focus"
                              data-placement="left"
                              data-html="true"
                              title="&lt;strong&gt;Critical Findings&lt;/strong&gt;"
                              data-content="{{ $popoverHtml }}">
                            &#9888; Critical
                        </span>
                    @endif
                </td>

                <td>{{ date('m/d/Y h:i A',strtotime($row->created_date))}}</td>

                {{-- Flags --}}
                <td>
                    @include('_partial.task_health_flags.flag_cell', [
                        'flags'            => $flags,
                        'flagName'         => $row->first_name . ' ' . $row->last_name,
                        'flagMasterId'     => $row->id,
                        'flagThPatientId'  => $row->task_health_patient_id,
                        'flagPatientId'    => $row->patient_id,
                        'flagTaskId'       => $row->task_id,
                    ])
                </td>

                {{-- Action --}}
                <td>
                    @if($isDeleted)
                        @php
                            $btnName       = addslashes($row->first_name . ' ' . $row->last_name);
                            $btnDob        = $row->dob ? date('m/d/Y', strtotime($row->dob)) : '';
                            $btnMobile     = addslashes($row->mobile ?? '');
                            $btnPhone      = addslashes($row->phone ?? '');
                            $btnAgencyName = addslashes($row->agencyDetails->agency_name ?? '');
                            $btnType       = addslashes($row->type ?? '');
                        @endphp
                        <button type="button"
                            class="btn btn-sm btn-danger mb-1"
                            onclick="openRevertModal({{ $row->id }}, {{ $row->agency_id ?? 0 }}, '{{ $btnName }}', '{{ $btnDob }}', '{{ $btnMobile }}', '{{ $btnPhone }}', '{{ $btnAgencyName }}', '{{ $btnType }}')"
                            title="Resend - Patient Deleted">
                            Resend
                        </button>
                    @else
                        —
                    @endif
                    @if($row->agency_id == '2' && $row->is_converted == 0)
                    <button type="button" data-url="{{$row->is_converted}}"
                        class="btn btn-sm btn-primary mb-1"
                        onclick="openConvertTaskHealthModal({{ $row->id }})"
                        title="Convert">
                        Convert
                    </button>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="13">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    $('.shimmer_id').hide();
    $('#blank_div').css('margin-top', '30px');
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
</script>
