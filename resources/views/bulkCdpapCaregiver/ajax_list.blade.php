<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <th>No</th>
        <th>Message</th>
        <th>Created Date</th>
        <th>Created By</th>
        <th>Action</th>
    </thead>
    <tbody>
    @php
    $i = 1 + ($query->currentPage() - 1) * $query->perPage();
    @endphp
   
    @forelse($query as $row)
            <tr>
                <td>{{ $i}}</td>
                <td style="white-space:pre-line"><a onclick="viewMessage('{{ $row->id}}')">{{ substr($row->message, 0, 50) . '...'; }}</a>
                    <span id="notes_{{ $row->id}}" style="display:none">{{ $row->message }}</span>

                </td>
                <td>{{ \Carbon\Carbon::parse($row->created_date)->format('m/d/Y h:i A ') }}</td>
                <td>{{ $row->first_name.' '.$row->last_name }}</td>
                <td>
                    @can('view-bulk-sms-cdpap-caregiver')
                    <a href="{{ url('/bulk-sms-cdpap-caregiver/view-detail')}}/{{ $row->id}}" title="View"><i class="fa fa-eye"></i></a>
                    @endcan
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="4" class="text-center">No records found</td>
            </tr>
        @endforelse

    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>