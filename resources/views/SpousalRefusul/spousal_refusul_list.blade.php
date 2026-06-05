@if(!empty($query))
    @php 
        $i = ($page *50) -  49; 
    @endphp
    @foreach($query as $val)
        <tr>
            <td>{{ $i++}}</td>
            <td>{{ $val->first_name}}</td>
            <td>{{ $val->last_name}}</td>
            <td>{{ $val->ssn}}</td>
            <td>{{ date('m/d/Y',strtotime( $val->dob))}}</td>

            <td> @if($val->created_date !="") {{ date('m/d/Y',strtotime($val->created_date))}} @endif</td>
            <td> @if($val->updated_date !="") {{ date('m/d/Y',strtotime($val->updated_date))}} @endif</td>
            <td><a  data-toggle="modal" class="pull-right"
                data-target="#exampleModal-spousal"
                data-whatever="@mdo" href="javascript:void(0)" onclick="EditSpousal('{{ $val->id}}')"><i class="fa fa-edit"></i></a></td>
        </tr>
    @endforeach
@endif


@if(empty($query))
        <tr><td>No record available</td></tr>
@endif
