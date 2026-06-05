<div class="table-responsive">

    <table id="" class="table recordtabletdwidth table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                @if(Auth()->user()->view_ssn_hub ==1)
                <th>SSN</th>
                @endif
                <th>Company</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Utilization</th>
                <th>Created Date / Created By</th>
             
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($hubUitlization->currentPage() - 1) * $hubUitlization->perPage();
            $lastDate = "";
            @endphp

            @forelse ($hubUitlization as $key=>$row)
            <tr>
                <td style="white-space:nowrap">{{$i++}}</td>
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->first_name}}  {{$row->last_name}}
                </td>
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->email}}
                </td>
                @if(Auth()->user()->view_ssn_hub ==1)
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->SSN}}
                </td>
                @endif
                <td style="min-width:220px; white-space:nowrap">
                    {{$row->company}}
                </td>
                <td style="min-width:220px; white-space:nowrap">
                    {{ ucfirst($row->gender)}}
                </td>
                <td style="min-width:220px; white-space:nowrap">{{$row->dob}}</td>
                <td style="min-width:220px; white-space:nowrap">{{$row->utilization}}</td>
                @if($key == 0)
                @php $lastDate = "lastUtiDate"; @endphp
                 @endif
                <td style="min-width:220px;  white-space:nowrap" id="{{ $lastDate }}" data-uti-date="{{ date('m/d/Y h:i A', strtotime($row->created_at)) }}">
                    
                    {{ date('m/d/Y h:i A', strtotime($row->created_at)) }}
                    <br>
                    {{ $row->user!=null ? $row->user->full_name : '' }}</td>
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
        {{ $hubUitlization->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

</div>