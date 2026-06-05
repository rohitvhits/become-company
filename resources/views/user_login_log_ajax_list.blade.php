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
                <th>Country</th>
                <th>Country Code</th>
                <th>Login status</th>
                <th>Created Date</th>
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
                    {{$row->ipaddress}}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->country}}</td>
                <td style="min-width:220px; white-space:nowrap">{{$row->country_code}}</td>
                <td style="min-width:220px; white-space:nowrap">{{$row->login_status}}</td>
                <td style="min-width:220px;  white-space:nowrap">
                    {{ date('m/d/Y h:i A', strtotime($row->created_at)) }}
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

    <div class="pull-right pegination-margin login-log-pegination">
        {{ $logList->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

</div>