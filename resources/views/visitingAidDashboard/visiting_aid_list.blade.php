<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th>Record Id</th>
            <th>Agency Name</th>
            <th>Patient ID</th>
            <th>Requested ID</th>
            <th>Name/Mobile/DOB/Services/Gender/Priority</th>
            <th>Type/Discipline</th>
            <th>Service Name</th>
            <th>Service Status</th>
            <th>API Name</th>
            <th>Due Date</th>
            <th>Created Date / Created By </th>
        </tr>
    </thead>
    <tbody>
        @if (count($vistingAidData) > 0)
            @foreach($vistingAidData as $data)
            <tr>
                <th scope="data">{{$data->id}}</th>
                <td>{{ $data->agencyDetails->agency_name}}</td>
                <td>{{ $data->patient_id}}</td>
                <td>{{ $data->requested_service_id }}</td>
                <td> {{ $data->first_name }} {{ $data->last_name }}<br />
                    {{ $data->mobile }}<br />
                    
                    @if(isset($data->dob) && $data->dob != '0001-01-01' && $data->dob !="0000-00-00" && $data->dob != '1000-01-01')
                        {{ date('m/d/Y', strtotime($data->dob)) }}<br />
                    @endif
                    <!-- {{ $data->name }}<br /> -->
                    {{$data->gender}}<br />
                    {{ $data->third_party_priority}}  </td>
                <td>
                        {{ $data->type }}<br />
                        {{ $data->discipline }}<br />
                        @if($data->location_branch != "")
                            <p class="text-muted" style="font-size:10px">({{ $data->location_branch }})</p>
                        @endif
                </td>
                <td>
                    {{ $data->serviceName }}
                </td>
                <td>
                    @if(isset($data->serviceDetails->status) && strtolower($data->serviceDetails->status)) 
                        @if(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'scheduled')
                            <span class="badge badge-secondary">Scheduled</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'booked')
                            <span class="badge badge-info">Booked</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'cancelled')
                            <span class="badge badge-secondary">Cancelled</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'MarkAsHospitalized/Rehab')
                            <span class="badge badge-default">hospitalized/rehab</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'inservice')
                            <span class="badge badge-primary">In Service</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'noshow')
                            <span class="badge badge-pink">No Show</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'notinterested')
                            <span class="badge badge-secondary">Not Interested</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'onhold')
                            <span class="badge badge-secondary">On Hold</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'onleave')
                            <span class="badge badge-info">On Leave</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'pendingtermination')
                            <span class="badge badge-danger">Pending Termination</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'processing')
                            <span class="badge badge-secondary">Processing</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'refused')
                            <span class="badge badge-light">Refused</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'terminated')
                            <span class="badge badge-danger">Terminated</span>
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'unabletocontact')
                            <span class="badge badge-danger">Unable To Contact</span>
                        
                        @elseif(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status) == 'scheduled')
                            <span class="badge badge-danger">Unable To Contact</span>
                        @else
                        
                            <span class="badge badge-primary">Pending</span>
                        @endif
                    @endif
                </td>
                <td>
                @if(isset($data->agencyGenerateDetails) && $data->agencyGenerateDetails->notes != "")
                                        {{ $data->agencyGenerateDetails->notes }}
                                    @endif
                </td>
                <td>@if(isset($data->due_date) && $data->due_date !="" && $data->due_date !="0000-00-00"){{ date('m/d/Y', strtotime($data->due_date)) }}@endif</td>
                <td>{{ date('m/d/Y h:i A', strtotime($data->created_date)) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif           
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $vistingAidData->appends(request()->query())->links() }}
</div>