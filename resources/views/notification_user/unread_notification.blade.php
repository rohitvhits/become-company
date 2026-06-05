@php
use App\Helpers\Utility;
@endphp
@if(!empty($data) && count($data) > 0)
<div class="notify_class">
    @foreach($data as $noti)
    @php
$messages = strlen($noti->message) > 65 && strpos($noti->message, '<br/>') !== false
    ? substr($noti->message, 0, 65) . '...'
    : (strlen($noti->message) > 150 
        ? substr($noti->message, 0, 65) . '...'
        : $noti->message);
@endphp
    @php $url = $patient_label = ""; @endphp
        @if($noti->record_id != NULL)
        @php 
            $url = url('patient/view') . '/' . $noti->record_id;  // Create the URL
            $patient_label =  "<a href='".$url."' style='text-decoration:none;'>#".$noti->record_id."</a> - ".$noti->first_name." ".$noti->last_name." (".$noti->patientType.") |";
        @endphp
        @else
            @php 
                $url = url('tasks/task-list');
            @endphp
        @endif
    <div class="dropdown-item preview-item" onclick="markAsRead('{{$noti->nid}}','{{$url}}')" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">
        <div class="card" style="width: 423px;border-radius: 10px;">
            <div class="card-body row" style>
                <div class="col-md-1" style="margin-left: -15px;">
                    @if($noti->type == 'Task')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                        <i class="mdi mdi-pencil mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Appointment')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-danger">
                        <i class="mdi mdi-account mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Notes')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-info">
                        <i class="mdi mdi-note-multiple mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Flag')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-primary">
                        <i class="mdi mdi-flag mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Document')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-warning">
                        <i class="mdi mdi-file-document mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Assign Appointment')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                        <i class="mdi mdi-library-books mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Service Status')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-warning">
                        <i class="fa fa-wrench mx-0"></i>
                        </div>
                    </div>
                    @elseif($noti->type == 'Esign')
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                        <i class="mdi mdi-grease-pencil mx-0"></i>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-md-11" style="margin-left: 9px;">
                    <a href="{{$url}}" target="_blank"><h6 class="card-title" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">{!! $noti->title !!}</h6></a>
                    <p class="tx-12 mb-2 text-muted" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">{!! $patient_label !!} {!! $messages !!}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <p class="tx-10 text-muted mb-0" style="margin-left: 45px;margin-top: -24px;">
                        @if($noti->created_by != 0)
                        {{$noti->user_first_name}} {{$noti->user_last_name}}
                        @else
                        @if($noti->sms)
                            +1{{$noti->sms}}
                        @endif
                        @if($noti->email)
                            @if($noti->sms)
                                | 
                            @endif
                            {{$noti->email}}
                        @endif
                        @endif
                        | {{ date('M d, Y, H:i A',strtotime($noti->created_at)) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif