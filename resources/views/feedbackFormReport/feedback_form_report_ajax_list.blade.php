<div id="accordion-1" class="accordion">
    @if (count($query) > 0)
    @foreach ($query as $row)
    <div class="card">
        <div class="card-header tableData" id="headingOne">
            <div class="mb-0" style="background:#ddd;">
                <div class="container-fluid py-2" style="justify-content:space-between;display:flex">
                    <strong>{{ $row->patientDetail->first_name}} {{ $row->patientDetail->last_name}} </strong>
                    <span>{{ date('m/d/Y H:i A',strtotime($row->created_at)) }}</span>
                </div>
            </div>
        </div>
        <div id="collapseOne" class="collapse show" style="margin-left: 10px;" aria-labelledby="headingOne" data-parent="#accordion-1">
            <div class="row">
                <div class="col-md-3">
                    <div class="card-body" style="justify-content:space-between;display:flex;margin: 0px;padding: 0px 18px;flex-direction: column">
                        <p><b>#Record ID </b>:<a target="_blank" href="{{url('patient/view/' . $row->patient_id)}}">{{$row->patient_id}}</a></p>
                        <p><b>Agency Name</b>: {{$row->patientDetail->agencyDetail->agency_name}}</p>
                        @if(isset($row->serviceRequestDetail->patientServiceRequestRelationShip))
                        <p><b>Services</b>:
                            @php $service_array = array(); @endphp
                            @foreach($row->serviceRequestDetail->patientServiceRequestRelationShip as $service)
                            @foreach($service->services as $ser)
                            @php $service_array[] = $ser->name ?? ''; @endphp
                            @endforeach
                            @endforeach
                            {{ implode(',', $service_array) }}
                        </p>
                        @endif
                        @if(isset($row->serviceRequestDetail->completed_date) && !empty($row->serviceRequestDetail->completed_date))
                        <p><b>Completed Date</b>: {{ date('m/d/Y',strtotime($row->serviceRequestDetail->completed_date)) }}</p>
                        @endif
                        <p><b>Ip Address</b>: {{$row->ip_address}}</p>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card-body" style="margin: 0px;padding: 0px 18px;max-height: 180px;overflow-y: auto;">
                        <p><b>Patient Feedback Summary</b>:
                        <div class="row">
                            @if(isset($row->answer_response) && !empty($row->answer_response))
                            @php $notShow = 0; @endphp
                            @php $json_data = json_decode($row->answer_response) @endphp
                            @foreach($json_data as $key => $val)
                            @if($val->answer != null)
                            <div class="col-md-6">
                                <p>{{ $key + 1 }}. <b>@php echo $val->question??''; @endphp</b></p>
                                <p>@php echo $val->answer??''; @endphp</p>
                            </div>
                            @else
                            @php $notShow++; @endphp
                            @endif
                            @endforeach
                            @if($notShow == count($json_data))
                            <div class="col-md-6">
                                <p>The patient has visited the link but submitted a blank response.</p>
                            </div>
                            @endif
                            @endif
                        </div>
                        </p>
                    </div>
                </div>
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