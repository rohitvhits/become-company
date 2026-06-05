<div class="row">
    <div class="col-md-12" id="sms-log">
        
        @forelse ($data as $row)
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                @if($row->created_by !='')
                                    <div class="d-flex justify-content-between">
                                            {{$row->first_name}} {{$row->last_name}}  ({{$row->mobile_no}})
                                        <span class="text-muted">{{ date('m/d/Y h:i A', strtotime($row->created_at)) }}</span>
                                    </div>
                                    <strong></strong>
                                @else
                                <div class="d-flex justify-content-between">
                                    {{$row->mobile_no}}
                                    <span class="text-muted">{{ date('m/d/Y h:i A', strtotime($row->created_at)) }}</span>
                                </div>
                                @endif
                             
                            </h5>
                            @if($row->send_sms_status !="")
                            <p class="card-text" style="margin-bottom: -10px;"><span class="badge badge-primary"> {{ ucfirst($row->send_sms_status) }} </span>  {{ date('m/d/Y h:i A', strtotime($row->send_sms_updated_date)) }}</p>
                            <hr>
                            @endif
                            
                            <p class="card-text"  style="margin-top: -10px;">{!! $row->sms !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <center><b>Data not found</b></center>
        @endforelse

    </div>
</div>
<div class="pull-right pegination-margin sms-log-pegination">
        {{ $data->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

<!-- <div >
    
    
    <div class="text-chat-messages" id="sms-log">
        @forelse ($data as $row)
        <div id="text-chat-messages-inner" class="text-notes-messages">
            <p>
               
                <span class="msg-block"><strong>{{$row->created_by}} <br>
                    {{$row->mobile_no}}
                    @if($row->patient_id != $record_id)
            <span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>
            @endif</strong><span class="time">{{ date('m/d/Y h:i A', strtotime($row->created_at)) }}</span>
                <span class="msg">{{$row->sms}}</span></span></p>
        </div>
        @empty
            <center><b>Data not found</b></center>
        @endforelse
    </div>
   

    <div class="pull-right pegination-margin log-pegination">
        {{ $data->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>  

</div> -->

<script>
$('#sms-log').animate({
    scrollTop: inner.height()
}, 20);
</script>