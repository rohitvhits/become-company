<div class="card">
    <div class="card-body">
        <div class="mt-2">
            <div class="accordion" id="accordion" role="tablist">
            @if (count($query) > 0)
                @foreach ($query as $row)
                    @php $patient_label = $url = ""; @endphp
                    @if($row->is_read == 0)
                        @php $label = '<span class="hide">New</span>'; @endphp
                    @endif 
                    @if($row->record_id != NULL)
                    @php 
                        $url = url('patient/view') . '/' . $row->record_id;  // Create the URL
                        $patient_label =  "<a href='".$url."' style='text-decoration:none;'>#".$row->record_id."</a> - ".$row->first_name." ".$row->last_name." (".$row->patientType.") |";
                    @endphp
                    @else
                        @php 
                            $url = url('tasks/task-list'); 
                        @endphp
                    @endif
                    @php $clickEvent = ''; @endphp
                    @if($row->is_read == 0)
                        @php 
                            $clickEvent = 'onclick="return markAsRead(' . $row->nid . ', \'' . $url . '\');"'; 
                        @endphp
                    @endif
                    <div class="card border-bottom">
                        <div class="card-header" role="tab" id="heading-1">
                            <div class="row mb-0 ml-1 mr-1" style="display: flex;justify-content: space-between;">
                                <div>
                                    <h6><a target="_blank" href="{{$url}}"> {{$row->title}} </a></h6>
                                </div>
                                <div>
                                    <div class="badge badge-success"> {{ $row->type }}</div>
                                </div>
                            </div>
                        </div>
                        <div id="collapse-1" class="collapse show mt-2" role="tabpanel" aria-labelledby="heading-1" data-parent="#accordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                    {!! $patient_label !!} {!! $row->message !!}
                                    </div>
                                </div>
                                <p class="text-muted mt-1 mb-0"> Created By: 
                                    @if($row->created_by != 0)
                                    {{$row->user_first_name}} {{$row->user_last_name}}
                                    @else
                                    @if($row->sms)
                                        +1{{$row->sms}}
                                    @endif
                                    @if($row->email)
                                        @if($row->sms)
                                            | 
                                        @endif
                                        {{$row->email}}
                                    @endif
                                    @endif
                                    | {{ date('M d, Y, H:i A',strtotime($row->created_at)) }} 
                                </p>
                                @if($row->is_read == 0)
                                    <div class="">
                                        <i class="fa fa-bell-o pull-right mb-2 mr-2"  {!! $clickEvent !!} title="Read" style="margin-top: -22px;font-size:23px;cursor:pointer"></i>
                                    </div>
                                @else
                                    <div class="">
                                        <i class="fa fa-bell pull-right mb-2 mr-2" style="margin-top: -22px;font-size:23px;"></i>
                                    </div>
                                @endif
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
        </div>
    </div>
</div>
<div class="pull-right pegination-margin task-list-pegination">
    {{$query->appends("pagination::bootstrap-4")}}
</div>