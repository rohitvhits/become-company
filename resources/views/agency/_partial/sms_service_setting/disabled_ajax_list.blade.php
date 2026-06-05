<table id="order-listing1" class="table table-bordered" >
        <thead>
            <tr>
                <th>No</th>
                <th>Services</th>
                <th>Type</th>
               
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $i = ($page * 50)-49;
            @endphp
           @if(count($query) >0)
                @foreach($query as $val)
                    <tr>
                        <td>{{ $i}}</td>
                        <td>{{ $val->name}}</td>
                        <td>{{ $val->types}}</td>
                        <td>
                            <label class="toggle-switch toggle-switch-success">
                                <input type="checkbox" name="is_service_sms" class="serviceSMSEnabled" @if(in_array($val->id,$disabledServices)) @else checked @endif onclick="disabledServiceStatus('{{ $val->id}}')">
                                <span class="toggle-slider round"></span>
                            </label>
                        </td>
                    </tr>
                    @php 
                        $i++;
                    @endphp
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="pull-right pagination-sms-service pegination-margin">
        
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>