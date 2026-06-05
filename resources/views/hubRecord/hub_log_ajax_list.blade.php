@if (in_array($user->user_type_fk, [5, 6]))
@php
$i = 1;
@endphp
@else
@php
$i = 0;
@endphp
@endif

<div class="table-responsive">

    <table id="" class="table recordtabletdwidth table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Ip Address</th>
                <th>Type</th>
                <th>Created Date</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($logList->currentPage() - 1) * $logList->perPage();
            @endphp

            @forelse ($logList as $row)
            <tr>
                <td style="white-space:nowrap">{{$i++}}</td>
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->ip}}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->type}}</td>
                <td style="min-width:220px;  white-space:nowrap">
                    {{ date('m/d/Y h:i A', strtotime($row->created_at)) }}
                </td>
                <td>{{ $row->user!=null ? $row->user->full_name : '' }}</td>
            </tr>
 
            @empty 
            <tr>
                <td colspan="12">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pull-right pegination-margin log-pagination">
        {{ $logList->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

</div>