<div class="">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Task ID</th>
                <th width="12%">Patient</th>
                <th width="10%">Agency</th>
                <th width="15%">Cron Name</th>
                <th width="7%">Type</th>
                <th width="25%">Message</th>
                <th width="10%">Created Date</th>
                <th width="5%">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $row)
                    @php
                        $rowData   = !empty($row->data) ? @unserialize($row->data) : null;
                        $jsonData  = ($rowData !== false && $rowData !== null) ? json_encode($rowData) : null;
                        $patient   = $row->patientDetails;
                        $extTaskId = $row->taskHealthMaster->task_id ?? $row->task_id;
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>
                            @if($extTaskId)
                                <a href="javascript:void(0)" class="th-task-id-link" onclick="openVisitModal({{ $extTaskId }})">
                                    #{{ $extTaskId }}
                                </a>
                            @elseif($row->task_health_id)
                                <span class="text-muted">#{{ $row->task_health_id }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->patient_id)
                                <a href="{{ url('patient/view/' . $row->patient_id) }}" target="_blank">
                                    @if($patient)
                                        {{ $patient->first_name . ' ' . $patient->last_name }}
                                        <br><small class="text-muted">#{{ $row->patient_id }}</small>
                                    @else
                                        #{{ $row->patient_id }}
                                    @endif
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $row->agencyDetails->agency_name ?? '-' }}</td>
                        <td>{{ $row->cron_name ?? '-' }}</td>
                        <td>
                            @if($row->type === 'error')
                                <span class="badge badge-danger">{{ $row->type }}</span>
                            @elseif($row->type === 'success')
                                <span class="badge badge-success">{{ $row->type }}</span>
                            @elseif($row->type === 'skipped')
                                <span class="badge badge-warning">{{ $row->type }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $row->type }}</span>
                            @endif
                        </td>
                        <td>{{ $row->message }}</td>
                        <td>{{ $row->created_at ? date('m/d/Y h:i A', strtotime($row->created_at)) : '-' }}</td>
                        <td>
                            @if($jsonData)
                                <span id="cron-log-{{ $row->id }}" style="display:none">{{ $jsonData }}</span>
                                <a onclick="showCronLogData('{{ $row->id }}')" style="cursor:pointer;">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
                <tr>
                    <td colspan="9">
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
