<table id="agn_token_id" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Api Call</th>
            <th>Created Date</th>
          
        </tr>
    </thead>
    <tbody>
        @php
            $cnt = ($page * 50)-49
        @endphp

        @if(!empty($token_list[0]))
            @foreach($token_list as $val)

            
           
                <tr>
                    
                    <td>{{ $cnt++}}</td>
                    <td>{{ $val->api_call}}</td>
                    <td>{{ date('m/d/Y h:i A',strtotime($val->created_date))}}</td>
                    
                </tr>

            @endforeach
        @endif

        @if(empty($token_list[0]))
            <tr>
                <td colspan="7">No record available</td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin agn_api_token_id">
            
            {{ $token_list->appends(request()->input())->links() }}
        </div>