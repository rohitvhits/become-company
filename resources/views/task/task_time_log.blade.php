

<div class="table-responsive">

    <table id="" class="table recordtabletdwidth">
        <thead>
            <tr>
                <th>#</th>
                <th>Start Date Time</th>
                <th>End Date Time</th>
                <th>Time Duration</th>
                
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp

            @forelse ($query as $row)
            <tr>
                <td style="white-space:nowrap">{{$i++}}</td>
                <td style="min-width:220px; white-space:nowrap">
                    {{ date('m/d/Y h:i:s', strtotime($row->start_date_time)) }}
                </td>
                <td style="min-width:220px; white-space:nowrap">
                    {{ ($row->end_date_time !="")?date('m/d/Y h:i:s', strtotime($row->end_date_time)):"" }}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->time_duration ?? ""}} </td>
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

    <div class="pull-right pegination-margin time-log-pegination">
        {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>


</div>