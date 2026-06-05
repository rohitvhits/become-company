<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
    <thead>
        <tr>
            <th style="white-space:nowrap">No</th>
            <th style="white-space:nowrap">Agency Name</th>
            <th style="white-space:nowrap">Patient Name</th>
            <th style="white-space:nowrap">HHA Document ID</th>
            <th style="white-space:nowrap">Attachment</th>
            <th style="white-space:nowrap">Created Date <br>Created By</th>
            <th style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
        @endphp

        @if (count($query) > 0)
            @foreach ($query as $row)
                <tr>
                    <td>{{ $i++ }}<br></td>
                    <td>{{ $row->agency_name }}</td>
                    <td>
                        @if ($row->patient)
                            <a href="{{ url('patient/view') }}/{{ $row->patient_id }}" target="_blank">{{ $row->full_name }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $row->hha_document_id ?? 'N/A' }}</td>
                    <td>
                        @can('hha-mdo-report-download')
                            @if ($row->attachment != "")
                                <a href="{{ url('hha/hha-mdo/mdo-report-log/download') }}/{{ $row->id }}">
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                        @endcan
                    </td>
                    <td>
                        @if ($row->created_date)
                            {{ date('m/d/Y h:i A', strtotime($row->created_date)) }}
                        @else
                            N/A
                        @endif
                        <br>
                        {{ $row->uFirstName . ' ' . $row->uLastName }}
                    </td>
                    <td>
                        @can('hha-mdo-report-view')
                        <a title="View Log" onclick="viewDocumentSendLog('{{ $row->id}}')"><i class="fa fa-eye"></i></a>
                        @endcan
                        
                    </td>
                </tr>
            @endforeach
        @endif

        @if (count($query) == 0)
            <tr>
                <td colspan="10">
                    <span class="text-center"><b>No record available</b></span>
                </td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>