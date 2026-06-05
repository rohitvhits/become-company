<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th style="width:10%">#</th>
            <th style="width:20%">Name</th>
            <th style="width:10%">Location</th>

            <th style="width:20%">Service</th>
            <th style="width:10%">Date</th>
            <th style="width:10%">Time</th>
            <th style="width:10%">Created Date</th>
            <th style="width:10%">Created By</th>
        </tr>
    </thead>
    <tbody>

                    @if(count($pastAppointment)>0)
                    @foreach($pastAppointment as $key => $appointment)
                    <tr>
                        <td>{{$key+1}}
                            @if($appointment->patient_id != $record->id)
                                <br/>
                                <span style="top: 0;background: #00BBE0;padding: 1px 2px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>
                            @endif
                            @if(isset($appointment->telehealth_date))
                                <br/>
                                <label for="" class="badge badge-primary">Telehealth</label>
                            @endif
                        </td>
                        <td class="white_space">{{$appointment->patient->full_name}}</td>
                        <td>@if(isset($appointment->location) && $appointment->location->address1){{$appointment->location->address1}} @endif</td>
                        <td>{{isset($servie[$key]) ? $servie[$key] : ''}}</td>
                        <td>@if(isset($appointment->appointment_date))
                                {{date('m/d/Y', strtotime($appointment->appointment_date))}}
                            @elseif(isset($appointment->telehealth_date))
                                {{date('m/d/Y', strtotime($appointment->telehealth_date))}}
                            @endif</td>
                        <td>@if(isset($appointment->appointment_time))
                                {{date('h:i:s A', strtotime($appointment->appointment_time))}}
                            @elseif(isset($appointment->telehealth_time_slot))
                                {{$appointment->telehealth_time_slot}}
                            @endif</td>
                        <td>{{date('m/d/Y h:i A', strtotime($appointment->created_at))}}</td>
                        <td>@if(isset($appointment->getCreatedBy->full_name)){{$appointment->getCreatedBy->full_name}}@endif</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="9" style="text-align: center;">Data not found</td>
                    </tr>
                    @endif

                </tbody>
    
</table>
<div class="pull-right pegination-margin">
</div>