<div class="table-responsive tableData">
<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>

            <th>ID</th>
            <th class="no_warp">SMS</th>
            <th>Status</th>
            @if (in_array($user->user_type_fk, [3, 184]))
           
                <th class="no_warp">Agency Name</th>
           @endif
            <th >Type/Discipline</th>
            <th class="no_warp">Patient Code </th>
            <th class="no_warp">Name/Mobile/DOB/Services </th>
            
            <th nowrap>Appointment Date - Location</th>
            <th>Created Date</th>
           
            
            <th nowrap>Completed Date</th>
            <th nowrap>Follow Up Date</th>
            
            <th >Action</th>
        </tr>
    </thead>
    <tbody>
        
        @if(count($response) > 0)
            @php 
                $i = 1 + (($response->currentPage() - 1) * $response->perPage());
            @endphp
            @foreach($response as $row)
            <tr>
                <td nowrap><a href="{{ url('/patient/view/')}}/{{$row->patient_id}}"># {{ $row->patient_id }}</a></td>
                <td nowrap>
                    @if($row->patient_sms_flag ==1)
                    <span class='badge badge-success'>Sent</span>
                    @else
                    <span class='badge badge-warning'>Pending</span>
                    @endif
                </td>
                <td nowrap>
                    
                    @if (strtolower($row->status) == 'pending')
                    <label class='badge badge-warning'>Pending</label>
                    @elseif(strtolower($row->status) == 'booked')
                    <label class='badge badge-info'>Booked</label>

                    @elseif(strtolower($row->status) == 'completed')
                    <label class='badge badge-success'>Completed</label>
                    @elseif(strtolower($row->status) == 'cancelled')
                    <label class='badge badge-danger'>Cancelled</label>
                    @elseif(strtolower($row->status) == 'noshow')
                    <label class='badge badge-light'>No Show</label>
                    @elseif(strtolower($row->status) == 'refused')
                    <label class='badge badge-danger'>Refused</label>
                    @elseif(strtolower($row->status) == 'processing')
                    <label class='badge badge-info'>processing</label>
                    @elseif(strtolower($row->status) == 'arrived')
                    <label class='badge badge-primary'>Arrived</label>
                    @elseif(strtolower($row->status) == 'checkin')
                    <label class='badge badge-primary'>Mark as ClockIn</label>
                    @elseif(strtolower($row->status) == 'not interested')
                    <label class='badge badge-primary'>Not Interested</label>
                    @elseif(strtolower($row->status) == 'hospitalized/rehab')
                    <label class='badge badge-secondary'>Hospitalized/Rehab</label>
                    @elseif(strtolower($row->status) == 'unabletocontact')
                    <label class='badge badge-primary'>Unable To Contact</label>
                    @endif


                </td>
                @if(in_array($user->user_type_fk, array(3, 184)))
                        <td nowrap>{{ $row->patient->agencyDetail->agency_name}}</td>
                @endif

                <td nowrap>
                    {{$row->patient->type}}<br>
                    {{$row->patient->diciplin}}<br>
                    @if($row->patient->location_branch !="")
                    <p class="text-muted" style="font-size:10px">({{ $row->location_branch}})</p>
                    @endif
                </td>
                <td nowrap>{{ $row->patient->patient_code}}</td>
                <td nowrap>{{ $row->patient->first_name . ' ' . $row->patient->last_name}}<br />
                    {{$row->patient->mobile}}<br />
                  
                    @if (isset($row->patient->dob) && $row->patient->dob != '0001-01-01' && $row->patient->dob != '1000-01-01')
                        {{ date('m/d/Y', strtotime($row->patient->dob))}} <br />
                    @endif
                    ({{ $row->patient->gender}})<br />
                    @php 
                        $serviceArray = [];
                    @endphp

                    @if(!empty($row->patientServiceRequestRelationShip[0]))
                   
                        @php 
                            $serviceArray = [];
                        @endphp
                        @foreach($row->patientServiceRequestRelationShip as $srd)
                        
                            @php
                            $serviceArray[] = $srd->services[0]->name
                            @endphp
                        @endforeach
                    @endif

                    @php
                        $srvs = ""
                    @endphp
                    @if(!empty($serviceArray[0]))
                        @php 
                            $srvs = implode(',',$serviceArray)
                        @endphp
                    @endif

                    {{$srvs}}

                </td>
                <td nowrap>
                    @if(isset($row->appointmentDetails->id) && $row->appointmentDetails->id !="")
                    {{ date('m/d/Y h:i A',strtotime($row->appointmentDetails->appointment_date))}}  <br>
                   
                    
                    @endif
                    @if(isset($row->appointmentDetails->location))
                    {{ $row->appointmentDetails->location->location_name}}
                    @endif
                </td>
                <td >{{ date('m/d/Y h:i A',strtotime($row->created_at))}}</td>
                <td nowrap>@if(isset($row->completed_at) && $row->completed_at !=""){{ date('m/d/Y h:i A',strtotime($row->completed_at))}} @endif</td>
                <td nowrap>@if(isset($row->followup_date) && $row->followup_date !="")
                        {{ date('m/d/Y h:i A',strtotime($row->followup_date))}}
                    @endif
                </td>
                <td nowrap></td>
            </tr>
            @endforeach
        @endif

        @if(count($response) == 0)
            <tr>
                <td colspan="14">No record available</td>
            </tr>
        @endif

    </tbody>


</table>

<div class="pull-right pegination-margin">
  
    {{ $response->links() }}
</div>
</div>
