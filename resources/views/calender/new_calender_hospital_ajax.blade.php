
<table class="table table-bordered" style="width:100%">
    <tr>
        <th>All-Days</th>
            @foreach($week as $day)
               <th> {{$day}}</th>
            @endforeach
     
    </tr>

    
<tbody id="table-body">
    @foreach($time as $tm)
        <tr>
            <td>{{$tm}}</td>
             @foreach($week as $day)
             <td>
                @if(!empty($finalArray[$day][date('h:i',strtotime($tm))][0]))
                   @foreach($finalArray[$day][date('h:i',strtotime($tm))] as $val)
                        {{$val->patient->first_name.' '. $val->patient->last_name}} 
                   @endforeach
                   
                @else

                @endif
                 </td>
             @endforeach
            
        </tr>
    @endforeach
</tbody>

</table>