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

    <table id="" class="table recordtabletdwidth">
        <thead>
            <tr>
                <th>#</th>
                <th>Ip Address</th>
                <th>Type</th>
                <th>Module</th>
                <th>Message</th>
                <th>Created Date</th>
                <th>Created By</th>
                <th>Action</th>
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
                <td style="min-width:220px; white-space:nowrap">{{$row->module}}</td>
                <td style="min-width:220px; white-space:nowrap">{{$row->message}}</td>
                <td style="min-width:220px;  white-space:nowrap">
                    {{ date('m/d/Y h:i A', strtotime($row->created_at)) }}
                </td>
                <td>{{ $row->userWithTrash!=null ? $row->userWithTrash->full_name : '' }}</td>
                <td>
                    @if($row->new_response !="")
                    <a href="javascript:void(0)" onclick="viewLog('{{ $row->id}}')"><i class="fa fa-eye mr-1"></i></a>
                    @endif
                </td>
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

    <div class="pull-right pegination-margin log-pegination">
        {{ $logList->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

</div>