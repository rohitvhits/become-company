<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th style="white-space: nowrap;">ID</th>
                <th style="white-space: nowrap;">Type</th>
                <th style="white-space: nowrap;">Cron Type</th>
                <th style="white-space: nowrap;">Agency Name</th>
                <th style="white-space: nowrap;">Employee ID</th>
                <th style="white-space: nowrap;">Line</th>
                <th style="white-space: nowrap;">Error Log</th>
                <th style="white-space: nowrap;">Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $value)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->type ?? '-' }}</td>
                    <td>{{ $value->cron_type ?? '-' }}</td>
                    <td>{{ $value->agency_name ?? '-' }}</td>
                    <td>{{ $value->employee_id ?? '-' }}</td>
                    <td>{{ $value->line ?? '-' }}</td>
                    <td  class="error-log-cell" title="{{ $value->trace ?? '' }}">{{ Str::limit($value->error_log, 50) ?? '-' }}</td>
                    <td style="white-space: nowrap;">{{ Common::convertMDYTime($value->created_at) }}</td>
                    <td>
                        <a href="javascript:void(0)" title="View Details" onclick="viewRecord({{ $value->id }})">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="10">No record available</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="pull-right pegination-margin">
        {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    var total = "{{ $query->total() }}";
    $('#total_count').text(total);
    $('#blank_div').attr('style', 'margin-top:12%');
    if (total == 0) {
        $('#blank_div').attr('style', 'margin-top:10%');
    }
</script>
