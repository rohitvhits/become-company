@php
    $totalLabel = $isArchived ? 'archived file(s)' : 'file(s)';
@endphp

<div id="afm-summary" data-total="{{ $files->total() }}" data-label="{{ $totalLabel }}"></div>

@if($files->isEmpty())
    <div class="afm-empty">
        <i class="fa {{ $isArchived ? 'fa-archive' : 'fa-folder-open-o' }}"></i>
        {{ $isArchived ? 'No archived files found.' : 'No files found.' }}
    </div>
@else
<table class="afm-table">
    <thead>
        <tr>
            <th class="cb-col">
                <input type="checkbox" class="afm-cb" id="selectAllCb" title="Select all">
            </th>
            <th style="width:28px;"></th>
            <th>File Name</th>
            <th>Agency</th>
            <th>Path</th>
            <th>Type</th>
            <th>Size</th>
            <th>Uploaded By</th>
            <th>{{ $isArchived ? 'Archived Date' : 'Upload Date' }}</th>
            <th>Linked Chart <br> Status / Resolution</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $file)
        @php
            $ext = strtolower($file->file_type ?? '');
            $iconMap = [
                'pdf'  => ['fa-file-pdf-o',     'pdf'],
                'jpg'  => ['fa-file-image-o',   'image'],
                'jpeg' => ['fa-file-image-o',   'image'],
                'png'  => ['fa-file-image-o',   'image'],
                'gif'  => ['fa-file-image-o',   'image'],
                'docx' => ['fa-file-word-o',    'doc'],
                'doc'  => ['fa-file-word-o',    'doc'],
                'xlsx' => ['fa-file-excel-o',   'excel'],
                'xls'  => ['fa-file-excel-o',   'excel'],
                'zip'  => ['fa-file-archive-o', 'zip'],
                'rar'  => ['fa-file-archive-o', 'zip'],
            ];
            $icon      = $iconMap[$ext][0] ?? 'fa-file-o';
            $iconClass = $iconMap[$ext][1] ?? '';
            $dateStr   = $isArchived ? $file->deleted_at : $file->created_at;
            $rowClass  = $isArchived ? 'afm-archived-row' : '';
            $previewTypes = ['jpg','jpeg','png','gif','pdf'];
        @endphp
        <tr class="{{ $rowClass }}" data-file-id="{{ $file->id }}">
            <td class="cb-col">
                <input type="checkbox" class="afm-cb afm-row-cb" data-id="{{ $file->id }}">
            </td>
            <td><i class="fa {{ $icon }} afm-icon {{ $iconClass }}"></i></td>
            <td>
                {{ $file->file_name }}
                @if($file->is_mdo)        <span class="badge-mdo">MDO</span>@endif
                @if($file->is_telehealth) <span class="badge-th">Telehealth</span>@endif
                @if($isArchived)          <span class="archived-badge">Archived</span>@endif
            </td>
            <td><span class="afm-badge-agency">{{ $file->agency_name ?? '—' }}</span></td>
            <td class="afm-path"><i class="fa fa-folder-o"></i> {{ $file->file_path_label ?? 'Root' }}</td>
            <td>{{ strtoupper($file->file_type ?? '') }}</td>
            <td>
                @php
                    $bytes = $file->file_size ?? 0;
                    if ($bytes >= 1048576) echo number_format($bytes/1048576, 2) . ' MB';
                    elseif ($bytes >= 1024) echo number_format($bytes/1024, 2) . ' KB';
                    else echo $bytes . ' B';
                @endphp
            </td>
            <td>{{ $file->uploaded_by ?? '—' }}</td>
            <td style="white-space:nowrap;">
                {{ $dateStr ? Common::convertMDYTime($dateStr) : '' }}
            </td>
            
            <td style="white-space:nowrap;">
               
                    @if($file->patient_id)
                        <span style="font-size:13px;">
                           <a target="_blank" href="{{URL::to('/')}}/patient/view/{{ $file->patient_id }}">#{{ $file->patient_id }}</a>
                           <br>
                            @if(!empty($file->pt_status))
                                @php $st = $file->pt_status; @endphp
                                @if($st === 'Pending' || strtolower($st) === 'pending')
                                    <label class="badge badge-warning mb-0">Pending</label>
                                @elseif(strtolower($st) === 'booked')
                                    <label class="badge badge-info mb-0">Booked</label>
                                @elseif($st === 'completed')
                                    <label class="badge badge-success mb-0">Completed</label>
                                @elseif($st === 'in process')
                                    <label class="badge badge-secondary mb-0">In process</label>
                                @elseif(in_array($st, ['cancelled','refuese','no show','no answer','unable to contact']))
                                    <label class="badge badge-danger mb-0">Cancelled</label>
                                @elseif($st === 'noshow')
                                    <label class="badge badge-light mb-0">No Show</label>
                                @elseif($st === 'arrived')
                                    <label class="badge badge-primary mb-0">Arrived</label>
                                @elseif($st === 'processing')
                                    <label class="badge badge-secondary mb-0">Processing</label>
                                @elseif($st === 'refused')
                                    <label class="badge badge-light mb-0">Refused</label>
                                @elseif($st === 'hospitalized/rehab')
                                    <label class="badge badge-info mb-0">Hospitalized/Rehab</label>
                                @elseif($st === 'Pending Termination')
                                    <label class="badge badge-danger mb-0">Pending Termination</label>
                                @elseif($st === 'On Hold' || $st === 'Onhold')
                                    <label class="badge badge-secondary mb-0">On Hold</label>
                                @elseif($st === 'On Leave')
                                    <label class="badge badge-info mb-0">On Leave</label>
                                @elseif($st === 'Terminated')
                                    <label class="badge badge-danger mb-0">Terminated</label>
                                @elseif($st === 'unableToContact')
                                    <label class="badge badge-danger mb-0">Unable To Contact</label>
                                @elseif($st === 'checkin')
                                    <label class="badge badge-primary mb-0">Check In</label>
                                @elseif(strtolower($st) === 'not interested')
                                    <label class="badge badge-secondary mb-0">Not Interested</label>
                                @elseif(in_array($st, ['Patient Deceased','Appointment was missed','Appointment Missed','Closed Temporarily']))
                                    <label class="badge badge-danger mb-0">{{ $st }}</label>
                                @elseif(in_array($st, ['Telehealth Completed','TelehealthCompleted-Pending Forms','Telehealth Completed , Pending Forms','Form Completed','Service Provided']))
                                    <label class="badge badge-success mb-0">{{ in_array($st, ['TelehealthCompleted-Pending Forms']) ? 'Telehealth Completed , Pending Forms' : $st }}</label>
                                @elseif(in_array($st, ['Signed','Signed-SentBacktotheAgency','Signed & Sent Back to the Agency','New Form Requested']))
                                    <label class="badge badge-primary mb-0">{{ $st === 'Signed-SentBacktotheAgency' ? 'Signed & Sent Back to the Agency' : $st }}</label>
                                @elseif(in_array($st, ['1st Attempt - Unable to Contact','2nd Attempt - Unable to Contact','3rd Attempt - Unable to Contact','Patient Asked to Reschedule','PatientAskedtoReschedule','New Order Received']))
                                    <label class="badge badge-info mb-0">{{ $st === 'PatientAskedtoReschedule' ? 'Patient Asked to Reschedule' : $st }}</label>
                                @elseif(strtolower($st) === 'inactive')
                                    <label class="badge badge-danger mb-0">Inactive</label>
                                @else
                                    <label class="badge badge-secondary mb-0">{{ ucfirst($st) }}</label>
                                @endif
                            @endif
                        </span>
                    @else
                       <span class="badge badge-secondary" style="font-size:11px;cursor:pointer;" onclick="openLinkChartModal({{ $file->id }}, '{{ addslashes($file->file_name) }}', {{ $file->agency_id ?? 0 }})" title="Link to chart">Not linked</span>
                    @endif
            </td>
            <td style="white-space:nowrap;">
                @if(in_array($ext, $previewTypes))
                    <button class="btn btn-outline-info btn-sm" title="Preview" onclick="previewFile({{ $file->id }}, '{{ addslashes($file->file_name) }}', '{{ $ext }}', {{ $file->agency_id ?? 0 }})"><i class="fa fa-eye"></i></button>
                @endif
                <a href="/file-manager/file/download/{{ $file->id }}?agency_id={{ $file->agency_id }}" class="btn btn-outline-success btn-sm" title="Download"><i class="fa fa-download"></i></a>
                @if(!$isArchived)
                    <a href="/file-manager?agency_id={{ $file->agency_id }}&folder_id={{ $file->folder_id }}" class="btn btn-outline-primary btn-sm" title="Open in File Manager"><i class="fa fa-folder-open"></i></a>
                    @if(auth()->user()->agency_fk == "" && !$file->patient_id)
                    <button class="btn btn-outline-warning btn-sm" title="Link to Chart" onclick="openLinkedPatientsModal({{ $file->id }}, '{{ addslashes($file->file_name) }}',{{$file->agency_id}})"><i class="fa fa-link"></i></button>
                    @endif
                    <button class="btn btn-outline-danger btn-sm" title="Archive" onclick="archiveFile({{ $file->id }}, '{{ addslashes($file->file_name) }}', {{ $file->agency_id ?? 0 }})"><i class="fa fa-archive"></i></button>
                @else
                    <button class="btn btn-outline-success btn-sm" title="Restore" onclick="restoreFile({{ $file->id }}, '{{ addslashes($file->file_name) }}')"><i class="fa fa-undo"></i></button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="pull-right pegination-margin" style="padding:10px 12px;">
    {{ $files->appends(array_merge(['search' => $search], $filters ?? []))->links('pagination::bootstrap-4') }}
</div>
@endif
