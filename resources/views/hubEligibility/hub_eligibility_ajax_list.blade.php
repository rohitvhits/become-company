<div class="table-responsive">

    <table id="" class="table recordtabletdwidth table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Ip Address</th>
                <th>File Name</th>
                <th>Created Date</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($logList->currentPage() - 1) * $logList->perPage();
           $lastDate = "";
           @endphp

            @forelse ($logList as $key=>$row)
            <tr>
                <td style="white-space:nowrap">{{$i++}}</td>
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->ip}}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->importLogs->file_name}}</td>
                @if($key == 0)
               @php $lastDate = "lastDate"; @endphp
                @endif
                
                <td style="min-width:220px;  white-space:nowrap" id="{{ $lastDate }}">
                    {{ date('m/d/Y h:i A', strtotime($row->created_at))}}
                </td> 
                <td>{{ $row->users!=null ? $row->users->full_name : '' }}</td>
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