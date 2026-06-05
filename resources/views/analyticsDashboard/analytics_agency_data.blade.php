@php $count = count($agencyData) > 0 ? count($agencyData)/2 : 0; @endphp 
<div class="col-md-6 pl-0">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Processing</th>
                <th>Arrived</th>
                <th>Checkin</th>
            </tr>
        </thead>
        <tbody>
            @if (count($agencyData) > 0)
                @foreach($agencyData as $key => $row)
                @if($key < $count) 
                <tr>
                    <th scope="row">{{$row->agency_name}}</a>
                    </th>
                    @if(array_key_exists($row->id,$data))
                        @if(isset($data[$row->id]['processing']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=processing&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['processing']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                        @if(isset($data[$row->id]['arrived']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=arrived&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['arrived']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                        @if(isset($data[$row->id]['checkin']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=checkin&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['checkin']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                    @else
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                    @endif
                </tr>
                @endif
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif           
        </tbody>
    </table>
</div>
<div class="col-md-6 pr-0">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Processing</th>
                <th>Arrived</th>
                <th>Checkin</th>
            </tr>
        </thead>
        <tbody>
            @if (count($agencyData) > 0)
                @foreach($agencyData as $key => $row)
                @if($key > $count) 
                <tr>
                    <th scope="row">{{$row->agency_name}}</a>
                    </th>
                    @if(array_key_exists($row->id,$data))
                        @if(isset($data[$row->id]['processing']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=processing&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['processing']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                        @if(isset($data[$row->id]['arrived']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=arrived&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['arrived']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                        @if(isset($data[$row->id]['checkin']))
                            <td class="text-center"><a target="_blank" href="{{ url('patient-service-requested')}}?status[]=checkin&{{$agency_id}}&agency_fk[]={{$row->id}}&type={{ucwords($type)}}" class="small-box-footer"><div class="badge badge-pill badge-primary">{{ $data[$row->id]['checkin']}}</div></a></td>
                        @else
                            <td class="text-center">0</td>
                        @endif
                    @else
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                    @endif
                </tr>
                @endif
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif           
        </tbody>
    </table>
</div>

