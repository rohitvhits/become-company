<div class="tableData table-responsive">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>Id</th>
                <th nowrap>Status</th>
                <th nowrap>Company</th>
                <th nowrap> Name </th>
                <th nowrap> Mobile / Phone / Email</th>
                <th nowrap> SSN </th>
                <th nowrap> Created Date/Created By </th>
                <th nowrap> Reason </th>
            </tr>
        </thead>
        <tbody>
            
            @php
            $cnt =1;
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            
            @php $clickHtml = $href = $target =''; @endphp
            @if($row->is_flag_read == 0)
                @php
                   
                    $clickHtml = "makeFlagRead('" . addslashes($row->flag_id) . "', '". addslashes($row->id) ."','Hub Record');";
                    $href = "javascript:void(0)";
                    $target = '';
                @endphp
            @else
                @php
                    $clickHtml = "";
                    $href = url('hub-record/view/' . $row->id);
                    $target = '_blank';
                @endphp
            @endif
            <tr>
                
                <td nowrap>
                <div id="preason{{ $cnt}}" style="display:none">
                    {{ $row->reasonNotes}}
                </div>
                    <div>
                        <a target="{{$target}}" onclick="{{$clickHtml}}" href="{{$href}}"><?= '#' . '' . $row->id ?></a>
                        @if($row->is_flag_read == 0)
                            <div style="position:relative"><span class="add_new_record left_record" >New</span></div>
                        @endif
                    </div>
                </td>
                <td nowrap>
                       {{ ucfirst($row->agency_status)}}
                </td>
                <td>
{{ $row->agency_name}} 
                </td>
                <td>
                     {{ $row->first_name}}  {{$row->last_name}} 
                </td>
              
                <td nowrap>
                   
                    {{$row->mobile}} <br />
                    {{$row->phone}} <br />
                    {{$row->email}}
                </td>
                <td>
                    {{Common::formatSSN($row->ssn)??'-'}}
                </td>
                <td nowrap><?= date('m/d/Y h:i A', strtotime($row->created_at)); ?><br />
                    {{$row->uFname??$row->uFname}} {{$row->uLname??$row->uLname}}
                </td>
                <td nowrap title ="{{ $row->reasonNotes }}">
                @php 
                $out = strlen($row->reasonNotes) > 50 ? substr($row->reasonNotes,0,50)."..." : $row->reasonNotes;
                @endphp    
                <div  style="text-decoration: none; color: inherit;cursor: pointer;" onclick="patientReasonDescription('{{  $cnt}}')">{{$out}} </div></td>
            </tr>
        @php 
            $cnt++;
        @endphp
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="8">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>