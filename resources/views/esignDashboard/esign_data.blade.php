<table id="" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Template Name</th>
            <th>Status</th>
            <th>Sender</th>
            <th>Review By</th>
            <th>Signers</th>
            <th>Added By</th>
        </tr>
    </thead>
    <tbody>
        @if (count($esignData) > 0)
            @foreach($esignData as $row)
            <tr>
                <th scope="row"><a href="{{url('patient/view/')}}/{{$row->main_intakeId}}">{{$row->id}}</a>
                </th>
                <td>{{ isset($row->templateDetails) && isset($row->templateDetails->template_name) 
                            ? $row->templateDetails->template_name 
                            : ( isset($row->writeDocumentDetails) && isset($row->writeDocumentDetails->document_name) 
                                ? $row->writeDocumentDetails->document_name 
                                : '-')}}</td>
                <td>
                    @if($row->signerRemaining == 0)
                        <span class="">Completed on <br>{{$row->completed_on}}</span><br/>
                        @if ($row->pdf_status == null)
                            <label class="badge badge-outline-success" style="color:#3bb001;">Completed</label>
                        @else
                            @if ($row->pdf_status == '0')
                                <label class="badge badge-outline-danger" style="color:#ff0000;">Rejected</label>
                            @elseif ($row->pdf_status == '1')
                                <label class="badge badge-outline-success" style="color:#28a745;">Approved</label>
                            @endif
                        @endif
                    @else
                        @if ($row->pdf_status == null)
                            <label class="badge badge-outline-warning" style="color:#d76718;">Pending</label>
                        @else
                            @if ($row->pdf_status == '0')
                                <label class="badge badge-outline-danger" style="color:#ff0000;">Rejected</label>
                            @elseif ($row->pdf_status == '1')
                                <label class="badge badge-outline-success" style="color:#28a745;">Approved</label>
                            @endif
                        @endif
                    @endif
                </td>
                <td>
                    @if ($row->signerRemaining == 0)
                        @if ($row->templete_id != 0)
                            @if ($row->pdf_status == null)
                                <label class="badge badge-outline-info" style="color:#00BBE0;">Review Remaining</label>
                            @else
                                {{ ($row->review_details->first_name ?? '') . ' ' . ($row->review_details->last_name ?? '') }}<br>
                                {{ $row->review_date ?? '' }}
                            @endif
                        @else
                            <div>-</div>
                        @endif
                    @else
                        <div>-</div>
                    @endif
                </td>
                <td>{{$row->sender_name}}</td>
                <td>
                    @if ($row->templete_id == 0)
                        <b>-</b>
                    @else
                       <b>{{$row->completedCount}}</b>/<b>{{$row->sentOnCount}}</b>
                    @endif
                </td>
                <td>
                    {{date('m/d/Y',strtotime($row->created_date))}}<br/>
                    {{$row->userDetails->first_name}}{{$row->userDetails->last_name}}
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif           
    </tbody>
</table>
<div class="pull-right pegination-margin">
{{ $esignData->appends(request()->query())->links() }}
</div>