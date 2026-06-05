<div class="table-responsive ">
<table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th>Portal Id</th>
                <th>Name</th>
                <th>Type</th>
                <th>Document Name</th>
                <th>Fax No</th>
                <th>Response</th>
                
                <th>Created Date / Created By</th>
              
            </tr>
        </thead>
        <tbody>
            
            @if(count($query) >0)
                @php 
                    $cnt =($page * 50) -49;
                @endphp
                @foreach($query as $val)
               
                    <tr>
                        <td>{{ $cnt}}</td>
                        <td><a href="{{ url('patient/view/')}}/{{ $val->patient_id}}" target="_blank">{{ $val->patient_id}}</a></td>
                        <td>{{ $val->first_name.' '.$val->last_name}}</td>
                        <td>{{ $val->type}}</td>
                        <td>{{ $val->document_name}}</td>
                        <td>{{ $val->fax_no}}</td>
                        <td>@if(isset($val->response['errors']))
                            @else
                                @foreach($val->response as $key=>$va)
                                    @foreach($va as $k=>$value)
                                        {{ ucfirst(str_replace('_',' ',$k)) }}: {{ $value}}<br>
                                    @endforeach
                                @endforeach
                            @endif
                        </td>
                        <td>{{ Common::convertMDYTime($val->created_date)}} /<br>{{ $val->uFirstName.' '.$val->uLastName}}</td>
                    </tr>
                    @php 

                    $cnt++;
                    @endphp
                @endforeach
            @endif
            
            @if(count($query) ==0)
                    <tr>
                        <td colspan="8" style="text-align: center;">No record available</td>
                    </tr>
            @endif
        </tbody>
    </table>
    <div class="pull-right pegination-margin">
        
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    var total = "{{ $query->total()}}";
    $('#total_record_id').html(total)
    $('#blank_div').attr('style','margin-top:13%')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:13%')
    }

</script>