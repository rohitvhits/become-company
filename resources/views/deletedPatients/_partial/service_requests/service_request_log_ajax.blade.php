
    @php
        $i = 0;
    @endphp


<div class="table-responsive">
    <table id="" class="table recordtabletdwidth">
        <thead>
            <tr>
                <th>#</th>
                <th>Patient </th>
                <th>Service</th>
                <th>Created By</th>
                <th>Create Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($reqServiceLog->currentPage() - 1) * $reqServiceLog->perPage();
            @endphp

            @forelse ($reqServiceLog as $row)
                   @php
                  $serviceName =  $row->services[0]->name;
                    
                   
                   @endphp

                   

                <tr>
                    <td style="white-space:nowrap">{{ $i++ }}</td>
                    <td style="min-width:220px; white-space:nowrap">{{ $serviceName }}</td>
                    <td style="min-width:220px; white-space:nowrap">{{ $serviceName }}</td>
                    <td style="min-width:220px; white-space:nowrap">{{ $serviceName }}</td>
                    <td style="min-width:220px; white-space:nowrap">{{ $serviceName }}</td>

                   
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
        {{ $reqServiceLog->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>
