<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
    <thead>
        <tr>
            <th>No</th>
            <th style="white-space:nowrap">Portal ID</th>
            <th style="white-space:nowrap">Patient Name</th>
            <th style="white-space:nowrap">Caregiver ID</th>
            <th style="white-space:nowrap">Type</th>
            <th style="white-space:nowrap">Module Name</th>
            <th style="white-space:nowrap">Action Type</th>
            <th style="white-space:nowrap">Created Date</th>
            <th style="white-space:nowrap">Created By</th>
            <th style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1 + ($list->currentPage() - 1) * $list->perPage();
        @endphp

        @if (count($list) > 0)
            @foreach ($list as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if($row->patient_id)
                            <a href="{{ url('patient/view') }}/{{ $row->patient_id }}" target="_blank">{{ $row->patient_id }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($row->patient_first_name || $row->patient_last_name)
                            <a href="{{ url('patient/view') }}/{{ $row->patient_id }}" target="_blank">{{ $row->patient_first_name }} {{ $row->patient_last_name }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $row->caregiver_id ?? 'N/A' }}</td>
                    <td>{{ $row->type ?? 'N/A' }}</td>
                    <td>{{ $row->hha_module_type ?? 'N/A' }}</td>
                    <td>{{ $row->action ?? 'N/A' }}</td>
                    <td nowrap>
                        @if($row->created_date)
                            {{ date('m/d/Y h:i A', strtotime($row->created_date)) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        {{ ($row->created_first_name ?? '') . ' ' . ($row->created_last_name ?? '') }}
                    </td>
                    <td>
                        <a href="javascript:void(0)" title="View Send Request" onclick="viewSendRequest('{{ $row->id }}')">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif

        @if (count($list) == 0)
            <tr>
                <td colspan="9">
                    <span class="text-center"><b>No record available</b></span>
                </td>
            </tr>
        @endif
    </tbody>
</table>

<script>
    $('#total_record_id').text('{{ $list->total() }}');
</script>

<div class="pull-right pegination-margin">
    {{ $list->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
