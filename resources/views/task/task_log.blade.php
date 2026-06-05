

<div class="table-responsive">

    <table id="" class="table recordtabletdwidth">
        <thead>
            <tr>
                <th>#</th>
                
                
                <th>Description</th>
                <th>Created By</th>
                <th>Created At</th>
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
                    {{$row->description ?? ""}}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->users->first_name ?? ""}} {{$row->users->last_name ?? ""}}</td>
                <td style="min-width:220px; white-space:nowrap">{{ date('m/d/Y h:i A', strtotime($row->created_at)) }}</td>
                
                
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
        {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>


</div>