<div id="tv-summary" data-total="{{ $files->total() }}" style="display:none;"></div>

@if($files->isEmpty())
    <div class="fm-empty"><i class="fa fa-folder-open-o"></i>No files found</div>
@else
<div class="p-2" style="overflow-y: scroll;">
    <table class="fm-table" style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr>
                <th style="width:36px;text-align:center;padding:8px 6px;">
                    <input type="checkbox" id="tvSelectAll" style="width:15px;height:15px;cursor:pointer;" title="Select all">
                </th>
                <th style="width:28px;"></th>
                <th>File Name</th>
                <th>Path</th>
                <th>Type</th>
                <th>Size</th>
                <th>Uploaded By</th>
                <th>Upload Date</th>
                <th>Linked Chart<br> Status / Resolution</th>
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
                $previewTypes = ['jpg','jpeg','png','gif','pdf'];
            @endphp
            <tr>
                <td style="text-align:center;padding:7px 6px;">
                    <input type="checkbox" class="tv-row-cb" data-id="{{ $file->id }}" style="width:15px;height:15px;cursor:pointer;">
                </td>
                <td style="padding:7px 8px;">
                    <span class="tbl-icon file-icon {{ $iconClass }}"><i class="fa {{ $icon }}"></i></span>
                </td>
                <td style="padding:7px 8px;">
                    {{ $file->file_name }}
                    @if($file->is_mdo)        <span class="badge-mdo">MDO</span>@endif
                    @if($file->is_telehealth) <span class="badge-th">Telehealth</span>@endif
                </td>
                <td style="padding:7px 8px;">
                    <span class="text-muted" style="font-size:12px;"><i class="fa fa-folder-o"></i> {{ $file->file_path_label ?? 'Root' }}</span>
                </td>
                <td style="padding:7px 8px;">{{ strtoupper($file->file_type ?? '') }}</td>
                <td style="padding:7px 8px;">
                    @php
                        $bytes = $file->file_size ?? 0;
                        if ($bytes >= 1048576) echo number_format($bytes/1048576, 2) . ' MB';
                        elseif ($bytes >= 1024) echo number_format($bytes/1024, 2) . ' KB';
                        else echo $bytes . ' B';
                    @endphp
                </td>
                <td style="padding:7px 8px;">{{ $file->uploaded_by ?? '—' }}</td>
                <td style="padding:7px 8px;white-space:nowrap;">{{ $file->created_at ? Common::convertMDYTime($file->created_at) : '' }}</td>
               
                <td style="padding:7px 8px;white-space:nowrap;">
                    @if($file->patient_id)
                        <a target="_blank" href="{{URL::to('/')}}/patient/view/{{ $file->patient_id }}">#{{ $file->patient_id }}</a>
                        @if(!empty($file->pt_status))
                            @php $st = $file->pt_status; @endphp
                            <br>
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
                            @elseif($st === 'On Hold')
                                <label class="badge badge-secondary mb-0">On Hold</label>
                            @elseif($st === 'On Leave')
                                <label class="badge badge-info mb-0">On Leave</label>
                            @elseif($st === 'Terminated')
                                <label class="badge badge-danger mb-0">Terminated</label>
                            @elseif($st === 'unableToContact')
                                <label class="badge badge-danger mb-0">Unable To Contact</label>
                            @elseif(in_array($st, ['Patient Deceased','Appointment was missed','Appointment Missed','Closed Temporarily']))
                                <label class="badge badge-danger mb-0">{{ $st }}</label>
                            @elseif(in_array($st, ['Telehealth Completed','Telehealth Completed , Pending Forms','Form Completed','Service Provided']))
                                <label class="badge badge-success mb-0">{{ $st }}</label>
                            @elseif(in_array($st, ['Signed','Signed & Sent Back to the Agency','New Form Requested']))
                                <label class="badge badge-primary mb-0">{{ $st }}</label>
                            @elseif(in_array($st, ['1st Attempt - Unable to Contact','2nd Attempt - Unable to Contact','3rd Attempt - Unable to Contact','Patient Asked to Reschedule','New Order Received']))
                                <label class="badge badge-info mb-0">{{ $st }}</label>
                            @elseif(strtolower($st) === 'inactive')
                                <label class="badge badge-danger mb-0">{{ ucfirst($st) }}</label>
                            @else
                                <label class="badge badge-secondary mb-0">{{ ucfirst($st) }}</label>
                            @endif
                        @endif
                    @else
                        <span class="badge badge-secondary" style="font-size:11px;cursor:pointer;" onclick="openLinkChartModal({{ $file->id }}, '{{ addslashes($file->file_name) }}', {{ $file->agency_id ?? 0 }})" title="Link to chart">Not linked</span>
                    @endif
                </td>
                <td style="padding:7px 8px;white-space:nowrap;" class="tbl-actions">
                    @if(in_array($ext, $previewTypes))
                        <button class="btn btn-outline-info btn-sm" onclick="previewFile({{ $file->id }}, '{{ addslashes($file->file_name) }}', '{{ $ext }}')" title="Preview"><i class="fa fa-eye"></i></button>
                    @endif
                    <button class="btn btn-outline-success btn-sm" onclick="downloadFile({{ $file->id }})" title="Download"><i class="fa fa-download"></i></button>
                    @if(auth()->user()->agency_fk == "" && !$file->patient_id)
                    <button class="btn btn-outline-warning btn-sm" onclick="openLinkChartModal({{ $file->id }}, '{{ addslashes($file->file_name) }}')" title="Link Chart"><i class="fa fa-link"></i></button>
                    @endif
                    <button class="btn btn-outline-secondary btn-sm" onclick="showRenameModal('file', {{ $file->id }}, '{{ addslashes($file->file_name) }}')" title="Rename"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteFile({{ $file->id }}, '{{ addslashes($file->file_name) }}')" title="Archive"><i class="fa fa-archive"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-right" style="padding:8px 4px;">
        {{ $files->appends(['search' => $search, 'agency_id' => $agencyId])->links('pagination::bootstrap-4') }}
    </div>
</div>
@endif
