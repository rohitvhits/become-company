<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Ip Address</th>
            <th>Portal ID</th>
            <th>Type</th>
            <th>Module</th>
            <th>Message</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>{{$cnt++}}</td>
                    <td>
                        {{ $val->ip }}
                    </td>
                    <td>
                        @if(in_array($val->module,['Patient Appointment','Patient']))
                        <a href="{{ url('/')}}/patient/view/{{ $val->object_id }}" target="_blank">{{ $val->object_id }}</a>
                        @else
                        {{ $val->object_id }}
                        @endif
                    </td>
                    <td>
                        {{ $val->type }}
                    </td>
                    <td>
                        {{ $val->module }}
                    </td>
                    
                    <td>
                        {{ $val->message }}
                    </td>
                    <td>
                         {{ Common::convertMDYTime($val->created_at)}}
                    </td>
                    <td>
                        @if(isset($val->user->first_name))
                        {{ $val->user->first_name.' '.$val->user->last_name}}
                        @endif
                    </td>
                    <td>
                        @if($val->old_response != "" || $val->new_response != "")
                            <a href="javascript::void();" onclick="viewLog('{{$val->id}}')"><i class="fa fa-eye mr-1"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr class="txt-center">
                <td colspan="8">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right audit_report_paginate pegination-margin" id="hub_record_report_paginate">
{{ $query->links() }}
</div>