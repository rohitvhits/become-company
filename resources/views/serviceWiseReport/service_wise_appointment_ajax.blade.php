<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>

            <th>HHAX Code</th>
            <th class="no_warp">Caregiver Last Name</th>
            <th>Caregiver First Name</th>

            <th>Caregiver Phone Number</th>
            <th class="no_warp">Caregiver Email</th>
            @foreach($master_list as $mtb)
            <th class="no_warp">{{$mtb}}</th>
            @endforeach


        </tr>
    </thead>
    <tbody>
    

    @if(!empty($list[0]))
    @php 
    $i = 1 + (($list->currentPage() - 1) * $list->perPage());
    @endphp
        @foreach($list as $val)
            <tr>
                <td><a target="_blank" href="{{ url('/patient/view/')}}/{{ $val->patientDetails->id }}">{{ $val->patientDetails->patient_code}}</a></td>
                <td><a target="_blank" href="{{ url('/patient/view/')}}/{{ $val->patientDetails->id }}">{{ $val->patientDetails->last_name}}</a></td>
                <td><a target="_blank" href="{{ url('/patient/view/')}}/{{ $val->patientDetails->id }}">{{ $val->patientDetails->first_name}}</a></td>
                 <td>{{ $val->patientDetails->mobile}}</td>
                 <td>
                 {{ $val->patientDetails->email}}
                 </td>
                 @foreach($master_list as $key=>$mtb)
                 @php 
                    $lowercase = $key;
                 @endphp
                 <td>

                 @if(isset($val[$lowercase]) && $val[$lowercase] !="")
                    
                        {{ date('m/d/Y',strtotime($val[$lowercase]))}}
                 @endif
                 </td>
                 @endforeach
              
             
            </tr>
        @endforeach
    @endif

    @if(empty($list[0]))
        <tr><td colspan="{{ count($master_list) +5}}">No Record available</td></tr>
    @endif

    </tbody>
</table>
<div class="pull-right pegination-margin">
  
{{ $list->appends(request()->input())->links('pagination::bootstrap-4') }}              
                     </div>
                     <script>
    $('#total_record_id').html("{{ $list->total()}}")
</script>