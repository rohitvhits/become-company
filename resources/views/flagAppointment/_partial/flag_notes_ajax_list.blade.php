
<style>
    
</style>
<div id="accordion-1" class="accordion">
    @if (count($query) > 0)
        @foreach ($query as $row)
            @if($row->is_flag_read == 0)
                @php
                    // Escape quotes in the PHP variables to avoid JavaScript syntax errors
                    $clickHtml = "makeFlagRead('" . addslashes($row->flag_id) . "','". addslashes($row->patient_id) ."','Notes');";
                    $href = "javascript:void(0)";
                    $target = '';
                @endphp
            @else
                @php
                    $clickHtml = "";
                    $href = url('patient/view/' . $row->patient_id);
                    $target = '_blank';
                @endphp
            @endif
            <div class="card">
                <div class="card-header tableData" id="headingOne">
                    <div class="mb-0" style="background:#ddd;">
                    <div class="container-fluid py-2" style="justify-content:space-between;display:flex">
                        <strong>{{$row->first_name}}  ({{$row->type}}) 
                            @if($row->is_flag_read == 0)
                                <div class="badge badge-info">New</div>
                            @endif
                        </strong>
                        <span>{{ date('m/d/Y H:i A',strtotime($row->created_date)) }}</span>
                    </div>
                    </div>
                </div>
                <div id="collapseOne" class="collapse show" style="margin-left: 10px;" aria-labelledby="headingOne" data-parent="#accordion-1">
                    <div class="card-body">
                    <p><b>#Record ID </b>:<a target="{{$target}}" onclick="{{$clickHtml}}" href="{{$href}}">{{$row->patient_id}}</a></p>
                    <p><b>Message</b>: {{$row->message}}</p>
                    <p><b>Reason</b>: {{$row->reason}}</p>
                    </div>
                </div>
            </div>
        @endforeach
        @endif
        @if (count($query) == 0)
        <tr>
            <td colspan="8">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        @endif
</div>